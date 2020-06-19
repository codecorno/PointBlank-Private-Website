<?php

namespace XF\Admin\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\FormAction;

class Moderator extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('user');
	}

	public function actionIndex(ParameterBag $params)
	{
		$modRepo = $this->getModRepo();
		$handlers = $modRepo->getModeratorHandlers();

		$superModerators = $modRepo->findModeratorsForList(true)->fetch();

		$contentModerators = $modRepo->findContentModeratorsForList()->fetch();
		$contentModerators = $contentModerators->filter(function(\XF\Entity\ModeratorContent $moderatorContent) use ($handlers)
		{
			return isset($handlers[$moderatorContent->content_type]);
		});

		$users = $contentModerators->pluckNamed('User', 'user_id');

		$viewParams = [
			'superModerators' => $superModerators,
			'contentModerators' => $contentModerators->groupBy('user_id'),
			'users' => $users
		];
		return $this->view('XF:Moderator\Listing', 'moderator_list', $viewParams);
	}

	protected function moderatorAddEdit(
		\XF\Entity\Moderator $generalModerator,
		\XF\Entity\ModeratorContent $contentModerator = null
	)
	{
		/** @var \XF\Repository\PermissionEntry $permissionEntryRepo */
		$permissionEntryRepo = $this->repository('XF:PermissionEntry');

		$modRepo = $this->getModRepo();

		$existingPermissionValues = $permissionEntryRepo->getGlobalUserPermissionEntries($generalModerator->user_id);

		if ($contentModerator)
		{
			$moderatorHandler = $modRepo->getModeratorHandler($contentModerator->content_type);
			if (!$moderatorHandler)
			{
				return $this->error(\XF::phrase('this_content_moderator_relates_to_unknown_content_type'));
			}

			$contentTitle = $moderatorHandler->getContentTitle($contentModerator->content_id);

			$contentPermissionValues = $permissionEntryRepo->getContentUserPermissionEntries(
				$contentModerator->content_type,
				$contentModerator->content_id,
				$contentModerator->user_id
			);
			$existingPermissionValues = \XF\Util\Arr::mapMerge($existingPermissionValues, $contentPermissionValues);
		}
		else
		{
			$contentTitle = '';
		}

		$user = $generalModerator->User;

		$moderatorPermissionData = $modRepo->getModeratorPermissionData(
			$contentModerator ? $contentModerator->content_type : null
		);

		$viewParams = [
			'user' => $user,
			'generalModerator' => $generalModerator,
			'contentModerator' => $contentModerator,

			'contentTitle' => $contentTitle,
			'isStaff' => $generalModerator->exists() ? $user->is_staff : true,

			'existingValues' => $existingPermissionValues,
			'allowValues' => ['allow', 'content_allow'],

			'interfaceGroups' => $moderatorPermissionData['interfaceGroups'],
			'globalPermissions' => $moderatorPermissionData['globalPermissions'],
			'contentPermissions' => $moderatorPermissionData['contentPermissions'],

			'userGroups' => $this->em()->getRepository('XF:UserGroup')->getUserGroupTitlePairs()
		];

		return $this->view('XF:Moderator\Edit', 'moderator_edit', $viewParams);
	}

	public function actionContentEdit(ParameterBag $params)
	{
		$contentModerator = $this->assertContentModeratorExists($params['moderator_id']);
		$generalModerator = $this->assertGeneralModeratorExists($contentModerator->user_id);
		return $this->moderatorAddEdit($generalModerator, $contentModerator);
	}

	public function actionSuperEdit(ParameterBag $params)
	{
		$generalModerator = $this->assertGeneralModeratorExists($params['user_id']);
		return $this->moderatorAddEdit($generalModerator, null);
	}

	public function actionAdd()
	{
		$input = $this->filter([
			'username' => 'str',
			'type' => 'str',
			'type_id' => 'array-uint'
		]);

		if ($input['username'] === '' || $input['type'] === '')
		{
			$viewParams = [
				'typeHandlers' => $this->app->getContentTypeField('moderator_handler_class'),
				'type' => $input['type'],
				'typeId' => $input['type_id']
			];
			return $this->view('XF:Moderator\AddChoice', 'moderator_add_choice', $viewParams);
		}

		$user = $this->finder('XF:User')->where('username', $input['username'])->fetchOne();
		if (!$user)
		{
			return $this->error(\XF::phrase('requested_user_not_found'));
		}

		$generalModerator = $this->em()->find('XF:Moderator', $user->user_id);
		if (!$generalModerator)
		{
			$generalModerator = $this->em()->create('XF:Moderator');
			$generalModerator->user_id = $user->user_id;
			$generalModerator->is_super_moderator = ($input['type'] == '_super');
		}

		if ($input['type'] != '_super')
		{
			$handler = $this->getModRepo()->getModeratorHandler($input['type']);
			if (!$handler)
			{
				return $this->error(\XF::phrase('please_choose_valid_moderator_type'), 404);
			}

			$contentId = isset($input['type_id'][$input['type']]) ? $input['type_id'][$input['type']] : 0;
			if (!$handler->getContentTitle($contentId))
			{
				return $this->error(\XF::phrase('please_select_a_valid_type_of_moderator'), 404);
			}

			$contentModerator = $this->finder('XF:ModeratorContent')
				->where([
						'content_type' => $input['type'],
						'content_id' => $contentId,
						'user_id' => $user->user_id
				])
				->fetchOne();

			if (!$contentModerator)
			{
				$contentModerator = $this->em()->create('XF:ModeratorContent');

				$contentModerator->content_type = $input['type'];
				$contentModerator->content_id = $contentId;
				$contentModerator->user_id = $user->user_id;
			}
		}
		else
		{
			$contentModerator = null;
		}

		return $this->moderatorAddEdit($generalModerator, $contentModerator);
	}

	protected function moderatorSaveProcess(
		\XF\Entity\Moderator $generalModerator,
		\XF\Entity\ModeratorContent $contentModerator = null
	)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'extra_user_group_ids' => 'array-uint',
			'globalPermissions' => 'array',
			'contentPermissions' => 'array',
			'is_staff' => 'bool'
		]);

		$user = $generalModerator->User;

		$form->basicEntitySave($user, [
			'is_staff' => $input['is_staff']
		]);

		/** @var \XF\Service\UpdatePermissions $permissionUpdater */
		$permissionUpdater = $this->service('XF:UpdatePermissions');
		$permissionUpdater->setUser($user);

		$form->basicEntitySave($generalModerator, [
			'extra_user_group_ids' => $input['extra_user_group_ids']
		]);
		$form->apply(function() use ($permissionUpdater, $input)
		{
			$permissionUpdater->setGlobal();
			$permissionUpdater->updatePermissions($input['globalPermissions']);
		});

		if ($contentModerator)
		{
			// need to get this saved, even though it has been configured already
			$form->basicEntitySave($contentModerator, []);

			$form->complete(function() use ($permissionUpdater, $contentModerator, $input)
			{
				$permissionUpdater->setContent($contentModerator->content_type, $contentModerator->content_id);
				$permissionUpdater->updatePermissions($input['contentPermissions']);
			});
		}

		// TODO: the permissions are actually rebuilt twice with this method

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		$findInput = $this->filter([
			'user_id' => 'uint',
			'content_type' => 'str',
			'content_id' => 'uint'
		]);

		$user = $this->assertRecordExists('XF:User', $findInput['user_id']);

		$generalModerator = $this->em()->find('XF:Moderator', $user->user_id);
		if (!$generalModerator)
		{
			$generalModerator = $this->em()->create('XF:Moderator');
			$generalModerator->user_id = $user->user_id;
		}

		$contentModerator = null;
		if ($findInput['content_type'] && $findInput['content_id'])
		{
			$contentModerator = $this->finder('XF:ModeratorContent')->where($findInput)->fetchOne();
			if (!$contentModerator)
			{
				$contentModerator = $this->em()->create('XF:ModeratorContent');
				$contentModerator->bulkSet($findInput);
			}
		}

		if (!$contentModerator)
		{
			$generalModerator->is_super_moderator = true;
		}

		$this->moderatorSaveProcess($generalModerator, $contentModerator)->run();

		return $this->redirect($this->buildLink('moderators'));
	}

	public function actionSuperDelete(ParameterBag $params)
	{
		$generalModerator = $this->assertGeneralModeratorExists($params['user_id']);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$generalModerator,
			$this->buildLink('moderators/super/delete', $generalModerator),
			$this->buildLink('moderators/super/edit', $generalModerator),
			$this->buildLink('moderators'),
			$generalModerator->User->username,
			'moderator_super_delete'
		);
	}

	public function actionContentDelete(ParameterBag $params)
	{
		$contentModerator = $this->assertContentModeratorExists($params['moderator_id']);
		$handler = $this->getModRepo()->getModeratorHandler($contentModerator->content_type);
		$contentTitle = $handler->getContentTitle($contentModerator->content_id);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$contentModerator,
			$this->buildLink('moderators/content/delete', $contentModerator),
			$this->buildLink('moderators/content/edit', $contentModerator),
			$this->buildLink('moderators'),
			sprintf("%s %s%s%s", 
				$contentModerator->User->username, \XF::language()->parenthesis_open, $contentTitle, \XF::language()->parenthesis_close
			)
		);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Moderator
	 */
	protected function assertGeneralModeratorExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:Moderator', $id, $with, $phraseKey);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\ModeratorContent
	 */
	protected function assertContentModeratorExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:ModeratorContent', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Moderator
	 */
	protected function getModRepo()
	{
		return $this->repository('XF:Moderator');
	}
}