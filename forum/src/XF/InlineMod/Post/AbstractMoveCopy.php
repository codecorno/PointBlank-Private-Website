<?php

namespace XF\InlineMod\Post;

use XF\Http\Request;
use XF\InlineMod\AbstractAction;
use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;

abstract class AbstractMoveCopy extends AbstractAction
{
	protected $targetForum;
	protected $targetNodeId;

	protected $targetThread;

	public function getTitle()
	{
		throw new \LogicException("The title phrase must be overridden.");
	}

	protected function canApplyInternal(AbstractCollection $entities, array $options, &$error)
	{
		$result = parent::canApplyInternal($entities, $options, $error);

		if ($result && $options['thread_type'])
		{
			$node = null;

			switch ($options['thread_type'])
			{
				case 'existing':
					$threadRepo = $this->app()->repository('XF:Thread');
					$thread = $threadRepo->getThreadFromUrl($options['existing_url'], null, $threadFetchError);
					if ($thread)
					{
						$node = $thread->Forum;
					}
					else
					{
						$error = $threadFetchError;
						return false;
					}
					break;

				case 'new':
					$node = $this->getTargetForum($options['node_id']);
					break;
			}

			if ($options['check_node_viewable'] && (!$node || !$node->canView($error)))
			{
				return false;
			}
		}

		return $result;
	}

	protected function canApplyToEntity(Entity $entity, array $options, &$error = null)
	{
		throw new \LogicException("canApplyToEntity must be overridden.");
	}

	public function applyInternal(AbstractCollection $entities, array $options)
	{
		throw new \LogicException("applyInternal must be overridden.");
	}

	protected function applyToEntity(Entity $entity, array $options)
	{
		throw new \LogicException("applyToEntity should not be called on post moving or copying");
	}

	public function getBaseOptions()
	{
		return [
			'thread_type' => '',
			'node_id' => 0,
			'check_node_viewable' => true,
			'prefix_id' => null,
			'title' => '',
			'existing_url' => null,
			'alert' => false,
			'alert_reason' => ''
		];
	}

	public function renderForm(AbstractCollection $entities, \XF\Mvc\Controller $controller)
	{
		throw new \LogicException("renderForm must be overridden.");
	}

	public function getFormOptions(AbstractCollection $entities, Request $request)
	{
		$options = [
			'thread_type' => $request->filter('thread_type', 'str'), // existing or new
			'node_id' => $request->filter('node_id', 'uint'),
			'prefix_id' => $request->filter('prefix_id', 'int'), // need to allow -1
			'title' => $request->filter('title', 'str'),
			'existing_url' => $request->filter('existing_url', 'str'),
			'alert' => $request->filter('author_alert', 'bool'),
			'alert_reason' => $request->filter('author_alert_reason', 'str')
		];
		if ($options['prefix_id'] < 0)
		{
			$options['prefix_id'] = null;
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
		if (!$nodeId)
		{
			return null;
		}

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

	protected function getTargetThreadFromOptions(array $options)
	{
		if ($options['thread_type'] == 'existing')
		{
			$thread = $this->app()->repository('XF:Thread')->getThreadFromUrl($options['existing_url']);
		}
		else
		{
			$node = $this->getTargetForum($options['node_id']);
			if (!$node)
			{
				throw new \InvalidArgumentException("No target forum available");
			}

			$thread = $this->app()->em()->create('XF:Thread');
			$thread->title = $options['title'];
			$thread->node_id = $node->node_id;
		}

		if (!$thread)
		{
			throw new \InvalidArgumentException("No target thread available");
		}

		return $thread;
	}
}