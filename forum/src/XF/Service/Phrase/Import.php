<?php

namespace XF\Service\Phrase;

use XF\Entity\Language;
use XF\Mvc\Entity\ArrayCollection;

class Import extends \XF\Service\AbstractService
{
	/**
	 * @var Language
	 */
	protected $language;

	public function __construct(\XF\App $app, Language $language)
	{
		parent::__construct($app);
		$this->language = $language;
	}

	public function getLanguage()
	{
		return $this->language;
	}

	public function importFromXml(\SimpleXMLElement $container, $addOnId)
	{
		$this->deleteExistingPhrases($addOnId);

		$languageId = $this->language->language_id;
		$existingPhrases = $this->getExistingPhraseMap();

		foreach ($container->phrase AS $xmlPhrase)
		{
			$title = (string)$xmlPhrase['title'];

			$phrase = null;
			if (isset($existingPhrases[$title]))
			{
				$phrase = $this->em()->find('XF:Phrase', $existingPhrases[$title]);
			}
			if (!$phrase)
			{
				$phrase = $this->em()->create('XF:Phrase');
			}

			$this->setPhraseOptions($phrase);

			$phrase->title = $title;
			$phrase->language_id = $languageId;
			$phrase->phrase_text = \XF\Util\Xml::processSimpleXmlCdata((string)$xmlPhrase);
			$phrase->global_cache = (int)$xmlPhrase['global_cache'];
			$phrase->version_id = (int)$xmlPhrase['version_id'];
			$phrase->version_string = (string)$xmlPhrase['version_string'];
			$phrase->addon_id = (string)$xmlPhrase['addon_id'];

			$phrase->save(false, false);
		}

		$this->app->jobManager()->enqueueUnique('languageRebuild', 'XF:Atomic', [
			'execute' => ['XF:PhraseRebuild', 'XF:TemplateRebuild']
		]);
	}

	protected function deleteExistingPhrases($addOnId)
	{
		$where = 'language_id = ?';
		$params = [$this->language->language_id];

		if ($addOnId)
		{
			$where .= ' AND addon_id = ?';
			$params[] = $addOnId;
		}

		$this->db()->delete('xf_phrase', $where, $params);
	}

	protected function getExistingPhraseMap()
	{
		return $this->db()->fetchPairs("
			SELECT title, phrase_id
			FROM xf_phrase
			WHERE language_id = ?
		", $this->language->language_id);
	}

	protected function setPhraseOptions(\XF\Entity\Phrase $phrase)
	{
		$phrase->setOption('recompile', false);
		$phrase->setOption('recompile_include', false);
		$phrase->setOption('rebuild_map', false);
		$phrase->setOption('check_duplicate', false);

		$phrase->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
	}
}