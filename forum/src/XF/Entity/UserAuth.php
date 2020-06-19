<?php

namespace XF\Entity;

use XF\Authentication\AbstractAuth;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property string scheme_class
 * @property array data
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class UserAuth extends Entity
{
	public function authenticate($password)
	{
		$handler = $this->getAuthenticationHandler();
		if (!$handler || !$handler->hasPassword())
		{
			return false;
		}
		return $handler->authenticate($this->user_id, $password);
	}

	/**
	 * @return null|AbstractAuth
	 */
	public function getAuthenticationHandler()
	{
		$class = $this->scheme_class;
		if (!$class)
		{
			return null;
		}

		// note: the following fallbacks will mostly be no longer touched
		// but need to stay to ensure upgrades from versions older than
		// 2.0.10 still work - without them, it may not be possible for
		// some admins to log into the upgrade system.
		if (substr($class, 0, 7) == 'XenForo')
		{
			$class = 'XF' . substr($class, 7);
		}

		$class = str_replace('_', '\\', $class);
		$class = str_replace('_Authentication_', ':', $class);

		if ($class == 'XF:Default')
		{
			$class = 'XF:Core';
		}

		return $this->app()->auth($class, $this->data ? $this->data : []);
	}

	public function resetPassword()
	{
		$password = \XF::generateRandomString(12);
		$password = strtr($password, [
			'I' => 'i',
			'l' => 'L',
			'0' => 'O',
			'o' => 'O'
		]);
		$password = trim($password, '_-');

		$isReset = $this->setPassword($password);
		if ($isReset)
		{
			return $password;
		}

		return false;
	}

	public function setPassword($password, $authClass = null, $updatePasswordDate = true)
	{
		$password = strval($password);
		if (!strlen($password))
		{
			$this->error(\XF::phrase('please_enter_valid_password'), 'password');
			return false;
		}

		$auth = $this->app()->auth($authClass);
		$this->scheme_class = $auth->getAuthenticationName();
		$this->data = $auth->generate($password);

		if ($updatePasswordDate && $this->isUpdate() && isset($this->User->Profile))
		{
			$this->User->Profile->password_date = \XF::$time;
			$this->addCascadedSave($this->User->Profile);
		}

		return true;
	}

	public function setNoPassword()
	{
		$auth = $this->app()->auth('XF:NoPassword');
		$this->scheme_class = $auth->getAuthenticationName();
		$this->data = $auth->generate('');

		if ($this->isUpdate() && isset($this->User->Profile))
		{
			$this->User->Profile->password_date = \XF::$time;
			$this->addCascadedSave($this->User->Profile);
		}

		return true;
	}

	protected function _preSave()
	{
		if (!$this->scheme_class)
		{
			$this->error(\XF::phrase('please_enter_valid_password'), 'password', false);
			// set these to prevent errors on the required fields
			$this->scheme_class = 'invalid';
		}
	}

	public function getChangeLogEntries()
	{
		$changes = [];

		if ($this->isUpdate() && $this->isChanged(['scheme_class', 'data']))
		{
			$changes['password'] = ['******', '********'];
		}

		return $changes;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_authenticate';
		$structure->shortName = 'XF:UserAuth';
		$structure->primaryKey = 'user_id';
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true],
			'scheme_class' => ['type' => self::STR, 'maxLength' => 100, 'required' => true],
			'data' => ['type' => self::SERIALIZED_ARRAY, 'default' => []]
			// note: this is intentionally still serialized!
		];
		$structure->behaviors = [
			'XF:ChangeLoggable' => ['contentType' => 'user', 'optIn' => true]
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