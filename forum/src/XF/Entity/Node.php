<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
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
 *
 * GETTERS
 * @property AbstractNode|null Data
 * @property array|null node_type_info
 * @property \XF\NodeType\AbstractHandler|null handler
 *
 * RELATIONS
 * @property \XF\Entity\Node Parent
 * @property \XF\Entity\NodeType NodeType
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\PermissionCacheContent[] Permissions
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\ModeratorContent[] Moderators
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\BookmarkItem[] Bookmarks
 */
class Node extends Entity
{
	use BookmarkTrait;

	public function canView(&$error = null)
	{
		/** @var AbstractNode $data */
		$data = $this->Data;
		if (!$data)
		{
			return false;
		}

		return $data->canView($error);
	}

	protected function canBookmarkContent(&$error = null)
	{
		return ($this->node_type_id == 'Page' && $this->canView($error));
	}

	public function hasChildren()
	{
		return ($this->rgt - $this->lft) > 1;
	}

	public function getNodeListExtras()
	{
		/** @var AbstractNode $data */
		$data = $this->Data;
		if (!$data)
		{
			return [];
		}

		return $data->getNodeListExtras();
	}

	public function getRoute($linkType = 'public')
	{
		$nodeType = $this->node_type_info;
		if (!$nodeType)
		{
			return '';
		}

		return $linkType == 'public' ? $nodeType['public_route'] : $nodeType['admin_route'];
	}

	public function getBreadcrumbs($includeSelf = true, $linkType = 'public')
	{
		/** @var \XF\Mvc\Router $router */
		$router = $this->app()->container('router.' . $linkType);
		$nodeTypes = $this->app()->container('nodeTypes');

		$output = [];
		if ($this->breadcrumb_data)
		{
			foreach ($this->breadcrumb_data AS $crumb)
			{
				if (!isset($nodeTypes[$crumb['node_type_id']]))
				{
					continue;
				}

				$nodeType = $nodeTypes[$crumb['node_type_id']];
				$route = $linkType == 'public' ? $nodeType['public_route'] : $nodeType['admin_route'];

				$output[] = [
					'value' => $crumb['title'],
					'href' => $router->buildLink($route, $crumb),
					'node_id' => $crumb['node_id']
				];
			}
		}

		$nodeType = $this->node_type_info;

		if ($includeSelf && $nodeType)
		{
			$route = $linkType == 'public' ? $nodeType['public_route'] : $nodeType['admin_route'];

			$output[] = [
				'value' => $this->title,
				'href' => $router->buildLink($route, $this),
				'node_id' => $this->node_id
			];
		}

		return $output;
	}

	public function getNodeTemplateRenderer($depth)
	{
		/** @var AbstractNode $data */
		$data = $this->Data;
		if (!$data)
		{
			return null;
		}

		return $data->getNodeTemplateRenderer($depth);
	}

	/**
	 * @return AbstractNode|null
	 */
	public function getData()
	{
		if (!$this->node_id)
		{
			return null;
		}

		$dataEntity = $this->getDataEntityName();
		if (!$dataEntity)
		{
			return null; // no node type record
		}

		return $this->_em->find($dataEntity, $this->node_id);
	}

	public function getDataEntityName()
	{
		$nodeType = $this->node_type_info;
		return $nodeType ? $nodeType['entity_identifier'] : null;
	}

	/**
	 * @param bool $cascadeSave
	 *
	 * @return AbstractNode
	 */
	public function getDataRelationOrDefault($cascadeSave = true)
	{
		$data = $this->getData();
		if (!$data)
		{
			$dataEntity = $this->getDataEntityName();
			if (!$dataEntity)
			{
				throw new \LogicException("No node type for '$this->node_type_id' could be found");
			}

			$data = $this->_em->create($dataEntity);
			$data->node_id = $this->_em->getDeferredValue(
				function() { return $this->getValue('node_id'); },
				'save'
			);

			$this->_getterCache['Data'] = $data;
		}

		if ($cascadeSave)
		{
			$this->addCascadedSave($data);
		}

		return $data;
	}

	/**
	 * @return \XF\NodeType\AbstractHandler|null
	 */
	public function getHandler()
	{
		$nodeType = $this->node_type_info;
		if (!$nodeType || !$nodeType['handler_class'])
		{
			return null;
		}

		$class = \XF::stringToClass($nodeType['handler_class'], '%s\NodeType\%s');
		$class = \XF::extendClass($class);

		return new $class($this->node_type_id, $nodeType);
	}

	/**
	 * @return array|null
	 */
	public function getNodeTypeInfo()
	{
		$nodeTypes = $this->app()->container('nodeTypes');
		$nodeTypeId = $this->node_type_id;

		return isset($nodeTypes[$nodeTypeId]) ? $nodeTypes[$nodeTypeId] : null;
	}

	// *********** VERIFIERS *************

	protected function verifyNodeName(&$name)
	{
		if (!$name)
		{
			if ($this->node_type_id == 'Page')
			{
				$this->error(\XF::phrase('please_enter_valid_url_portion'));
				return false;
			}

			$name = null;
			return true;
		}

		if ($name === strval(intval($name)) || $name == '-')
		{
			$this->error(\XF::phrase('node_names_contain_more_numbers_hyphen'), 'node_name');
			return false;
		}

		return true;
	}

	protected function verifyNodeTypeId(&$nodeTypeId)
	{
		$nodeTypes = $this->app()->container('nodeTypes');
		if (!isset($nodeTypes[$nodeTypeId]))
		{
			$this->error(\XF::phrase('please_choose_valid_node_type'), 'node_type_id');
			return false;
		}

		return true;
	}

	// *********** LIFE CYCLE **************

	protected function _preSave()
	{
		if ($this->isUpdate() && $this->isChanged('node_type_id'))
		{
			throw new \LogicException("Node types can't be changed");
		}
	}

	protected function _preDelete()
	{
		/** @var \XF\Mvc\Entity\Entity $data */
		$data = $this->Data;
		if ($data)
		{
			if (!$data->preDelete())
			{
				foreach ($data->getErrors() AS $key => $error)
				{
					$this->error($error, is_int($key) ? null : $key, false);
				}
			}
		}
	}

	protected function _postDelete()
	{
		/** @var \XF\Mvc\Entity\Entity $data */
		$data = $this->Data;
		if ($data)
		{
			$data->delete();
		}

		if ($this->Moderators)
		{
			/** @var \XF\Entity\ModeratorContent $moderator */
			foreach ($this->Moderators AS $moderator)
			{
				$moderator->delete();
			}
		}
	}

	/**
	 * @param \XF\Api\Result\EntityResult $result
	 * @param int $verbosity
	 * @param array $options
	 *
	 * @api-out array $breadcrumbs A list of breadcrumbs for this node, including the node_id, title, and node_type_id
	 * @api-out object $type_data Data related to the specific node type this represents. Contents will vary significantly.
	 */
	protected function setupApiResultData(
		\XF\Api\Result\EntityResult $result, $verbosity = self::VERBOSITY_NORMAL, array $options = []
	)
	{
		$breadcrumbs = [];
		if ($this->breadcrumb_data)
		{
			foreach ($this->breadcrumb_data AS $breadcrumb)
			{
				$breadcrumbs[] = [
					'node_id' => $breadcrumb['node_id'],
					'title' => $breadcrumb['title'],
					'node_type_id' => $breadcrumb['node_type_id']
				];
			}
		}

		$result->breadcrumbs = $breadcrumbs;

		$typeData = $this->Data ? $this->Data->getNodeTypeApiData($verbosity, $options) : (object)[];
		$result->type_data = $typeData;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_node';
		$structure->shortName = 'XF:Node';
		$structure->primaryKey = 'node_id';
		$structure->contentType = 'node';
		$structure->columns = [
			'node_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'title' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_title', 'api' => true
			],
			'node_name' => ['type' => self::STR, 'maxLength' => 50, 'nullable' => true, 'default' => null,
				'unique' => 'node_names_must_be_unique',
				'match' => 'alphanumeric_hyphen',
				'api' => true
			],
			'description' => ['type' => self::STR, 'default' => '', 'api' => true],
			'node_type_id' => ['type' => self::BINARY, 'maxLength' => 25, 'required' => true, 'api' => true],
			'parent_node_id' => ['type' => self::UINT, 'required' => true, 'default' => 0, 'api' => true],
			'display_order' => ['type' => self::UINT, 'default' => 1, 'api' => true],
			'lft' => ['type' => self::UINT],
			'rgt' => ['type' => self::UINT],
			'depth' => ['type' => self::UINT],
			'style_id' => ['type' => self::UINT, 'default' => 0],
			'effective_style_id' => ['type' => self::UINT, 'default' => 0],
			'display_in_list' => ['type' => self::BOOL, 'default' => true, 'api' => true],
			'breadcrumb_data' => ['type' => self::JSON_ARRAY, 'default' => []],
			'navigation_id' => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
			'effective_navigation_id' => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
		];
		$structure->getters = [
			'Data' => true,
			'node_type_info' => true,
			'handler' => true
		];
		$structure->behaviors = [
			'XF:TreeStructured' => [
				'parentField' => 'parent_node_id',
				'permissionContentType' => 'node',
				'rebuildExtraFields' => ['style_id', 'navigation_id', 'node_name'],
				'rebuildService' => 'XF:Node\RebuildNestedSet'
			]
		];
		$structure->relations = [
			'Parent' => [
				'entity' => 'XF:Node',
				'type' => self::TO_ONE,
				'conditions' => [
					['node_id', '=', '$parent_node_id']
				],
				'primary' => true
			],
			'NodeType' => [
				'entity' => 'XF:NodeType',
				'type' => self::TO_ONE,
				'conditions' => 'node_type_id',
				'primary' => true
			],
			'Permissions' => [
				'entity' => 'XF:PermissionCacheContent',
				'type' => self::TO_MANY,
				'conditions' => [
					['content_type', '=', 'node'],
					['content_id', '=', '$node_id']
				],
				'key' => 'permission_combination_id',
				'proxy' => true
			],
			'Moderators' => [
				'entity' => 'XF:ModeratorContent',
				'type' => self::TO_MANY,
				'conditions' => [
					['content_type', '=', 'node'],
					['content_id', '=', '$node_id']
				]
			]
		];
		$structure->withAliases = [
			'api' => [
				function()
				{
					return 'Permissions|' . \XF::visitor()->permission_combination_id;
				}
			]
		];

		static::addBookmarkableStructureElements($structure);

		return $structure;
	}

	/**
	 * @return \XF\Repository\Node
	 */
	protected function getNodeRepo()
	{
		return $this->repository('XF:Node');
	}
}