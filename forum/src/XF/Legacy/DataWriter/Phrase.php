<?php

namespace XF\Legacy\DataWriter;

use XF\Legacy\Application as XenForo_Application;
use XF\Legacy\Phrase as XenForo_Phrase;

class Phrase extends \XF\Legacy\DataWriter
{
	/**
	 * Option that controls whether language-related caches will be rebuild.
	 * Defaults to true.
	 *
	 * @var string
	 */
	const OPTION_REBUILD_LANGUAGE_CACHE = 'rebuildLanguageCache';

	/**
	 * Option that controls whether templates that use this phrase should be recompiled.
	 * This can be a slow process if updating a lot of phrases. Defaults to true.
	 *
	 * @var string
	 */
	const OPTION_FULL_RECOMPILE = 'fullRecompile';

	/**
	 * Controls whether templates including this phrase are recompiled on change.
	 * Defaults to true.
	 *
	 * @var string
	 */
	const OPTION_RECOMPILE_TEMPLATE = 'recompileTemplate';

	/**
	 * Controls whether effective phrase values are compiled for this phrase on change.
	 * Defaults to true.
	 *
	 * @var string
	 */
	const OPTION_RECOMPILE_PHRASE = 'recompilePhrase';

	/**
	 * Option that controls if phrase map is rebuild when phrase is changed. Defaults to true.
	 *
	 * @var string
	 */
	const OPTION_REBUILD_PHRASE_MAP = 'rebuildPhraseMap';

	/**
	 * If false, duplicate checking is disabled. An error will occur on dupes. Defaults to true.
	 *
	 * @var string
	 */
	const OPTION_CHECK_DUPLICATE = 'checkDuplicate';

	/**
	 * Title of the phrase that will be created when a call to set the
	 * existing data fails (when the data doesn't exist).
	 *
	 * @var string
	 */
	protected $_existingDataErrorPhrase = 'requested_phrase_not_found';

	/**
	* Gets the fields that are defined for the table. See parent for explanation.
	*
	* @return array
	*/
	protected function _getFields()
	{
		return [
			'xf_phrase' => [
				'phrase_id'    => ['type' => self::TYPE_UINT,   'autoIncrement' => true],
				'language_id'  => ['type' => self::TYPE_UINT,   'required' => true],
				'title'        => ['type' => self::TYPE_BINARY, 'maxLength' => 100,
					'verification' => ['$this', '_verifyTitle'],
					'required' => 'please_enter_valid_title'
				],
				'phrase_text'  => ['type' => self::TYPE_STRING, 'default' => '', 'noTrim' => true],
				'global_cache' => ['type' => self::TYPE_BOOLEAN, 'default' => 0],
				'addon_id'     => ['type' => self::TYPE_STRING, 'maxLength' => 25, 'default' => ''],
				'version_id'   => ['type' => self::TYPE_UINT, 'default' => 0],
				'version_string' => ['type' => self::TYPE_STRING,  'maxLength' => 30, 'default' => '']
			]
		];
	}

	/**
	* Gets the actual existing data out of data that was passed in. See parent for explanation.
	*
	* @param mixed
	*
	* @return array|false
	*/
	protected function _getExistingData($data)
	{
		if (!$phrase_id = $this->_getExistingPrimaryKey($data))
		{
			return false;
		}

		return ['xf_phrase' => $this->_getPhraseModel()->getPhraseById($phrase_id)];
	}

	/**
	* Gets SQL condition to update the existing record.
	*
	* @return string
	*/
	protected function _getUpdateCondition($tableName)
	{
		return 'phrase_id = ' . $this->_db->quote($this->getExisting('phrase_id'));
	}

	/**
	* Gets the default set of options for this data writer.
	* If in debug mode and we have a development directory config, we set the
	* dev output directory automatically.
	*
	* @return array
	*/
	protected function _getDefaultOptions()
	{
		$options = [
			self::OPTION_REBUILD_LANGUAGE_CACHE => true,
			self::OPTION_RECOMPILE_PHRASE => true,
			self::OPTION_RECOMPILE_TEMPLATE => true,
			self::OPTION_REBUILD_PHRASE_MAP => true,
			self::OPTION_CHECK_DUPLICATE => true
		];

		return $options;
	}

	/**
	 * Sets an option. If the OPTION_FULL_RECOMPILE option is specified, other options are
	 * set instead.
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function setOption($name, $value)
	{
		if ($name === self::OPTION_FULL_RECOMPILE)
		{
			parent::setOption(self::OPTION_RECOMPILE_PHRASE, $value);
			parent::setOption(self::OPTION_RECOMPILE_TEMPLATE, $value);
		}
		else
		{
			parent::setOption($name, $value);
		}
	}

	/**
	 * Verifies that the phrase title ID is valid.
	 *
	 * @param string $title
	 *
	 * @return boolean
	 */
	protected function _verifyTitle(&$title)
	{
		if (preg_match('/[^a-zA-Z0-9_]/', $title))
		{
			$this->error(new XenForo_Phrase('please_enter_title_using_only_alphanumeric'), 'title');
			return false;
		}

		return true;
	}

	/**
	 * Pre-save handling.
	 */
	protected function _preSave()
	{
		if ($this->getOption(self::OPTION_CHECK_DUPLICATE))
		{
			if ($this->isChanged('title') || $this->isChanged('language_id'))
			{
				$existing = $this->_getPhraseModel()->getPhraseInLanguageByTitle($this->get('title'), $this->get('language_id'));
				if ($existing)
				{
					$this->error(new XenForo_Phrase('phrase_titles_must_be_unique_in_language'), 'title');
				}
			}
		}

		if (
			($this->isChanged('addon_id') || $this->isChanged('title') || $this->isChanged('phrase_text'))
			&& !$this->isChanged('version_id')
		)
		{
			$this->updateVersionId();
		}
	}
}