<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int node_id
 * @property int publish_date
 * @property int modified_date
 * @property int view_count
 * @property bool log_visits
 * @property bool list_siblings
 * @property bool list_children
 * @property string callback_class
 * @property string callback_method
 * @property bool advanced_mode
 *
 * GETTERS
 * @property string|null node_name
 * @property string|null title
 * @property string|null description
 * @property int depth
 *
 * RELATIONS
 * @property \XF\Entity\Template MasterTemplate
 * @property \XF\Entity\Node Node
 */
class Page extends AbstractNode
{
	public function getNodeTemplateRenderer($depth)
	{
		return [
			'template' => 'node_list_page',
			'macro' => $depth <= 2 ? 'depth' . $depth : 'depthN'
		];
	}

	public function getTemplateName()
	{
		return '_page_node.' . $this->node_id;
	}

	/**
	 * @return Template
	 */
	public function getMasterTemplate()
	{
		$template = $this->MasterTemplate;
		if (!$template)
		{
			$template = $this->_em->create('XF:Template');
			$template->title = $this->_getDeferredValue(function() { return $this->getTemplateName(); }, 'save');
			$template->type = 'public';
			$template->style_id = 0;
			$template->addon_id = '';
		}

		return $template;
	}

	protected function _preSave()
	{
		if (
			$this->isChanged(['callback_class', 'callback_method'])
			&& ($this->callback_class || $this->callback_method)
		)
		{
			if (!\XF\Util\Php::validateCallbackPhrased($this->callback_class, $this->callback_method, $error))
			{
				$this->error($error, 'callback_method');
			}
		}
	}

	protected function _postSave()
	{
	}

	protected function _postDelete()
	{
		if ($this->MasterTemplate)
		{
			$this->MasterTemplate->delete();
		}
	}

	public function getNodeTypeApiData($verbosity = self::VERBOSITY_NORMAL, array $options = [])
	{
		$result = parent::getNodeTypeApiData();

		$result->content = $this->app()->templater()->renderTemplate('public:' . $this->getTemplateName());

		return $result;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_page';
		$structure->shortName = 'XF:Page';
		$structure->contentType = 'page';
		$structure->primaryKey = 'node_id';
		$structure->columns = [
			'node_id' => ['type' => self::UINT, 'required' => true],
			'publish_date' => ['type' => self::UINT, 'default' => \XF::$time, 'api' => true],
			'modified_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'view_count' => ['type' => self::UINT, 'default' => 0, 'api' => true],
			'log_visits' => ['type' => self::BOOL, 'default' => false],
			'list_siblings' => ['type' => self::BOOL, 'default' => false],
			'list_children' => ['type' => self::BOOL, 'default' => false],
			'callback_class' => ['type' => self::STR, 'maxLength' => 100, 'default' => ''],
			'callback_method' => ['type' => self::STR, 'maxLength' => 75, 'default' => ''],
			'advanced_mode' => ['type' => self::BOOL, 'default' => false]
		];
		$structure->getters = [];
		$structure->behaviors = [
			'XF:Indexable' => [
				'checkForUpdates' => ['modified_date']
			],
		];
		$structure->relations = [
			'MasterTemplate' => [
				'entity' => 'XF:Template',
				'type' => self::TO_ONE,
				'conditions' => [
					['style_id', '=', 0],
					['type', '=', 'public'],
					['title', '=', '_page_node.', '$node_id']
				]
			]
		];

		static::addDefaultNodeElements($structure);

		return $structure;
	}
}