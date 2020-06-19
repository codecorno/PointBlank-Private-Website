<?php

namespace XF\Service\Conversation;

use XF\Entity\ConversationMessage;

class MessageManager extends \XF\Service\AbstractService
{
	/**
	 * @var ConversationMessage
	 */
	protected $conversationMessage;

	protected $attachmentHash;

	protected $logIp = true;

	public function __construct(\XF\App $app, ConversationMessage $conversationMessage)
	{
		parent::__construct($app);
		$this->conversationMessage = $conversationMessage;
	}

	public function getConversationMessage()
	{
		return $this->conversationMessage;
	}

	public function setLogIp($logIp)
	{
		$this->logIp = $logIp;
	}

	public function setMessage($message, $format = true, $checkValidity = true)
	{
		$preparer = $this->getMessagePreparer($format);
		$this->conversationMessage->message = $preparer->prepare($message, $checkValidity);
		$this->conversationMessage->embed_metadata = $preparer->getEmbedMetadata();

		return $preparer->pushEntityErrorIfInvalid($this->conversationMessage);
	}

	/**
	 * @param bool $format
	 *
	 * @return \XF\Service\Message\Preparer
	 */
	protected function getMessagePreparer($format = true)
	{
		/** @var \XF\Service\Message\Preparer $preparer */
		$preparer = $this->service('XF:Message\Preparer', 'conversation_message', $this->conversationMessage);
		if (!$format)
		{
			$preparer->disableAllFilters();
		}

		return $preparer;
	}

	public function setAttachmentHash($hash)
	{
		$this->attachmentHash = $hash;
	}

	public function checkForSpam()
	{
		$conversationMessage = $this->conversationMessage;

		/** @var \XF\Entity\User $user */
		$user = $conversationMessage->User ?: $this->repository('XF:User')->getGuestUser($conversationMessage->username);
		$message = $conversationMessage->Conversation->title . "\n" . $conversationMessage->message;

		$checker = $this->app->spam()->contentChecker();
		$checker->check($user, $message, [
			'content_type' => 'conversation_message'
		]);

		$decision = $checker->getFinalDecision();
		switch ($decision)
		{
			case 'moderated':
			case 'denied':
				$checker->logSpamTrigger('conversation', null);
				$conversationMessage->error(\XF::phrase('your_content_cannot_be_submitted_try_later'));
				break;
		}
	}

	public function afterInsert()
	{
		if ($this->attachmentHash)
		{
			$this->associateAttachments($this->attachmentHash);
		}

		if ($this->logIp)
		{
			$ip = ($this->logIp === true ? $this->app->request()->getIp() : $this->logIp);
			$this->writeIpLog($ip);
		}
	}

	public function afterUpdate()
	{
		if ($this->attachmentHash)
		{
			$this->associateAttachments($this->attachmentHash);
		}

		// TODO: moderator log?
		// TODO: edit history?
	}

	protected function associateAttachments($hash)
	{
		$conversationMessage = $this->conversationMessage;

		/** @var \XF\Service\Attachment\Preparer $inserter */
		$inserter = $this->service('XF:Attachment\Preparer');
		$associated = $inserter->associateAttachmentsWithContent($hash, 'conversation_message', $conversationMessage->message_id);
		if ($associated)
		{
			$conversationMessage->fastUpdate('attach_count', $conversationMessage->attach_count + $associated);
		}
	}

	protected function writeIpLog($ip)
	{
		$conversationMessage = $this->conversationMessage;

		/** @var \XF\Repository\IP $ipRepo */
		$ipRepo = $this->repository('XF:Ip');
		$ipEnt = $ipRepo->logIp($conversationMessage->user_id, $ip, 'conversation_message', $conversationMessage->message_id);
		if ($ipEnt)
		{
			$conversationMessage->fastUpdate('ip_id', $ipEnt->ip_id);
		}
	}
}