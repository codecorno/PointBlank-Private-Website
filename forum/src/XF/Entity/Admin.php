<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property array extra_user_group_ids
 * @property int last_login
 * @property array permission_cache
 * @property int admin_language_id
 * @property bool is_super_admin
 *
 * GETTERS
 * @property string username
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class Admin extends Entity
{
	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->User->username;
	}

	public function hasAdminPermission($permissionId)
	{
		if ($this->is_super_admin)
		{
			return true;
		}

		return !empty($this->permission_cache[$permissionId]);
	}

	protected function verifyPermissionCache(&$permissionCache)
	{
		$keyedCache = [];

		foreach ($permissionCache AS $key => $value)
		{
			if ($value === true || $value === 1 || $value === "1")
			{
				$keyedCache[$key] = true;
			}
			else if (is_int($key) && is_string($value))
			{
				$keyedCache[$value] = true;
			}
		}

		$permissionCache = $keyedCache;

		return true;
	}

	protected function _postSave()
	{
		if ($this->isInsert())
		{
			/** @var \XF\Entity\User $user */
			$user = $this->User;
			if ($user)
			{
				$user->is_admin = true;
				$user->save();
			}
		}

		if ($this->isChanged('extra_user_group_ids'))
		{
			$this->getUserGroupChangeService()->addUserGroupChange(
				$this->user_id, 'admin', $this->extra_user_group_ids
			);
		}

		if ($this->isChanged('permission_cache') && $this->getOption('update_permission_entries'))
		{
			$userId = $this->user_id;
			$inserts = [];
			foreach ($this->permission_cache AS $permissionId => $null)
			{
				$inserts[] = [
					'user_id' => $userId,
					'admin_permission_id' => $permissionId
				];
			}

			$this->db()->delete('xf_admin_permission_entry', 'user_id = ?', $userId);
			if ($inserts)
			{
				$this->db()->insertBulk('xf_admin_permission_entry', $inserts);
			}
		}
	}

	protected function _preDelete()
	{
		if ($this->_em->getFinder('XF:Admin')->total() < 2)
		{
			$this->error(\XF::phrase('last_administrator_cannot_be_deleted'));
		}

		if (!$this->getOption('allow_self_delete'))
		{
			if ($this->user_id == \XF::visitor()->user_id)
			{
				$this->error(\XF::phrase('you_cannot_delete_your_own_administrator_record'));
			}
		}
	}

	protected function _postDelete()
	{
		/** @var \XF\Entity\User $user */
		$user = $this->User;
		if ($user)
		{
			$user->is_admin = false;
			$user->save();
		}

		$this->getUserGroupChangeService()->removeUserGroupChange(
			$this->user_id, 'admin'
		);

		$this->db()->delete('xf_admin_permission_entry', 'user_id = ?', $this->user_id);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_admin';
		$structure->shortName = 'XF:Admin';
		$structure->primaryKey = 'user_id';
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'unique' => 'specified_user_is_already_administrator'],
			'extra_user_group_ids' => ['type' => self::LIST_COMMA, 'default' => [],
				'list' => ['type' => 'posint', 'unique' => true, 'sort' => SORT_NUMERIC]
			],
			'last_login' => ['type' => self::UINT, 'default' => 0],
			'permission_cache' => ['type' => self::JSON_ARRAY, 'default' => []],
			'admin_language_id' => ['type' => self::UINT, 'default' => 0],
			'is_super_admin' => ['type' => self::BOOL, 'default' => false]
		];
		$structure->getters = [
			'username' => true
		];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			]
		];
		$structure->options = [
			'allow_self_delete' => false,
			'update_permission_entries' => true
		];

		return $structure;
	}

	/**
	 * @return \XF\Service\User\UserGroupChange
	 */
	protected function getUserGroupChangeService()
	{
		return $this->app()->service('XF:User\UserGroupChange');
	}
}