<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null property_group_id
 * @property string group_name
 * @property int style_id
 * @property string title_
 * @property string description_
 * @property int display_order
 * @property string addon_id
 *
 * GETTERS
 * @property Style Style
 * @property \XF\Phrase|string title
 * @property \XF\Phrase|string description
 * @property \XF\Phrase|string master_title
 * @property \XF\Phrase|string master_description
 *
 * RELATIONS
 * @property \XF\Entity\AddOn AddOn
 * @property \XF\Entity\Style Style_
 * @property \XF\Entity\Phrase MasterTitle
 * @property \XF\Entity\Phrase MasterDescription
 */
class StylePropertyGroup extends Entity
{
	public function canEdit()
	{
		$style = $this->Style;
		return (!$style || $style->canEdit());
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
	 * @return \XF\Phrase|string
	 */
	public function getTitle()
	{
		if ($this->style_id == 0)
		{
			return \XF::phrase($this->getPhraseName(true));
		}
		else
		{
			return $this->getValue('title');
		}
	}

	/**
	 * @return \XF\Phrase|string
	 */
	public function getDescription()
	{
		if ($this->style_id == 0)
		{
			return \XF::phrase($this->getPhraseName(false));
		}
		else
		{
			return $this->getValue('description');
		}
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
	 * @return \XF\Phrase|string
	 */
	public function getMasterDescription()
	{
		if ($this->exists() && $this->style_id == 0 && $this->MasterDescription)
		{
			return $this->MasterDescription->phrase_text;
		}

		return $this->getValue('description');
	}

	public function getPhraseName($title, $existing = false)
	{
		$name = $existing ? $this->getExistingValue('group_name') : $this->getValue('group_name');
		return 'style_prop_group' . ($title ? '' : '_desc') . '.' . $name;
	}

	/**
	 * @param bool $title
	 *
	 * @return Phrase|null
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
			throw new \LogicException("Cannot update the style of existing property groups");
		}

		if ($this->isChanged('group_name'))
		{
			$existingGroup = $this->em()->findOne('XF:StylePropertyGroup', [
				'style_id' => $this->style_id,
				'group_name' => $this->group_name
			]);
			if ($existingGroup)
			{
				$this->error(\XF::phrase('style_property_groups_must_be_unique_per_style'), 'group_name');
			}
		}

		if ($this->isChanged('style_id') && $this->style_id != 0)
		{
			$this->addon_id = '';
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

			if ($this->isUpdate() && $this->isChanged('group_name'))
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

		if ($this->isUpdate() && $this->isChanged('group_name'))
		{
			/** @var StyleProperty[] $properties */
			$properties = $this->finder('XF:StyleProperty')->where([
				'style_id' => $this->style_id,
				'group_name' => $this->getExistingValue('group_name')
			])->fetch();
			foreach ($properties AS $property)
			{
				$property->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

				$property->group_name = $this->group_name;
				$property->save(true, false);
			}
			// Updating these properties will cascade the change to customized versions of them. There's an edge case
			// this doesn't handle: properties created in child styles in this group will not be updated.
			// As we will be maintaining ungrouped properties, while not ideal, this isn't the end of the world.
			// It might not even come up.
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
	}

	protected function _setupDefaults()
	{
		/** @var \XF\Repository\AddOn $addOnRepo */
		$addOnRepo = $this->_em->getRepository('XF:AddOn');
		$this->addon_id = $addOnRepo->getDefaultAddOnId();
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_style_property_group';
		$structure->shortName = 'XF:StylePropertyGroup';
		$structure->primaryKey = 'property_group_id';
		$structure->columns = [
			'property_group_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'group_name' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_group_name',
				'match' => 'alphanumeric'
			],
			'style_id' => ['type' => self::UINT, 'required' => true],
			'title' => ['type' => self::STR, 'maxLength' => 100,
				'required' => 'please_enter_valid_title'
			],
			'description' => ['type' => self::STR, 'maxLength' => 255, 'default' => ''],
			'display_order' => ['type' => self::UINT, 'forced' => true, 'default' => 0],
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => '']
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
			'master_description' => true
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
			'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'style_prop_group.', '$group_name']
				]
			],
			'MasterDescription' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'style_prop_group_desc.', '$group_name']
				]
			]
		];
		$structure->options = [
			'update_phrase' => true
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
}