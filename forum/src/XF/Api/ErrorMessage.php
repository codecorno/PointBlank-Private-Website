<?php

namespace XF\Api;

class ErrorMessage
{
	protected $message;

	protected $code;

	protected $params;

	public function __construct($message, $code = null, array $params = null)
	{
		$this->message = $message;
		$this->code = $code;
		$this->params = $params;
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function getCode()
	{
		if ($this->code === null)
		{
			if ($this->message instanceof \XF\Phrase)
			{
				$this->code = $this->message->getName();
			}
			else
			{
				$this->code = 'unknown_api_error';
			}
		}

		return $this->code;
	}

	public function getParams()
	{
		if ($this->params === null)
		{
			if ($this->message instanceof \XF\Phrase)
			{
				return $this->message->getParams();
			}
		}

		return $this->params ?: [];
	}

	public function __toString()
	{
		return (string)$this->message;
	}
}