<?php

namespace XF\Service\Conversation;

use XF\Service\AbstractService;

class Notifier extends AbstractService
{
	protected $conversation;

	protected $onlyNotifyUsers = null;

	public function __construct(\XF\App $app, \XF\Entity\ConversationMaster $conversation)
	{
		parent::__construct($app);

		$this->conversation = $conversation;
	}

	public function addNotificationLimit($limit)
	{
		if (!is_array($this->onlyNotifyUsers))
		{
			$this->onlyNotifyUsers = [];
		}

		if (is_array($limit))
		{
			foreach ($limit AS $l)
			{
				if ($l instanceof \XF\Entity\User)
				{
					$this->onlyNotifyUsers[] = $l->user_id;
				}
				else
				{
					$this->onlyNotifyUsers[] = intval($l);
				}
			}
		}
		else if ($limit instanceof \XF\Entity\User)
		{
			$this->onlyNotifyUsers[] = $limit->user_id;
		}
		else
		{
			$this->onlyNotifyUsers[] = intval($limit);
		}

		return $this;
	}

	public function notifyCreate()
	{
		$message = $this->conversation->FirstMessage;
		$users = $this->_getRecipientUsers();

		return $this->_sendNotifications('create', $users, $message);
	}

	public function notifyReply(\XF\Entity\ConversationMessage $message)
	{
		$users = $this->_getRecipientUsers();

		return $this->_sendNotifications('reply', $users, $message);
	}

	public function notifyInvite(array $users, \XF\Entity\User $inviter)
	{
		$message = $this->conversation->FirstMessage;

		return $this->_sendNotifications('invite', $users, $message, $inviter);
	}

	protected function _getRecipientUsers()
	{
		$finder = $this->conversation->getRelationFinder('Recipients');
		$finder->where('recipient_state', 'active')
			->with(['User', 'User.Option'], true)
			->pluckFrom('User', 'user_id');
		return $finder->fetch()->toArray();
	}

	protected function _sendNotifications(
		$actionType, array $notifyUsers, \XF\Entity\ConversationMessage $message = null, \XF\Entity\User $sender = null
	)
	{
		if (!$sender && $message)
		{
			$sender = $message->User;
		}

		$usersNotified = [];

		/** @var \XF\Entity\User $user */
		foreach ($notifyUsers AS $user)
		{
			if (!$this->_canUserReceiveNotification($user, $sender))
			{
				continue;
			}

			$template = 'conversation_' . $actionType;

			$params = [
				'receiver' => $user,
				'sender' => $sender,
				'conversation' => $this->conversation,
				'message' => $message
			];

			$mailer = $this->app->mailer();
			$mail = $mailer->newMail();

			$sent = false;

			if ($this->_canUserReceiveEmailNotification($user, $sender))
			{
				$mail->setToUser($user)
					->setTemplate($template, $params)
					->queue();

				$sent = true;
			}

			if ($this->_canUserReceivePushNotification($user, $sender))
			{
				/** @var \XF\Service\Conversation\Pusher $pusher */
				$pusher = $this->service('XF:Conversation\Pusher', $user, $message, $actionType, $sender);
				$pusher->push();

				$sent = true;
			}

			if ($sent)
			{
				$usersNotified[$user->user_id] = $user;
			}
		}

		return $usersNotified;
	}

	protected function _canUserReceiveNotification(\XF\Entity\User $user, \XF\Entity\User $sender = null)
	{
		if (is_array($this->onlyNotifyUsers) && !in_array($user->user_id, $this->onlyNotifyUsers))
		{
			return false;
		}

		return (
			$user->user_state == 'valid'
			&& !$user->is_banned
			&& (!$sender || $sender->user_id != $user->user_id)
		);
	}

	protected function _canUserReceiveEmailNotification(\XF\Entity\User $user, \XF\Entity\User $sender = null)
	{
		if (is_array($this->onlyNotifyUsers) && !in_array($user->user_id, $this->onlyNotifyUsers))
		{
			return false;
		}

		return (
			$user->Option->email_on_conversation && $user->email
			&& $this->_canUserReceiveNotification($user, $sender)
		);
	}

	protected function _canUserReceivePushNotification(\XF\Entity\User $user, \XF\Entity\User $sender = null)
	{
		return (
			$user->Option->push_on_conversation
			&& $this->_canUserReceiveNotification($user, $sender)
		);
	}
}