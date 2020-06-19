<?php

namespace XF\Validator;

abstract class AbstractValidator
{
	protected $options = [];

	/**
	 * @var \XF\App
	 */
	protected $app;

	abstract public function isValid($value, &$errorKey = null);

	public function __construct(\XF\App $app)
	{
		$this->app = $app;
		$this->setupOptionDefaults();
	}

	protected function setupOptionDefaults()
	{
	}

	public function coerceValue($value)
	{
		return $value;
	}

	public function getPrintableErrorValue($errorKey)
	{
		return null;
	}

	public function setOption($key, $value)
	{
		if (!array_key_exists($key, $this->options))
		{
			throw new \InvalidArgumentException("Unknown option $key in " . get_class($this));
		}

		$this->options[$key] = $value;
	}

	public function setOptions(array $options)
	{
		foreach ($options AS $key => $value)
		{
			$this->setOption($key, $value);
		}
	}

	public function getOption($key)
	{
		if (!array_key_exists($key, $this->options))
		{
			throw new \InvalidArgumentException("Unknown option $key");
		}

		return $this->options[$key];
	}
}