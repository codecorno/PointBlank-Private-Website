<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;
use XF\Repository;

/**
 * COLUMNS
 * @property int|null style_id
 * @property int parent_id
 * @property array parent_list
 * @property string title
 * @property string description
 * @property array properties
 * @property int last_modified_date
 * @property bool user_selectable
 * @property string|null designer_mode
 *
 * RELATIONS
 * @property \XF\Entity\Style Parent
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\Template[] Templates
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\StyleProperty[] Properties
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\StylePropertyGroup[] PropertyGroups
 */
class Style extends Entity
{
	public function canEdit()
	{
		if (!$this->style_id && !\XF::$developmentMode)
		{
			return false;
		}

		return true;
	}

	public function canEditStylePropertyDefinitions()
	{
		return \XF::$developmentMode;
	}

	protected function verifyParentId($parentId)
	{
		if ($this->isUpdate() && $parentId)
		{
			$parent = $this->_em->find('XF:Style', $parentId);
			if (!$parent || in_array($this->style_id, $parent->parent_list))
			{
				$this->error(\XF::phrase('please_select_valid_parent_style'), 'parent_id');
				return false;
			}
		}

		return true;
	}

	protected function verifyUserSelectable($value)
	{
		if (!$value)
		{
			$defaultStyle = $this->app()->options()->defaultStyleId;
			if ($this->style_id == $defaultStyle)
			{
				$this->error(\XF::phrase('it_is_not_possible_to_prevent_users_selecting_the_default_style'), 'user_selectable');
				return false;
			}
		}

		return true;
	}

	protected function rebuildStyleCache()
	{
		$repo = $this->getStyleRepo();

		\XF::runOnce('styleCacheRebuild', function() use ($repo)
		{
			$repo->rebuildStyleCache();
		});
	}

	protected function triggerFullRebuild()
	{
		$this->getStyleRepo()->triggerStyleDataRebuild();
	}

	protected function _preSave()
	{
		if ($this->isChanged(['properties', 'parent_id]']))
		{
			$this->last_modified_date = time();
		}

		if ($this->isChanged('designer_mode')
			&& $this->getExistingValue('designer_mode')
			&& $this->getValue('designer_mode') !== null
		)
		{
			$this->error(\XF::phrase('once_enabled_it_is_not_possible_to_change_designer_mode_id_use_cli_disable_enable'));
		}
	}

	protected function _postSave()
	{
		if ($this->isChanged('parent_id'))
		{
			$this->triggerFullRebuild();
		}

		$this->rebuildStyleCache();
	}

	protected function _preDelete()
	{
		$styleCount = $this->finder('XF:Style')->total();
		if ($styleCount <= 1)
		{
			$this->error(\XF::phrase('it_is_not_possible_to_remove_last_style'));
		}

		if ($this->style_id == $this->app()->options()->defaultStyleId)
		{
			$this->error(\XF::phrase('it_is_not_possible_to_remove_default_style'));
		}
	}

	protected function _postDelete()
	{
		$id = $this->style_id;
		$db = $this->db();

		$db->delete('xf_template_map', 'style_id = ?', $id);
		$db->delete('xf_style_property_map', 'style_id = ?', $id);

		$hasChildren = (bool)$db->update('xf_style', ['parent_id' => $this->parent_id], 'parent_id = ?', $id);

		$db->update('xf_node', [
			'style_id' => 0,
			'effective_style_id' => 0
		], "style_id = ? OR effective_style_id = ?", [$id, $id]);

		$db->update('xf_user', ['style_id' => 0], "style_id = ?", $id);

		foreach ($this->Templates AS $template)
		{
			/** @var \XF\Entity\Template $template */
			$template->setOption('recompile', false);
			$template->setOption('rebuild_map', false);
			$template->getBehavior('XF:DesignerOutputWritable')->setOption('write_designer_output', false);
			$template->delete();
		}

		foreach ($this->Properties AS $property)
		{
			/** @var \XF\Entity\StyleProperty $property */
			$property->setOption('rebuild_map', false);
			$property->setOption('rebuild_style', false);
			$property->getBehavior('XF:DesignerOutputWritable')->setOption('write_designer_output', false);
			$property->delete();
		}

		foreach ($this->PropertyGroups AS $propertyGroup)
		{
			/** @var \XF\Entity\StylePropertyGroup $propertyGroup */
			$propertyGroup->getBehavior('XF:DesignerOutputWritable')->setOption('write_designer_output', false);
			$propertyGroup->delete();
		}

		$this->deleteCompiledTemplates();

		if ($hasChildren)
		{
			$this->triggerFullRebuild();
		}

		$this->rebuildStyleCache();
	}

	protected function deleteCompiledTemplates()
	{
		$path = 'code-cache://templates';
		$styleDir = 's' . $this->style_id;
		$fs = $this->app()->fs();

		foreach ($fs->listContents($path, false) AS $child)
		{
			if ($child['type'] != 'dir')
			{
				continue;
			}

			$stylePath = "$path/$child[basename]/$styleDir";
			if (!$fs->has($stylePath))
			{
				continue;
			}

			try
			{
				$fs->deleteDir($stylePath);
			}
			catch (\Exception $e) {}
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_style';
		$structure->shortName = 'XF:Style';
		$structure->primaryKey = 'style_id';
		$structure->columns = [
			'style_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'parent_id' => ['type' => self::UINT, 'default' => 0],
			'parent_list' => ['type' => self::LIST_COMMA, 'maxLength' => 100, 'default' => []],
			'title' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_title'
			],
			'description' => ['type' => self::STR, 'maxLength' => 100, 'default' => ''],
			'properties' => ['type' => self::JSON_ARRAY, 'default' => []],
			'last_modified_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'user_selectable' => ['type' => self::BOOL, 'default' => true],
			'designer_mode' => ['type' => self::STR, 'maxLength' => 50, 'nullable' => true, 'default' => null,
				'unique' => 'style_designer_mode_ids_must_be_unique',
				'match' => [
					'#^$|^[a-z][a-z0-9_-]*$#i',
					'please_enter_valid_designer_mode_id_using_rules'
				]
			]
		];
		$structure->relations = [
			'Parent' => [
				'entity' => 'XF:Style',
				'type' => self::TO_ONE,
				'conditions' => [
					['style_id', '=', '$parent_id']
				],
				'primary' => true
			],
			'Templates' => [
				'entity' => 'XF:Template',
				'type' => self::TO_MANY,
				'conditions' => 'style_id'
			],
			'Properties' => [
				'entity' => 'XF:StyleProperty',
				'type' => self::TO_MANY,
				'conditions' => 'style_id'
			],
			'PropertyGroups' => [
				'entity' => 'XF:StylePropertyGroup',
				'type' => self::TO_MANY,
				'conditions' => 'style_id'
			]
		];

		return $structure;
	}

	/**
	 * @return Repository\Style
	 */
	protected function getStyleRepo()
	{
		return $this->_em->getRepository('XF:Style');
	}
}