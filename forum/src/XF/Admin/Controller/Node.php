<?php

namespace XF\Admin\Controller;

use XF\Mvc\ParameterBag;

class Node extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('node');
	}

	public function actionIndex()
	{
		$nodeRepo = $this->getNodeRepo();
		$nodeTree = $nodeRepo->createNodeTree($nodeRepo->getFullNodeList(null, 'NodeType'));

		/** @var \XF\Repository\Moderator $moderatorRepo */
		$moderatorRepo = $this->repository('XF:Moderator');
		$moderators = $moderatorRepo->findContentModeratorsForList()
			->where('content_type', 'node')
			->fetch()->groupBy('content_id');

		$customPermissions = $this->repository('XF:PermissionEntry')->getContentWithCustomPermissions('node');

		$viewParams = [
			'nodeTree' => $nodeTree,
			'moderators' => $moderators,
			'customPermissions' => $customPermissions
		];
		return $this->view('XF:Node\Listing', 'node_list', $viewParams);
	}

	public function actionSort()
	{
		$nodeRepo = $this->getNodeRepo();
		$nodeTree = $nodeRepo->createNodeTree($nodeRepo->getFullNodeList(null, 'NodeType'));

		if ($this->isPost())
		{
			$sorter = $this->plugin('XF:Sort');
			$sortTree = $sorter->buildSortTree($this->filter('nodes', 'json-array'));
			$sorter->sortTree($sortTree, $nodeTree->getAllData(), 'parent_node_id');

			return $this->redirect($this->buildLink('nodes'));
		}
		else
		{
			$viewParams = [
				'nodeTree' => $nodeTree
			];
			return $this->view('XF:Node\Sort', 'node_sort', $viewParams);
		}
	}

	public function actionEdit(ParameterBag $params)
	{
		$node = $this->assertNodeExists($params['node_id']);
		if (!$node->NodeType)
		{
			return $this->error(\XF::phrase('node_type_for_node_not_found_not_modifiable'), 404);
		}

		return $this->redirect($this->buildLink($node->NodeType->admin_route . '/edit', $node));
	}

	public function actionAdd()
	{
		$nodeTypeId = $this->filter('node_type_id', 'str');
		$parentNodeId = $this->filter('parent_node_id', 'uint');
		if ($nodeTypeId)
		{
			$nodeType = $this->em()->find('XF:NodeType', $nodeTypeId);
			if ($nodeType)
			{
				return $this->redirect($this->buildLink($nodeType->admin_route . '/add', null, ['parent_node_id' => $parentNodeId]), '');
			}
		}

		$nodeTypes = $this->finder('XF:NodeType')->order('node_type_id')->fetch();

		$viewParams = [
			'nodeTypes' => $nodeTypes,
			'parentNodeId' => $parentNodeId
		];
		return $this->view('XF:Node\Add', 'node_add', $viewParams);
	}

	public function actionDelete(ParameterBag $params)
	{
		$node = $this->assertNodeExists($params['node_id']);
		if (!$node->NodeType)
		{
			return $this->error(\XF::phrase('node_type_for_node_not_found_not_modifiable'), 404);
		}

		return $this->redirect($this->buildLink($node->NodeType->admin_route . '/delete', $node), '');
	}

	/**
	 * @return \XF\ControllerPlugin\NodePermission
	 */
	protected function getNodePermissionPlugin()
	{
		/** @var \XF\ControllerPlugin\NodePermission $plugin */
		$plugin = $this->plugin('XF:NodePermission');
		$plugin->setFormatters('XF\Node\Permission%s', 'node_permission_%s');
		$plugin->setRoutePrefix('nodes/permissions');

		return $plugin;
	}

	public function actionPermissions(ParameterBag $params)
	{
		return $this->getNodePermissionPlugin()->actionList($params);
	}

	public function actionPermissionsEdit(ParameterBag $params)
	{
		return $this->getNodePermissionPlugin()->actionEdit($params);
	}

	public function actionPermissionsSave(ParameterBag $params)
	{
		return $this->getNodePermissionPlugin()->actionSave($params);
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
		if (!is_array($with))
		{
			$with = $with ? [$with] : [];
		}
		$with[] = 'NodeType';

		return $this->assertRecordExists('XF:Node', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Node
	 */
	protected function getNodeRepo()
	{
		return $this->repository('XF:Node');
	}
}