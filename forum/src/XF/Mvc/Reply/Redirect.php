<?php

namespace XF\Mvc\Reply;

class Redirect extends AbstractReply
{
	const TEMPORARY = 'temporary';
	const PERMANENT = 'permanent';

	protected $url = '';
	protected $type = '';
	protected $message = '';

	public function __construct($url, $type = 'temporary', $message = '')
	{
		$this->setUrl($url);
		$this->setType($type);
		$this->setMessage($message);
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function setUrl($url)
	{
		$this->url = $url;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setType($type)
	{
		switch ($type)
		{
			case self::TEMPORARY:
			case self::PERMANENT:
				$this->type = $type;
				break;

			default:
				throw new \InvalidArgumentException("Invalid redirect type '$type'");
		}
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