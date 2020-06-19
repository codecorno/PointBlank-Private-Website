<?php

namespace XF\Repository;

use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Language extends Repository
{
	/**
	 * @return Finder
	 */
	public function findLanguages()
	{
		return $this->finder('XF:Language')->order('language_id');
	}

	public function showMasterLanguage()
	{
		return \XF::$developmentMode;
	}

	public function getMasterLanguage()
	{
		$language = $this->em->create('XF:Language');
		$language->setTrusted('language_id', 0);
		$language->setTrusted('parent_id', -1);
		$language->title = \XF::phrase('master_language');
		$language->setReadOnly(true);

		return $language;
	}

	public function getLanguageTree($withMaster = null)
	{
		$languages = $this->findLanguages()->fetch();
		return $this->createLanguageTree($languages, $withMaster);
	}

	public function createLanguageTree($languages, $withMaster = null, $rootId = null)
	{
		if ($withMaster === null)
		{
			$withMaster = \XF::$developmentMode;
		}
		if ($withMaster)
		{
			if ($languages instanceof AbstractCollection)
			{
				$languages = $languages->toArray();
			}
			$languages[0] = $this->getMasterLanguage();
		}

		if ($rootId === null)
		{
			$rootId = $withMaster ? -1 : 0;
		}

		return new \XF\Tree($languages, 'parent_id', $rootId);
	}

	public function rebuildGlobalPhraseCache()
	{
		$titles = $this->finder('XF:Phrase')
			->where('language_id', 0)
			->where('global_cache', 1)
			->fetch()
			->pluckNamed('title');

		$languages = $this->findLanguages()->fetch();
		
		$globalPhrases = [];

		if (!$titles)
		{
			return;
		}

		$result = $this->db()->query('
			SELECT language_id, title, phrase_text
			FROM xf_phrase_compiled
			WHERE title IN (' . $this->db()->quote($titles) . ')
		');
		while ($phrase = $result->fetch())
		{
			$globalPhrases[$phrase['language_id']][$phrase['title']] = $phrase['phrase_text'];
		}

		$this->db()->beginTransaction();

		/** @var $language \XF\Entity\Language */
		foreach ($languages AS $languageId => $language)
		{
			if (isset($globalPhrases[$languageId]))
			{
				$phrases = $globalPhrases[$languageId];
			}
			else
			{
				$phrases = [];
			}
			$language->fastUpdate('phrase_cache', $phrases);
		}

		$this->db()->commit();
	}

	public function getLanguageCacheData()
	{
		$languages = $this->finder('XF:Language')->fetch();
		$cache = [];

		foreach ($languages AS $language)
		{
			/** @var \XF\Entity\Language $language */
			$cache[$language->language_id] = $language->toArray();
		}

		return $cache;
	}

	public function rebuildLanguageCache()
	{
		$this->rebuildGlobalPhraseCache();
		$cache = $this->getLanguageCacheData();
		\XF::registry()->set('languages', $cache);
		return $cache;
	}
}