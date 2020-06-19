<?php

namespace XF\Service\Conversation;

use XF\Entity\ConversationMaster;
use XF\Entity\ConversationMessage;

class Replier extends \XF\Service\AbstractService
{
	use \XF\Service\ValidateAndSavableTrait;

	/**
	 * @var ConversationMaster
	 */
	protected $conversation;

	/**
	 * @var ConversationMessage
	 */
	protected $message;

	/**
	 * @var \XF\Entity\User
	 */
	protected $user;

	/**
	 * @var MessageManager
	 */
	protected $messageManager;

	protected $autoSpamCheck = true;
	protected $autoSendNotifications = true;
	protected $performValidations = true;

	public function __construct(\XF\App $app, ConversationMaster $conversation, \XF\Entity\User $user)
	{
		parent::__construct($app);

		$this->conversation = $conversation;
		$this->user = $user;
		$this->message = $conversation->getNewMessage($user);
		$this->messageManager = $this->service('XF:Conversation\MessageManager', $this->message);

		$this->setupDefaults();
	}

	protected function setupDefaults()
	{
	}

	public function getConversation()
	{
		return $this->conversation;
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function getMessageManager()
	{
		return $this->messageManager;
	}

	public function setLogIp($logIp)
	{
		$this->messageManager->setLogIp($logIp);
	}

	public function setAutoSpamCheck($check)
	{
		$this->autoSpamCheck = (bool)$check;
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

	public function setAutoSendNotifications($send)
	{
		$this->autoSendNotifications = (bool)$send;
	}

	public function setMessageContent($message, $format = true)
	{
		return $this->messageManager->setMessage($message, $format, $this->performValidations);
	}

	public function setAttachmentHash($hash)
	{
		$this->messageManager->setAttachmentHash($hash);
	}

	public function checkForSpam()
	{
		if ($this->user->isSpamCheckRequired())
		{
			$this->messageManager->checkForSpam();
		}
	}

	protected function finalSetup()
	{
		$this->message->message_date = time();

		if ($this->autoSpamCheck)
		{
			$this->checkForSpam();
		}
	}

	protected function _validate()
	{
		$this->finalSetup();

		$this->message->preSave();
		return $this->message->getErrors();
	}

	protected function _save()
	{
		$message = $this->message;

		$db = $this->db();
		$db->beginTransaction();

		$convLatest = $this->db()->fetchRow("
			SELECT *
			FROM xf_conversation_master
			WHERE conversation_id = ?
			FOR UPDATE
		", $message->conversation_id);

		$message->save(true, false);

		$this->messageManager->afterInsert();
		$this->markReadIfNeeded();

		$db->commit();

		if ($this->autoSendNotifications)
		{
			$this->sendNotifications();
		}

		return $message;
	}

	protected function markReadIfNeeded()
	{
		$userConv = $this->conversation->Users[$this->user->user_id];
		if ($userConv && $userConv->is_unread)
		{
			/** @var \XF\Repository\Conversation $convRepo */
			$convRepo = $this->repository('XF:Conversation');
			$convRepo->markUserConversationRead($userConv, $this->message->message_date);
		}
	}

	public function sendNotifications()
	{
		/** @var \XF\Service\Conversation\Notifier $notifier */
		$notifier = $this->service('XF:Conversation\Notifier', $this->conversation);
		$notifier->notifyReply($this->message);
	}
}