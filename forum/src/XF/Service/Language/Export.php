<?php

namespace XF\Service\Language;

use XF\Entity\Language;

class Export extends \XF\Service\AbstractService
{
	const EXPORT_VERSION_ID = 2;

	/**
	 * @var Language
	 */
	protected $language;

	/**
	 * @var \XF\Entity\AddOn|null
	 */
	protected $addOn;

	protected $includeUntranslated = false;

	public function __construct(\XF\App $app, Language $language)
	{
		parent::__construct($app);
		$this->setLanguage($language);
	}

	public function setLanguage(Language $language)
	{
		$this->language = $language;
	}

	public function getLanguage()
	{
		return $this->language;
	}

	public function setAddOn(\XF\Entity\AddOn $addOn = null)
	{
		$this->addOn = $addOn;
	}

	public function getAddOn()
	{
		$this->addOn;
	}

	public function setIncludeUntranslated($includeUntranslated)
	{
		$this->includeUntranslated = (bool)$includeUntranslated;
	}

	public function getIncludeUntranslated()
	{
		return $this->includeUntranslated;
	}

	public function exportToXml()
	{
		$document = $this->createXml();
		$languageNode = $this->getLanguageNode($document);
		$document->appendChild($languageNode);

		foreach ($this->getExportablePhrases() AS $phrase)
		{
			$phraseNode = $this->getPhraseNode($document, $phrase);
			$languageNode->appendChild($phraseNode);
		}

		return $document;
	}

	public function getExportFileName()
	{
		$title = str_replace(' ', '-', utf8_romanize(utf8_deaccent($this->language->title)));
		$addOnLimit = $this->addOn ? '-' . $this->addOn->addon_id : '';

		return "language-{$title}{$addOnLimit}.xml";
	}

	/**
	 * @return \DOMDocument
	 */
	protected function createXml()
	{
		$document = new \DOMDocument('1.0', 'utf-8');
		$document->formatOutput = true;

		return $document;
	}

	/**
	 * @param \DOMDocument $document
	 * @return \DOMElement
	 */
	protected function getLanguageNode(\DOMDocument $document)
	{
		$language = $this->language;

		$languageNode = $document->createElement('language');
		$languageNode->setAttribute('title', $language->title);
		$languageNode->setAttribute('date_format', $language->date_format);
		$languageNode->setAttribute('time_format', $language->time_format);
		$languageNode->setAttribute('currency_format', $language->currency_format);
		$languageNode->setAttribute('week_start', $language->week_start);
		$languageNode->setAttribute('decimal_point', $language->decimal_point);
		$languageNode->setAttribute('thousands_separator', $language->thousands_separator);
		$languageNode->setAttribute('label_separator', $language->label_separator);
		$languageNode->setAttribute('comma_separator', $language->comma_separator);
		$languageNode->setAttribute('ellipsis', $language->ellipsis);
		$languageNode->setAttribute('parenthesis_open', $language->parenthesis_open);
		$languageNode->setAttribute('parenthesis_close', $language->parenthesis_close);
		$languageNode->setAttribute('language_code', $language->language_code);
		$languageNode->setAttribute('text_direction', $language->text_direction);
		if ($this->addOn)
		{
			$languageNode->setAttribute('addon_id', $this->addOn->addon_id);
			$languageNode->setAttribute('base_version_id', $this->addOn->version_id);
		}
		else
		{
			$languageNode->setAttribute('base_version_id', \XF::$versionId);
		}
		$languageNode->setAttribute('export_version', self::EXPORT_VERSION_ID);
		$document->appendChild($languageNode);

		return $languageNode;
	}

	protected function getPhraseNode(\DOMDocument $document, array $phrase)
	{
		$phraseNode = $document->createElement('phrase');
		$phraseNode->setAttribute('title', $phrase['title']);
		$phraseNode->setAttribute('addon_id', $phrase['addon_id']);
		if ($phrase['global_cache'])
		{
			$phraseNode->setAttribute('global_cache', $phrase['global_cache']);
		}
		$phraseNode->setAttribute('version_id', $phrase['version_id']);
		$phraseNode->setAttribute('version_string', $phrase['version_string']);
		$phraseNode->appendChild(
			\XF\Util\Xml::createDomCdataSection($document, $phrase['phrase_text'])
		);

		return $phraseNode;
	}

	/**
	 * @return array
	 */
	protected function getExportablePhrases()
	{
		$language = $this->language;

		$db = $this->db();

		$addonLimitSql = ($this->addOn ? " AND master.addon_id = " . $db->quote($this->addOn->addon_id) : '');

		if ($this->includeUntranslated)
		{
			return $db->fetchAll("
				SELECT phrase.*,
					IF(master.phrase_id IS NOT NULL, master.addon_id, phrase.addon_id) AS addon_id
				FROM xf_phrase_map AS map
				INNER JOIN xf_phrase AS phrase ON (map.phrase_id = phrase.phrase_id)
				LEFT JOIN xf_phrase AS master ON (master.title = phrase.title AND master.language_id = 0)
				WHERE map.language_id = ?
					$addonLimitSql
				ORDER BY map.title
			", $language->language_id);
		}
		else
		{
			return $db->fetchAll("
				SELECT phrase.*,
					IF(master.phrase_id, master.addon_id, phrase.addon_id) AS addon_id
				FROM xf_phrase AS phrase
				LEFT JOIN xf_phrase AS master ON (master.title = phrase.title AND master.language_id = 0)
				WHERE phrase.language_id = ?
					$addonLimitSql
				ORDER BY phrase.title
			", $language->language_id);
		}
	}
}