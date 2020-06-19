<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string navigation_id
 * @property string parent_navigation_id
 * @property int display_order
 * @property string link
 * @property string icon
 * @property string admin_permission_id
 * @property bool debug_only
 * @property bool development_only
 * @property bool hide_no_children
 * @property string addon_id
 *
 * GETTERS
 * @property \XF\Phrase title
 *
 * RELATIONS
 * @property \XF\Entity\AddOn AddOn
 * @property \XF\Entity\AdminNavigation Parent
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\AdminNavigation[] Children
 * @property \XF\Entity\Phrase MasterTitle
 */
class AdminNavigation extends Entity
{
	/**
	 * @return \XF\Phrase
	 */
	public function getTitle()
	{
		return \XF::phrase($this->getPhraseName());
	}

	public function getPhraseName()
	{
		return 'admin_navigation.' . $this->navigation_id;
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
			/** @var $child AdminNavigation */
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

			/** @var AdminNavigation $child */
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

		\XF::runOnce('adminNavigationCacheRebuild', function() use ($repo)
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
		$structure->table = 'xf_admin_navigation';
		$structure->shortName = 'XF:AdminNavigation';
		$structure->primaryKey = 'navigation_id';
		$structure->columns = [
			'navigation_id' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_admin_navigation_id',
				'unique' => 'admin_navigation_ids_must_be_unique',
				'match' => 'alphanumeric'
			],
			'parent_navigation_id' => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
			'display_order' => ['type' => self::UINT, 'default' => 1],
			'link' => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
			'icon' => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
			'admin_permission_id' => ['type' => self::STR, 'maxLength' => 25, 'default' => ''],
			'debug_only' => ['type' => self::BOOL, 'default' => false],
			'development_only' => ['type' => self::BOOL, 'default' => false],
			'hide_no_children' => ['type' => self::BOOL, 'default' => false],
			'addon_id' => ['type' => self::BINARY, 'maxLength' => 50, 'default' => '']
		];
		$structure->behaviors = [
			'XF:DevOutputWritable' => []
		];
		$structure->getters = [
			'title' => true
		];
		$structure->relations = [
			'AddOn' => [
				'entity' => 'XF:AddOn',
				'type' => self::TO_ONE,
				'conditions' => 'addon_id',
				'primary' => true
			],
			'Parent' => [
				'entity' => 'XF:AdminNavigation',
				'type' => self::TO_ONE,
				'conditions' => [
					['navigation_id', '=', '$parent_navigation_id']
				],
				'primary' => true
			],
			'Children' => [
				'entity' => 'XF:AdminNavigation',
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
					['title', '=', 'admin_navigation.', '$navigation_id']
				]
			]
		];
		$structure->options = [
			'verify_parent' => true
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\AdminNavigation
	 */
	protected function getNavigationRepo()
	{
		return $this->repository('XF:AdminNavigation');
	}
}