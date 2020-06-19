<?php

namespace XF\Job;

class UserAction extends AbstractUserCriteriaJob
{
	protected $defaultData = [
		'actions' => []
	];

	protected function executeAction(\XF\Entity\User $user)
	{
		if ($user->is_super_admin)
		{
			return; // no updating of super admins
		}

		if ($this->getActionValue('delete'))
		{
			if (!$user->is_admin && !$user->is_moderator)
			{
				$user->delete(false, false);
			}
			return; // no further action required
		}

		$this->applyInternalUserChange($user);
		$user->save(false, false);

		$this->applyExternalUserChange($user);
	}

	protected function getActionDescription()
	{
		$actionPhrase = \XF::phrase('updating');
		$typePhrase = \XF::phrase('users');

		return sprintf('%s... %s', $actionPhrase, $typePhrase);
	}

	protected function getActionValue($action)
	{
		if (!empty($this->data['actions'][$action]))
		{
			return $this->data['actions'][$action];
		}
		else
		{
			return null;
		}
	}

	protected function applyInternalUserChange(\XF\Entity\User $user)
	{
		if ($setPrimaryGroupId = $this->getActionValue('set_primary_group_id'))
		{
			$user->user_group_id = $setPrimaryGroupId;
		}

		$groups = $user->secondary_group_ids;
		$changeSecondaryGroups = false;
		if ($addGroupId = $this->getActionValue('add_group_id'))
		{
			$groups[] = $addGroupId;
			$changeSecondaryGroups = true;
		}
		if ($removeGroupId = $this->getActionValue('remove_group_id'))
		{
			$key = array_search(intval($removeGroupId), $groups);
			if ($key !== false)
			{
				unset($groups[$key]);
				$changeSecondaryGroups = true;
			}
		}
		if ($changeSecondaryGroups)
		{
			$user->secondary_group_ids = $groups;
		}

		/** @var \XF\Entity\UserOption $option */
		$option = $user->getRelationOrDefault('Option', false);

		if ($this->getActionValue('discourage'))
		{
			$option->is_discouraged = true;
			$user->addCascadedSave($option);
		}
		if ($this->getActionValue('undiscourage'))
		{
			$option->is_discouraged = false;
			$user->addCascadedSave($option);
		}

		/** @var \XF\Entity\UserProfile $profile */
		$profile = $user->getRelationOrDefault('Profile', false);
		if ($this->getActionValue('remove_signature'))
		{
			$profile->signature = '';
			$user->addCascadedSave($profile);
		}
		if ($this->getActionValue('remove_website'))
		{
			$profile->website = '';
			$user->addCascadedSave($profile);
		}

		if ($customTitle = $this->getActionValue('custom_title'))
		{
			$user->custom_title = $customTitle;
		}
	}

	protected function applyExternalUserChange(\XF\Entity\User $user)
	{
		if ($this->getActionValue('remove_avatar'))
		{
			/** @var \XF\Service\User\Avatar $avatarService */
			$avatarService = $this->app->service('XF:User\Avatar', $user);
			$avatarService->deleteAvatar();
		}

		if ($this->getActionValue('ban') && !$user->is_admin && !$user->is_moderator)
		{
			/** @var \XF\Repository\Banning $banRepo */
			$banRepo = $this->app->repository('XF:Banning');
			$banRepo->banUser($user, 0, '');
		}

		if ($this->getActionValue('unban') && $ban = $user->Ban)
		{
			$ban->delete(false, false);
		}
	}

	public function canCancel()
	{
		return true;
	}

	public function canTriggerByChoice()
	{
		return false;
	}
}