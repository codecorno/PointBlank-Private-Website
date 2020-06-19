<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property bool is_super_moderator
 * @property array extra_user_group_ids
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class Moderator extends Entity
{
	protected function _postSave()
	{
		if ($this->isChanged('extra_user_group_ids'))
		{
			$this->getUserGroupChangeService()->addUserGroupChange(
				$this->user_id, 'moderator', $this->extra_user_group_ids
			);
		}

		if ($this->User)
		{
			$this->User->is_moderator = true;
			$this->User->save();
		}
	}

	protected function _postDelete()
	{
		$contentModerators = $this->finder('XF:ModeratorContent')
			->where('user_id', $this->user_id)
			->fetch();

		foreach ($contentModerators AS $contentModerator)
		{
			$contentModerator->delete(false);
		}

		if ($this->User)
		{
			$this->User->is_moderator = false;
			$this->User->is_staff = false;
			$this->User->save();

			$permissions = $this->finder('XF:Permission')
				->where('Interface.is_moderator', 1)
				->where('permission_type', 'flag') // all that's supported
				->fetch();

			$permissionValues = [];
			foreach ($permissions AS $permission)
			{
				$permissionValues[$permission->permission_group_id][$permission->permission_id] = 'unset';
			}

			/** @var \XF\Service\UpdatePermissions $permissionUpdater */
			$permissionUpdater = $this->app()->service('XF:UpdatePermissions');
			$permissionUpdater->setUser($this->User)->setGlobal();
			$permissionUpdater->updatePermissions($permissionValues);
		}

		$this->getUserGroupChangeService()->removeUserGroupChange(
			$this->user_id, 'moderator'
		);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_moderator';
		$structure->shortName = 'XF:Moderator';
		$structure->primaryKey = 'user_id';
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true],
			'is_super_moderator' => ['type' => self::BOOL, 'default' => false],
			'extra_user_group_ids' => ['type' => self::LIST_COMMA, 'default' => [],
				'list' => ['type' => 'posint', 'unique' => true, 'sort' => SORT_NUMERIC]
			]
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			]
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