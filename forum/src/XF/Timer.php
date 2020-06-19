<?php

namespace XF;

class Timer
{
	protected $start;
	protected $limit;

	public function __construct($limit = null, $start = null)
	{
		if ($start === null)
		{
			$start = microtime(true);
		}

		$this->start = $start;

		if ($limit !== null && $limit <= 0)
		{
			throw new \InvalidArgumentException("The limit must be greater than zero");
		}
		$this->limit = $limit;
	}

	public function start()
	{
		return $this->start;
	}

	public function reset()
	{
		$this->start = microtime(true);
	}

	public function now()
	{
		return microtime(true);
	}

	public function diff()
	{
		return (microtime(true) - $this->start);
	}

	public function remaining()
	{
		if ($this->limit === null)
		{
			return null;
		}
		else
		{
			return max(0, $this->limit - $this->diff());
		}
	}

	public function limit()
	{
		return $this->limit;
	}

	public function limitExceeded()
	{
		if ($this->limit === null)
		{
			return false;
		}
		else
		{
			return $this->diff() > $this->limit;
		}
	}
}