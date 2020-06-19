<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property int reject_date
 * @property int reject_user_id
 * @property string reject_reason
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\User RejectUser
 */
class UserReject extends Entity
{
	public function setFromVisitor()
	{
		$this->setFromUser(\XF::visitor());
	}

	public function setFromUser(User $user)
	{
		$this->reject_user_id = $user->user_id;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_reject';
		$structure->shortName = 'XF:UserReject';
		$structure->primaryKey = 'user_id';
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true,
				'unique' => 'this_user_is_already_rejected'
			],
			'reject_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'reject_user_id' => ['type' => self::UINT, 'default' => 0],
			'reject_reason' => ['type' => self::STR, 'maxLength' => 200, 'default' => '']
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'RejectUser' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [
					['user_id', '=', '$reject_user_id']
				],
				'primary' => true
			]
		];

		$structure->options = [];

		return $structure;
	}
}