<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null conversation_id
 * @property int owner_user_id
 * @property bool is_unread
 * @property int reply_count
 * @property int last_message_date
 * @property int last_message_id
 * @property int last_message_user_id
 * @property string last_message_username
 * @property bool is_starred
 *
 * GETTERS
 * @property string title
 *
 * RELATIONS
 * @property \XF\Entity\ConversationMaster Master
 * @property \XF\Entity\ConversationRecipient Recipient
 * @property \XF\Entity\User User
 */
class ConversationUser extends Entity
{
	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->Master ? $this->Master->title : '';
	}

	public function isUnread()
	{
		return $this->Master->last_message_date > $this->Recipient->last_read_date;
	}

	protected function _postSave()
	{
		if ($this->isInsert() && $this->is_unread)
		{
			$userUnreadChange = 1;
		}
		else if ($this->isUpdate() && $this->isChanged('is_unread'))
		{
			$userUnreadChange = $this->is_unread ? 1 : -1; // if now unread, increment; otherwise decrement
		}
		else
		{
			$userUnreadChange = 0;
		}

		if ($userUnreadChange && $this->User)
		{
			$newValue = max(0, $this->User->conversations_unread + $userUnreadChange);
			$this->User->fastUpdate('conversations_unread', $newValue);
		}
	}

	protected function _postDelete()
	{
		if ($this->is_unread && $this->User)
		{
			$newValue = max(0, $this->User->conversations_unread -1);
			$this->User->fastUpdate('conversations_unread', $newValue);
		}
	}

	protected final function setupApiResultData(
		\XF\Api\Result\EntityResult $result, $verbosity = self::VERBOSITY_NORMAL, array $options = []
	)
	{
		if ($this->Master)
		{
			return $this->Master->toApiResult($verbosity, $options);
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_conversation_user';
		$structure->shortName = 'XF:ConversationUser';
		$structure->primaryKey = ['conversation_id', 'owner_user_id'];
		$structure->columns = [
			'conversation_id' => ['type' => self::UINT, 'required' => true, 'nullable' => true],
			'owner_user_id' => ['type' => self::UINT, 'required' => true],
			'is_unread' => ['type' => self::BOOL, 'default' => false],
			'reply_count' => ['type' => self::UINT, 'default' => 0],
			'last_message_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'last_message_id' => ['type' => self::UINT, 'required' => true],
			'last_message_user_id' => ['type' => self::UINT, 'required' => true],
			'last_message_username' => ['type' => self::STR, 'required' => true],
			'is_starred' => ['type' => self::BOOL, 'default' => false]
		];
		$structure->getters = [
			'title' => false
		];
		$structure->relations = [
			'Master' => [
				'entity' => 'XF:ConversationMaster',
				'type' => self::TO_ONE,
				'conditions' => 'conversation_id',
				'primary' => true
			],
			'Recipient' => [
				'entity' => 'XF:ConversationRecipient',
				'type' => self::TO_ONE,
				'conditions' => [
					'conversation_id',
					['user_id', '=', '$owner_user_id']
				],
				'primary' => true
			],
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [
					['user_id', '=', '$owner_user_id']
				],
				'primary' => true
			]
		];
		$structure->defaultWith = ['Master', 'Recipient'];
		$structure->withAliases = [
			'api' => [
				'Master.api'
			]
		];

		return $structure;
	}
}