<?php

namespace XF\InlineMod;

use XF\Mvc\Entity\Entity;

class SimpleAction extends AbstractAction
{
	protected $title;

	protected $canApply;

	protected $apply;

	public function __construct(AbstractHandler $handler, $title, $canApply, \Closure $apply)
	{
		parent::__construct($handler);
		$this->setTitle($title);
		$this->setCanApply($canApply);
		$this->setApply($apply);
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getTitle()
	{
		if ($this->title instanceof \Closure)
		{
			$title = $this->title;
			return $title();
		}
		else
		{
			return $this->title;
		}
	}

	public function setCanApply($canApply)
	{
		$this->canApply = $canApply;
	}

	public function setApply(\Closure $apply)
	{
		$this->apply = $apply;
	}

	protected function canApplyToEntity(Entity $entity, array $options, &$error = null)
	{
		$canApply = $this->canApply;

		if (is_string($canApply))
		{
			return $entity->{$canApply}($error);
		}
		else if ($canApply instanceof \Closure)
		{
			return $canApply($entity, $options, $error);
		}
		else if ($canApply === true)
		{
			return true;
		}
		else
		{
			throw new \InvalidArgumentException("canApply must be a string for a method, a closure or true");
		}
	}

	protected function applyToEntity(Entity $entity, array $options)
	{
		$apply = $this->apply;

		if ($apply instanceof \Closure)
		{
			$apply($entity, $options);
		}
		else
		{
			throw new \InvalidArgumentException("Apply must be overridden (with a closure)");
		}
	}
}