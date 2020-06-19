<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null conversation_id
 * @property int user_id
 * @property string recipient_state
 * @property int last_read_date
 *
 * RELATIONS
 * @property \XF\Entity\ConversationMaster Conversation
 * @property \XF\Entity\ConversationUser ConversationUser
 * @property \XF\Entity\User User
 */
class ConversationRecipient extends Entity
{
	protected function _postSave()
	{
		$participationChange = $this->isStateChanged('recipient_state', 'active');
		if ($participationChange)
		{
			$conversation = $this->Conversation;

			if ($participationChange == 'enter')
			{
				/** @var \XF\Entity\ConversationUser $conversationUser */
				$conversationUser = $this->getRelationOrDefault('ConversationUser', false);

				$conversationUser->bulkSet([
					'conversation_id' => $this->conversation_id,
					'owner_user_id' => $this->user_id,
					'is_unread' => ($this->last_read_date < $conversation->last_message_date),
					'reply_count' => $conversation->reply_count,
					'last_message_date' => $conversation->last_message_date,
					'last_message_id' => $conversation->last_message_id,
					'last_message_user_id' => $conversation->last_message_user_id,
					'last_message_username' => $conversation->last_message_username,
					'is_starred' => false
				]);
				$conversationUser->save();
			}
			else if ($participationChange == 'leave')
			{
				if ($this->ConversationUser)
				{
					$this->ConversationUser->delete();
				}

				$conversation->recipientRemoved($this);
			}
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_conversation_recipient';
		$structure->shortName = 'XF:ConversationRecipient';
		$structure->primaryKey = ['conversation_id', 'user_id'];
		$structure->columns = [
			'conversation_id' => ['type' => self::UINT, 'required' => true, 'nullable' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'recipient_state' => ['type' => self::STR, 'required' => true,
				'allowedValues' => ['active', 'deleted', 'deleted_ignored']
			],
			'last_read_date' => ['type' => self::UINT, 'default' => 0]

		];
		$structure->getters = [];
		$structure->relations = [
			'Conversation' => [
				'entity' => 'XF:ConversationMaster',
				'type' => self::TO_ONE,
				'conditions' => 'conversation_id',
				'primary' => true
			],
			'ConversationUser' => [
				'entity' => 'XF:ConversationUser',
				'type' => self::TO_ONE,
				'conditions' => [
					['conversation_id', '=', '$conversation_id'],
					['owner_user_id', '=', '$user_id']
				],
				'primary' => true
			],
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
		];

		return $structure;
	}
}