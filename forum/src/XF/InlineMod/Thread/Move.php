<?php

namespace XF\InlineMod\Thread;

use XF\Http\Request;
use XF\InlineMod\AbstractAction;
use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;

class Move extends AbstractAction
{
	protected $targetForum;
	protected $targetNodeId;

	public function getTitle()
	{
		return \XF::phrase('move_threads...');
	}
	
	protected function canApplyInternal(AbstractCollection $entities, array $options, &$error)
	{
		$result = parent::canApplyInternal($entities, $options, $error);
		
		if ($result)
		{
			if ($options['target_node_id'])
			{
				$node = $this->getTargetForum($options['target_node_id']);
				if (!$node)
				{
					return false;
				}
				
				if ($options['check_node_viewable'] && !$node->canView($error))
				{
					return false;
				}
				
				if ($options['check_all_same_node'])
				{
					$allSame = true;
					foreach ($entities AS $entity)
					{
						/** @var \XF\Entity\Thread $entity */
						if ($entity->node_id != $options['target_node_id'])
						{
							$allSame = false;
							break;
						}
					}
					
					if ($allSame)
					{
						$error = \XF::phraseDeferred('all_threads_in_destination_forum');
						return false;
					}
				}
			}
		}
		
		return $result;
	}

	protected function canApplyToEntity(Entity $entity, array $options, &$error = null)
	{
		/** @var \XF\Entity\Thread $entity */
		return $entity->canMove($error);
	}

	protected function applyToEntity(Entity $entity, array $options)
	{
		/** @var \XF\Entity\Thread $entity */

		$node = $this->getTargetForum($options['target_node_id']);
		if (!$node)
		{
			throw new \InvalidArgumentException("No target specified");
		}

		/** @var \XF\Service\Thread\Mover $mover */
		$mover = $this->app()->service('XF:Thread\Mover', $entity);

		if ($options['alert'])
		{
			$mover->setSendAlert(true, $options['alert_reason']);
		}

		if ($options['notify_watchers'])
		{
			$mover->setNotifyWatchers();
		}

		if ($options['redirect'] && $entity->discussion_type != 'redirect')
		{
			$mover->setRedirect(true, $options['redirect_length']);
		}

		if ($options['prefix_id'] !== null)
		{
			$mover->setPrefix($options['prefix_id']);
		}

		$mover->move($node);

		$this->returnUrl = $this->app()->router('public')->buildLink('forums', $node);
	}

	public function getBaseOptions()
	{
		return [
			'target_node_id' => 0,
			'check_node_viewable' => true,
			'check_all_same_node' => true,
			'prefix_id' => null,
			'redirect' => false,
			'redirect_length' => 0,
			'notify_watchers' => false,
			'alert' => false,
			'alert_reason' => ''
		];
	}

	public function renderForm(AbstractCollection $entities, \XF\Mvc\Controller $controller)
	{
		/** @var \XF\Repository\Node $nodeRepo */
		$nodeRepo = $this->app()->repository('XF:Node');
		$nodes = $nodeRepo->getFullNodeList()->filterViewable();

		$prefixes = $this->app()->finder('XF:ThreadPrefix')
			->order('materialized_order')
			->fetch();

		$viewParams = [
			'threads' => $entities,
			'prefixes' => $prefixes->groupBy('prefix_group_id'),
			'total' => count($entities),
			'nodeTree' => $nodeRepo->createNodeTree($nodes),
			'first' => $entities->first()
		];
		return $controller->view('XF:Public:InlineMod\Thread\Move', 'inline_mod_thread_move', $viewParams);
	}

	public function getFormOptions(AbstractCollection $entities, Request $request)
	{
		$options = [
			'target_node_id' => $request->filter('target_node_id', 'uint'),
			'apply_thread_prefix' => $request->filter('apply_thread_prefix', 'bool'),
			'prefix_id' => $request->filter('prefix_id', 'uint'),
			'notify_watchers' => $request->filter('notify_watchers', 'bool'),
			'alert' => $request->filter('starter_alert', 'bool'),
			'alert_reason' => $request->filter('starter_alert_reason', 'str')
		];
		if (!$options['apply_thread_prefix'])
		{
			$options['prefix_id'] = null;
		}

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

	/**
	 * @param integer $nodeId
	 * 
	 * @return null|\XF\Entity\Forum
	 */
	protected function getTargetForum($nodeId)
	{
		$nodeId = intval($nodeId);

		if ($this->targetNodeId && $this->targetNodeId == $nodeId)
		{
			return $this->targetForum;
		}
		if (!$nodeId)
		{
			return null;
		}

		$forum = $this->app()->em()->find('XF:Forum', $nodeId);
		if (!$forum)
		{
			throw new \InvalidArgumentException("Invalid target forum ($nodeId)");
		}

		$this->targetNodeId = $nodeId;
		$this->targetForum = $forum;

		return $this->targetForum;
	}
}