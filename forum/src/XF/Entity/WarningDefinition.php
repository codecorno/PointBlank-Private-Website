<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null warning_definition_id
 * @property int points_default
 * @property string expiry_type
 * @property int expiry_default
 * @property array extra_user_group_ids
 * @property bool is_editable
 *
 * GETTERS
 * @property \XF\Phrase title
 * @property \XF\Phrase conversation_title
 * @property \XF\Phrase conversation_text
 *
 * RELATIONS
 * @property \XF\Entity\Phrase MasterTitle
 * @property \XF\Entity\Phrase MasterConversationTitle
 * @property \XF\Entity\Phrase MasterConversationText
 */
class WarningDefinition extends Entity
{
	/**
	 * @return \XF\Phrase
	 */
	public function getTitle()
	{
		return \XF::phrase($this->getTitlePhraseName());
	}

	public function getTitlePhraseName()
	{
		return 'warning_title.' . $this->warning_definition_id;
	}

	/**
	 * @return \XF\Phrase
	 */
	public function getConversationTitle()
	{
		return \XF::phrase($this->getConversationPhraseName(true));
	}

	/**
	 * @return \XF\Phrase
	 */
	public function getConversationText()
	{
		return \XF::phrase($this->getConversationPhraseName(false));
	}

	public function getConversationPhraseName($title)
	{
		return 'warning_conv_' . ($title ? 'title' : 'text') . '.' . $this->warning_definition_id;
	}

	public function getSpecificConversationContent(
		\XF\Entity\User $receiver,
		$contentType,
		\XF\Mvc\Entity\Entity $content,
		\XF\Entity\User $sender = null
	)
	{
		if (!$sender)
		{
			$sender = \XF::visitor();
		}

		$warningHandler = $this->getWarningRepo()->getWarningHandler($contentType, true);

		$replace = [
			'{title}' => $warningHandler->getStoredTitle($content),
			'{content}' => $warningHandler->getContentForConversation($content),
			'{url}' => $warningHandler->getContentUrl($content, true),
			'{name}' => $receiver->username,
			'{staff}' => $sender->username
		];

		$language = $this->app()->language($receiver->language_id);
		$title = $language->phrase($this->getConversationPhraseName(true));
		$message = $language->phrase($this->getConversationPhraseName(false));

		$title = strtr(strval($title), $replace);
		$message = strtr(strval($message), $replace);

		return [$title, $message];
	}

	protected function _preSave()
	{
		if ($this->expiry_default == 0)
		{
			$this->expiry_type = 'never';
		}
		else if ($this->expiry_type == 'never')
		{
			$this->expiry_default = 0;
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_warning_definition';
		$structure->shortName = 'XF:WarningDefinition';
		$structure->primaryKey = 'warning_definition_id';
		$structure->columns = [
			'warning_definition_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'points_default' => ['type' => self::UINT, 'max' => 65535, 'default' => 1],
			'expiry_type' => ['type' => self::STR, 'default' => 'never',
				'allowedValues' => ['never', 'days', 'weeks', 'months', 'years']
			],
			'expiry_default' => ['type' => self::UINT, 'max' => 65535, 'default' => 0],
			'extra_user_group_ids' => ['type' => self::LIST_COMMA, 'default' => [],
				'list' => ['type' => 'posint', 'unique' => true, 'sort' => SORT_NUMERIC]
			],
			'is_editable' => ['type' => self::BOOL, 'default' => true]
		];
		$structure->getters = [
			'title' => true,
			'conversation_title' => true,
			'conversation_text' => true
		];
		$structure->relations = [
			'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'warning_title.', '$warning_definition_id']
				],
				'cascadeDelete' => true
			],
			'MasterConversationTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'warning_conv_title.', '$warning_definition_id']
				],
				'cascadeDelete' => true
			],
			'MasterConversationText' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'warning_conv_text.', '$warning_definition_id']
				],
				'cascadeDelete' => true
			],
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Warning
	 */
	protected function getWarningRepo()
	{
		return $this->repository('XF:Warning');
	}
}