<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string ip
 * @property string match_type
 * @property string first_byte
 * @property string start_range
 * @property string end_range
 * @property int create_user_id
 * @property int create_date
 * @property string reason
 * @property int last_triggered_date
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class IpMatch extends Entity
{
	protected function _preSave()
	{
		$exists = $this->finder('XF:IpMatch')
			->where('ip', $this->ip)
			->where('match_type', $this->match_type)
			->fetchOne();
		if ($exists && $exists != $this)
		{
			$this->error(\XF::phrase('specified_ip_address_match_already_exists'));
		}
	}

	protected function _postSave()
	{
		if ($this->match_type == 'banned')
		{
			$this->rebuildBannedIpCache();
		}
		else if ($this->match_type == 'discouraged')
		{
			$this->rebuildDiscouragedIpCache();
		}
	}

	protected function _postDelete()
	{
		if ($this->match_type == 'banned')
		{
			$this->rebuildBannedIpCache();
		}
		else if ($this->match_type == 'discouraged')
		{
			$this->rebuildDiscouragedIpCache();
		}
	}

	protected function rebuildBannedIpCache()
	{
		\XF::runOnce('bannedIpCache', function()
		{
			$this->getBanningRepo()->rebuildBannedIpCache();
		});
	}

	protected function rebuildDiscouragedIpCache()
	{
		\XF::runOnce('discouragedIpCache', function()
		{
			$this->getBanningRepo()->rebuildDiscouragedIpCache();
		});
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_ip_match';
		$structure->shortName = 'XF:IpMatch';
		$structure->primaryKey = ['ip', 'match_type'];
		$structure->columns = [
			'ip' => ['type' => self::STR, 'required' => true, 'maxLength' => 43],
			'match_type' => ['type' => self::STR, 'required' => true,
				'allowedValues' => ['banned', 'discouraged']
			],
			'first_byte' => ['type' => self::BINARY, 'maxLength' => 1],
			'start_range' => ['type' => self::BINARY, 'maxLength' => 16],
			'end_range' => ['type' => self::BINARY, 'maxLength' => 16],
			'create_user_id' => ['type' => self::UINT, 'required' => true],
			'create_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'reason' => ['type' => self::STR, 'maxLength' => 255, 'default' => ''],
			'last_triggered_date' => ['type' => self::UINT, 'default' => 0]
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [
					['user_id', '=', '$create_user_id']
				],
				'primary' => true
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Banning
	 */
	protected function getBanningRepo()
	{
		return $this->repository('XF:Banning');
	}
}