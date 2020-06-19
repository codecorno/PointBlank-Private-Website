<?php

namespace XF\InlineMod\Thread;

use XF\Http\Request;
use XF\InlineMod\AbstractAction;
use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;

class Merge extends AbstractAction
{
	public function getTitle()
	{
		return \XF::phrase('merge_threads...');
	}
	
	protected function canApplyInternal(AbstractCollection $entities, array $options, &$error)
	{
		$result = parent::canApplyInternal($entities, $options, $error);
		
		if ($result)
		{
			foreach ($entities AS $entity)
			{
				if ($entity->discussion_type == 'redirect')
				{
					return false;
				}
			}

			if ($options['target_thread_id'])
			{
				if (!isset($entities[$options['target_thread_id']]))
				{
					return false;
				}
			}

			if ($entities->count() < 2)
			{
				return false;
			}
		}
		
		return $result;
	}

	protected function canApplyToEntity(Entity $entity, array $options, &$error = null)
	{
		/** @var \XF\Entity\Thread $entity */
		return $entity->canMerge($error);
	}

	public function applyInternal(AbstractCollection $entities, array $options)
	{
		if (!$options['target_thread_id'])
		{
			throw new \InvalidArgumentException("No target thread selected");
		}

		$source = $entities->toArray();
		$target = $source[$options['target_thread_id']];
		unset($source[$options['target_thread_id']]);

		/** @var \XF\Service\Thread\Merger $merger */
		$merger = $this->app()->service('XF:Thread\Merger', $target);

		if ($options['alert'])
		{
			$merger->setSendAlert(true, $options['alert_reason']);
		}

		if ($options['redirect'])
		{
			$merger->setRedirect(true, $options['redirect_length']);
		}

		$merger->merge($source);

		$this->returnUrl = $this->app()->router()->buildLink('threads', $target);
	}

	protected function applyToEntity(Entity $entity, array $options)
	{
		throw new \LogicException("applyToEntity should not be called on thread merging");
	}

	public function getBaseOptions()
	{
		return [
			'target_thread_id' => 0,
			'redirect' => false,
			'redirect_length' => 0,
			'alert' => false,
			'alert_reason' => ''
		];
	}

	public function renderForm(AbstractCollection $entities, \XF\Mvc\Controller $controller)
	{
		$viewParams = [
			'threads' => $entities,
			'total' => count($entities),
			'first' => $entities->first()
		];
		return $controller->view('XF:Public:InlineMod\Thread\Merge', 'inline_mod_thread_merge', $viewParams);
	}

	public function getFormOptions(AbstractCollection $entities, Request $request)
	{
		$options = [
			'target_thread_id' => $request->filter('target_thread_id', 'uint'),
			'alert' => $request->filter('starter_alert', 'bool'),
			'alert_reason' => $request->filter('starter_alert_reason', 'str')
		];

		$redirectType = $request->filter('redirect_type', 'str');
		if ($redirectType == 'permanent')
		{
			$options['redirect'] = true;
			$options['redirect_length'] = 0;
		}
		else if ($redirectType == 'temporary')
		{
			$options['redirect'] = true;
			$options['redirect_length'] = $request->filter('redirect_length', 'timeoffset');
		}
		else
		{
			$options['redirect'] = false;
			$options['redirect_length'] = 0;
		}

		return $options;
	}
}