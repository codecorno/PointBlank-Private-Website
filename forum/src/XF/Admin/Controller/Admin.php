<?php

namespace XF\Admin\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\FormAction;

class Admin extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertSuperAdmin();
	}

	public function actionIndex(ParameterBag $params)
	{
		if ($params['user_id'])
		{
			return $this->rerouteController(__CLASS__, 'edit', $params);
		}

		$viewParams = [
			'admins' => $this->getAdminRepo()->findAdminsForList()->fetch()
		];
		return $this->view('XF:Admin\Listing', 'admin_list', $viewParams);
	}

	protected function adminAddEdit(\XF\Entity\Admin $admin)
	{
		$viewParams = [
			'admin' => $admin,
			'permissions' => $this->em()->getRepository('XF:AdminPermission')->getPermissionTitlePairs(),
			'userGroups' => $this->em()->getRepository('XF:UserGroup')->getUserGroupTitlePairs()
		];
		return $this->view('XF:Admin\Edit', 'admin_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$admin = $this->assertAdminExists($params['user_id']);
		return $this->adminAddEdit($admin);
	}

	public function actionAdd()
	{
		$admin = $this->em()->create('XF:Admin');
		return $this->adminAddEdit($admin);
	}

	protected function adminSaveProcess(\XF\Entity\Admin $admin)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'extra_user_group_ids' => 'array-uint',
			'permission_cache' => 'array-str',
			'is_super_admin' => 'bool'
		]);
		$username = $this->filter('username', 'str');
		$password = $this->filter('visitor_password', 'str');

		$form->validate(function(FormAction $form) use ($username, $admin)
		{
			if (!$admin->exists())
			{
				$user = $this->finder('XF:User')
					->where('username', $username)
					->fetchOne();
				if ($user)
				{
					if ($user->is_admin)
					{
						$form->logError(\XF::phrase('specified_user_is_already_administrator'));
					}
					else
					{
						$admin->user_id = $user->user_id;
					}
				}
				else
				{
					$form->logError(\XF::phrase('requested_user_not_found'));
				}
			}
		});
		$form->validate(function(FormAction $form) use ($password)
		{
			if (!\XF::visitor()->authenticate($password))
			{
				$form->logError(\XF::phrase('your_existing_password_is_not_correct'), 'password');
			}
		});
		$form->validate(function(FormAction $form) use ($admin, $input)
		{
			if ($admin->user_id === \XF::visitor()->user_id
				&& $input['is_super_admin'] === false
			)
			{
				$form->logError(\XF::phrase('you_cannot_demote_yourself_from_being_super_admin'), 'is_super_admin');
			}
		});
		$form->basicEntitySave($admin, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['user_id'])
		{
			$admin = $this->assertAdminExists($params['user_id']);
		}
		else
		{
			$admin = $this->em()->create('XF:Admin');
		}

		$this->adminSaveProcess($admin)->run();

		return $this->redirect($this->buildLink('admins'));
	}

	public function actionDelete(ParameterBag $params)
	{
		$admin = $this->assertAdminExists($params['user_id']);
		if ($this->isPost())
		{
			if (!\XF::visitor()->authenticate($this->filter('visitor_password', 'str')))
			{
				return $this->error(\XF::phrase('your_existing_password_is_not_correct'));
			}

			$admin->delete();

			return $this->redirect($this->buildLink('admins'));
		}
		else
		{
			$viewParams = [
				'admin' => $admin
			];
			return $this->view('XF:Admin\Delete', 'admin_delete', $viewParams);
		}
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Admin
	 */
	protected function assertAdminExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:Admin', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Admin
	 */
	protected function getAdminRepo()
	{
		return $this->repository('XF:Admin');
	}
}