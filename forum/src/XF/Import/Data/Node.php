<?php

namespace XF\Import\Data;

/**
 * Class Node
 *
 * @package XF\Import\Data
 *
 * COLUMNS
 * @property int|null node_id
 * @property string title
 * @property string|null node_name
 * @property string description
 * @property string node_type_id
 * @property int parent_node_id
 * @property int display_order
 * @property int lft
 * @property int rgt
 * @property int depth
 * @property int style_id
 * @property int effective_style_id
 * @property bool display_in_list
 * @property array breadcrumb_data
 * @property string navigation_id
 * @property string effective_navigation_id
 */
class Node extends AbstractEmulatedData
{
	/**
	 * @var AbstractNode|null
	 */
	protected $typeData;

	public function getImportType()
	{
		return 'node';
	}

	public function getEntityShortName()
	{
		return 'XF:Node';
	}

	public function setType($nodeTypeId, AbstractNode $typeData)
	{
		$this->node_type_id = $nodeTypeId;
		$this->typeData = $typeData;

		return $this;
	}

	protected function preSave($oldId)
	{
		$this->forceNotEmpty('title', $oldId);

		if (!$this->typeData)
		{
			throw new \LogicException("Must provide a node type and data");
		}
	}

	protected function postSave($oldId, $newId)
	{
		$this->typeData->node_id = $newId;
		$this->typeData->save($oldId);

		\XF::runOnce('nodeImport', function()
		{
			/** @var \XF\Service\Node\RebuildNestedSet $service */
			$service = \XF::service('XF:Node\RebuildNestedSet', 'XF:Node', [
				'parentField' => 'parent_node_id'
			]);
			$service->rebuildNestedSetInfo();
		});
	}
}