<?php

namespace XF\Api\ControllerPlugin;

use XF\Mvc\FormAction;

class User extends AbstractPlugin
{
	/**
	 * @return array
	 *
	 * @api-in str $option[creation_watch_state]
	 * @api-in str $option[interaction_watch_state]
	 * @api-in bool $option[content_show_signature]
	 * @api-in bool $option[email_on_conversation]
	 * @api-in bool $option[push_on_conversation]
	 * @api-in bool $option[receive_admin_email]
	 * @api-in bool $option[show_dob_year]
	 * @api-in bool $option[show_dob_date]
	 * @api-in str $profile[location]
	 * @api-in str $profile[website]
	 * @api-in str $profile[about]
	 * @api-in str $profile[signature]
	 * @api-in str $privacy[allow_view_profile]
	 * @api-in str $privacy[allow_post_profile]
	 * @api-in str $privacy[allow_receive_news_feed]
	 * @api-in str $privacy[allow_send_personal_conversation]
	 * @api-in str $privacy[allow_view_identities]
	 * @api-in bool $visible
	 * @api-in bool $activity_visible
	 * @api-in str $timezone
	 * @api-in str $custom_title
	 */
	public function getAccountEditInput()
	{
		$tableInput = $this->filter([
			'option' => [
				'creation_watch_state' => '?str',
				'interaction_watch_state' => '?str',
				'content_show_signature' => '?bool',
				'email_on_conversation' => '?bool',
				'push_on_conversation' => '?bool',
				'receive_admin_email' => '?bool',
				'show_dob_year' => '?bool',
				'show_dob_date' => '?bool',
			],
			'profile' => [
				'location' => '?str',
				'website' => '?str',
				'about' => '?str,',
				'signature' => '?str'
			],
			'privacy' => [
				'allow_view_profile' => '?str',
				'allow_post_profile' => '?str',
				'allow_receive_news_feed' => '?str',
				'allow_send_personal_conversation' => '?str',
				'allow_view_identities' => '?str'
			]
		]);

		$tableInput['user'] = $this->filter([
			'visible' => '?bool',
			'activity_visible' => '?bool',
			'timezone' => '?str',
			'custom_title' => '?str',
		]);

		return $tableInput;
	}

	/**
	 * @return mixed
	 *
	 * @api-in bool $option[is_discouraged]
	 * @api-in str $username
	 * @api-in str $email
	 * @api-in int $user_group_id
	 * @api-in int[] $secondary_group_ids
	 * @api-in str $user_state
	 * @api-in bool $is_staff
	 * @api-in int $message_count
	 * @api-in int $reaction_score
	 * @api-in int $trophy_points
	 */
	public function getAdminEditInput()
	{
		$tableInput = $this->filter([
			'option' => [
				'is_discouraged' => '?bool'
			]
		]);

		$tableInput['user'] = $this->filter([
			'username' => '?str',
			'email' => '?str',
			'user_group_id' => '?uint',
			'secondary_group_ids' => '?array-uint',
			'user_state' => '?str',
			'is_staff' => '?bool',
			'message_count' => '?uint',
			'reaction_score' => '?int',
			'trophy_points' => '?uint',
		]);

		return $tableInput;
	}

	public function mergeUserEditInput(array $first, array $second)
	{
		foreach ($second AS $table => $inputs)
		{
			if (isset($first[$table]))
			{
				$first[$table] = array_replace($first[$table], $inputs);
			}
			else
			{
				$first[$table] = $inputs;
			}
		}

		return $first;
	}

	public function saveEditFormFromTableInput(FormAction $form, \XF\Entity\User $user, array $tableInput)
	{
		if (!isset($tableInput['user']))
		{
			throw new \LogicException("Input must be broken up into tables with top keys of user, option, profile, and privacy");
		}

		$form->basicEntitySave($user, \XF\Util\Arr::filterNull($tableInput['user']));

		if (isset($tableInput['option']))
		{
			$userOptions = $user->getRelationOrDefault('Option');
			$form->setupEntityInput($userOptions, \XF\Util\Arr::filterNull($tableInput['option']));
		}

		if (isset($tableInput['profile']))
		{
			$userProfile = $user->getRelationOrDefault('Profile');
			$form->setupEntityInput($userProfile, \XF\Util\Arr::filterNull($tableInput['profile']));
		}

		if (isset($tableInput['privacy']))
		{
			$userPrivacy = $user->getRelationOrDefault('Privacy');
			$form->setupEntityInput($userPrivacy, \XF\Util\Arr::filterNull($tableInput['privacy']));
		}
	}

	/**
	 * @param \XF\Entity\User $user
	 * @return FormAction
	 *
	 * @api-see self::getAccountEditInput()
	 * @api-see self::getAdminEditInput()
	 * @api-in str $password
	 * @api-in int $dob[day]
	 * @api-in int $dob[month]
	 * @api-in int $dob[year]
	 * @api-in string $custom_fields[<name>]
	 */
	public function userSaveProcessAdmin(\XF\Entity\User $user)
	{
		$form = $this->formAction();

		/** @var \XF\Api\ControllerPlugin\User $userPlugin */
		$userPlugin = $this->plugin('XF:Api:User');

		$accountInput = $this->getAccountEditInput();
		$adminInput = $this->getAdminEditInput();

		$tableInput = $userPlugin->mergeUserEditInput($accountInput, $adminInput);

		if ($user->is_admin)
		{
			// don't let these be changed for admins -- only support it via /me if the current password is known
			unset($tableInput['user']['email']);
		}
		else
		{
			// password change
			$password = $this->filter('password', 'str');
			if (strlen($password))
			{
				/** @var \XF\Entity\UserAuth $auth */
				$auth = $user->getRelationOrDefault('Auth');
				$auth->setPassword($password);
			}
		}

		$user->setOption('admin_edit', true);

		/** @var \XF\Entity\UserProfile $profile */
		$profile = $user->getRelationOrDefault('Profile');
		$profile->setOption('admin_edit', true);

		$userPlugin->saveEditFormFromTableInput($form, $user, $tableInput);

		$dobInput = $this->filter([
			'dob' => [
				'day' => '?uint',
				'month' => '?uint',
				'year' => '?uint'
			]
		]);
		$dob = \XF\Util\Arr::filterNull($dobInput['dob']);
		if ($dob)
		{
			if (!isset($dob['day']) || !isset($dob['month']))
			{
				$profile->error(\XF::phrase('please_enter_valid_date_of_birth'), 'dob');
			}
			else
			{
				$profile->setDob($dob['day'], $dob['month'], isset($dob['year']) ? $dob['year'] : 0);
			}
		}

		$this->customFieldsSaveProcess($form, $profile, true);

		return $form;
	}

	public function customFieldsSaveProcess(
		FormAction $form, \XF\Entity\UserProfile $userProfile, $isAdmin = false, $group = null
	)
	{
		/** @var \XF\CustomField\Set $fieldSet */
		$fieldSet = $userProfile->custom_fields;
		$fieldDefinition = $fieldSet->getDefinitionSet()
			->filterEditable($fieldSet, $isAdmin ? 'admin' : 'user');

		if ($group)
		{
			$fieldDefinition = $fieldDefinition->filterGroup($group);
		}

		$customFields = $this->filter('custom_fields', 'array');
		$customFieldsShown = array_keys($fieldDefinition->getFieldDefinitions());

		// only update fields present in the input to allow partial updates
		foreach ($customFieldsShown AS $k => $fieldName)
		{
			if (!isset($customFields[$fieldName]))
			{
				unset($customFieldsShown[$k]);
			}
		}

		if ($customFieldsShown)
		{
			$form->setup(function() use ($fieldSet, $customFields, $customFieldsShown, $isAdmin)
			{
				$fieldSet->bulkSet($customFields, $customFieldsShown, $isAdmin ? 'admin' : 'user');
			});
		}
	}

	/**
	 * @param \XF\Entity\User $user
	 * @return \XF\Api\Mvc\Reply\ApiResult|\XF\Mvc\Reply\Error
	 *
	 * @api-in file $avatar The uploaded new avatar
	 *
	 * @api-out true $success
	 */
	public function actionUpdateAvatar(\XF\Entity\User $user)
	{
		/** @var \XF\Service\User\Avatar $avatarService */
		$avatarService = $this->service('XF:User\Avatar', $user);

		$upload = $this->request->getFile('avatar', false, false);
		if ($upload)
		{
			if (!$avatarService->setImageFromUpload($upload))
			{
				return $this->error($avatarService->getError());
			}

			if (!$avatarService->updateAvatar())
			{
				return $this->error(\XF::phrase('new_avatar_could_not_be_processed'));
			}
		}

		return $this->apiSuccess();
	}

	/**
	 * @param \XF\Entity\User $user
	 * @return \XF\Api\Mvc\Reply\ApiResult
	 *
	 * @api-out true $success
	 */
	public function actionDeleteAvatar(\XF\Entity\User $user)
	{
		/** @var \XF\Service\User\Avatar $avatarService */
		$avatarService = $this->service('XF:User\Avatar', $user);
		$avatarService->deleteAvatar();

		return $this->apiSuccess();
	}
}