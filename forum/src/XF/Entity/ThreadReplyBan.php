<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null thread_reply_ban_id
 * @property int thread_id
 * @property int user_id
 * @property int ban_date
 * @property int|null expiry_date
 * @property string reason
 * @property int ban_user_id
 *
 * RELATIONS
 * @property \XF\Entity\Thread Thread
 * @property \XF\Entity\User User
 * @property \XF\Entity\User BannedBy
 */
class ThreadReplyBan extends Entity
{
	protected function _preSave()
	{
		$ban = $this->em()->findOne('XF:ThreadReplyBan', [
			'thread_id' => $this->thread_id,
			'user_id' => $this->user_id
		]);
		if ($ban && $ban != $this)
		{
			$this->error(\XF::phrase('this_user_is_already_reply_banned_from_this_thread'));
		}
	}

	protected function _postDelete()
	{
		/** @var \XF\Repository\UserAlert $alertRepo */
		$alertRepo = $this->repository('XF:UserAlert');
		$alertRepo->fastDeleteAlertsToUser($this->User, 'thread', $this->thread_id, 'reply_ban');

		$this->app()->logger()->logModeratorAction(
			'thread', $this->Thread, 'reply_ban_delete', ['name' => $this->User->username]
		);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_thread_reply_ban';
		$structure->shortName = 'XF:ThreadReplyBan';
		$structure->primaryKey = 'thread_reply_ban_id';
		$structure->columns = [
			'thread_reply_ban_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'thread_id' => ['type' => self::UINT, 'required' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'ban_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'expiry_date' => ['type' => self::UINT, 'required' => true, 'nullable' => true],
			'reason' => ['type' => self::STR, 'default' => '', 'maxLength' => 100],
			'ban_user_id' => ['type' => self::UINT, 'required' => true],
		];
		$structure->getters = [];
		$structure->relations = [
			'Thread' => [
				'entity' => 'XF:Thread',
				'type' => self::TO_ONE,
				'conditions' => 'thread_id',
				'primary' => true
			],
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'BannedBy' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [['user_id', '=', '$ban_user_id']],
				'primary' => true
			]
		];

		return $structure;
	}
}