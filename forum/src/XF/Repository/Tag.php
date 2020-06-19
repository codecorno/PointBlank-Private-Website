<?php

namespace XF\Repository;

use XF\Mvc\Entity\Repository;
use XF\Util\Arr;

class Tag extends Repository implements \XF\ResultSetInterface
{
	public function splitTagList($tagList)
	{
		return Arr::stringToArray($tagList, '/\s*,\s*/');
	}

	public function normalizeTag($tag)
	{
		$tag = utf8_strtolower($tag);

		try
		{
			// if this matches, then \v isn't known (appears to be PCRE < 7.2) so don't strip
			if (!preg_match('/\v/', 'v'))
			{
				$new = preg_replace('/\v+/u', ' ', $tag);
				if (is_string($new))
				{
					$tag = $new;
				}
			}
		}
		catch (\Exception $e) {}
		$tag = preg_replace('/\s+/u', ' ', $tag);

		$tag = preg_replace('/^[^\d\pL]+(.*)[^\d\pL]+$/siUu', '$1', $tag);
		$tag = trim($tag);

		return $tag;
	}

	public function isValidTag($tag)
	{
		$tag = $this->normalizeTag($tag);

		$length = utf8_strlen($tag);
		$lengthLimits = $this->options()->tagLength;

		$minLength = max($lengthLimits['min'], 1);
		$maxLength = $lengthLimits['max'] <= 0 ? 100 : min($lengthLimits['max'], 100);

		if ($length < $minLength)
		{
			return false;
		}
		if ($length > $maxLength)
		{
			return false;
		}

		$validation = $this->options()->tagValidation;

		$disallowed = Arr::stringToArray($validation['disallowedWords'], '/\r?\n/');
		if ($disallowed)
		{
			foreach ($disallowed AS $disallowedCheck)
			{
				$disallowedCheck = trim($disallowedCheck);
				if ($disallowedCheck === '')
				{
					continue;
				}
				if (stripos($tag, $disallowedCheck) !== false)
				{
					return false;
				}
			}
		}

		if ($validation['matchRegex'] && !preg_match('/\W[\s\w]*e[\s\w]*$/', $validation['matchRegex']))
		{
			try
			{
				if (!preg_match($validation['matchRegex'], $tag))
				{
					return false;
				}
			}
			catch (\Exception $e)
			{
				$this->app()->logException($e, false, 'Error with tag validation regex: ');
			}
		}

		$censored = $this->app()->stringFormatter()->censorText($tag);
		if ($censored != $tag)
		{
			return false;
		}

		return true;
	}

	public function getTagAutoCompleteResults($search, $maxResults = 10)
	{
		$finder = $this->finder('XF:Tag');
		$tags = $this->finder('XF:Tag')
			->where('tag', 'like', $finder->escapeLike($search, '?%'))
			->whereOr(
				['use_count', '>', 0],
				['permanent', '=', 1]
			)
			->order('tag')
			->fetch($maxResults);

		if ($tags->count() < $maxResults)
		{
			$finder = $this->finder('XF:Tag');
			$extraTags = $this->finder('XF:Tag')
				->where('tag', 'like', $finder->escapeLike($search, '%?%'))
				->where('tag', 'not like', $finder->escapeLike($search, '?%'))
				->whereOr(
					['use_count', '>', 0],
					['permanent', '=', 1]
				)
				->order('tag')
				->fetch($maxResults - $tags->count());

			$tags = $tags->merge($extraTags);
		}

		return $tags;
	}

	public function createTag($tagName)
	{
		$tag = $this->em->create('XF:Tag');
		$tag->tag = $tagName;
		$tag->preSave();

		if ($tag->hasErrors())
		{
			return $this->finder('XF:Tag')->where('tag', $tagName)->fetchOne();
		}

		$tag->save();

		return $tag;
	}

	public function generateTagUrlVersion($tag)
	{
		$urlVersion = preg_replace('/[^a-zA-Z0-9_ -]/', '', utf8_romanize(utf8_deaccent($tag)));
		$urlVersion = preg_replace('/[ -]+/', '-', $urlVersion);

		$db = $this->db();

		if (!strlen($urlVersion))
		{
			$urlVersion = 1 + intval($db->fetchOne("
				SELECT MAX(tag_id)
				FROM xf_tag
			"));
		}
		else
		{
			$existing = $db->fetchRow("
				SELECT *
				FROM xf_tag
				WHERE tag_url = ?
					OR (tag_url LIKE ? AND tag_url REGEXP ?)
				ORDER BY tag_id DESC
				LIMIT 1
			", [$urlVersion, "$urlVersion-%", "^{$urlVersion}-[0-9]+\$"]);
			if ($existing)
			{
				$counter = 1;
				if ($existing['tag_url'] != $urlVersion && preg_match('/-(\d+)$/', $existing['tag_url'], $match))
				{
					$counter = $match[1];
				}

				$testExists = true;
				while ($testExists)
				{
					$counter++;
					$testExists = $db->fetchOne("
						SELECT tag_id
						FROM xf_tag
						WHERE tag_url = ?
					", "$urlVersion-$counter");
				}

				$urlVersion .= "-$counter";
			}
		}

		return $urlVersion;
	}

	public function getTags(array $tags, &$notFound = [])
	{
		$notFound = [];

		$normalized = [];
		foreach ($tags AS $k => $tag)
		{
			$tag = $this->normalizeTag($tag);
			if (strlen($tag))
			{
				$normalKey = utf8_deaccent($tag);
				$normalized[$normalKey] = $tag;
			}
		}

		if (!$normalized)
		{
			return [];
		}

		$normalized = array_values($normalized);

		$tags = $this->finder('XF:Tag')->where('tag', $normalized)->fetch();

		return $this->getNamedTagsInList($normalized, $tags->toArray(), $notFound);
	}

	public function getNamedTagsInList(array $named, array $list, &$notFound = [])
	{
		$found = [];
		$notFound = [];

		foreach ($named AS $tagName)
		{
			$tagName = $this->normalizeTag($tagName);
			$tagCompare = utf8_strtolower(utf8_deaccent($tagName));
			$foundKey = null;

			foreach ($list AS $key => $tag)
			{
				$listTagCompare = utf8_strtolower(utf8_deaccent($tag->tag));
				if ($tagCompare == $listTagCompare)
				{
					$foundKey = $key;
					break;
				}
			}

			if ($foundKey === null)
			{
				$notFound[$tagCompare] = $tagName;
			}
			else
			{
				$found[$foundKey] = $list[$foundKey];
			}
		}

		$notFound = array_values($notFound); // prevent the same tag potentially being not found multiple times

		return $found;
	}

	public function getTagsForCloud($limit, $minUses = 1)
	{
		$db = $this->db();
		$ids = $db->fetchAllColumn($db->limit("
			SELECT tag_id
			FROM xf_tag
			WHERE use_count >= ?
			ORDER BY use_count DESC
		", $limit), $minUses);
		if (!$ids)
		{
			return [];
		}

		return $this->finder('XF:Tag')->where('tag_id', $ids)->order('tag')->fetch()->toArray();
	}

	public function getTagCloud(array $tags, $levels = 7)
	{
		if (!$tags)
		{
			return [];
		}

		$min = PHP_INT_MAX;
		$max = 0;

		foreach ($tags AS $tag)
		{
			$uses = $tag->use_count;
			if ($uses < $min)
			{
				$min = $uses;
			}
			if ($uses > $max)
			{
				$max = $uses;
			}
		}

		$levelSize = ($max - $min) / $levels;
		$output = [];

		if ($min == $max)
		{
			$middle = ceil($levels / 2);
			foreach ($tags AS $key => $tag)
			{
				$output[$key] = [
					'tag' => $tag,
					'level' => $middle
				];
			}
		}
		else
		{
			foreach ($tags AS $key => $tag)
			{
				$diffFromMin = $tag->use_count - $min;
				if (!$diffFromMin)
				{
					$level = 1;
				}
				else
				{
					$level = min($levels, ceil($diffFromMin / $levelSize));
				}

				$output[$key] = [
					'tag' => $tag,
					'level' => $level
				];
			}
		}

		return $output;
	}

	/**
	 * @param string $contentType
	 * @param int $contentId
	 *
	 * @return \XF\Mvc\Entity\Finder
	 */
	public function findContentTags($contentType, $contentId)
	{
		$finder = $this->finder('XF:TagContent');
		$finder->where([
				'content_type' => $contentType,
				'content_id' => $contentId
			])
			->with('Tag', true)
			->order('Tag.tag');

		return $finder;
	}

	public function modifyContentTags($contentType, $contentId, array $addIds, array $removeIds, $userId = null)
	{
		$handler = $this->getTagHandler($contentType, true);
		$content = $handler->getContent($contentId);
		if (!$content)
		{
			return null;
		}

		if ($userId === null)
		{
			$userId = \XF::visitor()->user_id;
		}

		$db = $this->db();
		$db->beginTransaction();

		if ($removeIds)
		{
			$this->removeTagIdsFromContent($removeIds, $contentType, $contentId);
		}

		if ($addIds)
		{
			$contentDate = $handler->getContentDate($content);
			$contentVisible = $handler->getContentVisibility($content);

			$this->addTagIdsToContent($addIds, $contentType, $contentId, $contentDate, $contentVisible, $userId);
		}

		$cache = $this->getContentTagCache($contentType, $contentId);
		$handler->updateContentTagCache($content, $cache);

		$db->commit();

		return $cache;
	}

	protected function removeTagIdsFromContent(array $tagIds, $contentType, $contentId)
	{
		if ($tagIds)
		{
			$db = $this->db();
			$db->query("
				DELETE FROM xf_tag_content
				WHERE tag_id IN (" . $db->quote($tagIds) . ")
					AND content_type = ?
					AND content_id = ?
			", [$contentType, $contentId]);
			$this->recalculateTagUsageCache($tagIds);
		}
	}

	protected function addTagIdsToContent(array $tagIds, $contentType, $contentId, $contentDate, $contentVisible, $addUserId)
	{
		$db = $this->db();

		$visibleSql = $contentVisible ? 1 : 0;

		$insertedIds = [];

		foreach ($tagIds AS $addId)
		{
			$inserted = $db->insert('xf_tag_content', [
				'content_type' => $contentType,
				'content_id' => $contentId,
				'tag_id' => $addId,
				'add_user_id' => $addUserId,
				'add_date' => \XF::$time,
				'content_date' => $contentDate,
				'visible' => $visibleSql
			], false, false, 'IGNORE');
			$contentTagId = $db->lastInsertId();

			if ($inserted && $contentVisible)
			{
				$db->query("
					UPDATE xf_tag
					SET use_count = use_count + 1,
						last_use_date = ?
					WHERE tag_id = ?
				", [\XF::$time, $addId]);
			}
			if ($inserted)
			{
				$insertedIds[$contentTagId] = $addId;
			}
		}

		return $insertedIds;
	}

	public function removeContentTags($contentType, $contentId)
	{
		$db = $this->db();
		$tagIds = $db->fetchPairs("
			SELECT tag_id, visible
			FROM xf_tag_content
			WHERE content_type = ?
				AND content_id = ?
		", [$contentType, $contentId]);
		if (!$tagIds)
		{
			return;
		}

		$recalc = [];
		foreach ($tagIds AS $id => $visible)
		{
			if ($visible)
			{
				$recalc[] = $id;
			}
		}

		$db->beginTransaction();

		$db->query("
			DELETE FROM xf_tag_content
			WHERE content_type = ?
				AND content_id = ?
		", [$contentType, $contentId]);

		$this->recalculateTagUsageCache($recalc);

		$db->commit();
	}

	public function getContentTagCache($contentType, $contentId)
	{
		$tags = $this->db()->fetchAll("
			SELECT t.*
			FROM xf_tag_content AS tc
			INNER JOIN xf_tag AS t ON (tc.tag_id = t.tag_id)
			WHERE tc.content_type = ?
				AND tc.content_id = ?
			ORDER BY t.tag
		", [$contentType, $contentId]);
		$cache = [];
		foreach ($tags AS $tag)
		{
			$cache[$tag['tag_id']] = [
				'tag' => $tag['tag'],
				'tag_url' => $tag['tag_url']
			];
		}

		return $cache;
	}

	public function rebuildContentTagCache($contentType, $contentId)
	{
		$handler = $this->getTagHandler($contentType, false);
		if (!$handler)
		{
			return false;
		}

		$content = $handler->getContent($contentId);
		if (!$content)
		{
			return false;
		}

		$cache = $this->getContentTagCache($contentType, $contentId);
		$handler->updateContentTagCache($content, $cache);

		return true;
	}

	public function updateContentVisibility($contentType, $contentId, $visibility)
	{
		$db = $this->db();
		$tagIds = $db->fetchAll("
			SELECT tag_id, tag_content_id, visible
			FROM xf_tag_content
			WHERE content_type = ?
				AND content_id = ?
		", [$contentType, $contentId]);
		if (!$tagIds)
		{
			return;
		}

		$newVisibleSql = $visibility ? 1 : 0;
		$update = [];
		$recalc = [];
		foreach ($tagIds AS $tag)
		{
			if ($newVisibleSql != $tag['visible'])
			{
				$update[] = $tag['tag_content_id'];
				$recalc[] = $tag['tag_id'];
			}
		}
		if (!$update)
		{
			return;
		}

		$db->beginTransaction();

		$db->update('xf_tag_content',
			['visible' => $newVisibleSql],
			'tag_content_id IN (' . $db->quote($update) . ')'
		);
		$this->recalculateTagUsageCache($recalc);

		$db->commit();
	}

	public function recalculateTagUsageCache($tagIds)
	{
		if (!$tagIds)
		{
			return;
		}

		if (!is_array($tagIds))
		{
			$tagIds = [$tagIds];
		}

		$db = $this->db();

		$tags = $db->fetchPairs("
			SELECT tag_id, permanent
			FROM xf_tag
			WHERE tag_id IN (" . $db->quote($tagIds) . ")
		");
		$results = $db->fetchAllKeyed("
			SELECT tag_id,
				COUNT(IF(visible, 1, NULL)) AS use_count,
				COUNT(*) AS raw_use_count,
				MAX(IF(visible, add_date, 0)) AS last_use_date
			FROM xf_tag_content
			WHERE tag_id IN (" . $db->quote($tagIds) . ")
			GROUP BY tag_id
		", 'tag_id');

		$db->beginTransaction();

		foreach ($tags AS $tagId => $permanent)
		{
			$delete = false;

			if (isset($results[$tagId]))
			{
				$result = $results[$tagId];
				if (!$result['use_count'] && !$result['raw_use_count'])
				{
					// this shouldn't actually happen since there shouldn't be a row
					$delete = true;
				}
				else
				{
					$db->update('xf_tag', [
						'use_count' => $result['use_count'],
						'last_use_date' => $result['last_use_date']
					], 'tag_id = ?', $tagId);
				}
			}
			else
			{
				$delete = true;
			}

			if ($delete)
			{
				if ($permanent)
				{
					$db->update('xf_tag', [
						'use_count' => 0,
						'last_use_date' => 0
					], 'tag_id = ?', $tagId);
				}
				else
				{
					$db->delete('xf_tag', 'tag_id = ?', $tagId);
				}
			}
		}

		$db->commit();
	}

	public function recalculateTagUsageCacheByContent($contentType, $contentId)
	{
		$tagIds = $this->db()->fetchAllColumn("
			SELECT tag_id
			FROM xf_tag_content
			WHERE content_type = ?
				AND content_id = ?
		", [$contentType, $contentId]);
		$this->recalculateTagUsageCache($tagIds);
	}

	public function getTagSearchResults($tagId, $limit, $visibleOnly = true)
	{
		$limit = max(1, intval($limit));

		$results = $this->db()->query("
			SELECT content_type, content_id
			FROM xf_tag_content
			WHERE tag_id = ?
				" . ($visibleOnly ? "AND visible = 1" : '') . "
			ORDER BY content_date DESC
			LIMIT {$limit}
		", $tagId);
		$output = [];
		while ($result = $results->fetch())
		{
			$type = $result['content_type'];
			$id = $result['content_id'];
			$output["{$type}-{$id}"] = [$type, $id];
		}

		return $output;
	}

	/**
	 * @param int $tagId
	 * @param null|int $userId
	 *
	 * @return \XF\Entity\TagResultCache
	 */
	public function getTagResultCache($tagId, $userId = null)
	{
		if ($userId === null)
		{
			$userId = \XF::visitor()->user_id;
		}

		$cache = $this->finder('XF:TagResultCache')->where([
			'tag_id' => $tagId,
			'user_id' => $userId
		])->fetchOne();
		if (!$cache)
		{
			$cache = $this->em->create('XF:TagResultCache');
			$cache->tag_id = $tagId;
			$cache->user_id = $userId;
		}

		return $cache;
	}

	public function pruneTagResultsCache($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = \XF::$time;
		}

		$this->db()->delete('xf_tag_result_cache', 'expiry_date <= ?', $cutOff);
	}

	public function getTagResultSet(array $results)
	{
		return new \XF\ResultSet($this, $results);
	}

	public function getResultSetData($type, array $ids, $filterViewable = true, array $results = null)
	{
		$handler = $this->getTagHandler($type, false);
		if (!$handler)
		{
			return [];
		}

		$entities = $handler->getContent($ids, true);

		if ($filterViewable)
		{
			$entities = $entities->filter(function($entity) use ($handler)
			{
				return $handler->canViewContent($entity);
			});
		}

		return $entities;
	}

	/**
	 * @param \XF\ResultSet $resultSet
	 * @param array $options
	 *
	 * @return \XF\Tag\RenderWrapper[]
	 */
	public function wrapResultsForRender(\XF\ResultSet $resultSet, array $options = [])
	{
		return $resultSet->getResultsDataCallback(function($result, $type, $id) use ($options)
		{
			return new \XF\Tag\RenderWrapper($this->getTagHandler($type), $result, $options);
		});
	}

	/**
	 * @return \XF\Tag\AbstractHandler[]
	 */
	public function getTagHandlers()
	{
		$handlers = [];

		foreach (\XF::app()->getContentTypeField('tag_handler_class') AS $contentType => $handlerClass)
		{
			if (class_exists($handlerClass))
			{
				$handlerClass = \XF::extendClass($handlerClass);
				$handlers[$contentType] = new $handlerClass($contentType);
			}
		}

		return $handlers;
	}

	/**
	 * @param string $type
	 * @param bool $throw
	 *
	 * @return \XF\Tag\AbstractHandler|null
	 */
	public function getTagHandler($type, $throw = false)
	{
		$handlerClass = \XF::app()->getContentTypeFieldValue($type, 'tag_handler_class');
		if (!$handlerClass)
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("No tag handler for '$type'");
			}
			return null;
		}

		if (!class_exists($handlerClass))
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("Tag handler for '$type' does not exist: $handlerClass");
			}
			return null;
		}

		$handlerClass = \XF::extendClass($handlerClass);
		return new $handlerClass($type);
	}
}