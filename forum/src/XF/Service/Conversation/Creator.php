<?php

namespace XF\Service\Conversation;

use XF\Entity\ConversationMaster;
use XF\Entity\ConversationMessage;

class Creator extends \XF\Service\AbstractService
{
	use \XF\Service\ValidateAndSavableTrait;

	/** @var  ConversationMaster */
	protected $conversation;

	/** @var  \XF\Entity\User */
	protected $starter;

	/** @var  ConversationMessage */
	protected $conversationMessage;

	/** @var  MessageManager */
	protected $messageManager;

	/** @var  \XF\Repository\Conversation */
	protected $conversationRepo;

	protected $recipients = [];
	protected $notifyUsers = [];

	protected $overrideMaxAllowed = null;

	protected $autoSpamCheck = true;
	protected $autoSendNotifications = true;
	protected $performValidations = true;

	public function __construct(\XF\App $app, \XF\Entity\User $starter)
	{
		parent::__construct($app);

		$this->conversation = $this->em()->create('XF:ConversationMaster');
		$this->conversation->user_id = $starter->user_id;
		$this->conversation->username = $starter->username;

		$this->starter = $starter;

		$this->conversationMessage = $this->conversation->getNewMessage($starter);

		$this->conversationRepo = $this->repository('XF:Conversation');

		$this->messageManager = $this->service('XF:Conversation\MessageManager', $this->conversationMessage);

		$this->conversation->addCascadedSave($this->conversationMessage);
		$this->conversationMessage->hydrateRelation('Conversation', $this->conversation);
	}

	public function getConversation()
	{
		return $this->conversation;
	}

	public function getMessage()
	{
		return $this->conversationMessage;
	}

	public function getMessageManager()
	{
		return $this->messageManager;
	}

	public function setAutoSpamCheck($check)
	{
		$this->autoSpamCheck = (bool)$check;
	}

	public function setAutoSendNotifications($send)
	{
		$this->autoSendNotifications = (bool)$send;
	}

	public function setOptions(array $options)
	{
		$this->conversation->bulkSet($options);
	}

	public function overrideMaxAllowed($override)
	{
		if ($override !== null)
		{
			$override = intval($override);
		}
		$this->overrideMaxAllowed = $override;
	}

	public function setRecipients($recipients, $checkPrivacy = true, $triggerErrors = true)
	{
		$this->recipients = $this->conversationRepo->getValidatedRecipients(
			$recipients, $this->starter, $error, $checkPrivacy
		);

		if ($triggerErrors)
		{
			if ($error)
			{
				$this->conversation->error($error, 'recipients');
			}
			else
			{
				if (is_int($this->overrideMaxAllowed))
				{
					$maxAllowed = $this->overrideMaxAllowed;
				}
				else
				{
					$maxAllowed = $this->conversation->getMaximumAllowedRecipients($this->starter);
				}

				if ($maxAllowed > -1 && count($this->recipients) > $maxAllowed)
				{
					$this->conversation->error(
						\XF::phrase(
							'you_may_only_invite_x_members_to_join_this_conversation',
							['count' => $maxAllowed]
						),
						'recipients'
					);
				}
			}
		}
	}

	public function setRecipientsTrusted($recipients)
	{
		$this->setRecipients($recipients, false, false);
	}

	public function getRecipients()
	{
		return $this->recipients;
	}

	public function setContent($title, $message, $format = true)
	{
		$this->conversation->title = $title;
		return $this->messageManager->setMessage($message, $format, $this->performValidations);
	}

	public function setLogIp($log)
	{
		$this->messageManager->setLogIp($log);
	}

	public function setPerformValidations($perform)
	{
		$this->performValidations = (bool)$perform;
	}

	public function getPerformValidations()
	{
		return $this->performValidations;
	}

	public function setIsAutomated()
	{
		$this->setLogIp(false);
		$this->setAutoSpamCheck(false);
		$this->setPerformValidations(false);
	}

	public function setAttachmentHash($hash)
	{
		$this->messageManager->setAttachmentHash($hash);
	}

	public function checkForSpam()
	{
		if ($this->starter->isSpamCheckRequired())
		{
			$this->messageManager->checkForSpam();
		}
	}

	protected function finalSetup()
	{
		$date = time();

		$this->conversation->start_date = $date;
		$this->conversation->last_message_date = $date;
		$this->conversation->last_message_user_id = $this->conversation->user_id;
		$this->conversation->last_message_username = $this->conversation->username;

		$this->conversationMessage->message_date = $date;

		if ($this->autoSpamCheck)
		{
			$this->checkForSpam();
		}
	}

	protected function _validate()
	{
		$this->finalSetup();

		if (!$this->recipients)
		{
			$this->conversation->error(\XF::phrase('please_enter_at_least_one_valid_recipient'), 'recipients', false);
		}

		$this->conversation->preSave();
		return $this->conversation->getErrors();
	}

	protected function _save()
	{
		if (!$this->recipients)
		{
			throw new \LogicException("A conversation must have at least one recipient");
		}

		$conversation = $this->conversation;

		$db = $this->db();
		$db->beginTransaction();

		$conversation->save(true, false);
		// message will also be saved now

		$conversation->fastUpdate([
			'first_message_id' => $this->conversationMessage->message_id,
			'last_message_id' => $this->conversationMessage->message_id
		]);

		$recipients = $this->recipients;
		$recipients[$this->starter->user_id] = $this->starter;

		$this->conversationRepo->insertRecipients($conversation, $recipients, $this->starter);

		$this->messageManager->afterInsert();

		$db->commit();

		if ($this->autoSendNotifications)
		{
			$this->sendNotifications();
		}

		return $conversation;
	}

	public function sendNotifications()
	{
		/** @var \XF\Service\Conversation\Notifier $notifier */
		$notifier = $this->service('XF:Conversation\Notifier', $this->conversation);
		$notifier->notifyCreate();
	}
}