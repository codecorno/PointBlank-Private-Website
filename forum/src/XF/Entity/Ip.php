<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null ip_id
 * @property int|null user_id
 * @property string content_type
 * @property int content_id
 * @property string action
 * @property int log_date
 * @property string ip
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class Ip extends Entity
{
	protected function verifyIp(&$ip)
	{
		$ip = \XF\Util\Ip::convertIpStringToBinary($ip);
		if ($ip === false)
		{
			// this will fail later
			$ip = '';
		}

		return true;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_ip';
		$structure->shortName = 'XF:Ip';
		$structure->primaryKey = 'ip_id';
		$structure->columns = [
			'ip_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'user_id' => ['type' => self::UINT, 'nullable' => true, 'default' => null],
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'content_id' => ['type' => self::UINT, 'required' => true],
			'action' => ['type' => self::STR, 'maxLength' => 25, 'default' => ''],
			'log_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'ip' => ['type' => self::BINARY, 'maxLength' => 16, 'required' => true]
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
		];

		return $structure;
	}
} 