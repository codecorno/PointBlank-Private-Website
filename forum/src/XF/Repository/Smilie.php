<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;
use XF\Util\Arr;

class Smilie extends Repository
{
	/**
	 * @return Finder
	 */
	public function findSmiliesForList($displayInEditorOnly = false)
	{
		$finder = $this->finder('XF:Smilie')
			->order('display_order');

		if ($displayInEditorOnly)
		{
			$finder->where('display_in_editor', 1);
		}

		return $finder;
	}

	public function getSmilieListData($displayInEditorOnly = false)
	{
		$smilies = $this->findSmiliesForList($displayInEditorOnly)
			->fetch();

		$smilieCategories = $this->getSmilieCategoryRepo()
			->findSmilieCategoriesForList(true);

		return [
			'smilieCategories' => $smilieCategories,
			'totalSmilies' => $smilies->count(),
			'smilies' => $smilies->groupBy('smilie_category_id')
		];
	}

	public function findSmiliesByText($matchText)
	{
		$smilies = $this->finder('XF:Smilie')->fetch();
		return $this->getTextMatchesFromSmilies($smilies, $matchText);
	}

	public function findSmiliesByTextFromSmilies($matchText, $smilies)
	{
		return $this->getTextMatchesFromSmilies($smilies, $matchText);
	}

	protected function getTextMatchesFromSmilies($smilies, $matchText)
	{
		if (!is_array($matchText))
		{
			$matchText = Arr::stringToArray($matchText, '/\r?\n/');
		}

		if (!$matchText)
		{
			return [];
		}

		$matches = [];

		foreach ($smilies AS $smilie)
		{
			$smilieText = Arr::stringToArray($smilie['smilie_text'], '/\r?\n/');

			$textMatch = array_intersect($matchText, $smilieText);
			foreach ($textMatch AS $text)
			{
				$matches[$text] = $smilie;
			}
		}

		return $matches;
	}

	public function getAbstractedImportedXmlFilePath($fileName = null)
	{
		return 'internal-data://imported_xml/' . ($fileName !== null ? $this->validateImportedXmlFileName($fileName) : '');
	}

	protected $importedSmiliesXmlRegex = '/^(smilies\.)?(.+)(\.xml)$/si';

	public function validateImportedXmlFileName($fileName = null)
	{
		if ($fileName === null)
		{
			return null;
		}

		return preg_replace($this->importedSmiliesXmlRegex, 'smilies.$2.xml', $fileName);
	}

	public function getSmilieImportXMLFiles()
	{
		$files = [];

		foreach ($this->app()->fs()->listContents($this->getAbstractedImportedXmlFilePath()) AS $file)
		{
			if ($file['type'] == 'file' && preg_match($this->importedSmiliesXmlRegex, $file['basename']))
			{
				$files[$file['basename']] = $file['basename'] . ' - ' . $this->app()->language()->dateTime($file['timestamp']);
			}
		}

		return $files;
	}

	public function getSmilieCacheData()
	{
		$smilies = $this->finder('XF:Smilie')
			->order(['display_order', 'title'])
			->fetch();

		$cache = [];

		foreach ($smilies AS $smilieId => $smilie)
		{
			$smilie = $smilie->toArray();

			$cache[$smilieId] = $smilie;
			$cache[$smilieId]['smilieText'] = Arr::stringToArray($smilie['smilie_text'], '/\r?\n/');

			if (!$smilie['sprite_mode'] || !$smilie['sprite_params'])
			{
				unset($cache[$smilieId]['sprite_params']);
			}

			unset($cache[$smilieId]['sprite_mode'], $cache[$smilieId]['smilie_text']);
		}

		return $cache;
	}

	public function rebuildSmilieCache()
	{
		$cache = $this->getSmilieCacheData();
		\XF::registry()->set('smilies', $cache);
		return $cache;
	}

	public function getSmilieSpriteCacheData()
	{
		$smilies = $this->finder('XF:Smilie')
			->order(['display_order', 'title'])
			->fetch();

		$cache = [];

		foreach ($smilies AS $smilieId => $smilie)
		{
			if ($smilie->sprite_mode && !empty($smilie->sprite_params))
			{
				$cache[$smilieId] = ['sprite_css' => sprintf('width: %1$dpx; height: %2$dpx; background: url(\'%3$s\') no-repeat %4$dpx %5$dpx;',
					(int)$smilie->sprite_params['w'],
					(int)$smilie->sprite_params['h'],
					htmlspecialchars($smilie->image_url),
					(int)$smilie->sprite_params['x'],
					(int)$smilie->sprite_params['y']
				)];

				if (!empty($smilie->sprite_params['bs']))
				{
					$cache[$smilieId]['sprite_css'] .= ' background-size: ' . htmlspecialchars($smilie->sprite_params['bs']);
				}
			}
		}

		return $cache;
	}

	public function rebuildSmilieSpriteCache()
	{
		$cache = $this->getSmilieSpriteCacheData();
		\XF::registry()->set('smilieSprites', $cache);
		$this->repository('XF:Style')->updateAllStylesLastModifiedDateLater();
		return $cache;
	}

	/**
	 * @return SmilieCategory
	 */
	protected function getSmilieCategoryRepo()
	{
		return $this->repository('XF:SmilieCategory');
	}
}