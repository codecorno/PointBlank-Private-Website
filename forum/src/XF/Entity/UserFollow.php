<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property int follow_user_id
 * @property int follow_date
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\User FollowUser
 */
class UserFollow extends Entity
{
	protected function _preSave()
	{
		if ($this->isInsert())
		{
			if ($this->user_id == $this->follow_user_id)
			{
				$this->error(\XF::phrase('you_may_not_follow_yourself'));
			}

			$exists = $this->em()->findOne('XF:UserFollow', [
				'user_id' => $this->user_id,
				'follow_user_id' => $this->follow_user_id
			]);
			if ($exists)
			{
				$this->error(\XF::phrase('you_already_following_this_member'));
			}

			$followFinder = $this->finder('XF:UserFollow');
			$total = $followFinder
				->where('user_id', $this->user_id)
				->total();
			$followLimit = 1000;
			if ($total >= $followLimit)
			{
				$this->error(\XF::phrase('you_may_only_follow_x_people', ['count' => $followLimit]));
			}
		}
	}

	protected function _postSave()
	{
		$this->rebuildFollowingCache();
	}

	protected function _postDelete()
	{
		$this->rebuildFollowingCache();
	}

	protected function rebuildFollowingCache()
	{
		$this->getFollowRepo()->rebuildFollowingCache($this->user_id);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_follow';
		$structure->shortName = 'XF:UserFollow';
		$structure->primaryKey = ['user_id', 'follow_user_id'];
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true],
			'follow_user_id' => ['type' => self::UINT, 'required' => true],
			'follow_date' => ['type' => self::UINT, 'default' => \XF::$time]
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'FollowUser' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [['user_id', '=', '$follow_user_id']],
				'primary' => true
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\UserFollow
	 */
	protected function getFollowRepo()
	{
		return $this->repository('XF:UserFollow');
	}
}