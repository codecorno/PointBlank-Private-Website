<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property int ban_user_id
 * @property int ban_date
 * @property int end_date
 * @property string user_reason
 * @property bool triggered
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\User BanUser
 */
class UserBan extends Entity
{
	protected function _preSave()
	{
		if ($this->isInsert())
		{
			if (!$this->User || $this->User->is_admin || $this->User->is_moderator)
			{
				$this->error(\XF::phraseDeferred('this_user_is_an_admin_or_moderator_choose_another'), 'user_id');
			}
			// ban check handled in the unique part of the user_id structure
		}
		else
		{
			if ($this->isChanged('user_id'))
			{
				throw new \LogicException("Cannot change user_id of a ban record");
			}
		}
	}

	protected function _postSave()
	{
		if ($this->isInsert())
		{
			$this->setIsBanned(true);
		}
	}

	protected function _postDelete()
	{
		$this->setIsBanned(false);
	}

	protected function setIsBanned($isBanned)
	{
		/** @var \XF\Entity\User $user */
		$user = $this->User;
		if (!$user)
		{
			return;
		}

		$user->is_banned = $isBanned;
		$isChanged = $user->isChanged('is_banned');

		if (
			$isBanned
			&& $isChanged
			&& $user->user_state == 'moderated'
			&& !$this->end_date
		)
		{
			// User has been permanently banned while awaiting approval, so make them valid so they
			// don't appear on the approval list any longer. Don't use rejected as an unban would require
			// 2 steps then.
			$user->user_state = 'valid';
		}

		$user->save();

		if ($isChanged)
		{
			/** @var \XF\Service\User\UserGroupChange $userGroupChangeService */
			$userGroupChangeService = $this->app()->service('XF:User\UserGroupChange');

			if ($isBanned)
			{
				if ($banGroup = $this->getOption('ban_user_group'))
				{
					$userGroupChangeService->addUserGroupChange($user->user_id, 'banGroup', $banGroup);
				}
			}
			else
			{
				$userGroupChangeService->removeUserGroupChange($user->user_id, 'banGroup');
			}
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_ban';
		$structure->shortName = 'XF:UserBan';
		$structure->primaryKey = 'user_id';
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true,
				'unique' => 'this_user_is_already_banned'
			],
			'ban_user_id' => ['type' => self::UINT, 'required' => true],
			'ban_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'end_date' => ['type' => self::UINT, 'required' => true],
			'user_reason' => ['type' => self::STR, 'maxLength' => 255, 'default' => ''],
			'triggered' => ['type' => self::BOOL, 'default' => false]
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'BanUser' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [
					['user_id', '=', '$ban_user_id']
				],
				'primary' => true
			]
		];

		$options = \XF::options();

		$structure->options = [
			'ban_user_group' => !empty($options->addBanUserGroup)
				? intval($options->addBanUserGroup)
				: 0
		];

		$structure->defaultWith[] = 'User';

		return $structure;
	}
}