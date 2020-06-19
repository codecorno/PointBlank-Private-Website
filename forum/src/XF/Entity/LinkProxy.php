<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null link_id
 * @property string url
 * @property string url_hash
 * @property int first_request_date
 * @property int last_request_date
 * @property int hits
 *
 * RELATIONS
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\LinkProxyReferrer[] Referrers
 */
class LinkProxy extends Entity
{
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_link_proxy';
		$structure->shortName = 'XF:LinkProxy';
		$structure->primaryKey = 'link_id';
		$structure->columns = [
			'link_id' => ['type' => self::UINT, 'nullable' => true, 'autoIncrement' => true],
			'url' => ['type' => self::STR, 'required' => true],
			'url_hash' => ['type' => self::STR, 'maxLength' => 32, 'required' => true],
			'first_request_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'last_request_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'hits' => ['type' => self::UINT, 'default' => 0]
		];
		$structure->getters = [];
		$structure->relations = [
			'Referrers' => [
				'entity' => 'XF:LinkProxyReferrer',
				'type' => self::TO_MANY,
				'conditions' => 'link_id',
				'order' => ['last_date', 'DESC']
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\LinkProxy
	 */
	protected function getProxyRepo()
	{
		return $this->repository('XF:LinkProxy');
	}
}