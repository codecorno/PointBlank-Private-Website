<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string navigation_id
 * @property string parent_navigation_id
 * @property int display_order
 * @property string navigation_type_id
 * @property array type_config
 * @property string condition_expression
 * @property string condition_setup
 * @property string data_expression
 * @property string data_setup
 * @property string global_setup
 * @property bool enabled
 * @property bool is_customized
 * @property array default_value
 * @property string addon_id
 *
 * GETTERS
 * @property \XF\Phrase title
 * @property \XF\Navigation\AbstractType|null typeHandler
 *
 * RELATIONS
 * @property \XF\Entity\AddOn AddOn
 * @property \XF\Entity\Navigation Parent
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\Navigation[] Children
 * @property \XF\Entity\Phrase MasterTitle
 */
class Navigation extends Entity
{
	public function canEdit()
	{
		if (!$this->addon_id || $this->isInsert())
		{
			return true;
		}
		else
		{
			return \XF::$developmentMode;
		}
	}

	public function canDelete()
	{
		return ($this->canEdit() && $this->exists());
	}

	public function isUserEdit()
	{
		if (!$this->getOption('user_edit'))
		{
			return false;
		}

		if (!$this->addon_id || $this->isInsert())
		{
			// not associated with an add-on or inserting, so this is an owner edit
			return false;
		}

		if (\XF::$developmentMode)
		{
			// in debug mode, so we have to assume this is an add-on developer edit their own entries
			return false;
		}

		return true;
	}

	/**
	 * @return \XF\Phrase
	 */
	public function getTitle()
	{
		return \XF::phrase($this->getPhraseName());
	}

	public function getPhraseName()
	{
		return 'nav.' . $this->navigation_id;
	}

	public function getMasterPhrase()
	{
		$phrase = $this->MasterTitle;
		if (!$phrase)
		{
			$phrase = $this->_em->create('XF:Phrase');
			$phrase->addon_id = $this->_getDeferredValue(function() { return $this->addon_id; });
			$phrase->title = $this->_getDeferredValue(function() { return $this->getPhraseName(); });
			$phrase->language_id = 0;
		}

		return $phrase;
	}

	/**
	 * @return \XF\Navigation\AbstractType|null
	 */
	public function getTypeHandler()
	{
		return $this->getNavigationRepo()->getTypeHandler($this->navigation_type_id);
	}

	public function getCompiledEntry()
	{
		$compiled = new \XF\Navigation\CompiledEntry($this->navigation_id, $this->data_expression, $this->data_setup);
		$compiled->applyCondition($this->condition_expression, $this->condition_setup);
		$compiled->setGlobalSetup($this->global_setup);

		return $compiled;
	}

	protected function verifyNavigationTypeId($typeId)
	{
		$handler = $this->getNavigationRepo()->getTypeHandler($typeId);
		if (!$handler)
		{
			// this error generally shouldn't come up
			$this->error(\XF::phrase('please_enter_valid_value'), 'navigation_type_id');
			return false;
		}

		$this->_getterCache['typeHandler'] = $handler;

		return true;
	}

	public function setTypeFromInput($typeId, array $config = [])
	{
		if (!$this->set('navigation_type_id', $typeId))
		{
			return false;
		}

		$navCompiler = $this->getNavigationCompiler();

		$finalConfig = $this->typeHandler->validateConfigInput($this, $config, $navCompiler, $configError, $errorField);
		if (!is_array($finalConfig))
		{
			$this->error($configError, $errorField ?: 'type_config');
			return false;
		}
		else
		{
			$this->type_config = $finalConfig;
			return true;
		}
	}

	public function revertToDefault()
	{
		if (!$this->is_customized)
		{
			return;
		}

		foreach ($this->default_value AS $key => $value)
		{
			$this->set($key, $value);
		}

		$this->is_customized = false;
	}

	protected function _preSave()
	{
		if ($this->isUpdate() && $this->isChanged('parent_navigation_id') && $this->getOption('verify_parent'))
		{
			$parentValid = $this->getNavigationRepo()->createNavigationTree()->isNewParentValid(
				$this->getExistingValue('navigation_id'), $this->parent_navigation_id
			);
			if (!$parentValid)
			{
				$this->error(\XF::phrase('please_select_valid_parent_navigation_entry'), 'parent_navigation_id');
			}
		}

		if (!$this->getErrors())
		{
			$handler = $this->typeHandler;
			if ($handler)
			{
				$navCompiler = $this->getNavigationCompiler();
				$navCompiler->initializeCompilation();

				$compiled = $handler->compileCode($this, $navCompiler);

				$this->condition_expression = $compiled->conditionExpression;
				$this->condition_setup = $compiled->conditionSetup;
				$this->data_expression = $compiled->dataExpression;
				$this->data_setup = $compiled->dataSetup;
				$this->global_setup = $compiled->globalSetup;
			}

			// changes here need to be matched in \XF\AddOn\DataType\Navigation
			$config = [
				'parent_navigation_id' => $this->parent_navigation_id,
				'navigation_type_id' => $this->navigation_type_id,
				'type_config' => $this->type_config
			];
			$defaultConfigChanged = $this->isChanged(array_keys($config));

			if ($this->isUserEdit())
			{
				// user editing, see if they're changing from the default
				$this->is_customized = ($config != $this->default_value);
			}
			else if ($this->getOption('master_import'))
			{
				// importing the master version - if we're not customized, the default value is what we have.
				// otherwise we expect default_value to be updated externally
				if (!$this->is_customized)
				{
					$this->default_value = $config;
				}
			}
			else if ($defaultConfigChanged)
			{
				// master edit - if we changed something in the default value area, assume what we have now is the default
				$this->default_value = $config;
				$this->is_customized = false;
			}
		}
		else if ($this->isInsert())
		{
			$this->data_expression = '[]'; // prevent required error here
		}
	}

	protected function _postSave()
	{
		if ($this->isUpdate())
		{
			if ($this->isChanged('addon_id') || $this->isChanged('navigation_id'))
			{
				/** @var Phrase $phrase */
				$phrase = $this->getExistingRelation('MasterTitle');
				if ($phrase)
				{
					$writeDevOutput = $this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output');
					$phrase->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

					$phrase->addon_id = $this->addon_id;
					$phrase->title = $this->getPhraseName();
					$phrase->save();
				}
			}
		}

		$this->rebuildChildEntries();
		$this->rebuildNavigationCache();
	}

	protected function _preDelete()
	{
		if ($this->navigation_id == $this->app()->get('defaultNavigationId'))
		{
			$this->error(\XF::phrase('you_may_not_delete_the_default_item'));
		}
	}

	protected function _postDelete()
	{
		$writeDevOutput = $this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output');

		/** @var Phrase $phrase */
		$phrase = $this->MasterTitle;
		if ($phrase)
		{
			$phrase->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

			$phrase->delete();
		}

		foreach ($this->Children AS $child)
		{
			/** @var $child Navigation */
			$child->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

			$child->delete();
		}

		$this->rebuildNavigationCache();
	}

	protected function rebuildChildEntries()
	{
		$existingChildren = $this->getExistingRelation('Children');
		if ($this->isUpdate() && $this->isChanged('navigation_id') && $existingChildren)
		{
			$writeDevOutput = $this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output');

			/** @var Navigation $child */
			foreach ($existingChildren AS $child)
			{
				$child->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);
				$child->parent_navigation_id = $this->navigation_id;
				$child->save();
			}
		}
	}

	protected function rebuildNavigationCache()
	{
		$repo = $this->getNavigationRepo();

		\XF::runOnce('navigationCacheRebuild', function() use ($repo)
		{
			$repo->rebuildNavigationCache();
		});
	}

	protected function _setupDefaults()
	{
		/** @var \XF\Repository\AddOn $addOnRepo */
		$addOnRepo = $this->_em->getRepository('XF:AddOn');
		$this->addon_id = $addOnRepo->getDefaultAddOnId();
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_navigation';
		$structure->shortName = 'XF:Navigation';
		$structure->primaryKey = 'navigation_id';
		$structure->columns = [
			'navigation_id' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_navigation_id',
				'unique' => 'navigation_ids_must_be_unique',
				'match' => 'alphanumeric'
			],
			'parent_navigation_id' => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
			'display_order' => ['type' => self::UINT, 'default' => 1],
			'navigation_type_id' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'type_config' => ['type' => self::JSON_ARRAY, 'default' => []],
			'condition_expression' => ['type' => self::BINARY, 'default' => ''],
			'condition_setup' => ['type' => self::BINARY, 'default' => ''],
			'data_expression' => ['type' => self::BINARY, 'required' => true],
			'data_setup' => ['type' => self::BINARY, 'default' => ''],
			'global_setup' => ['type' => self::BINARY, 'default' => ''],
			'enabled' => ['type' => self::BOOL, 'default' => true],
			'is_customized' => ['type' => self::BOOL, 'default' => false],
			'default_value' => ['type' => self::JSON_ARRAY, 'default' => []],
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => '']
		];
		$structure->behaviors = [
			'XF:DevOutputWritable' => []
		];
		$structure->getters = [
			'title' => true,
			'typeHandler' => true
		];
		$structure->relations = [
			'AddOn' => [
				'entity' => 'XF:AddOn',
				'type' => self::TO_ONE,
				'conditions' => 'addon_id',
				'primary' => true
			],
			'Parent' => [
				'entity' => 'XF:Navigation',
				'type' => self::TO_ONE,
				'conditions' => [
					['navigation_id', '=', '$parent_navigation_id']
				],
				'primary' => true
			],
			'Children' => [
				'entity' => 'XF:Navigation',
				'type' => self::TO_MANY,
				'conditions' => [
					['parent_navigation_id', '=', '$navigation_id']
				]
			],
			'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'nav.', '$navigation_id']
				]
			]
		];
		$structure->options = [
			'verify_parent' => true,
			'user_edit' => false,
			'master_import' => false
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Navigation
	 */
	protected function getNavigationRepo()
	{
		return $this->repository('XF:Navigation');
	}

	/**
	 * @return \XF\Navigation\Compiler
	 */
	protected function getNavigationCompiler()
	{
		return $this->app()['navigation.compiler'];
	}
}