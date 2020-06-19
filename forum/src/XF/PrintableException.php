<?php

namespace XF;

class PrintableException extends \Exception
{
	protected $messages;

	/**
	 * PrintableException constructor.
	 *
	 * @param string|array $message
	 * @param int $code
	 * @param \Exception|null $previous
	 */
	public function __construct($message, $code = 0, \Exception $previous = null)
	{
		$this->messages = is_array($message) ? $message : [$message];

		if (is_array($message) && count($message) > 0)
		{
			$singleMessage = reset($message);

			if ($code === 0)
			{
				$code = key($message);
				if (!is_string($code))
				{
					$code = 0;
				}
			}

			$message = $singleMessage;
		}

		if (!is_string($message))
		{
			$message = strval($message);
		}

		parent::__construct($message, 0, $previous);
		$this->code = $code; // base class only allows integer codes
	}

	public function getMessages()
	{
		return $this->messages;
	}
}