<?php

namespace XF\Http;

use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
	use StreamDecoratorTrait;

	protected $maxTime = -1;
	protected $maxSize = -1;

	protected $startTime = 0;
	protected $written = 0;

	protected $errorReason = 0;

	public function __construct(StreamInterface $stream, $maxSize = -1, $maxTime = -1)
	{
		$this->stream = $stream;
		$this->maxSize = $maxSize;
		$this->maxTime = $maxTime;

		$this->startTime = microtime(true);
	}

	public function write($string)
	{
		$value = $this->stream->write($string);
		if ($value === false)
		{
			return false;
		}

		if ($this->maxTime > -1)
		{
			$spent = microtime(true) - $this->startTime;
			if ($spent > $this->maxTime)
			{
				$this->errorReason = Reader::ERROR_TIME;
				return false;
			}
		}

		if ($this->maxSize > -1)
		{
			$this->written += strlen($string);
			if ($this->written > $this->maxSize)
			{
				$this->errorReason = Reader::ERROR_SIZE;
				return false;
			}
		}

		return $value;
	}

	public function hasError(&$errorKey = null)
	{
		if ($this->errorReason)
		{
			$errorKey = $this->errorReason;
			return true;
		}
		else
		{
			return false;
		}
	}
}