<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null referrer_id
 * @property int link_id
 * @property string referrer_hash
 * @property string referrer_url
 * @property int hits
 * @property int first_date
 * @property int last_date
 *
 * RELATIONS
 * @property \XF\Entity\LinkProxy Link
 */
class LinkProxyReferrer extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_link_proxy_referrer';
		$structure->shortName = 'XF:LinkProxyReferrer';
		$structure->primaryKey = 'referrer_id';
		$structure->columns = [
			'referrer_id' => ['type' => self::UINT, 'nullable' => true, 'autoIncrement' => true],
			'link_id' => ['type' => self::UINT, 'required' => true],
			'referrer_hash' => ['type' => self::STR, 'maxLength' => 32, 'required' => true],
			'referrer_url' => ['type' => self::STR, 'required' => true],
			'hits' => ['type' => self::UINT, 'default' => 0],
			'first_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'last_date' => ['type' => self::UINT, 'default' => \XF::$time],
		];
		$structure->getters = [];
		$structure->relations = [
			'Link' => [
				'entity' => 'XF:LinkProxy',
				'type' => self::TO_ONE,
				'conditions' => 'link_id',
				'primary' => true
			],
		];

		return $structure;
	}
}