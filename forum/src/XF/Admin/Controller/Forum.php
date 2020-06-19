<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class Forum extends AbstractNode
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		switch (strtolower($action))
		{
			case 'index':
				break;

			default:
				parent::preDispatchController($action, $params);
		}
	}

	public function actionIndex()
	{
		$this->setSectionContext('forums');

		return $this->view('XF:Forums', 'forums');
	}

	protected function getNodeTypeId()
	{
		return 'Forum';
	}

	protected function getDataParamName()
	{
		return 'forum';
	}

	protected function getTemplatePrefix()
	{
		return 'forum';
	}

	protected function getViewClassPrefix()
	{
		return 'XF:Forum';
	}

	protected function nodeAddEdit(\XF\Entity\Node $node)
	{
		$reply = parent::nodeAddEdit($node);

		if ($reply instanceof \XF\Mvc\Reply\View)
		{
			/** @var \XF\Repository\ThreadField $fieldRepo */
			$fieldRepo = $this->repository('XF:ThreadField');
			$availableFields = $fieldRepo->findFieldsForList()->fetch();
		//	$availableFields = $availableFields->pluckNamed('title', 'field_id');

			/** @var \XF\Repository\ThreadPrefix $prefixRepo */
			$prefixRepo = $this->repository('XF:ThreadPrefix');
			$availablePrefixes = $prefixRepo->findPrefixesForList()->fetch()->pluckNamed('title', 'prefix_id');
			$prefixListData = $prefixRepo->getPrefixListData();

			/** @var \XF\Repository\ThreadPrompt $promptRepo */
			$promptRepo = $this->repository('XF:ThreadPrompt');
			$availablePrompts = $promptRepo->findPromptsForList()->fetch()->pluckNamed('title', 'prompt_id');
			$promptListData = $promptRepo->getPromptListData();

			$reply->setParams([
				'availableFields' => $availableFields,

				'availablePrefixes' => $availablePrefixes,

				'availablePrompts' => $availablePrompts,

				'prefixGroups' => $prefixListData['prefixGroups'],
				'prefixesGrouped' => $prefixListData['prefixesGrouped'],

				'promptGroups' => $promptListData['promptGroups'],
				'promptsGrouped' => $promptListData['promptsGrouped']
			]);
		}

		return $reply;
	}

	protected function saveTypeData(FormAction $form, \XF\Entity\Node $node, \XF\Entity\AbstractNode $data)
	{
		$forumInput = $this->filter([
			'allow_posting' => 'bool',
			'allow_poll' => 'bool',
			'moderate_threads' => 'bool',
			'moderate_replies' => 'bool',
			'count_messages' => 'bool',
			'find_new' => 'bool',
			'allowed_watch_notifications' => 'str',
			'default_sort_order' => 'str',
			'default_sort_direction' => 'str',
			'list_date_limit_days' => 'uint',
			'default_prefix_id' => 'uint',
			'require_prefix' => 'bool',
			'min_tags' => 'uint'
		]);

		/** @var \XF\Entity\Forum $data */
		$data->bulkSet($forumInput);

		$prefixIds = $this->filter('available_prefixes', 'array-uint');
		$form->complete(function() use($data, $prefixIds)
		{
			/** @var \XF\Repository\ForumPrefix $repo */
			$repo = $this->repository('XF:ForumPrefix');
			$repo->updateContentAssociations($data->node_id, $prefixIds);
		});

		if (!in_array($data->default_prefix_id, $prefixIds))
		{
			$data->default_prefix_id = 0;
		}

		$fieldIds = $this->filter('available_fields', 'array-str');
		$form->complete(function () use ($data, $fieldIds)
		{
			/** @var \XF\Repository\ForumField $repo */
			$repo = $this->repository('XF:ForumField');
			$repo->updateContentAssociations($data->node_id, $fieldIds);
		});

		$promptIds = $this->filter('available_prompts', 'array-uint');
		$form->complete(function() use($data, $promptIds)
		{
			/** @var \XF\Repository\ForumPrompt $repo */
			$repo = $this->repository('XF:ForumPrompt');
			$repo->updateContentAssociations($data->node_id, $promptIds);
		});
	}

	public function actionPrefixes(ParameterBag $params)
	{
		$this->assertPostOnly();

		$viewParams = [];

		$nodeId = $this->filter('val', 'uint');
		if ($nodeId)
		{
			/** @var \XF\Entity\Forum $forum */
			$node = $this->assertNodeExists($nodeId);
			$forum = $node->getDataRelationOrDefault();

			$viewParams['forum'] = $forum;
			$viewParams['prefixes'] = $forum->getPrefixesGrouped();
		}

		return $this->view('XF:Forum\Prefixes', 'public:forum_prefixes', $viewParams);
	}
}