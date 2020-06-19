<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null permission_combination_id
 * @property int user_id
 * @property array user_group_list
 * @property array cache_value
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\PermissionCombinationUserGroup[] UserGroupRelations
 */
class PermissionCombination extends Entity implements \XF\Mvc\Entity\Proxyable
{
	public static function instantiateProxied(array $values)
	{
		\XF::app()->permissionCache()->setGlobalPerms(
			$values['permission_combination_id'],
			@json_decode($values['cache_value'], true) ?: []
		);
	}

	protected function _preSave()
	{
		if ($this->isUpdate() && ($this->isChanged('user_id') || $this->isChanged('user_group_list')))
		{
			throw new \LogicException("New permission combinations should be created for different combinations");
		}
	}

	protected function _postSave()
	{
		if ($this->isChanged('user_group_list'))
		{
			$groups = [];
			foreach ($this->user_group_list AS $userGroupId)
			{
				$groups[] = [
					'permission_combination_id' => $this->permission_combination_id,
					'user_group_id' => $userGroupId
				];
			}

			$this->db()->delete('xf_permission_combination_user_group',
				'permission_combination_id = ?', $this->permission_combination_id
			);
			if ($groups)
			{
				$this->db()->insertBulk('xf_permission_combination_user_group', $groups);
			}
		}

		if ($this->getOption('rebuild_permission_cache'))
		{
			$this->app()->permissionBuilder()->rebuildCombination($this);
		}
	}

	protected function _postDelete()
	{
		// note: this assumes that all users have already been shifted off. It doesn't make sense to
		// delete it otherwise.

		$this->db()->delete('xf_permission_combination_user_group',
			'permission_combination_id = ?', $this->permission_combination_id
		);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_permission_combination';
		$structure->shortName = 'XF:PermissionCombination';
		$structure->primaryKey = 'permission_combination_id';
		$structure->columns = [
			'permission_combination_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'user_id' => ['type' => self::UINT, 'default' => 0],
			'user_group_list' => ['type' => self::LIST_COMMA, 'required' => true,
				'list' => ['type' => 'posint', 'unique' => true, 'sort' => SORT_NUMERIC]
			],
			'cache_value' => ['type' => self::JSON_ARRAY, 'default' => []]
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'UserGroupRelations' => [
				'entity' => 'XF:PermissionCombinationUserGroup',
				'type' => self::TO_MANY,
				'conditions' => 'permission_combination_id',
				'key' => 'user_group_id'
			],
		];
		$structure->options = [
			'rebuild_permission_cache' => true
		];

		return $structure;
	}
}