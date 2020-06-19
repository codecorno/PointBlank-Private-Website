<?php

namespace XF\Service\User;

class EmailConfirmation extends AbstractConfirmationService
{
	public function getType()
	{
		return 'email';
	}

	public function canTriggerConfirmation(&$error = null)
	{
		if (!$this->user->isAwaitingEmailConfirmation())
		{
			$error = \XF::phrase('your_account_does_not_require_confirmation');
			return false;
		}

		if (!$this->user->email)
		{
			$error = \XF::phrase('this_account_cannot_be_confirmed_without_email_address');
			return false;
		}

		return true;
	}

	public function emailConfirmed()
	{
		$user = $this->user;
		if (!$user->isAwaitingEmailConfirmation())
		{
			return false;
		}

		$originalUserState = $user->user_state;

		if ($user->user_state == 'email_confirm')
		{
			// don't log when changing from initial confirm state as it creates a lot of noise
			$user->getBehavior('XF:ChangeLoggable')->setOption('enabled', false);
		}

		$this->advanceUserState();
		$user->save();

		if ($this->confirmation->exists())
		{
			$this->confirmation->delete();
		}

		if ($originalUserState == 'email_confirm' && $user->user_state == 'valid')
		{
			/** @var \XF\Service\User\Welcome $userWelcome */
			$userWelcome = $this->service('XF:User\Welcome', $user);
			$userWelcome->send();
		}

		$this->repository('XF:Ip')->logIp($user->user_id, \XF::app()->request()->getIp(), 'user', $user->user_id, 'email_confirm');

		return true;
	}

	protected function advanceUserState()
	{
		$user = $this->user;

		switch ($user->user_state)
		{
			case 'email_confirm':
				if ($this->app->options()->registrationSetup['moderation'])
				{
					$user->user_state = 'moderated';
					break;
				}
			// otherwise, fall through

			case 'email_confirm_edit': // this is a user editing email, never send back to moderation
			case 'moderated':
				$user->user_state = 'valid';
				break;
		}
	}
}