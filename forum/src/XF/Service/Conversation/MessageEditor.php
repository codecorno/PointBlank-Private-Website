<?php

namespace XF\Service\Conversation;

use XF\Entity\ConversationMessage;

class MessageEditor extends \XF\Service\AbstractService
{
	use \XF\Service\ValidateAndSavableTrait;

	/**
	 * @var ConversationMessage
	 */
	protected $message;

	/**
	 * @var \XF\Service\Conversation\MessageManager
	 */
	protected $messageManager;

	protected $autoSpamCheck = true;
	protected $performValidations = true;

	public function __construct(\XF\App $app, ConversationMessage $message)
	{
		parent::__construct($app);

		$this->message = $message;
		$this->messageManager = $this->service('XF:Conversation\MessageManager', $this->message);
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function getMessageManager()
	{
		return $this->messageManager;
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
		$this->setAutoSpamCheck(false);
		$this->setPerformValidations(false);
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
		if (!$this->message->User || $this->message->User->isSpamCheckRequired())
		{
			$this->messageManager->checkForSpam();
		}
	}

	protected function finalSetup()
	{
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
		$db = $this->db();
		$db->beginTransaction();

		$this->message->save(true, false);

		$this->messageManager->afterUpdate();

		$db->commit();

		return $this->message;
	}
}