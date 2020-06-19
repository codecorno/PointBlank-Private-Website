<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class UserGroup extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('userGroup');
	}

	public function actionIndex()
	{
		$viewParams = [
			'userGroups' => $this->getUserGroupRepo()->findUserGroupsForList()->fetch(),
		];
		return $this->view('XF:UserGroup\Listing', 'user_group_list', $viewParams);
	}

	protected function userGroupAddEdit(\XF\Entity\UserGroup $userGroup)
	{
		$displayStyles = [
			'userBanner userBanner--hidden',
			'userBanner userBanner--primary',
			'userBanner userBanner--accent',
			'userBanner userBanner--red',
			'userBanner userBanner--green',
			'userBanner userBanner--olive',
			'userBanner userBanner--lightGreen',
			'userBanner userBanner--blue',
			'userBanner userBanner--royalBlue',
			'userBanner userBanner--skyBlue',
			'userBanner userBanner--gray',
			'userBanner userBanner--silver',
			'userBanner userBanner--yellow',
			'userBanner userBanner--orange',
		];

		/** @var \XF\Repository\Permission $permissionRepo */
		$permissionRepo = $this->repository('XF:Permission');
		$permissionData = $permissionRepo->getGlobalPermissionListData();

		/** @var \XF\Repository\PermissionEntry $entryRepo */
		$entryRepo = $this->repository('XF:PermissionEntry');
		$permissionData['values'] = $entryRepo->getGlobalUserGroupPermissionEntries($userGroup->user_group_id);

		$viewParams = [
			'userGroup' => $userGroup,
			'displayStyles' => $displayStyles,

			'permissionData' => $permissionData
		];
		return $this->view('XF:UserGroup\Edit', 'user_group_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$userGroup = $this->assertUserGroupExists($params->user_group_id);
		return $this->userGroupAddEdit($userGroup);
	}

	public function actionAdd()
	{
		$userGroup = $this->em()->create('XF:UserGroup');
		return $this->userGroupAddEdit($userGroup);
	}

	protected function userGroupSaveProcess(\XF\Entity\UserGroup $userGroup)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'title' => 'str',
			'display_style_priority' => 'uint',
			'username_css' => 'str',
			'banner_css_class' => 'str',
			'banner_text' => 'str'
		]);

		$input['user_title'] = $this->filter('user_title_override', 'bool')
			? $this->filter('user_title', 'str')
			: '';

		if (!$input['banner_css_class'])
		{
			$input['banner_css_class'] = $this->filter('banner_css_class_other', 'str');
		}

		$form->basicEntitySave($userGroup, $input);

		/** @var \XF\Service\UpdatePermissions $permissionUpdater */
		$permissionUpdater = $this->service('XF:UpdatePermissions');
		$permissions = $this->filter('permissions', 'array');

		$form->apply(function() use ($userGroup, $permissions, $permissionUpdater)
		{
			$permissionUpdater->setUserGroup($userGroup)->setGlobal();
			$permissionUpdater->updatePermissions($permissions);
		});

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->user_group_id)
		{
			$userGroup = $this->assertUserGroupExists($params->user_group_id);
		}
		else
		{
			$userGroup = $this->em()->create('XF:UserGroup');
		}

		$this->userGroupSaveProcess($userGroup)->run();

		return $this->redirect($this->buildLink('user-groups') . $this->buildLinkHash($userGroup->user_group_id));
	}

	public function actionDelete(ParameterBag $params)
	{
		$userGroup = $this->assertUserGroupExists($params->user_group_id);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$userGroup,
			$this->buildLink('user-groups/delete', $userGroup),
			$this->buildLink('user-groups/edit', $userGroup),
			$this->buildLink('user-groups'),
			$userGroup->title
		);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\UserGroup
	 */
	protected function assertUserGroupExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:UserGroup', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\UserGroup
	 */
	protected function getUserGroupRepo()
	{
		return $this->repository('XF:UserGroup');
	}
}