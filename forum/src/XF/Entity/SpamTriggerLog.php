<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null trigger_log_id
 * @property string content_type
 * @property int content_id
 * @property int log_date
 * @property int user_id
 * @property string ip_address
 * @property string result
 * @property array details_
 * @property array request_state
 *
 * GETTERS
 * @property mixed details
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class SpamTriggerLog extends Entity
{
	public function getDetails()
	{
		$output = [];

		foreach ($this->details_ AS $detail)
		{
			if (!isset($detail['phrase']))
			{
				continue;
			}

			$output[] = \XF::phrase($detail['phrase'], isset($detail['data']) ? $detail['data'] : []);
		}

		return implode(', ', $output);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_spam_trigger_log';
		$structure->shortName = 'XF:SpamTriggerLog';
		$structure->primaryKey = 'trigger_log_id';
		$structure->columns = [
			'trigger_log_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'content_type' => ['type' => self::STR, 'required' => true],
			'content_id' => ['type' => self::UINT, 'required' => true],
			'log_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'ip_address' => ['type' => self::BINARY, 'maxLength' => 16],
			'result' => ['type' => self::STR, 'required' => true],
			'details' => ['type' => self::JSON_ARRAY, 'required' => true],
			'request_state' => ['type' => self::JSON_ARRAY, 'required' => true]
		];
		$structure->getters = [
			'details' => true
		];
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
}