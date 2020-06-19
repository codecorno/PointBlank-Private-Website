<?php

namespace XF\Legacy\Model;

use XF\Legacy\Phrase as XenForo_Phrase;
use XF\Legacy\DataWriter as XenForo_DataWriter;

class Phrase extends \XF\Legacy\Model
{
	/**
	 * Gets language ID/phrase ID pairs for all languages where the named phrase
	 * is modified.
	 *
	 * @param string $phraseTitle
	 *
	 * @return array Format: [language_id] => phrase_id
	 */
	public function getPhraseIdInLanguagesByTitle($phraseTitle)
	{
		return $this->_getDb()->fetchPairs('
			SELECT language_id, phrase_id
			FROM xf_phrase
			WHERE title = ?
		', $phraseTitle);
	}

	/**
	 * Fetches a phrase from a particular language based on its title.
	 * Note that if a version of the requested phrase does not exist
	 * in the specified language, nothing will be returned.
	 *
	 * @param string $title Title
	 * @param integer $languageId language ID (defaults to master language)
	 *
	 * @return array
	 */
	public function getPhraseInLanguageByTitle($title, $languageId = 0)
	{
		return $this->_getDb()->fetchRow('
			SELECT *
			FROM xf_phrase
			WHERE title = ?
				AND language_id = ?
		', [$title, $languageId]);
	}

	/**
	 * Gets the value for the named master phrase.
	 *
	 * @param string $title
	 *
	 * @return string Empty string if phrase is value
	 */
	public function getMasterPhraseValue($title)
	{
		$phrase = $this->getPhraseInLanguageByTitle($title, 0);
		return ($phrase ? $phrase['phrase_text'] : '');
	}

	/**
	 * Inserts or updates an array of master (language 0) phrases. Errors will be silently ignored.
	 *
	 * @param array $phrases Key-value pairs of phrases to insert/update
	 * @param string $addOnId Add-on all phrases belong to
	 * @param array $extra Extra fields to set
	 * @param array $options
	 *
	 * @param array $phrases Format: [title] => value
	 */
	public function insertOrUpdateMasterPhrases(array $phrases, $addOnId, array $extra = [], array $options = [])
	{
		foreach ($phrases AS $title => $value)
		{
			$this->insertOrUpdateMasterPhrase($title, $value, $addOnId, $extra, $options);
		}
	}

	/**
	 * Inserts or updates a master (language 0) phrase. Errors will be silently ignored.
	 *
	 * @param string $title
	 * @param string $text
	 * @param string $addOnId
	 * @param array $extra Extra fields to set
	 * @param array $options
	 */
	public function insertOrUpdateMasterPhrase($title, $text, $addOnId = '', array $extra = [], array $options = [])
	{
		$phrase = $this->getPhraseInLanguageByTitle($title, 0);

		$dw = XenForo_DataWriter::create('XenForo_DataWriter_Phrase', XenForo_DataWriter::ERROR_SILENT);
		foreach ($options AS $key => $value)
		{
			$dw->setOption($key, $value);
		}
		if ($phrase)
		{
			$dw->setExistingData($phrase, true);
		}
		else
		{
			$dw->set('language_id', 0);
		}
		$dw->set('title', $title);
		$dw->set('phrase_text', $text);
		$dw->set('addon_id', $addOnId);
		$dw->bulkSet($extra);
		$dw->save();
	}

	/**
	 * Deletes the named master phrases if they exist.
	 *
	 * @param array $phraseTitles Phrase titles
	 * @param array $options
	 */
	public function deleteMasterPhrases(array $phraseTitles, array $options = [])
	{
		foreach ($phraseTitles AS $title)
		{
			$this->deleteMasterPhrase($title, $options);
		}
	}

	/**
	 * Deletes the named master phrase if it exists.
	 *
	 * @param string $title
	 * @param array $options
	 */
	public function deleteMasterPhrase($title, array $options = [])
	{
		$phrase = $this->getPhraseInLanguageByTitle($title, 0);
		if (!$phrase)
		{
			return;
		}

		$dw = XenForo_DataWriter::create('XenForo_DataWriter_Phrase', XenForo_DataWriter::ERROR_SILENT);
		foreach ($options AS $key => $value)
		{
			$dw->setOption($key, $value);
		}
		$dw->setExistingData($phrase, true);
		$dw->delete();
	}

	/**
	 * Renames a list of master phrases. If you get a conflict, it will
	 * be silently ignored.
	 *
	 * @param array $phraseMap Format: [old name] => [new name]
	 * @param array $options
	 */
	public function renameMasterPhrases(array $phraseMap, array $options = [])
	{
		foreach ($phraseMap AS $oldName => $newName)
		{
			$this->renameMasterPhrase($oldName, $newName);
		}
	}

	/**
	 * Renames a master phrase. If you get a conflict, it will
	 * be silently ignored.
	 *
	 * @param string $oldName
	 * @param string $newName
	 * @param array $options
	 */
	public function renameMasterPhrase($oldName, $newName, array $options = [])
	{
		$phrase = $this->getPhraseInLanguageByTitle($oldName, 0);
		if (!$phrase)
		{
			return;
		}

		$dw = XenForo_DataWriter::create('XenForo_DataWriter_Phrase', XenForo_DataWriter::ERROR_SILENT);
		foreach ($options AS $key => $value)
		{
			$dw->setOption($key, $value);
		}
		$dw->setExistingData($phrase, true);
		$dw->set('title', $newName);
		$dw->save();
	}

	/**
	 * Change the add-on for all phrases with a particular name.
	 *
	 * @param string $phraseName
	 * @param string $addOnId
	 */
	public function changePhraseAddOn($phraseName, $addOnId)
	{
		$phrases = $this->getPhraseIdInLanguagesByTitle($phraseName);
		foreach ($phrases AS $phrase)
		{
			$dw = XenForo_DataWriter::create('XenForo_DataWriter_Phrase', XenForo_DataWriter::ERROR_SILENT);
			$dw->setOption(XenForo_DataWriter_Phrase::OPTION_FULL_RECOMPILE, false);
			$dw->setOption(XenForo_DataWriter_Phrase::OPTION_REBUILD_LANGUAGE_CACHE, false);
			$dw->setExistingData($phrase, true);
			$dw->set('addon_id', $addOnId);
			$dw->save();
		}
	}
}