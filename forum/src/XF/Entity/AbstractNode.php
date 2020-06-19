<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * Class AbstractNode
 *
 * @package XF\Entity
 *
 * COLUMNS
 * @property int node_id
 *
 * GETTERS
 * @property string|null node_name
 * @property string title
 * @property string description
 * @property int depth
 *
 * RELATIONS
 * @property \XF\Entity\Node Node
 */
abstract class AbstractNode extends Entity
{
	abstract public function getNodeTemplateRenderer($depth);

	public function canView(&$error = null)
	{
		return \XF::visitor()->hasNodePermission($this->node_id, 'view');
	}

	public function getNodeListExtras()
	{
		return [];
	}

	/**
	 * @return string|null
	 */
	public function getNodeName()
	{
		return $this->Node ? $this->Node->node_name : null;
	}

	/**
	 * @return string|null
	 */
	public function getTitle()
	{
		return $this->Node ? $this->Node->title : '';
	}

	/**
	 * @return string|null
	 */
	public function getDescription()
	{
		return $this->Node ? $this->Node->description : '';
	}

	/**
	 * @return int
	 */
	public function getDepth()
	{
		return $this->Node ? $this->Node->depth : 0;
	}

	public function getBreadcrumbs($includeSelf = true, $linkType = 'public')
	{
		return $this->Node ? $this->Node->getBreadcrumbs($includeSelf, $linkType) : [];
	}

	public static function getListedWith()
	{
		return [];
	}

	/**
	 * @param int $verbosity
	 * @param array $options
	 *
	 * @return \XF\Api\Result\EntityResult
	 */
	public function getNodeTypeApiData($verbosity = self::VERBOSITY_NORMAL, array $options = [])
	{
		$result = new \XF\Api\Result\EntityResult($this);

		return $result;
	}

	/**
	 * This passes off API data generation to the node handler. To override what extra data is returned for this node,
	 * extend getNodeTypeApiData.
	 *
	 * @param \XF\Api\Result\EntityResult $result
	 * @param int $verbosity
	 * @param array $options
	 *
	 * @return void|\XF\Api\Result\EntityResult
	 */
	protected final function setupApiResultData(
		\XF\Api\Result\EntityResult $result, $verbosity = self::VERBOSITY_NORMAL, array $options = []
	)
	{
		// we expose specific node types as nodes + extra data, rather than vice versa
		if ($this->Node)
		{
			return $this->Node->toApiResult($verbosity, $options);
		}
	}

	protected static function addDefaultNodeElements(Structure $structure)
	{
		$structure->getters['node_name'] = ['getter' => 'getNodeName', 'cache' => false];
		$structure->getters['title'] = ['getter' => 'getTitle', 'cache' => false];
		$structure->getters['description'] = ['getter' => 'getDescription', 'cache' => false];
		$structure->getters['depth'] = ['getter' => 'getDepth', 'cache' => false];

		$structure->relations['Node'] = [
			'entity' => 'XF:Node',
			'type' => self::TO_ONE,
			'conditions' => 'node_id',
			'primary' => true,
			'cascadeDelete' => true
		];

		$structure->defaultWith[] = 'Node';

		if (!isset($structure->withAliases['api']))
		{
			$structure->withAliases['api'] = [];
		}

		$structure->withAliases['api'][] = 'Node.api';
	}
}