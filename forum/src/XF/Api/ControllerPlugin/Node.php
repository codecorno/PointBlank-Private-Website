<?php

namespace XF\Api\ControllerPlugin;

use XF\Mvc\FormAction;

class Node extends AbstractPlugin
{
	/**
	 * @return array
	 *
	 * @api-in str $node[title]
	 * @api-in str $node[node_name]
	 * @api-in str $node[description]
	 * @api-in int $node[parent_node_id]
	 * @api-in int $node[display_order]
	 * @api-int bool $node[display_in_list]
	 */
	public function getBaseNodeInput()
	{
		$input = $this->filter([
			'node' => [
				'title' => '?str',
				'node_name' => '?str',
				'description' => '?str',
				'parent_node_id' => '?uint',
				'display_order' => '?uint',
				'display_in_list' => '?bool'
			]
		]);

		return \XF\Util\Arr::filterNull($input['node']);
	}

	/**
	 * @return array
	 *
	 * @api-in array $type_data Type-specific node data. The available options will vary based on the node type involved.
	 */
	public function getNodeTypeInput()
	{
		return $this->filter('type_data', 'array');
	}

	public function setupNodeSaveFromInput(FormAction $form, \XF\Entity\Node $node, array $nodeInput, array $typeInput)
	{
		$nodeInput = \XF\Util\Arr::filterNull($nodeInput);
		$form->basicEntitySave($node, $nodeInput);

		$nodeHandler = $node->handler;
		if ($nodeHandler)
		{
			$data = $node->getDataRelationOrDefault();
			if ($typeInput)
			{
				$dataFilterer = $this->request->getNewArrayFilterer($typeInput);
				$nodeHandler->setupApiTypeDataEdit($node, $data, $dataFilterer, $form);
			}
		}
		else
		{
			$form->logError(\XF::phrase('this_node_type_does_not_support_api_manipulation'));
		}

		return $form;
	}

	/**
	 * @param \XF\Entity\Node $node
	 *
	 * @return FormAction
	 *
	 * @api-see self::getBaseNodeInput()
	 * @api-see self::getNodeTypeInput()
	 */
	public function setupNodeSave(\XF\Entity\Node $node)
	{
		$form = $this->formAction();
		$nodeInput = $this->getBaseNodeInput();
		$typeInput = $this->getNodeTypeInput();

		return $this->setupNodeSaveFromInput($form, $node, $nodeInput, $typeInput);
	}
}