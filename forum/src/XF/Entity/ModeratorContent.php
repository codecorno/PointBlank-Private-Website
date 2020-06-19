<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null moderator_id
 * @property string content_type
 * @property int content_id
 * @property int user_id
 *
 * GETTERS
 * @property string content_title
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\Moderator Moderator
 */
class ModeratorContent extends Entity
{
	/**
	 * @return string
	 */
	public function getContentTitle()
	{
		$handler = $this->getModRepo()->getModeratorHandler($this->content_type);
		if (!$handler)
		{
			return '';
		}
		else
		{
			return $handler->getContentTitle($this->content_id);
		}
	}

	protected function _postDelete()
	{
		if ($this->User)
		{
			$permissions = $this->finder('XF:Permission')
				->where('Interface.is_moderator', 1)
				->where('permission_type', 'flag')
				->fetch();

			$permissionValues = [];
			foreach ($permissions AS $permission)
			{
				$permissionValues[$permission->permission_group_id][$permission->permission_id] = 'unset';
			}

			/** @var \XF\Service\UpdatePermissions $permissionUpdater */
			$permissionUpdater = $this->app()->service('XF:UpdatePermissions');
			$permissionUpdater->setUser($this->User)->setContent($this->content_type, $this->content_id);
			$permissionUpdater->updatePermissions($permissionValues);
		}

		if (
			$this->Moderator
			&& $this->Moderator->is_super_moderator == 0
			&& !$this->finder('XF:ModeratorContent')->where('user_id', $this->user_id)->total()
		)
		{
			$this->Moderator->delete();
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_moderator_content';
		$structure->shortName = 'XF:ModeratorContent';
		$structure->primaryKey = 'moderator_id';
		$structure->columns = [
			'moderator_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'content_id' => ['type' => self::UINT, 'required' => true],
			'user_id' => ['type' => self::UINT, 'required' => true]
		];
		$structure->getters = [
			'content_title' => false
		];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'Moderator' => [
				'entity' => 'XF:Moderator',
				'type' => self::TO_ONE,
				'conditions' => [['user_id', '=', '$user_id']],
				'primary' => true
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Moderator
	 */
	protected function getModRepo()
	{
		return $this->repository('XF:Moderator');
	}
}