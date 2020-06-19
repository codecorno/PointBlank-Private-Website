<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int thread_id
 * @property string target_url
 * @property string redirect_key
 * @property int expiry_date
 *
 * RELATIONS
 * @property \XF\Entity\Thread Thread
 */
class ThreadRedirect extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_thread_redirect';
		$structure->shortName = 'XF:ThreadRedirect';
		$structure->primaryKey = 'thread_id';
		$structure->columns = [
			'thread_id' => ['type' => self::UINT, 'required' => true],
			'target_url' => ['type' => self::STR, 'required' => true],
			'redirect_key' => ['type' => self::STR, 'maxLength' => 50, 'required' => true],
			'expiry_date' => ['type' => self::UINT, 'default' => 0]
		];
		$structure->getters = [];
		$structure->relations = [
			'Thread' => [
				'entity' => 'XF:Thread',
				'type' => self::TO_ONE,
				'conditions' => 'thread_id',
				'primary' => true
			],
		];

		return $structure;
	}
}