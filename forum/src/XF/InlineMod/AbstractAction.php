<?php

namespace XF\InlineMod;

use XF\Http\Request;
use XF\HTTP\Response;
use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Reply\AbstractReply;

abstract class AbstractAction
{
	/**
	 * @var AbstractHandler
	 */
	protected $handler;

	/**
	 * @var AbstractCollection
	 */
	protected $entities;

	protected $returnUrl;

	abstract public function getTitle();
	abstract protected function canApplyToEntity(Entity $entity, array $options, &$error = null);
	abstract protected function applyToEntity(Entity $entity, array $options);

	public function __construct(AbstractHandler $handler)
	{
		$this->handler = $handler;
	}

	public function getBaseOptions()
	{
		return [];
	}

	public function renderForm(AbstractCollection $entities, \XF\Mvc\Controller $controller)
	{
		return null;
	}

	public function getFormOptions(AbstractCollection $entities, Request $request)
	{
		return [];
	}

	protected function standardizeOptions(array $options)
	{
		return array_merge($this->getBaseOptions(), $options);
	}

	public function canApply(AbstractCollection $entities, array $options = [], &$error = null)
	{
		$options = $this->standardizeOptions($options);
		return $this->canApplyInternal($entities, $options, $error);
	}

	protected function canApplyInternal(AbstractCollection $entities, array $options, &$error)
	{
		foreach ($entities AS $entity)
		{
			if (!$this->handler->canViewContent($entity, $error))
			{
				return false;
			}
			if (!$this->canApplyToEntity($entity, $options, $error))
			{
				return false;
			}
		}

		return true;
	}

	public function apply(AbstractCollection $entities, array $options = [])
	{
		$options = $this->standardizeOptions($options);
		$this->applyInternal($entities, $options);
	}

	protected function applyInternal(AbstractCollection $entities, array $options)
	{
		foreach ($entities AS $entity)
		{
			$this->applyToEntity($entity, $options);
		}
	}

	public function getReturnUrl()
	{
		return $this->returnUrl;
	}

	public function postApply(AbstractCollection $entities, AbstractReply &$reply, Response $response)
	{
		$this->handler->clearCookie($response);
	}

	protected function app()
	{
		return $this->handler->app();
	}
}