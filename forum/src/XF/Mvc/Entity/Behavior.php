<?php

namespace XF\Mvc\Entity;

abstract class Behavior
{
	/**
	 * @var Entity
	 */
	protected $entity;

	protected $config = [];
	protected $options = [];

	public function __construct(Entity $entity, array $config = [])
	{
		$this->entity = $entity;
		$this->config = array_merge($this->getDefaultConfig(), $config);
		$this->options = $this->getDefaultOptions();
		$this->verifyConfig();
	}

	public function onSetup()
	{
	}

	protected function getDefaultConfig()
	{
		return [];
	}

	protected function verifyConfig()
	{
	}

	public function getConfig($name)
	{
		if (!array_key_exists($name, $this->config))
		{
			throw new \InvalidArgumentException("Invalid config '$name'");
		}

		return $this->config[$name];
	}

	protected function getDefaultOptions()
	{
		return [];
	}

	public function getOptions()
	{
		return $this->options;
	}

	public function getOption($name)
	{
		if (!array_key_exists($name, $this->options))
		{
			throw new \InvalidArgumentException("Invalid option '$name'");
		}

		return $this->options[$name];
	}

	public function setOption($name, $value)
	{
		if (!array_key_exists($name, $this->options))
		{
			throw new \InvalidArgumentException("Invalid option '$name'");
		}

		$this->options[$name] = $value;
	}

	public function resetOptions()
	{
		$this->options = $this->getDefaultOptions();
	}

	public function preSave()
	{
	}

	public function postSave()
	{
	}

	public function preDelete()
	{
	}

	public function postDelete()
	{
	}

	public function contentType()
	{
		return $this->entity->structure()->contentType;
	}

	public function id()
	{
		return $this->entity->getEntityId();
	}

	/**
	 * @param string $repo
	 *
	 * @return Repository
	 */
	public function repository($repo)
	{
		return $this->entity->repository($repo);
	}

	/**
	 * @return \XF\App
	 */
	public function app()
	{
		return $this->entity->app();
	}
}