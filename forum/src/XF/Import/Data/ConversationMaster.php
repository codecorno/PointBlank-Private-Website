<?php

namespace XF\Import\Data;

class ConversationMaster extends AbstractEmulatedData
{
	protected $recipients = [];

	/**
	 * @var ConversationMessage[]
	 */
	protected $messages = [];

	public function getImportType()
	{
		return 'conversation';
	}

	public function getEntityShortName()
	{
		return 'XF:ConversationMaster';
	}

	public function addRecipient($userId, $state = 'active', array $extra = [])
	{
		$extra['recipient_state'] = $state;

		$this->recipients[$userId] = $extra;
	}

	public function addMessage($oldMessageId, ConversationMessage $message)
	{
		$this->messages[$oldMessageId] = $message;
	}

	protected function preSave($oldId)
	{
		if (!$this->recipients || !$this->messages)
		{
			return false;
		}

		$hasRecipients = false;
		foreach ($this->recipients AS $recipient)
		{
			if ($recipient['recipient_state'] == 'active')
			{
				$hasRecipients = true;
				break;
			}
		}
		if (!$hasRecipients)
		{
			return false;
		}

		$this->forceNotEmpty('title', $oldId);

		uasort($this->messages, function(ConversationMessage $m1, ConversationMessage $m2)
		{
			if ($m1->message_date == $m2->message_date)
			{
				return 0;
			}

			return ($m1->message_date < $m2->message_date ? -1 : 1);
		});

		$firstMessage = reset($this->messages);
		$lastMessage = end($this->messages);

		$this->bulkSet([
			'user_id' => $firstMessage->user_id,
			'username' => $firstMessage->username,
			'start_date' => $firstMessage->message_date,
			'recipient_count' => count($this->recipients),
			'reply_count' => count($this->messages) - 1,
			'last_message_date' => $lastMessage->message_date,
			'last_message_user_id' => $lastMessage->user_id,
			'last_message_username' => $lastMessage->username
		]);

		return null;
	}

	protected function postSave($oldId, $newId)
	{
		$db = $this->db();

		foreach ($this->messages AS $oldMessageId => $message)
		{
			$message->conversation_id = $newId;
			$message->useTransaction(false);
			$message->checkExisting(false); // if the conversation didn't exist, this shouldn't

			if (!$message->save($oldMessageId))
			{
				unset($this->messages[$oldMessageId]);
			}
			// new message ID now in $message->message_id
		}

		$firstMessage = reset($this->messages);
		$lastMessage = end($this->messages);

		$this->db()->update('xf_conversation_master', [
			'first_message_id' => $firstMessage->message_id,
			'last_message_id' => $lastMessage->message_id
		], 'conversation_id = ?', $this->conversation_id);

		$baseUserData = [
			'conversation_id' => $newId,
			'reply_count' => $this->reply_count,
			'last_message_date' => $lastMessage->message_date,
			'last_message_user_id' => $lastMessage->user_id,
			'last_message_username' => $lastMessage->username,
			'last_message_id' => $lastMessage->message_id
		];

		$lastMessageDate = $lastMessage->message_date;

		foreach ($this->recipients AS $userId => $recipient)
		{
			if (isset($recipient['is_unread']))
			{
				$lastReadDate = $recipient['is_unread'] ? 0 : $lastMessageDate;
				$isUnread = $recipient['is_unread'];
			}
			else if (isset($recipient['last_read_date']))
			{
				$lastReadDate = $recipient['last_read_date'];
				$isUnread = ($lastReadDate < $lastMessageDate);
			}
			else
			{
				$lastReadDate = 0;
				$isUnread = true;
			}

			$db->insert('xf_conversation_recipient', [
				'conversation_id' => $newId,
				'user_id' => $userId,
				'recipient_state' => $recipient['recipient_state'],
				'last_read_date' => $lastReadDate
			]);

			if ($recipient['recipient_state'] == 'active')
			{
				$userData = $baseUserData;
				$userData['owner_user_id'] = $userId;
				$userData['is_unread'] = $isUnread ? 1 : 0;
				$userData['is_starred'] = !empty($recipient['is_starred']) ? 1 : 0;

				$db->insert('xf_conversation_user', $userData);
			}
		}
	}
}