<?php

namespace XF\Service\ApiKey;

use XF\Entity\ApiKey;
use XF\Service\AbstractService;

class Manager extends AbstractService
{
	use \XF\Service\ValidateAndSavableTrait;

	/**
	 * @var ApiKey
	 */
	protected $key;

	public function __construct(\XF\App $app, ApiKey $key)
	{
		parent::__construct($app);

		$this->key = $key;
	}

	/**
	 * @return ApiKey
	 */
	public function getKey()
	{
		return $this->key;
	}

	public function setTitle($title)
	{
		$this->key->title = $title;
	}

	public function setActive($active)
	{
		$this->key->active = $active;
	}

	public function setScopes($allowAllScopes, array $scopes = [])
	{
		$this->key->allow_all_scopes = $allowAllScopes;
		$this->key->scopes = $scopes;
	}

	public function setKeyType($keyType, $user = null, $forceAllow = false)
	{
		if (!$forceAllow && $this->key->exists())
		{
			return false;
		}

		$key = $this->key;

		switch ($keyType)
		{
			case '':
				// no type present - do nothing if possible
				if (!$key->exists())
				{
					$key->is_super_user = false;
					$key->user_id = 0;
				}
				break;

			case 'super':
				$key->is_super_user = true;
				$key->user_id = 0;
				break;

			case 'user':
				if (is_string($user))
				{
					$userEnt = $this->em()->findOne('XF:User', ['username' => $user]);
					if (!$userEnt)
					{
						$this->key->error(\XF::phrase('requested_user_not_found'), 'user_id');
						break;
					}

					$userId = $userEnt->user_id;
				}
				else if (is_int($user))
				{
					$userId = $user;
				}
				else if ($user instanceof \XF\Entity\User)
				{
					$userId = $user->user_id;
				}
				else
				{
					$userId = 0;
				}

				$key->is_super_user = false;
				$key->user_id = $userId;
				break;

			case 'guest':
			default:
				$key->is_super_user = false;
				$key->user_id = 0;
				break;
		}

		return true;
	}

	public function regenerate()
	{
		if (!$this->key->exists())
		{
			return;
		}

		$this->key->setOption('allow_unsafe_edit', true);
		$this->key->api_key = $this->key->generateKeyValue();
	}

	protected function _validate()
	{
		$this->key->preSave();
		return $this->key->getErrors();
	}

	protected function _save()
	{
		$key = $this->key;

		$sendNotification = $key->hasNotifiableChanges();

		$this->key->save();

		if ($sendNotification)
		{
			$this->contactSuperAdmins();
		}
	}

	protected function contactSuperAdmins()
	{
		$superAdmins = $this->em()->getFinder('XF:Admin')
			->where('is_super_admin', 1)
			->with('User', true)
			->fetch();

		foreach ($superAdmins AS $superAdmin)
		{
			$this->sendApiKeyNotification($superAdmin->User);
		}
	}

	protected function sendApiKeyNotification(\XF\Entity\User $user)
	{
		if (!$user->email)
		{
			return;
		}

		$mail = $this->app->mailer()->newMail();
		$mail->setToUser($user)
			->setTemplate('api_key_change', [
				'user' => $user,
				'changer' => \XF::visitor(),
				'apiKey' => $this->key
			]);
		$mail->send();
	}
}