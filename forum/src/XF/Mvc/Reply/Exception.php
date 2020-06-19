<?php

namespace XF\Mvc\Reply;

class Exception extends \Exception
{
	/**
	 * @var AbstractReply
	 */
	protected $reply;

	public function __construct(AbstractReply $reply)
	{
		parent::__construct();
		$this->reply = $reply;
	}

	/**
	 * @return AbstractReply
	 */
	public function getReply()
	{
		return $this->reply;
	}
}