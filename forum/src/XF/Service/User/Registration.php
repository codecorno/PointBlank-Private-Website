<?php

namespace XF\Service\User;

class Registration extends \XF\Service\AbstractService
{
	use \XF\Service\ValidateAndSavableTrait;

	/**
	 * @var \XF\Entity\User
	 */
	protected $user;

	protected $fieldMap = [
		'username' => 'username',
		'email' => 'email',
		'timezone' => 'timezone',
		'location' => 'Profile.location',
	];

	protected $logIp = true;

	protected $avatarUrl = null;

	protected $skipEmailConfirm = false;

	protected function setup()
	{
		$this->user = $this->app->repository('XF:User')->setupBaseUser();
	}

	public function getUser()
	{
		return $this->user;
	}

	public function setMapped(array $input)
	{
		foreach ($this->fieldMap AS $inputKey => $entityKey)
		{
			if (!isset($input[$inputKey]))
			{
				continue;
			}

			$value = $input[$inputKey];
			if (strpos($entityKey, '.'))
			{
				list($relation, $relationKey) = explode('.', $entityKey, 2);
				$this->user->{$relation}->{$relationKey} = $value;
			}
			else
			{
				$this->user->{$entityKey} = $value;
			}
		}
	}

	public function setPassword($password, $passwordConfirm = '', $doPasswordConfirmation = true)
	{
		if ($doPasswordConfirmation)
		{
			if ($password !== $passwordConfirm)
			{
				$this->user->error(\XF::phrase('passwords_did_not_match'));
				return false;
			}
		}

		if ($this->user->Auth->setPassword($password))
		{
			$this->user->Profile->password_date = \XF::$time;
		}
		return true;
	}

	public function setNoPassword()
	{
		$this->user->Auth->setNoPassword();
		$this->user->Profile->password_date = \XF::$time;
	}

	public function setDob($day, $month, $year)
	{
		return $this->user->Profile->setDob($day, $month, $year);
	}

	public function setCustomFields(array $values)
	{
		/** @var \XF\CustomField\Set $fieldSet */
		$fieldSet = $this->user->Profile->custom_fields;

		$fieldDefinition = $fieldSet->getDefinitionSet()
			->filterEditable($fieldSet, 'user')
			->filter('registration');

		$customFieldsShown = array_keys($fieldDefinition->getFieldDefinitions());

		if ($customFieldsShown)
		{
			$fieldSet->bulkSet($values, $customFieldsShown);
		}
	}

	public function setFromInput(array $input)
	{
		$this->setMapped($input);

		if (isset($input['password']))
		{
			$password = $input['password'];
			if (isset($input['password_confirm']))
			{
				$passwordConfirm = $input['password_confirm'];
				$doPasswordConfirmation = true;
			}
			else
			{
				$passwordConfirm = '';
				$doPasswordConfirmation = false;
			}

			$this->setPassword($password, $passwordConfirm, $doPasswordConfirmation);
		}

		if (isset($input['dob_day'], $input['dob_month'], $input['dob_year']))
		{
			$day = isset($input['dob_day']) ? $input['dob_day'] : 0;
			$month = isset($input['dob_month']) ? $input['dob_month'] : 0;
			$year = isset($input['dob_year']) ? $input['dob_year'] : 0;

			$this->setDob($day, $month, $year);
		}

		if (isset($input['custom_fields']))
		{
			$this->setCustomFields($input['custom_fields']);
		}

		if (isset($input['email_choice']))
		{
			$this->setReceiveAdminEmail($input['email_choice']);
		}
	}

	public function setReceiveAdminEmail($choice)
	{
		$this->user->Option->receive_admin_email = $choice;
	}

	public function setAvatarUrl($url)
	{
		$this->avatarUrl = $url;
	}

	public function checkForSpam()
	{
		$user = $this->user;

		$userChecker = $this->app->spam()->userChecker();
		$userChecker->check($user);

		$decision = $userChecker->getFinalDecision();
		switch ($decision)
		{
			case 'denied':
				$user->rejectUser(\XF::phrase('spam_prevention_registration_rejected'));
				break;

			case 'moderated':
				$user->user_state = 'moderated';
				break;
		}
	}

	public function skipEmailConfirmation($skip = true)
	{
		$this->skipEmailConfirm = $skip;
	}

	protected function _validate()
	{
		$this->finalSetup();

		$user = $this->user;
		$user->preSave();

		$this->applyExtraValidation();

		return $user->getErrors();
	}

	protected function finalSetup()
	{
		$user = $this->user;

		if (!$user->getErrors() && $user->email && !$this->avatarUrl)
		{
			if ($this->app->options()->gravatarEnable && $this->app->validator('Gravatar')->isValid($user->email))
			{
				$user->gravatar = $user->email;
			}
		}

		$this->setInitialUserState();
		$this->setPolicyAcceptance();
	}

	protected function setInitialUserState()
	{
		$user = $this->user;
		$options = $this->app->options();

		if ($user->user_state != 'valid')
		{
			return; // We have likely already set the user state elsewhere, e.g. spam trigger
		}

		if ($options->registrationSetup['emailConfirmation'] && !$this->skipEmailConfirm)
		{
			$user->user_state = 'email_confirm';
		}
		else if ($options->registrationSetup['moderation'])
		{
			$user->user_state = 'moderated';
		}
		else
		{
			$user->user_state = 'valid';
		}
	}

	protected function setPolicyAcceptance()
	{
		$user = $this->user;

		if ($this->app->container('privacyPolicyUrl'))
		{
			$user->privacy_policy_accepted = \XF::$time;
		}
		if ($this->app->container('tosUrl'))
		{
			$user->terms_accepted = \XF::$time;
		}
	}

	protected function applyExtraValidation()
	{
		$user = $this->user;
		$options = $this->app->options();
		$age = $user->Profile->getAge(true);

		if ($options->registrationSetup['requireDob'])
		{
			if (!$age)
			{
				// incomplete dob
				$user->error(\XF::phrase('please_enter_valid_date_of_birth'), 'dob');
			}
			else if ($options->registrationSetup['minimumAge'])
			{
				if ($age < intval($options->registrationSetup['minimumAge']))
				{
					$user->error(\XF::phrase('sorry_you_too_young_to_create_an_account'), 'dob');
				}
			}
		}

		if (!empty($options->registrationSetup['requireLocation']) && !$user->Profile->location)
		{
			$user->error(\XF::phrase('please_enter_valid_location'), 'location');
		}
	}

	protected function _save()
	{
		$user = $this->user;

		$user->save();

		$this->app->spam()->userChecker()->logSpamTrigger('user', $user->user_id);

		if ($this->logIp)
		{
			$ip = ($this->logIp === true ? $this->app->request()->getIp() : $this->logIp);
			$this->writeIpLog($ip);
		}

		$this->writeInitialChangeLogs();
		$this->updateUserAchievements();
		$this->sendRegistrationContact();

		if ($this->avatarUrl)
		{
			// Only apply the avatar if the user would have permission. This reads the permission set directly
			// to ensure that we check their "real" permissions. Otherwise, if this is set and the user hasn't gone
			// directly to the valid state, the permission is likely to be a false negative.
			$permissions = $this->app->permissionCache()->getPermissionSet(
				$user->getValue('permission_combination_id')
			);
			if ($permissions->hasGlobalPermission('avatar', 'allowed'))
			{
				$this->applyAvatarFromUrl($this->avatarUrl);
			}
		}

		return $user;
	}

	protected function writeIpLog($ip)
	{
		$user = $this->user;

		/** @var \XF\Repository\IP $ipRepo */
		$ipRepo = $this->repository('XF:Ip');
		$ipRepo->logIp($user->user_id, $ip, 'user', $user->user_id, 'register');
	}

	protected function writeInitialChangeLogs()
	{
		/** @var \XF\Repository\ChangeLog $changeLogRepo */
		$changeLogRepo = $this->repository('XF:ChangeLog');

		$user = $this->user;

		$changes = [];

		if ($this->app->options()->registrationSetup['requireEmailChoice'])
		{
			$changes['receive_admin_email'] = [0, $user->Option->receive_admin_email ? 1 : 0];
		}
		if ($this->app->container('privacyPolicyUrl'))
		{
			$changes['privacy_policy_accepted'] = [0, \XF::$time];
		}
		if ($this->app->container('tosUrl'))
		{
			$changes['terms_accepted'] = [0, \XF::$time];
		}

		if ($changes)
		{
			$changeLogRepo->logChanges('user', $user->user_id, $changes, $user->user_id);
		}
	}

	protected function updateUserAchievements()
	{
		/** @var \XF\Repository\UserGroupPromotion $userGroupPromotionRepo */
		$userGroupPromotionRepo = $this->repository('XF:UserGroupPromotion');
		$userGroupPromotionRepo->updatePromotionsForUser($this->user);

		if ($this->app->options()->enableTrophies)
		{
			/** @var \XF\Repository\Trophy $trophyRepo */
			$trophyRepo = $this->repository('XF:Trophy');
			$trophyRepo->updateTrophiesForUser($this->user);
		}
	}

	protected function sendRegistrationContact()
	{
		$user = $this->user;

		if ($user->user_state == 'email_confirm')
		{
			/** @var \XF\Service\User\EmailConfirmation $emailConfirmation */
			$emailConfirmation = $this->service('XF:User\EmailConfirmation', $user);
			$emailConfirmation->triggerConfirmation();
		}
		else if ($user->user_state == 'valid')
		{
			/** @var \XF\Service\User\Welcome $userWelcome */
			$userWelcome = $this->service('XF:User\Welcome', $user);
			$userWelcome->send();
		}
	}

	public function applyAvatarFromUrl($url)
	{
		if (!$this->user->user_id)
		{
			throw new \LogicException("User is not saved yet");
		}

		$app = $this->app;

		$validator = $app->validator('Url');
		if (!$validator->isValid($url))
		{
			return false;
		}

		$tempFile = \XF\Util\File::getTempFile();
		if ($app->http()->reader()->getUntrusted($url, [], $tempFile))
		{
			/** @var \XF\Service\User\Avatar $avatarService */
			$avatarService = $this->service('XF:User\Avatar', $this->user);
			if (!$avatarService->setImage($tempFile))
			{
				return false;
			}
			return $avatarService->updateAvatar();
		}
		else
		{
			return false;
		}
	}
}