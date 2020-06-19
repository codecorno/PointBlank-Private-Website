<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string interface_group_id
 * @property int display_order
 * @property bool is_moderator
 * @property string addon_id
 *
 * GETTERS
 * @property \XF\Phrase title
 *
 * RELATIONS
 * @property \XF\Entity\AddOn AddOn
 * @property \XF\Entity\Phrase MasterTitle
 */
class PermissionInterfaceGroup extends Entity
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
		return 'permission_interface.' . $this->interface_group_id;
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

	protected function _postSave()
	{
		if ($this->isUpdate())
		{
			$writeDevOutput = $this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output');

			if ($this->isChanged('addon_id') || $this->isChanged('interface_group_id'))
			{
				/** @var Phrase $phrase */
				$phrase = $this->getExistingRelation('MasterTitle');
				if ($phrase)
				{
					$phrase->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

					$phrase->addon_id = $this->addon_id;
					$phrase->title = $this->getPhraseName();
					$phrase->save();
				}
			}

			if ($this->isChanged('interface_group_id'))
			{
				$permissions = $this->finder('XF:Permission')
					->where('interface_group_id', $this->getExistingValue('interface_group_id'))
					->fetch();

				foreach ($permissions AS $permission)
				{
					$permission->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

					$permission->interface_group_id = $this->interface_group_id;
					$permission->save();
				}
			}
		}
	}

	protected function _preDelete()
	{
		if ($this->getOption('delete_empty_only'))
		{
			$hasPermissions = $this->db()->fetchOne(
				'SELECT 1 FROM xf_permission WHERE interface_group_id = ? LIMIT 1',
				$this->interface_group_id
			);
			if ($hasPermissions)
			{
				$this->error(\XF::phrase('you_must_delete_all_permissions_within_interface_group_before_deleted'));
			}
		}
	}

	protected function _postDelete()
	{
		$phrase = $this->MasterTitle;
		if ($phrase)
		{
			$writeDevOutput = $this->getBehavior('XF:DevOutputWritable')->getOption('write_dev_output');
			$phrase->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', $writeDevOutput);

			$phrase->delete();
		}

		if (!$this->getOption('delete_empty_only'))
		{
			$this->db()->update(
				'xf_permission',
				['interface_group_id' => ''],
				'interface_group_id = ?',
				$this->interface_group_id
			);
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
		$structure->table = 'xf_permission_interface_group';
		$structure->shortName = 'XF:PermissionInterfaceGroup';
		$structure->primaryKey = 'interface_group_id';
		$structure->columns = [
			'interface_group_id' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_permission_interface_group_id',
				'unique' => 'permission_interface_group_ids_must_be_unique',
				'match' => 'alphanumeric'
			],
			'display_order' => ['type' => self::UINT, 'default' => 1],
			'is_moderator' => ['type' => self::BOOL, 'default' => false],
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
			'MasterTitle' => [
				'entity' => 'XF:Phrase',
				'type' => self::TO_ONE,
				'conditions' => [
					['language_id', '=', 0],
					['title', '=', 'permission_interface.', '$interface_group_id']
				]
			]
		];
		$structure->options = [
			'delete_empty_only' => true
		];

		return $structure;
	}
}