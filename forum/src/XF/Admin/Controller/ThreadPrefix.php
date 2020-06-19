<?php

namespace XF\Admin\Controller;

use XF\Mvc\Entity\ArrayCollection;
use XF\Mvc\ParameterBag;
use XF\Mvc\FormAction;

class ThreadPrefix extends AbstractPrefix
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('thread');
	}

	protected function getClassIdentifier()
	{
		return 'XF:ThreadPrefix';
	}

	protected function getLinkPrefix()
	{
		return 'thread-prefixes';
	}

	protected function getTemplatePrefix()
	{
		return 'thread_prefix';
	}

	protected function getNodeParams(\XF\Entity\ThreadPrefix $prefix)
	{
		/** @var \XF\Repository\Node $nodeRepo */
		$nodeRepo = \XF::repository('XF:Node');
		$nodeTree = $nodeRepo->createNodeTree($nodeRepo->getFullNodeList());

		// only list nodes that are forums or contain forums
		$nodeTree = $nodeTree->filter(null, function($id, $node, $depth, $children, $tree)
		{
			return ($children || $node->node_type_id == 'Forum');
		});

		/** @var \XF\Mvc\Entity\ArrayCollection $forumPrefixAssociations */
		$forumPrefixAssociations = $prefix->getRelationOrDefault('ForumPrefixes', false);

		return [
			'nodeTree' => $nodeTree,
			'nodeIds' => $forumPrefixAssociations->pluckNamed('node_id')
		];
	}

	protected function prefixAddEditResponse(\XF\Entity\AbstractPrefix $prefix)
	{
		$reply = parent::prefixAddEditResponse($prefix);

		if ($reply instanceof \XF\Mvc\Reply\View)
		{
			$nodeParams = $this->getNodeParams($prefix);
			$reply->setParams($nodeParams);
		}

		return $reply;
	}

	protected function quickSetAdditionalData(FormAction $form, ArrayCollection $prefixes)
	{
		$input = $this->filter([
			'apply_node_ids' => 'bool',
			'node_ids' => 'array-uint'
		]);

		if ($input['apply_node_ids'])
		{
			$form->complete(function() use($prefixes, $input)
			{
				$mapRepo = $this->getForumPrefixRepo();

				foreach ($prefixes AS $prefix)
				{
					$mapRepo->updatePrefixAssociations($prefix, $input['node_ids']);
				}
			});
		}

		return $form;
	}

	public function actionQuickSet()
	{
		$reply = parent::actionQuickSet();

		if ($reply instanceof \XF\Mvc\Reply\View)
		{
			if ($reply->getTemplateName() == $this->getTemplatePrefix() . '_quickset_editor')
			{
				$nodeParams = $this->getNodeParams($reply->getParam('prefix'));
				$reply->setParams($nodeParams);
			}
		}

		return $reply;
	}

	protected function saveAdditionalData(FormAction $form, \XF\Entity\AbstractPrefix $prefix)
	{
		$nodeIds = $this->filter('node_ids', 'array-uint');

		$form->complete(function() use($prefix, $nodeIds)
		{
			$this->getForumPrefixRepo()->updatePrefixAssociations($prefix, $nodeIds);
		});

		return $form;
	}

	/**
	 * @return \XF\Repository\ForumPrefix
	 */
	protected function getForumPrefixRepo()
	{
		return $this->repository('XF:ForumPrefix');
	}
}