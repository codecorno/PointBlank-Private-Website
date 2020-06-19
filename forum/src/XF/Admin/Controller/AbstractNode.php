<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

abstract class AbstractNode extends AbstractController
{
	abstract protected function getNodeTypeId();

	abstract protected function getDataParamName();

	abstract protected function getTemplatePrefix();

	abstract protected function getViewClassPrefix();

	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('node');
	}

	protected function nodeAddEdit(\XF\Entity\Node $node)
	{
		$nodeRepo = $this->getNodeRepo();
		$nodeTree = $nodeRepo->createNodeTree($nodeRepo->getFullNodeList());

		/** @var \XF\Repository\Style $styleRepo */
		$styleRepo = $this->repository('XF:Style');
		$styleTree = $styleRepo->getStyleTree(false);

		/** @var \XF\Repository\Navigation $navRepo */
		$navRepo = $this->repository('XF:Navigation');
		$navChoices = $navRepo->getTopLevelEntries();

		$viewParams = [
			'node' => $node,
			$this->getDataParamName() => $node->getDataRelationOrDefault(),
			'nodeTree' => $nodeTree,
			'styleTree' => $styleTree,
			'navChoices' => $navChoices,
		];
		return $this->view($this->getViewClassPrefix() . '\Edit', $this->getTemplatePrefix() . '_edit', $viewParams);
	}

	public function actionAdd()
	{
		/** @var \XF\Entity\Node $node */
		$node = $this->em()->create('XF:Node');
		$node->node_type_id = $this->getNodeTypeId();
		$node->parent_node_id = $this->filter('parent_node_id', 'uint');
		return $this->nodeAddEdit($node);
	}

	public function actionEdit(ParameterBag $params)
	{
		$node = $this->assertNodeExists($params['node_id']);

		return $this->nodeAddEdit($node);
	}

	protected function nodeSaveProcess(\XF\Entity\Node $node)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'node' => [
				'title' => 'str',
				'node_name' => 'str',
				'description' => 'str',
				'parent_node_id' => 'uint',
				'display_order' => 'uint',
				'display_in_list' => 'bool',
				'style_id' => 'uint',
				'navigation_id' => 'str'
			]
		]);

		if (!$this->filter('style_override', 'bool'))
		{
			$input['node']['style_id'] = 0;
		}

		$data = $node->getDataRelationOrDefault();
		$node->addCascadedSave($data);

		$form->basicEntitySave($node, $input['node']);
		$this->saveTypeData($form, $node, $data);

		return $form;
	}

	protected function saveTypeData(FormAction $form, \XF\Entity\Node $node, \XF\Entity\AbstractNode $data)
	{
	}

	public function actionSave(ParameterBag $params)
	{
		if ($params['node_id'])
		{
			$node = $this->assertNodeExists($params['node_id']);
		}
		else
		{
			/** @var \XF\Entity\Node $node */
			$node = $this->em()->create('XF:Node');
			$node->node_type_id = $this->getNodeTypeId();
		}

		$this->nodeSaveProcess($node)->run();

		return $this->redirect($this->buildLink('nodes') . $this->buildLinkHash($node->node_id));
	}

	protected function nodeDelete(\XF\Entity\Node $node)
	{
		$node->delete();
	}

	public function actionDelete(ParameterBag $params)
	{
		$node = $this->assertNodeExists($params['node_id']);

		if (!$node->preDelete())
		{
			return $this->error($node->getErrors());
		}

		if ($this->isPost())
		{
			$childAction = $this->filter('child_nodes_action', 'str');
			$node->getBehavior('XF:TreeStructured')->setOption('deleteChildAction', $childAction);

			$this->nodeDelete($node);
			return $this->redirect($this->buildLink('nodes'));
		}
		else
		{
			$nodeRepo = $this->getNodeRepo();

			$nodeTree = $nodeRepo->createNodeTree($nodeRepo->getFullNodeList());
			$nodeTree = $nodeTree->filter(function($nodeId) use ($node)
			{
				// Filter out the current node from the node tree.
				return ($nodeId == $node->node_id ? false : true);
			});

			$viewParams = [
				'node' => $node,
				'nodeTree' => $nodeTree,
				$this->getDataParamName() => $node->getDataRelationOrDefault(),
			];
			return $this->view($this->getViewClassPrefix() . '\Delete', $this->getTemplatePrefix() . '_delete', $viewParams);
		}
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Node
	 */
	protected function assertNodeExists($id, $with = null, $phraseKey = null)
	{
		$node = $this->assertRecordExists('XF:Node', $id, $with, $phraseKey);
		if ($node->node_type_id != $this->getNodeTypeId())
		{
			throw $this->exception($this->error(\XF::phrase('requested_node_not_found'), 404));
		}
		return $node;
	}

	/**
	 * @return \XF\Repository\Node
	 */
	protected function getNodeRepo()
	{
		return $this->repository('XF:Node');
	}
}