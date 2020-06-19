<?php

namespace XF\Service\Phrase;

class Rebuild extends \XF\Service\AbstractService
{
	/**
	 * @var \XF\Tree
	 */
	protected $languageTree;

	protected function setupLanguageTree()
	{
		if ($this->languageTree)
		{
			return;
		}

		/** @var \XF\Repository\Language $repo */
		$repo = $this->app->em()->getRepository('XF:Language');
		$this->languageTree = $repo->getlanguageTree(false);
	}

	public function rebuildFullPhraseMap()
	{
		$this->setupLanguageTree();

		$phrasesGrouped = [];
		$phraseRes = $this->db()->query("
			SELECT phrase_id, title, language_id
			FROM xf_phrase
		");
		while ($phrase = $phraseRes->fetch())
		{
			$phrasesGrouped[$phrase['language_id']][$phrase['title']] = $phrase['phrase_id'];
		}

		$this->db()->beginTransaction();
		$this->db()->delete('xf_phrase_map', null); // not using emptyTable for transaction safety
		$this->_rebuildPhraseMap(0, [], $phrasesGrouped);
		$this->db()->commit();
	}

	public function rebuildPhraseMapForTitle($title)
	{
		$this->setupLanguageTree();

		$phrasesGrouped = [];
		$phraseRes = $this->db()->query("
			SELECT phrase_id, title, language_id
			FROM xf_phrase
			WHERE title = ?
		", $title);
		while ($phrase = $phraseRes->fetch())
		{
			$phrasesGrouped[$phrase['language_id']][$phrase['title']] = $phrase['phrase_id'];
		}

		$this->db()->beginTransaction();
		$this->db()->delete('xf_phrase_map', 'title = ?', $title);
		$this->_rebuildPhraseMap(0, [], $phrasesGrouped);
		$this->db()->commit();
	}

	protected function _rebuildPhraseMap($id, array $map, array $phraseList)
	{
		if (isset($phraseList[$id]))
		{
			foreach ($phraseList[$id] AS $title => $phraseId)
			{
				$map[$title] = $phraseId;
			}
		}

		$sql = [];
		foreach ($map AS $title => $phraseId)
		{
			$parts = explode('.', $title);

			$sql[] = [
				'title' => $title,
				'language_id' => $id,
				'phrase_id' => $phraseId,
				'phrase_group' => isset($parts[1]) ? $parts[0] : null
			];
		}
		if ($sql)
		{
			$this->db()->insertBulk('xf_phrase_map', $sql);
		}

		foreach ($this->languageTree->childIds($id) AS $childId)
		{
			$this->_rebuildPhraseMap($childId, $map, $phraseList);
		}
	}
}