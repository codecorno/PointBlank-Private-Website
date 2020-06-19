<?php

namespace XF\Service\Conversation;

use XF\Entity\ConversationMaster;
use XF\Service\AbstractService;
use XF\Service\ValidateAndSavableTrait;

class Editor extends AbstractService
{
	use ValidateAndSavableTrait;

	/**
	 * @var ConversationMaster
	 */
	protected $conversation;

	public function __construct(\XF\App $app, ConversationMaster $conversation)
	{
		parent::__construct($app);

		$this->conversation = $conversation;
	}

	public function setTitle($title)
	{
		$this->conversation->title = $title;
	}

	public function setOpenInvite($openInvite)
	{
		$this->conversation->open_invite = $openInvite;
	}

	public function setConversationOpen($conversationOpen)
	{
		$this->conversation->conversation_open = $conversationOpen;
	}

	protected function finalSetup()
	{
	}

	protected function _validate()
	{
		$this->finalSetup();

		$this->conversation->preSave();
		$errors = $this->conversation->getErrors();

		return $errors;
	}

	protected function _save()
	{
		$this->conversation->save();

		return $this->conversation;
	}
}