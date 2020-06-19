<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property string provider
 * @property string provider_key
 * @property array extra_data
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class UserConnectedAccount extends Entity
{
	protected function _postSave()
	{
		if ($this->isChanged('provider_key'))
		{
			$this->rebuildUserConnectedAccountCache();
		}
	}

	protected function _postDelete()
	{
		$this->rebuildUserConnectedAccountCache();
	}

	protected function rebuildUserConnectedAccountCache(User $user = null)
	{
		$user = $user ?: $this->User;
		if ($user)
		{
			$this->repository('XF:ConnectedAccount')->rebuildUserConnectedAccountCache($user);
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_connected_account';
		$structure->shortName = 'XF:UserConnectedAccount';
		$structure->primaryKey = ['user_id', 'provider'];
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true],
			'provider' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'provider_key' => ['type' => self::STR, 'required' => true],
			'extra_data' => ['type' => self::JSON_ARRAY, 'default' => []],
		];
		$structure->getters = [];
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