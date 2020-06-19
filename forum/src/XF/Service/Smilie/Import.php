<?php

namespace XF\Service\Smilie;

use XF\Mvc\Entity\Entity;

class Import extends \XF\Service\AbstractService
{
	public function importSmilies(array $smilies, array $newCategories, &$errors = [])
	{
		$placeholderCategoryMap = [];

		$this->db()->beginTransaction();

		foreach ($newCategories AS $placeholderCategoryId => $newCategory)
		{
			if ($placeholderCategoryId >= 0)
			{
				// this would appear to be a regular category
				continue;
			}

			$import = false;

			foreach ($smilies AS $smilie)
			{
				if ($smilie['smilie_category_id'] == $placeholderCategoryId)
				{
					// only import categories that contain imported smilies
					$import = true;
					break;
				}
			}

			if (!$import)
			{
				continue;
			}

			$title = isset($newCategory['title']) ? trim(strval($newCategory['title'])) : '';
			if (!strlen($title))
			{
				continue;
			}

			/** @var \XF\Entity\SmilieCategory $categoryEm */
			$categoryEnt = $this->em()->create('XF:SmilieCategory');
			$categoryEnt->display_order = isset($newCategory['display_order']) ? $newCategory['display_order'] : 0;

			$masterTitle = $categoryEnt->getMasterPhrase();
			$masterTitle->phrase_text = $title;
			$categoryEnt->addCascadedSave($masterTitle);

			if (!$categoryEnt->preSave())
			{
				foreach ($categoryEnt->getErrors() AS $field => $error)
				{
					$errors[$field . '__' . $placeholderCategoryId] = $error;
				}

				$this->db()->rollback();
				return false;
			}

			$categoryEnt->save(true, false);

			$placeholderCategoryMap[$placeholderCategoryId] = $categoryEnt->get('smilie_category_id');
		}

		$entities = [];

		foreach ($smilies AS $smilieId => $smilie)
		{
			$smilieCatId = $smilie['smilie_category_id'];
			if ($smilieCatId && $smilieCatId < 0)
			{
				$smilieCatId = isset($placeholderCategoryMap[$smilieCatId])
					? $placeholderCategoryMap[$smilieCatId]
					: 0;
			}
			unset($smilie['smilie_category_id']);

			/** @var \XF\Entity\Smilie $ent */
			$ent = $this->em()->create('XF:Smilie');
			$ent->smilie_category_id = $smilieCatId;
			$ent->bulkSet($smilie);

			if (!$ent->preSave())
			{
				foreach ($ent->getErrors() AS $field => $error)
				{
					$errors[$field . '__' . $smilieId] = $error;
				}
			}
			else
			{
				$entities[] = $ent;
			}
		}

		if (!$errors)
		{
			/** @var Entity $em */
			foreach ($entities AS $em)
			{
				$em->save();
			}

			$this->db()->commit();
			return true;
		}
		else
		{
			$this->db()->rollback();
			return false;
		}
	}

	public function getSmilieDataFromXml(\SimpleXMLElement $xml)
	{
		$existingCategoryPairs = $this->getSmilieCategoryRepo()->getSmilieCategoryTitlePairs();

		$categoryIdMap = [];
		$newCategories = [];
		$newCategoryPairs = [];

		foreach ($xml->smilie_categories->smilie_category AS $newCategory)
		{
			$newId = (int)$newCategory['id'];
			$newTitle = (string)$newCategory['title'];

			$existingId = array_search($newTitle, $existingCategoryPairs);
			if ($existingId !== false)
			{
				$categoryIdMap[$newId] = $existingId;
			}
			else
			{
				$placeholderId = $newId * -1;
				$categoryIdMap[$newId] = $placeholderId;

				$newCategories[$placeholderId] = [
					'smilie_category_id' => $placeholderId,
					'display_order' => (int)$newCategory['display_order'],
					'title' => $newTitle
				];

				$newCategoryPairs[$placeholderId] = $newTitle;
			}
		}

		$smilies = [];
		$smilieCategoryMap = [];
		$i = 0;

		foreach ($xml->smilies->smilie AS $smilie)
		{
			$smilies[$i] = $this->getSmilieFromXmlRecord($smilie);

			$expectedCategoryId = (int)$smilie['smilie_category_id'];
			if ($expectedCategoryId && isset($categoryIdMap[$expectedCategoryId]))
			{
				$smilieCategoryMap[$i] = $categoryIdMap[$expectedCategoryId];
			}

			$i++;
		}

		return [
			'smilies' => $smilies,
			'smilieCategoryMap' => $smilieCategoryMap,
			'categories' => $newCategories,
			'categoryPairs' => $newCategoryPairs
		];
	}

	protected function getSmilieFromXmlRecord(\SimpleXMLElement $smilie)
	{
		$ent = $this->em()->create('XF:Smilie');
		$ent->setOption('check_duplicate', false);

		$smilieText = '';
		foreach ($smilie->smilie_text AS $text)
		{
			$smilieText .= (string)$text . "\n";
		}

		$ent->bulkSet([
			'title' => (string)$smilie['title'],
			'display_order' => (int)$smilie['display_order'],
			'display_in_editor' => (int)$smilie['display_in_editor'],
			'image_url' => (string)$smilie->image_url,
			'image_url_2x' => (string)$smilie->image_url_2x,
			'sprite_mode' => ($smilie->sprite_params ? 1 : 0),
			'smilie_text' => trim($smilieText)
		]);

		if ($smilie->sprite_params)
		{
			$ent->sprite_params = [
				'w' => (int)$smilie->sprite_params['w'],
				'h' => (int)$smilie->sprite_params['h'],
				'x' => (int)$smilie->sprite_params['x'],
				'y' => (int)$smilie->sprite_params['y'],
				'bs' => (string)$smilie->sprite_params['bs']
			];
		}

		return $ent;
	}

	public function getSmilieDataFromDirectory($directory)
	{
		$directory = str_replace('\\', '/', $directory);
		$directory = rtrim($directory, '/');
		$fullPath = \XF\Util\File::canonicalizePath($directory);

		if (!file_exists($fullPath) || !is_readable($fullPath))
		{
			throw new \XF\PrintableException(\XF::phrase('invalid_or_unreadable_directory'));
		}

		$i = 1;
		$imageTypes = ['jpg', 'jpe', 'jpeg', 'gif', 'png'];
		$smilies = [];

		foreach (scandir($fullPath) AS $smilieFile)
		{
			$filePath = "$fullPath/$smilieFile";
			$fileUrl = "$directory/$smilieFile";
			$extension = pathinfo($smilieFile, PATHINFO_EXTENSION);

			if (!in_array(strtolower($extension), $imageTypes) || !is_file($filePath) || !is_readable($filePath))
			{
				continue;
			}

			$smilies[] = $this->getSmilieFromFile($filePath, $fileUrl, $i);
			$i++;
		}

		return [
			'smilies' => $smilies,
			'smilieCategoryMap' => [],
			'categories' => [],
			'categoryPairs' => []
		];
	}

	protected function getSmilieFromFile($filePath, $url, $i)
	{
		$baseName = pathinfo($filePath, PATHINFO_FILENAME);

		$smilie = $this->em()->create('XF:Smilie');
		$smilie->setOption('check_duplicate', false);

		$smilie->bulkSet([
			'title' => ucwords(strtr($baseName, '-_', '  ')),
			'smilie_text' => ':' . strtolower(preg_replace('/[\s_]+/s', '-', $baseName)) . ':',
			'image_url' => $url,
			'display_order' => $i * 10,
			'display_in_editor' => 1
		]);

		return $smilie;
	}

	/**
	 * @return \XF\Repository\SmilieCategory
	 */
	protected function getSmilieCategoryRepo()
	{
		return $this->repository('XF:SmilieCategory');
	}
}