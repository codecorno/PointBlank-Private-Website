<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;
use XF\Util\Arr;

class Bookmark extends Repository
{
	/**
	 * @param $userId
	 *
	 * @return Finder
	 */
	public function findBookmarksForUser($userId)
	{
		return $this->finder('XF:BookmarkItem')
			->where('user_id', $userId)
			->setDefaultOrder('bookmark_date', 'DESC');
	}

	/**
	 * @param $userId
	 * @param $labels
	 *
	 * @return Finder
	 */
	public function findBookmarksForUserByLabel($userId, $label)
	{
		return $this->finder('XF:BookmarkLabelUse')
			->with('Label', true)
			->with('Bookmark', true)
			->where('Label.user_id', $userId)
			->where('Label.label', $label)
			->setDefaultOrder('Bookmark.bookmark_date', 'DESC')
			->pluckFrom('Bookmark');
	}

	/**
	 * @param $userId
	 *
	 * @return Finder
	 */
	public function findLabelsForUser($userId)
	{
		return $this->finder('XF:BookmarkLabel')
			->where('user_id', $userId)
			->where('use_count', '>', 0)
			->setDefaultOrder('use_count', 'DESC');
	}

	/**
	 * @param \XF\Mvc\Entity\ArrayCollection|\XF\Entity\BookmarkItem[] $bookmarks
	 */
	public function addContentToBookmarks($bookmarks)
	{
		$contentMap = [];
		foreach ($bookmarks AS $key => $bookmark)
		{
			$contentType = $bookmark->content_type;
			if (!isset($contentMap[$contentType]))
			{
				$contentMap[$contentType] = [];
			}
			$contentMap[$contentType][$key] = $bookmark->content_id;
		}

		foreach ($contentMap AS $contentType => $contentIds)
		{
			$handler = $this->getBookmarkHandler($contentType);
			if (!$handler)
			{
				continue;
			}
			$data = $handler->getContent($contentIds);
			foreach ($contentIds AS $bookmarkId => $contentId)
			{
				$content = isset($data[$contentId]) ? $data[$contentId] : null;
				$bookmarks[$bookmarkId]->setContent($content);
			}
		}
	}

	public function fastDeleteBookmarksForContent($contentType, $contentId)
	{
		$finder = $this->finder('XF:BookmarkItem')
			->where([
				'content_type' => $contentType,
				'content_id' => $contentId
			]);
		$this->deleteBookmarksInternal($finder);
	}

	protected function deleteBookmarksInternal(Finder $matches)
	{
		$delete = $matches->fetchColumns('bookmark_id');

		$db = $this->db();
		$db->beginTransaction();

		if ($delete)
		{
			$db->delete('xf_bookmark_item', 'bookmark_id IN (' . $db->quote($delete) . ')');

			$labelIds = $db->fetchAllColumn('
				SELECT label_id
				FROM xf_bookmark_label_use
				WHERE bookmark_id IN(' . $db->quote($delete) . ')
			');

			if ($labelIds)
			{
				$db->delete('xf_bookmark_label_use', 'bookmark_id IN(' . $db->quote($delete) . ')');
				$this->recalculateLabelUsageCache($labelIds);
			}
		}

		$db->commit();
	}

	/**
	 * @param $type
	 * @param bool $throw
	 *
	 * @return \XF\Bookmark\AbstractHandler|null
	 * @throws \Exception
	 */
	public function getBookmarkHandler($type, $throw = false)
	{
		$handlerClass = \XF::app()->getContentTypeFieldValue($type, 'bookmark_handler_class');
		if (!$handlerClass)
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("No Bookmark handler for '$type'");
			}
			return null;
		}

		if (!class_exists($handlerClass))
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("Bookmark handler for '$type' does not exist: $handlerClass");
			}
			return null;
		}

		$handlerClass = \XF::extendClass($handlerClass);
		return new $handlerClass($type);
	}

	public function splitLabels($tagList)
	{
		return Arr::stringToArray($tagList, '/\s*,\s*/');
	}

	public function generateLabelUrlVersion($label, \XF\Entity\User $user = null)
	{
		if ($user === null)
		{
			$user = \XF::visitor();
		}

		$urlVersion = preg_replace('/[^a-zA-Z0-9_ -]/', '', utf8_romanize(utf8_deaccent($label)));
		$urlVersion = preg_replace('/[ -]+/', '-', $urlVersion);

		$db = $this->db();

		if (!strlen($urlVersion))
		{
			$urlVersion = 1 + intval($db->fetchOne("
				SELECT MAX(label_id)
				FROM xf_bookmark_label
				WHERE user_id = ?
			", $user->user_id));
		}
		else
		{
			$existing = $db->fetchRow("
				SELECT *
				FROM xf_bookmark_label
				WHERE (label_url = ?
					OR (label_url LIKE ? AND label_url REGEXP ?))
				AND user_id = ?
				ORDER BY label_id DESC
				LIMIT 1
			", [$urlVersion, "$urlVersion-%", "^{$urlVersion}-[0-9]+\$", $user->user_id]);
			if ($existing)
			{
				$counter = 1;
				if ($existing['label_url'] != $urlVersion && preg_match('/-(\d+)$/', $existing['label_url'], $match))
				{
					$counter = $match[1];
				}

				$testExists = true;
				while ($testExists)
				{
					$counter++;
					$testExists = $db->fetchOne("
						SELECT label_id
						FROM xf_bookmark_label
						WHERE label_url = ?
						AND user_id = ?
					", ["$urlVersion-$counter", $user->user_id]);
				}

				$urlVersion .= "-$counter";
			}
		}

		return $urlVersion;
	}

	public function getLabelsForUser(array $labelNames, \XF\Entity\User $user, &$notFound = [])
	{
		$notFound = [];

		$labels = $this->finder('XF:BookmarkLabel')
			->where('label', array_values($labelNames))
			->where('user_id', $user->user_id)
			->fetch();

		return $this->getNamedLabelsInList($labelNames, $labels->toArray(), $notFound);
	}

	public function getNamedLabelsInList(array $named, array $list, &$notFound = [])
	{
		$found = [];
		$notFound = [];

		$duplicateTest = [];

		foreach ($named AS $labelName)
		{
			$testLabelName = utf8_deaccent(utf8_strtolower($labelName));
			if (isset($duplicateTest[$testLabelName]))
			{
				continue;
			}

			$duplicateTest[$testLabelName] = true;

			$foundKey = null;

			foreach ($list AS $key => $label)
			{
				if ($testLabelName == utf8_deaccent(utf8_strtolower($label->label)))
				{
					$foundKey = $key;
					break;
				}
			}

			if ($foundKey === null)
			{
				$notFound[$testLabelName] = $labelName;
			}
			else
			{
				$found[$foundKey] = $list[$foundKey];
			}
		}

		$notFound = array_values($notFound); // prevent the same label potentially being not found multiple times

		return $found;
	}

	public function getLabelAutoCompleteResults($search, \XF\Entity\User $user = null, $maxResults = 10)
	{
		if ($user === null)
		{
			$user = \XF::visitor();
		}

		$finder = $this->finder('XF:BookmarkLabel');
		$labels = $finder
			->where('label', 'like', $finder->escapeLike($search, '?%'))
			->where('user_id', $user->user_id)
			->where('use_count', '>', 0)
			->order('label')
			->fetch($maxResults);

		if ($labels->count() < $maxResults)
		{
			$finder = $this->finder('XF:BookmarkLabel');
			$extraTags = $finder
				->where('label', 'like', $finder->escapeLike($search, '%?%'))
				->where('label', 'not like', $finder->escapeLike($search, '?%'))
				->where('user_id', $user->user_id)
				->where('use_count', '>', 0)
				->order('label')
				->fetch($maxResults - $labels->count());

			$labels = $labels->merge($extraTags);
		}

		return $labels;
	}

	public function createLabelForUser($labelName, \XF\Entity\User $user)
	{
		/** @var \XF\Entity\BookmarkLabel $label */
		$label = $this->em->create('XF:BookmarkLabel');
		$label->label = $labelName;
		$label->user_id = $user->user_id;
		$label->preSave();

		if ($label->hasErrors())
		{
			return $this->finder('XF:BookmarkLabel')
				->where('label', $labelName)
				->where('user_id', $user->user_id)
				->fetchOne();
		}

		$label->save();

		return $label;
	}

	public function modifyBookmarkLabelUses(\XF\Entity\BookmarkItem $bookmark, array $addIds, array $removeIds)
	{
		$db = $this->db();
		$db->beginTransaction();

		if ($removeIds)
		{
			$this->removeLabelUsesFromBookmark($removeIds, $bookmark->bookmark_id);
		}

		if ($addIds)
		{
			$this->addLabelIdsToBookmark($addIds, $bookmark->bookmark_id);
		}

		$cache = $this->getLabelUseCache($bookmark->bookmark_id);
		$bookmark->labels = $cache;
		$bookmark->save();

		$db->commit();

		return $cache;
	}

	protected function removeLabelUsesFromBookmark(array $labelIds, $bookmarkId)
	{
		if ($labelIds)
		{
			$db = $this->db();
			$db->query("
				DELETE FROM xf_bookmark_label_use
				WHERE label_id IN (" . $db->quote($labelIds) . ")
					AND bookmark_id = ?
			", $bookmarkId);
			$this->recalculateLabelUsageCache($labelIds);
		}
	}

	public function recalculateLabelUsageCache($labelIds)
	{
		if (!$labelIds)
		{
			return;
		}

		if (!is_array($labelIds))
		{
			$labelIds = [$labelIds];
		}

		$db = $this->db();

		$labels = $db->fetchAllColumn("
			SELECT label_id
			FROM xf_bookmark_label
			WHERE label_id IN (" . $db->quote($labelIds) . ")
		");
		$results = $db->fetchAllKeyed("
			SELECT label_id,
				COUNT(*) AS use_count,
				use_date AS last_use_date
			FROM xf_bookmark_label_use
			WHERE label_id IN (" . $db->quote($labelIds) . ")
			GROUP BY label_id
		", 'label_id');

		$db->beginTransaction();

		foreach ($labels AS $labelId)
		{
			$delete = false;

			if (isset($results[$labelId]))
			{
				$result = $results[$labelId];
				if (!$result['use_count'])
				{
					// this shouldn't actually happen since there shouldn't be a row
					$delete = true;
				}
				else
				{
					$db->update('xf_bookmark_label', [
						'use_count' => $result['use_count'],
						'last_use_date' => $result['last_use_date']
					], 'label_id = ?', $labelId);
				}
			}
			else
			{
				$delete = true;
			}

			if ($delete)
			{
				$db->delete('xf_bookmark_label', 'label_id = ?', $labelId);
			}
		}

		$db->commit();
	}

	protected function addLabelIdsToBookmark(array $labelIds, $bookmarkId)
	{
		$db = $this->db();

		$insertedIds = [];

		foreach ($labelIds AS $addId)
		{
			$inserted = $db->insert('xf_bookmark_label_use', [
				'bookmark_id' => $bookmarkId,
				'label_id' => $addId,
				'use_date' => \XF::$time,
			], false, false, 'IGNORE');
			$contentLabelId = $db->lastInsertId();

			$db->query("
				UPDATE xf_bookmark_label
				SET use_count = use_count + 1,
					last_use_date = ?
				WHERE label_id = ?
			", [\XF::$time, $addId]);
			if ($inserted)
			{
				$insertedIds[$contentLabelId] = $addId;
			}
		}

		return $insertedIds;
	}

	public function rebuildBookmarkLabelCache($bookmarkId)
	{
		$bookmark = $this->em->find('XF:BookmarkItem', $bookmarkId);
		if (!$bookmark)
		{
			return false;
		}

		$cache = $this->getLabelUseCache($bookmark->bookmark_id);

		$bookmark->labels = $cache;
		$bookmark->save();

		return true;
	}

	public function getLabelUseCache($bookmarkId)
	{
		$labels = $this->db()->fetchAll("
			SELECT l.*
			FROM xf_bookmark_label_use AS lu
			INNER JOIN xf_bookmark_label AS l ON (lu.label_id = l.label_id)
			WHERE lu.bookmark_id = ?
			ORDER BY l.label
		", $bookmarkId);
		$cache = [];
		foreach ($labels AS $label)
		{
			$cache[$label['label_id']] = [
				'label' => $label['label'],
				'label_url' => $label['label_url']
			];
		}

		return $cache;
	}
}