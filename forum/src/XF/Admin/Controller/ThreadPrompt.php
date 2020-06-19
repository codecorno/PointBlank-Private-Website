<?php

namespace XF\Admin\Controller;

use XF\Mvc\Entity\ArrayCollection;
use XF\Mvc\ParameterBag;
use XF\Mvc\FormAction;

class ThreadPrompt extends AbstractPrompt
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('node');
	}

	protected function getClassIdentifier()
	{
		return 'XF:ThreadPrompt';
	}

	protected function getLinkPrefix()
	{
		return 'thread-prompts';
	}

	protected function getTemplatePrefix()
	{
		return 'thread_prompt';
	}

	protected function getNodeParams(\XF\Entity\ThreadPrompt $prompt)
	{
		/** @var \XF\Repository\Node $nodeRepo */
		$nodeRepo = \XF::repository('XF:Node');
		$nodeTree = $nodeRepo->createNodeTree($nodeRepo->getFullNodeList());

		// only list nodes that are forums or contain forums
		$nodeTree = $nodeTree->filter(null, function($id, $node, $depth, $children, $tree)
		{
			return ($children || $node->node_type_id == 'Forum');
		});

		/** @var \XF\Mvc\Entity\ArrayCollection $forumPromptAssociations */
		$forumPromptAssociations = $prompt->getRelationOrDefault('ForumPrompts', false);

		return [
			'nodeTree' => $nodeTree,
			'nodeIds' => $forumPromptAssociations->pluckNamed('node_id')
		];
	}

	protected function promptAddEditResponse(\XF\Entity\AbstractPrompt $prompt)
	{
		$reply = parent::promptAddEditResponse($prompt);

		if ($reply instanceof \XF\Mvc\Reply\View)
		{
			$nodeParams = $this->getNodeParams($prompt);
			$reply->setParams($nodeParams);
		}

		return $reply;
	}

	protected function quickSetAdditionalData(FormAction $form, ArrayCollection $prompts)
	{
		$input = $this->filter([
			'apply_node_ids' => 'bool',
			'node_ids' => 'array-uint'
		]);

		if ($input['apply_node_ids'])
		{
			$form->complete(function() use($prompts, $input)
			{
				$mapRepo = $this->getForumPromptRepo();

				foreach ($prompts AS $prompt)
				{
					$mapRepo->updatePromptAssociations($prompt, $input['node_ids']);
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
				$nodeParams = $this->getNodeParams($reply->getParam('prompt'));
				$reply->setParams($nodeParams);
			}
		}

		return $reply;
	}

	protected function saveAdditionalData(FormAction $form, \XF\Entity\AbstractPrompt $prompt)
	{
		$nodeIds = $this->filter('node_ids', 'array-uint');

		$form->complete(function() use($prompt, $nodeIds)
		{
			$this->getForumPromptRepo()->updatePromptAssociations($prompt, $nodeIds);
		});

		return $form;
	}

	/**
	 * @return \XF\Repository\ForumPrompt
	 */
	protected function getForumPromptRepo()
	{
		return $this->repository('XF:ForumPrompt');
	}
}