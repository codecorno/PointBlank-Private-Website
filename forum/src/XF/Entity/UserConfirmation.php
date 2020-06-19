<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property string confirmation_type
 * @property string confirmation_key
 * @property int confirmation_date
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class UserConfirmation extends Entity
{
	public function getTemplateName()
	{
		return 'user_' . $this->confirmation_type . '_confirmation';
	}

	public function regenerateKey()
	{
		$key = \XF::generateRandomString(16);
		$this->confirmation_key = $key;
		$this->confirmation_date = \XF::$time;

		return $key;
	}

	protected function _preSave()
	{
		if (!$this->confirmation_key)
		{
			$this->regenerateKey();
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_confirmation';
		$structure->shortName = 'XF:UserConfirmation';
		$structure->primaryKey = ['user_id', 'confirmation_type'];
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true],
			'confirmation_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'confirmation_key' => ['type' => self::STR, 'maxLength' => 16, 'required' => true],
			'confirmation_date' => ['type' => self::UINT, 'default' => \XF::$time]
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