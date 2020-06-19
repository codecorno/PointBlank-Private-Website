<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null user_tfa_id
 * @property int user_id
 * @property string provider_id
 * @property array provider_data
 * @property int last_used_date
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\TfaProvider Provider
 */
class UserTfa extends Entity
{
	protected function _postSave()
	{
		if ($this->isInsert())
		{
			/** @var User $user */
			$user = $this->User;
			if ($user && $user->Option)
			{
				/** @var UserOption $userOption */
				$userOption = $user->Option;
				$userOption->use_tfa = true;
				$userOption->save(true, false);
			}
		}
	}

	protected function _postDelete()
	{
		if ($this->User && !$this->getTfaRepo()->userRequiresTfa($this->User))
		{
			$this->getTfaRepo()->disableTfaForUser($this->User);
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_tfa';
		$structure->shortName = 'XF:UserTfa';
		$structure->primaryKey = 'user_tfa_id';
		$structure->columns = [
			'user_tfa_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'provider_id' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'provider_data' => ['type' => self::JSON_ARRAY, 'default' => []],
			'last_used_date' => ['type' => self::UINT, 'default' => \XF::$time]
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'Provider' => [
				'entity' => 'XF:TfaProvider',
				'type' => self::TO_ONE,
				'conditions' => 'provider_id',
				'primary' => true
			],
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Tfa
	 */
	protected function getTfaRepo()
	{
		return $this->repository('XF:Tfa');
	}
}