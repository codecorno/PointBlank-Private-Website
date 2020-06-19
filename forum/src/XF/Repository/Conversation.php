<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;
use XF\Util\Arr;

class Conversation extends Repository
{
	public function findUserConversations(\XF\Entity\User $user, $forList = true)
	{
		/** @var \XF\Finder\ConversationUser $finder */
		$finder = $this->finder('XF:ConversationUser');
		$finder->forUser($user, $forList)
			->setDefaultOrder('last_message_date', 'desc');

		return $finder;
	}

	public function findUserConversationsForPopupList(\XF\Entity\User $user, $unread, $cutOff = null)
	{
		$finder = $this->findUserConversations($user);
		$finder->where('is_unread', $unread ? 1 : 0);

		if ($cutOff)
		{
			$finder->where('last_message_date', '>', $cutOff);
		}

		return $finder;
	}

	public function getUserConversationsForPopup(\XF\Entity\User $user,$maxLimit, $cutOff, array $extraWith = [])
	{
		$unreadFinder = $this->findUserConversationsForPopupList($user, true)->with($extraWith);
		$unread = $unreadFinder->fetch($maxLimit);
		$totalUnread = $unread->count();

		if ($totalUnread < $maxLimit)
		{
			$readFinder = $this->findUserConversationsForPopupList($user, false, $cutOff)->with($extraWith);
			$read = $readFinder->fetch($maxLimit - $totalUnread)->toArray();
		}
		else
		{
			$read = [];
		}

		return [
			'unread' => $unread->toArray(),
			'read' => $read
		];
	}

	public function markUserConversationRead(\XF\Entity\ConversationUser $userConv, $newRead = null)
	{
		if ($newRead === null)
		{
			$newRead = \XF::$time;
		}

		if (!$userConv->Master)
		{
			return;
		}

		$markRecipient = ($userConv->Recipient && $newRead > $userConv->Recipient->last_read_date);
		$markUser = ($userConv->is_unread && $newRead >= $userConv->Master->last_message_date);

		if ($markRecipient || $markUser)
		{
			$this->db()->beginTransaction();

			if ($markRecipient)
			{
				$userConv->Recipient->last_read_date = $newRead;
				$userConv->Recipient->save(false, false);
			}

			if ($markUser)
			{
				$userConv->is_unread = false;
				$userConv->save(false, false);
			}

			$this->db()->commit();
		}
	}

	/**
	 * @param \XF\Entity\User $user
	 *
	 * @return \XF\Mvc\Entity\Finder
	 */
	public function findConversationsStartedByUser(\XF\Entity\User $user)
	{
		return $this->finder('XF:ConversationMaster')
			->where('user_id', $user->user_id)
			->setDefaultOrder('start_date', 'DESC');
	}

	public function getValidatedRecipients($recipients, \XF\Entity\User $from, &$error = null, $checkPrivacy = true)
	{
		$error = null;

		if (is_string($recipients))
		{
			$recipients = Arr::stringToArray($recipients, '#\s*,\s*#');
		}
		else if ($recipients instanceof \XF\Entity\User)
		{
			$recipients = [$recipients];
		}

		if (!count($recipients))
		{
			return [];
		}

		if ($recipients instanceof \XF\Mvc\Entity\AbstractCollection)
		{
			$first = $recipients->first();
		}
		else
		{
			$first = reset($recipients);
		}

		if ($first instanceof \XF\Entity\User)
		{
			$type = 'user';
		}
		else
		{
			$type = 'name';
		}

		foreach ($recipients AS $k => $recipient)
		{
			if ($type == 'user' && !($recipient instanceof \XF\Entity\User))
			{
				throw new \InvalidArgumentException("Recipient at key $k must be a user entity");
			}
		}

		if ($type == 'name')
		{
			/** @var \XF\Repository\User $userRepo */
			$userRepo = $this->repository('XF:User');
			$users = $userRepo->getUsersByNames($recipients, $notFound, ['Privacy']);

			if ($notFound)
			{
				$error = \XF::phraseDeferred('the_following_recipients_could_not_be_found_x',
					['names' => implode(', ', $notFound)]
				);
			}
		}
		else
		{
			$users = $recipients;
		}

		if (is_array($users))
		{
			if (count($users) === 1 && reset($users) === $from)
			{
				$error = \XF::phraseDeferred('you_cannot_start_conversation_with_yourself');
				return [];
			}
		}
		else if ($users instanceof \XF\Mvc\Entity\ArrayCollection)
		{
			if ($users->count() === 1 && $users->first() === $from)
			{
				$error = \XF::phraseDeferred('you_cannot_start_conversation_with_yourself');
				return [];
			}
		}

		$newRecipients = [];
		$cantStart = [];

		foreach ($users AS $user)
		{
			if ($user->user_id == $from->user_id)
			{
				continue;
			}

			if ($checkPrivacy && !$from->canStartConversationWith($user))
			{
				$cantStart[$user->user_id] = $user->username;
				continue;
			}

			$newRecipients[$user->user_id] = $user;
		}

		if ($cantStart)
		{
			$error = \XF::phraseDeferred('you_may_not_start_a_conversation_with_the_following_recipients_x',
				['names' => implode(', ', $cantStart)]
			);
		}

		return $newRecipients;
	}

	public function insertRecipients(
		\XF\Entity\ConversationMaster $conversation, array $recipientUsers, \XF\Entity\User $from = null
	)
	{
		$existingRecipients = $conversation->Recipients->populate();
		$insertedActiveUsers = [];
		$inserted = 0;
		$fromUserId = $from ? $from->user_id : null;

		$this->db()->beginTransaction();

		/** @var \XF\Entity\User $user */
		foreach ($recipientUsers AS $user)
		{
			if ($fromUserId && $user->isIgnoring($fromUserId))
			{
				$state = 'deleted_ignored';
			}
			else
			{
				$state = 'active';
			}

			if (isset($existingRecipients[$user->user_id]))
			{
				$recipient = $existingRecipients[$user->user_id];
				if ($recipient->recipient_state == 'deleted_ignored' || $recipient->recipient_state == $state)
				{
					// ignoring the conversation or already active
					continue;
				}
			}
			else
			{
				$recipient = $conversation->getNewRecipient($user);
			}

			if ($fromUserId && $user->user_id == $fromUserId)
			{
				// if inserting by self, that would imply they're creating a conversation, so mark it read
				$recipient->last_read_date = $conversation->last_message_date;
			}

			$recipient->recipient_state = $state;

			if ($recipient->isInsert())
			{
				$inserted++; // need to update recipient count and cache

				if ($recipient->recipient_state == 'active')
				{
					$insertedActiveUsers[$user->user_id] = $user;
				}
			}

			$recipient->save(true, false);
		}

		if ($inserted)
		{
			$this->rebuildConversationRecipientCache($conversation);
		}

		$this->db()->commit();

		return $insertedActiveUsers;
	}

	public function getConversationRecipientCache(\XF\Entity\ConversationMaster $conversation, &$total = 0)
	{
		$cache = $this->db()->fetchAllKeyed("
			SELECT recipient.user_id, COALESCE(user.username, '') AS username
			FROM xf_conversation_recipient AS recipient
			LEFT JOIN xf_user AS user ON (recipient.user_id = user.user_id)
			WHERE recipient.conversation_id = ?
			ORDER BY user.username
		", 'user_id', $conversation->conversation_id);

		$total = count($cache);

		unset($cache[$conversation->user_id]);

		return $cache;
	}

	public function rebuildConversationRecipientCache(\XF\Entity\ConversationMaster $conversation)
	{
		$cache = $this->getConversationRecipientCache($conversation, $recipientTotal);
		$conversation->fastUpdate([
			'recipient_count' => $recipientTotal,
			'recipients' => $cache
		]);
		$conversation->clearCache('Recipients');
		$conversation->clearCache('Users');

		return $cache;
	}

	public function updateRecipientCacheForUserChange($oldUserId, $newUserId, $oldUserName, $newUserName)
	{
		// note that xf_conversation_recipient must already be updated
		$oldFind = '"' . intval($oldUserId) . '":' . '{"user_id":' . intval($oldUserId) . ',"username":"' . $oldUserName . '"}';
		$newReplace = '"' . intval($newUserId) . '":' . '{"user_id":' . intval($newUserId) . ',"username":"' . $newUserName . '"}';

		$this->db()->query("
			UPDATE (
				SELECT conversation_id
				FROM xf_conversation_recipient
				WHERE user_id = ?
			) AS temp
			INNER JOIN xf_conversation_master AS master ON (master.conversation_id = temp.conversation_id)
			SET master.recipients = REPLACE(master.recipients, ?, ?)
		", [$newUserId, $oldFind, $newReplace]);
	}

	public function findRecipientsForList(\XF\Entity\ConversationMaster $conversation)
	{
		$finder = $conversation->getRelationFinder('Recipients');
		$finder->with('User')->order('User.username');

		return $finder;
	}
}