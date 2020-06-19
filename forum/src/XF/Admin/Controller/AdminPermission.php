<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class AdminPermission extends AbstractController
{
	/**
	 * @param $action
	 * @param ParameterBag $params
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertDevelopmentMode();
	}

	public function actionIndex()
	{
		$viewParams = [
			'permissions' => $this->getPermissionRepo()->findPermissionsForList()->fetch()
		];
		return $this->view('XF:AdminPermission\Listing', 'admin_permission_list', $viewParams);
	}

	protected function permissionAddEdit(\XF\Entity\AdminPermission $permission)
	{
		$viewParams = [
			'permission' => $permission
		];
		return $this->view('XF:AdminPermission\Edit', 'admin_permission_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$permission = $this->assertPermissionExists($params['admin_permission_id']);
		return $this->permissionAddEdit($permission);
	}

	public function actionAdd()
	{
		$permission = $this->em()->create('XF:AdminPermission');
		return $this->permissionAddEdit($permission);
	}

	protected function permissionSaveProcess(\XF\Entity\AdminPermission $permission)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'admin_permission_id' => 'str',
			'display_order' => 'uint',
			'addon_id' => 'str'
		]);
		$form->basicEntitySave($permission, $input);

		$phraseInput = $this->filter([
			'title' => 'str'
		]);
		$form->validate(function(FormAction $form) use ($phraseInput)
		{
			if ($phraseInput['title'] === '')
			{
				$form->logError(\XF::phrase('please_enter_valid_title'), 'title');
			}
		});
		$form->apply(function() use ($phraseInput, $permission)
		{
			$title = $permission->getMasterPhrase();
			$title->phrase_text = $phraseInput['title'];
			$title->save();
		});

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['admin_permission_id'])
		{
			$permission = $this->assertPermissionExists($params['admin_permission_id']);
		}
		else
		{
			$permission = $this->em()->create('XF:AdminPermission');
		}

		$this->permissionSaveProcess($permission)->run();

		return $this->redirect($this->buildLink('admin-permissions'));
	}

	public function actionDelete(ParameterBag $params)
	{
		$permission = $this->assertPermissionExists($params['admin_permission_id']);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$permission,
			$this->buildLink('admin-permissions/delete', $permission),
			$this->buildLink('admin-permissions/edit', $permission),
			$this->buildLink('admin-permissions'),
			$permission->title
		);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\AdminPermission
	 */
	protected function assertPermissionExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:AdminPermission', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\AdminPermission
	 */
	protected function getPermissionRepo()
	{
		return $this->repository('XF:AdminPermission');
	}
}