<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Phrase extends Repository
{
	/**
	 * @param \XF\Entity\Language $language
	 *
	 * @return \XF\Finder\PhraseMap
	 */
	public function findEffectivePhrasesInLanguage(\XF\Entity\Language $language)
	{
		/** @var \XF\Finder\PhraseMap $finder */
		$finder = $this->finder('XF:PhraseMap');
		$finder
			->where('language_id', $language->language_id)
			->with('Phrase', true)
			->orderTitle()
			->pluckFrom('Phrase', 'phrase_id');

		return $finder;
	}

	/**
	 * @param \XF\Entity\Language $language
	 * @param $title
	 *
	 * @return \XF\Entity\Phrase
	 */
	public function getEffectivePhraseByTitle(\XF\Entity\Language $language, $title)
	{
		$finder = $this->finder('XF:PhraseMap');
		return $finder
			->where('language_id', $language->language_id)
			->where('title', $title)
			->pluckFrom('Phrase', 'phrase_id')
			->fetchOne();
	}

	/**
	 * @param \XF\Entity\Language $language
	 * @param array $titles
	 *
	 * @return \XF\Mvc\Entity\ArrayCollection|\XF\Entity\Phrase[]
	 */
	public function getEffectivePhrasesByTitles(\XF\Entity\Language $language, array $titles)
	{
		$finder = $this->finder('XF:PhraseMap');
		return $finder
			->where('language_id', $language->language_id)
			->where('title', $titles)
			->pluckFrom('Phrase', 'title')
			->fetch();
	}

	public function countOutdatedPhrases()
	{
		return count($this->getBaseOutdatedPhraseData());
	}

	public function getOutdatedPhrases()
	{
		$data = $this->getBaseOutdatedPhraseData();
		$phraseIds = array_keys($data);

		if (!$phraseIds)
		{
			return [];
		}

		$phrases = $this->em->findByIds('XF:Phrase', $phraseIds);

		$output = [];
		foreach ($data AS $phraseId => $outdated)
		{
			if (!isset($phrases[$phraseId]))
			{
				continue;
			}

			$outdated['phrase'] = $phrases[$phraseId];
			$output[$phraseId] = $outdated;
		}

		return $output;
	}

	protected function getBaseOutdatedPhraseData()
	{
		$db = $this->db();

		return $db->fetchAllKeyed('
			SELECT phrase.phrase_id,
				parent.version_string AS parent_version_string
			FROM xf_phrase AS phrase
			INNER JOIN xf_language AS language ON (language.language_id = phrase.language_id)
			INNER JOIN xf_phrase_map AS map ON (map.language_id = language.parent_id AND map.title = phrase.title)
			INNER JOIN xf_phrase AS parent ON (map.phrase_id = parent.phrase_id AND parent.version_id > phrase.version_id)
			WHERE phrase.language_id > 0
			ORDER BY phrase.title
		', 'phrase_id');
	}

	public function quickCustomizePhrase(\XF\Entity\Language $language, $title, $text, array $extra = [])
	{
		$existingPhrase = $this->getEffectivePhraseByTitle($language, $title);
		if (!$existingPhrase)
		{
			// first time this phrase exists
			$phrase = $this->em->create('XF:Phrase');
			$phrase->language_id = $language->language_id;
			$phrase->title = $title;
			$phrase->addon_id = ''; // very likey to be correct, can be overridden if needed
		}
		else if ($existingPhrase->language_id != $language->language_id)
		{
			// phrase exists in a parent
			$phrase = $this->em->create('XF:Phrase');
			$phrase->language_id = $language->language_id;
			$phrase->title = $title;
			$phrase->addon_id = $existingPhrase->addon_id;
		}
		else
		{
			// phrase already exists in this language
			$phrase = $existingPhrase;
		}

		$phrase->phrase_text = $text;
		$phrase->bulkSet($extra);
		$phrase->save();

		return $phrase;
	}
}