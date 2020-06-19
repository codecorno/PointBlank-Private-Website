<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null property_id
 * @property int style_id
 * @property string property_name
 * @property string group_name
 * @property string title_
 * @property string description_
 * @property string property_type
 * @property array css_components
 * @property string value_type
 * @property string value_parameters
 * @property string depends_on
 * @property string value_group
 * @property array|null property_value
 * @property int display_order
 * @property string addon_id
 *
 * GETTERS
 * @property Style Style
 * @property \XF\Phrase title
 * @property \XF\Phrase description
 * @property \XF\Phrase|string master_title
 * @property \XF\Phrase|\XF\PreEscaped|string master_description
 * @property mixed value_group_title
 *
 * RELATIONS
 * @property \XF\Entity\AddOn AddOn
 * @property \XF\Entity\Style Style_
 * @property \XF\Entity\StylePropertyGroup Group
 * @property \XF\Entity\Phrase MasterTitle
 * @property \XF\Entity\Phrase MasterDescription
 */
class StyleProperty extends Entity
{
	public function isValidCssComponent($component)
	{
		if ($this->property_type != 'css')
		{
			return false;
		}

		if (is_array($component))
		{
			foreach ($component AS $c)
			{
				if (in_array($c, $this->css_components))
				{
					return true;
				}
			}

			return false;
		}
		else
		{
			return in_array($component, $this->css_components);
		}
	}

	public function getCssPropertyValue($cssProperty, $default = '')
	{
		if ($this->property_type != 'css')
		{
			return $default;
		}

		$value = $this->property_value;
		if (!is_array($value) || !isset($value[$cssProperty]))
		{
			return $default;
		}

		return $value[$cssProperty];
	}

	public function getPropertyCopyInStyle(Style $style)
	{
		$copy = $this->em()->create('XF:StyleProperty');
		$copy->style_id = $style->style_id;

		foreach ($this->getChildPushedFields() AS $field)
		{
			$copy->set($field, $this->getValue($field));
		}

		$copy->property_value = $this->property_value;

		if ($style->style_id)
		{
			$copy->addon_id = '';
		}
		else
		{
			$copy->addon_id = $this->addon_id;
		}

		return $copy;
	}

	public function updatePropertyValue($newValue)
	{
		if ($this->property_type == 'css')
		{
			if (!is_array($newValue))
			{
				$newValue = [];
			}

			foreach ($newValue AS $k => &$v)
			{
				$v = trim($v);
				if (!strlen($v))
				{
					unset($newValue[$k]);
				}
			}
		}
		else
		{
			$options = $this->getValueOptions();
			if (!$this->getPropertyRepo()->castAndValidatePropertyValue($this->value_type, $options, $newValue, $error))
			{
				$newValue = $this->getPropertyRepo()->getDefaultPropertyValue($this->value_type, $options);
			}
		}

		if ($newValue !== $this->property_value)
		{
			$this->property_value = $newValue;
			return true;
		}
		else
		{
			return false;
		}
	}

	public function getValueOptions()
	{
		if ($this->property_type == 'value')
		{
			return $this->app()->stringFormatter()->createKeyValueSetFromString($this->value_parameters);
		}

		return [];
	}

	/**
	 * @return Style
	 */
	public function getStyle()
	{
		if ($this->style_id == 0)
		{
			return $this->getStyleRepo()->getMasterStyle();
		}
		else
		{
			return $this->getRelation('Style');
		}
	}

	/**
	 * @return \XF\Phrase
	 */
	public function getTitle()
	{
		$phrase = \XF::phrase($this->getPhraseName(true));
		$phrase->fallback($this->getValue('title'));

		return $phrase;
	}

	/**
	 * @return \XF\Phrase
	 */
	public function getDescription()
	{
		$phrase = \XF::phrase($this->getPhraseName(false));
		$phrase->fallback($this->getValue('description'), true);

		return $phrase;
	}

	/**
	 * @return \XF\Phrase|string
	 */
	public function getMasterTitle()
	{
		if ($this->exists() && $this->style_id == 0 && $this->MasterTitle)
		{
			return $this->MasterTitle->phrase_text;
		}

		return $this->getValue('title');
	}

	/**
	 * @return \XF\Phrase|\XF\PreEscaped|string
	 */
	public function getMasterDescription()
	{
		if ($this->exists() && $this->style_id == 0 && $this->MasterDescription)
		{
			return $this->MasterDescription->phrase_text;
		}

		return $this->getValue('description');
	}

	public function getValueGroupTitle()
	{
		$formatter = $this->app()->stringFormatter();
		$valueGroup = $this->value_group;

		if (!$valueGroup)
		{
			return '';
		}

		$phraseName = "style_prop_vgroup.{$valueGroup}";

		$phrase = \XF::phrase($phraseName);
		$phrase->fallback(utf8_ucfirst($formatter->fromCamelCase($valueGroup, ' ')));

		return $phrase;
	}

	public function getPhraseName($title, $existing = false)
	{
		$name = $existing ? $this->getExistingValue('property_name') : $this->getValue('property_name');
		return 'style_prop' . ($title ? '' : '_desc') . '.' . $name;
	}

	/**
	 * @param bool $title
	 *
	 * @return null|Phrase
	 */
	public function getMasterPhrase($title)
	{
		if ($this->style_id != 0)
		{
			return null;
		}

		$phrase = $title ? $this->MasterTitle : $this->MasterDescription;
		if (!$phrase)
		{
			$phrase = $this->_em->create('XF:Phrase');
			$phrase->addon_id = $this->_getDeferredValue(function() { return $this->addon_id; });
			$phrase->title = $this->_getDeferredValue(function() use ($title) { return $this->getPhraseName($title); });
			$phrase->language_id = 0;
		}

		return $phrase;
	}

	protected function _preSave()
	{
		if ($this->isUpdate() && $this->isChanged('style_id'))
		{
			throw new \LogicException("Cannot update the style of existing properties");
		}

		if ($this->isChanged('property_name'))
		{
			$existingProperty = $this->em()->findOne('XF:StyleProperty', [
				'style_id' => $this->style_id,
				'property_name' => $this->property_name
			]);
			if ($existingProperty)
			{
				$this->error(\XF::phrase('style_property_definitions_must_be_unique_per_style'), 'property_name');
			}
		}

		if ($this->isChanged('style_id') && $this->style_id != 0)
		{
			$this->addon_id = '';
		}

		switch ($this->property_type)
		{
			case 'css':
				$this->value_type = '';
				$this->value_parameters = '';

				if (!$this->css_components)
				{
					$this->error(\XF::phrase('css_style_property_must_have_at_least_one_css_component'), 'css_components');
				}

				if ($this->isUpdate() && $this->isChanged('property_type'))
				{
					// wasn't a CSS type, have to reset
					$this->property_value = [];
				}

				if (!is_array($this->property_value))
				{
					$this->property_value = [];
				}
				break;

			case 'value':
				$this->css_components = [];

				if (!$this->value_type)
				{
					$this->error(\XF::phrase('value_style_properties_must_have_specific_type'), 'value_type');
				}

				if ($this->value_type == 'template' && $this->isChanged(['value_type', 'value_parameters']))
				{
					$options = $this->getValueOptions();
					if (empty($options['template']) || empty($options['type']))
					{
						$this->error(\XF::phrase('template_style_properties_must_define_template_and_type_via_value'), 'value_parameters');
					}
				}

				if ($this->isChanged(['property_type', 'value_type', 'value_parameters']))
				{
					$options = $this->getValueOptions();
					$propertyRepo = $this->getPropertyRepo();

					if (
						($this->isUpdate() && $this->isChanged('property_type'))
						|| ($this->isInsert() && !$this->isChanged('property_value'))
					)
					{
						$this->property_value = $propertyRepo->getDefaultPropertyValue($this->value_type, $options);
					}
					else
					{
						$newValue = $this->property_value;
						if (!$propertyRepo->castAndValidatePropertyValue($this->value_type, $options, $newValue))
						{
							$newValue = $propertyRepo->getDefaultPropertyValue($this->value_type, $options);
						}
						$this->property_value = $newValue;
					}
				}
				break;

			// other types are invalid and will error
		}
	}

	protected function _postSave()
	{
		$writeDevOutput = $this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output');

		if ($this->style_id == 0 && $this->getOption('update_phrase'))
		{
			$title = $this->getMasterPhrase(true);
			$title->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);
			$title->addon_id = $this->addon_id;
			$title->phrase_text = $this->getValue('title');
			$title->saveIfChanged();

			$description = $this->getMasterPhrase(false);
			$description->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);
			$description->addon_id = $this->addon_id;
			$description->phrase_text = $this->getValue('description');
			$description->saveIfChanged();

			if ($this->isUpdate() && $this->isChanged('property_name'))
			{
				$existingMasterTitle = $this->getExistingRelation('MasterTitle');
				if ($existingMasterTitle)
				{
					$existingMasterTitle->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);
					$existingMasterTitle->delete();
				}

				$existingMasterDescription = $this->getExistingRelation('MasterDescription');
				if ($existingMasterDescription)
				{
					$existingMasterDescription->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);
					$existingMasterDescription->delete();
				}
			}
		}
		// preSave prevents the style ID from being changed, so don't need to handle that

		// push changes to the structure of this field to children
		$pushFields = $this->getChildPushedFields();
		if ($this->isUpdate() && $this->isChanged($pushFields))
		{
			$childProperties = $this->getPropertyRepo()->getPropertiesDerivedFrom($this->property_id);
			foreach ($childProperties AS $childProperty)
			{
				// rebuilds will be done here if needed
				$childProperty->setOption('rebuild_map', false);
				$childProperty->setOption('rebuild_style', false);
				foreach ($pushFields AS $pushField)
				{
					$childProperty->set($pushField, $this->getValue($pushField));
				}
				$childProperty->saveIfChanged($null, true, false);

				// TODO: if changing property_type or value_type, should we just delete children?
			}
		}

		if ($this->isChanged('property_name') && $this->getOption('rebuild_map'))
		{
			$propertyRebuilder = $this->getPropertyRebuildService();
			$propertyRebuilder->rebuildPropertyMapForProperty($this->property_name);

			if ($this->isUpdate())
			{
				$propertyRebuilder->rebuildPropertyMapForProperty($this->getExistingValue('property_name'));
			}
		}

		if ($this->getOption('rebuild_style')
			&& $this->isChanged(['property_value', 'property_name', 'property_type', 'css_components'])
		)
		{
			$this->rebuildPropertyStyleCache();
			// the style ID can't change, so don't need to worry about that
		}
	}

	protected function _postDelete()
	{
		$writeDevOutput = $this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output');

		if ($this->style_id == 0)
		{
			$existingMasterTitle = $this->MasterTitle;
			if ($existingMasterTitle)
			{
				$existingMasterTitle->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);
				$existingMasterTitle->delete();
			}

			$existingMasterDescription = $this->MasterDescription;
			if ($existingMasterDescription)
			{
				$existingMasterDescription->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);
				$existingMasterDescription->delete();
			}
		}

		if ($this->getOption('force_child_delete'))
		{
			$deleteChildren = true;
		}
		else
		{
			$parentPropertyId = $this->db()->fetchOne("
				SELECT parent_property_id
				FROM xf_style_property_map
				WHERE style_id = ?
					AND property_name = ?
			", [$this->style_id, $this->property_name]);

			$deleteChildren = !$parentPropertyId;
		}
		if ($deleteChildren)
		{
			$writeDesignerOutput = $this->getBehavior('XF:DesignerOutputWritable')->getOption('write_designer_output');

			// this is the root version of the property. It doesn't make sense to leave it in children.
			$childProperties = $this->getPropertyRepo()->getPropertiesDerivedFrom($this->property_id);
			foreach ($childProperties AS $childProperty)
			{
				// rebuilds will be done here if needed
				$childProperty->setOption('rebuild_map', false);
				$childProperty->setOption('rebuild_style', false);
				// this won't register as a root version as the map isn't updated yet, but we know it needs to be removed
				$childProperty->setOption('force_child_delete', true);

				$childProperty->getBehavior('XF:DesignerOutputWritable')->setOption('write_designer_output', $writeDesignerOutput);

				$childProperty->delete(true, false);
			}
		}

		if ($this->getOption('rebuild_map'))
		{
			$this->getPropertyRebuildService()->rebuildPropertyMapForProperty($this->property_name);
		}

		if ($this->getOption('rebuild_style'))
		{
			$this->rebuildPropertyStyleCache();
		}
	}

	protected function rebuildPropertyStyleCache()
	{
		\XF::runOnce('stylePropertyCacheRebuild' . $this->style_id, function()
		{
			$this->getPropertyRebuildService()->rebuildPropertyStyleCacheForStyle($this->style_id);
		});
	}

	protected function getChildPushedFields()
	{
		return [
			'property_name',
			'group_name',
			'title',
			'description',
			'property_type',
			'css_components',
			'value_type',
			'value_parameters',
			'depends_on',
			'value_group',
			'display_order'
		];
	}

	protected function _setupDefaults()
	{
		/** @var \XF\Repository\AddOn $addOnRepo */
		$addOnRepo = $this->_em->getRepository('XF:AddOn');
		$this->addon_id = $addOnRepo->getDefaultAddOnId();
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_style_property';
		$structure->shortName = 'XF:StyleProperty';
		$structure->primaryKey = 'property_id';
		$structure->columns = [
			'property_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'style_id' => ['type' => self::UINT, 'required' => true],
			'property_name' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_property_name',
				'match' => 'alphanumeric'
			],
			'group_name' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_group_name'
			],
			'title' => ['type' => self::STR, 'maxLength' => 100,
				'required' => 'please_enter_valid_title'
			],
			'description' => ['type' => self::STR, 'maxLength' => 255, 'default' => ''],
			'property_type' => ['type' => self::STR, 'required' => true,
				'allowedValues' => ['value', 'css']
			],
			'css_components' => ['type' => self::LIST_COMMA, 'default' => []],
			'value_type' => ['type' => self::STR, 'default' => '',
				'allowedValues' => ['', 'string', 'color', 'unit', 'number', 'boolean', 'radio', 'select', 'template']
			],
			'value_parameters' => ['type' => self::STR, 'default' => ''],
			'depends_on' => ['type' => self::STR, 'maxLength' => 50, 'default' => '',
				'match' => 'alphanumeric'
			],
			'value_group' => ['type' => self::STR, 'maxLength' => 50, 'default' => '',
				'match' => 'alphanumeric'
			],
			'property_value' => ['type' => self::JSON, 'default' => ''],
			'display_order' => ['type' => self::UINT, 'forced' => true, 'default' => 0],
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => ''],
		];
		$structure->behaviors = [
			'XF:DevOutputWritable' => [],
			'XF:DesignerOutputWritable' => []
		];
		$structure->getters = [
			'Style' => true,
			'title' => true,
			'description' => true,
			'master_title' => true,
			'master_description' => true,
			'value_group_title' => true,
		];
		$structure->relations = [
			'AddOn' => [
				'entity' => 'XF:AddOn',
				'type' => self::TO_ONE,
				'conditions' => 'addon_id',
				'primary' => true
			],
			'Style' => [
				'type' => self::TO_ONE,
				'entity' => 'XF:Style',
				'conditions' => 'style_id',
				'primary' => true
			],
			'Group' => [
				'type' => self::TO_ONE,
				'entity' => 'XF:StylePropertyGroup',
				'conditions' => 'group_name'
			],
			'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'style_prop.', '$property_name']
				]
			],
			'MasterDescription' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'style_prop_desc.', '$property_name']
				]
			]
		];
		$structure->options = [
			'update_phrase' => true,
			'rebuild_map' => true,
			'rebuild_style' => true,
			'force_child_delete' => false
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Style
	 */
	protected function getStyleRepo()
	{
		return $this->repository('XF:Style');
	}

	/**
	 * @return \XF\Repository\StyleProperty
	 */
	protected function getPropertyRepo()
	{
		return $this->repository('XF:StyleProperty');
	}

	/**
	 * @return \XF\Service\StyleProperty\Rebuild
	 */
	protected function getPropertyRebuildService()
	{
		return $this->app()->service('XF:StyleProperty\Rebuild');
	}
}