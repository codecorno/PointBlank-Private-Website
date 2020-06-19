<?php

namespace XF\Mvc\Entity;

class DeferredValue
{
	protected $handler;
	protected $assignTime;

	protected $assignableMatrix = [
		'get' => ['get' => true, 'preSave' => true],
		'preSave' => ['get' => false, 'preSave' => true],
		'save' => ['get' => false, 'preSave' => false]
	];

	public function __construct(\Closure $handler, $assignTime = 'preSave')
	{
		$this->handler = $handler;

		if ($this->isValidContext($assignTime))
		{
			$this->assignTime = $assignTime;
		}
		else
		{
			throw new \InvalidArgumentException("Assign time may only be get, preSave or save");
		}
	}

	public function __invoke(Entity $e, $context)
	{
		if (!$this->isValidContext($context))
		{
			throw new \InvalidArgumentException("Invalid deferred value invocation context");
		}

		$handler = $this->handler;
		return $handler($e, $context);
	}

	public function getHandler()
	{
		return $this->handler;
	}

	public function getAssignTime()
	{
		return $this->assignTime;
	}

	public function isAssignableAt($time)
	{
		if (!$this->isValidContext($time))
		{
			throw new \InvalidArgumentException("Time may only be get, preSave or save");
		}

		if ($time == 'save')
		{
			// all values must be assigned by save so they're not in the matrix
			return true;
		}

		$assignable = $this->assignableMatrix[$this->assignTime];
		return $assignable[$time];
	}

	protected function isValidContext($context)
	{
		switch ($context)
		{
			case 'get':
			case 'preSave':
			case 'save':
				return true;

			default:
				return false;
		}
	}
}