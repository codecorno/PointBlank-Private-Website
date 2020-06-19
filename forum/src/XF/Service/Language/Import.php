<?php

namespace XF\Service\Language;

use XF\Entity\Language;

class Import extends \XF\Service\AbstractService
{
	/**
	 * @var Language|null
	 */
	protected $overwriteLanguage;

	/**
	 * @var Language|null
	 */
	protected $parentLanguage;

	public function setOverwriteLanguage(Language $language)
	{
		$this->overwriteLanguage = $language;
		$this->parentLanguage = null;
	}

	public function getOverwriteLanguage()
	{
		return $this->overwriteLanguage;
	}

	public function setParentLanguage(Language $language = null)
	{
		$this->parentLanguage = $language;
		$this->overwriteLanguage = null;
	}

	public function getParentLanguage()
	{
		return $this->parentLanguage;
	}

	public function isValidXml($rootElement, &$error = null)
	{
		if (!($rootElement instanceof \SimpleXMLElement))
		{
			$error = \XF::phrase('provided_file_is_not_valid_language_xml');
			return false;
		}

		if ($rootElement->getName() != 'language' || (string)$rootElement['title'] === '')
		{
			$error = \XF::phrase('provided_file_is_not_valid_language_xml');
			return false;
		}

		if ((string)$rootElement['export_version'] != (string)Export::EXPORT_VERSION_ID)
		{
			$error = \XF::phrase('this_language_xml_file_was_not_built_for_this_version_of_xenforo');
			return false;
		}

		return true;
	}

	public function isValidConfiguration(\SimpleXMLElement $document, &$errors = null)
	{
		$errors = [];

		$addOnId = (string)$document['addon_id'];

		if ($addOnId && $addOnId != 'XF')
		{
			$addOn = $this->app->addOnManager()->getById($addOnId);
			if (!$addOn)
			{
				$errors['addon_id'] = \XF::phrase('xml_file_relates_add_on_not_installed_install_first');
				$expectedVersionId = null;
			}
			else
			{
				$expectedVersionId = $addOn->version_id;
			}
		}
		else
		{
			$expectedVersionId = \XF::$versionId;
		}

		$baseVersionId = (int)$document['base_version_id'];

		if ($expectedVersionId && $baseVersionId)
		{
			if ($baseVersionId > $expectedVersionId)
			{
				$errors['version_id'] = \XF::phrase('xml_file_based_on_newer_version_than_installed');
			}
		}

		if ($this->overwriteLanguage)
		{
			$languageCode = (string)$document['language_code'];
			if ($languageCode != $this->overwriteLanguage->language_code)
			{
				$errors['language_code'] = \XF::phrase('locale_of_language_importing_differs_overwriting_is_correct');
			}
		}

		return (count($errors) == 0);
	}

	public function importFromXml(\SimpleXMLElement $document)
	{
		$db = $this->db();
		$db->beginTransaction();

		$addOnId = (string)$document['addon_id'];

		$language = $this->getTargetLanguage($document);

		/** @var \XF\Service\Phrase\Import $phraseImporter */
		$phraseImporter = $this->service('XF:Phrase\Import', $language);
		$phraseImporter->importFromXml($document, $addOnId);

		$db->commit();

		return $language;
	}

	protected function getTargetLanguage(\SimpleXMLElement $document)
	{
		if ($this->overwriteLanguage)
		{
			return $this->overwriteLanguage;
		}
		else
		{
			$language = $this->em()->create('XF:Language');
			$language->title = (string)$document['title'];
			$language->parent_id = $this->parentLanguage ? $this->parentLanguage->language_id : 0;
			$language->date_format = (string)$document['date_format'];
			$language->time_format = (string)$document['time_format'];
			$language->currency_format = (string)$document['currency_format'];
			$language->week_start = (string)$document['week_start'];
			$language->decimal_point = (string)$document['decimal_point'];
			$language->thousands_separator = (string)$document['thousands_separator'];
			$language->label_separator = (string)$document['label_separator'];
			$language->comma_separator = (string)$document['comma_separator'];
			$language->ellipsis = (string)$document['ellipsis'];
			$language->parenthesis_open = (string)$document['parenthesis_open'];
			$language->parenthesis_close = (string)$document['parenthesis_close'];
			$language->language_code = (string)$document['language_code'];
			$language->text_direction = (string)$document['text_direction'] ?: 'LTR';

			$language->save(true, false);

			return $language;
		}
	}
}