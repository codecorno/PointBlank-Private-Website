<?php

namespace XF\Mvc\Reply;

class Message extends AbstractReply
{
	protected $message = '';

	public function __construct($message, $responseCode = 200)
	{
		$this->setMessage($message);
		$this->setResponseCode($responseCode);
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function setMessage($message)
	{
		$this->message = $message;
	}
}