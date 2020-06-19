<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string content_type
 * @property int content_id
 * @property int delete_date
 * @property int delete_user_id
 * @property string delete_username
 * @property string delete_reason
 */
class DeletionLog extends Entity
{
	public function setFromVisitor()
	{
		$this->setFromUser(\XF::visitor());
	}

	public function setFromUser(User $user)
	{
		$this->delete_user_id = $user->user_id;
		$this->delete_username = $user->username;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_deletion_log';
		$structure->shortName = 'XF:DeletionLog';
		$structure->primaryKey = ['content_type', 'content_id'];
		$structure->columns = [
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'content_id' => ['type' => self::UINT, 'required' => true],
			'delete_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'delete_user_id' => ['type' => self::UINT, 'default' => 0],
			'delete_username' => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
			'delete_reason' => ['type' => self::STR, 'maxLength' => 100, 'default' => '', 'censor' => true]
		];
		$structure->getters = [];
		$structure->relations = [];

		return $structure;
	}
}