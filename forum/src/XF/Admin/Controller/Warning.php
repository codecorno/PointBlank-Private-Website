<?php

namespace XF\Admin\Controller;

use XF\Mvc\Entity\Finder;
use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class Warning extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('warning');
	}

	public function actionIndex(ParameterBag $params)
	{
		$warningRepo = $this->getWarningRepo();

		$viewParams = [
			'warnings' => $warningRepo->findWarningDefinitionsForList()->fetch(),
			'actions' => $warningRepo->findWarningActionsForList()->fetch()
		];
		return $this->view('XF:Warning\Listing', 'warning_list', $viewParams);
	}
	
	public function warningAddEdit(\XF\Entity\WarningDefinition $warning)
	{
		$viewParams = [
			'warning' => $warning,
			'userGroups' => $this->repository('XF:UserGroup')->getUserGroupTitlePairs()
		];
		return $this->view('XF:Warning\Edit', 'warning_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$warning = $this->assertWarningDefinitionExists($params->warning_definition_id);
		return $this->warningAddEdit($warning);
	}

	public function actionAdd()
	{
		$warning = $this->em()->create('XF:WarningDefinition');
		return $this->warningAddEdit($warning);
	}

	protected function warningSaveProcess(\XF\Entity\WarningDefinition $warning)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'points_default' => 'uint',
			'expiry_type' => 'str',
			'expiry_default' => 'uint',
			'extra_user_group_ids' => 'array-uint',
			'is_editable' => 'bool'
		]);
		if ($this->filter('expiry_type_base', 'str') == 'never')
		{
			$input['expiry_type'] = 'never';
		}
		$form->basicEntitySave($warning, $input);

		$phraseInput = $this->filter([
			'title' => 'str',
			'conversation_title' => 'str',
			'conversation_text' => 'str',
		]);
		$form->validate(function(FormAction $form) use ($phraseInput)
		{
			if ($phraseInput['title'] === '')
			{
				$form->logError(\XF::phrase('please_enter_valid_title'), 'title');
			}
		});
		$form->apply(function() use ($phraseInput, $warning)
		{
			$masterTitle = $warning->getRelationOrDefault('MasterTitle', false);
			$masterTitle->addon_id = '';
			$masterTitle->phrase_text = $phraseInput['title'];
			$masterTitle->save();

			$masterConversationTitle = $warning->getRelationOrDefault('MasterConversationTitle', false);
			$masterConversationTitle->addon_id = '';
			$masterConversationTitle->phrase_text = $phraseInput['conversation_title'];
			$masterConversationTitle->save();

			$masterConversationTitle = $warning->getRelationOrDefault('MasterConversationText', false);
			$masterConversationTitle->addon_id = '';
			$masterConversationTitle->phrase_text = $phraseInput['conversation_text'];
			$masterConversationTitle->save();
		});

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->warning_definition_id)
		{
			$warning = $this->assertWarningDefinitionExists($params->warning_definition_id);
		}
		else
		{
			$warning = $this->em()->create('XF:WarningDefinition');
		}

		$this->warningSaveProcess($warning)->run();

		return $this->redirect($this->buildLink('warnings'));
	}

	public function actionDelete(ParameterBag $params)
	{
		$warning = $this->assertWarningDefinitionExists($params->warning_definition_id);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$warning,
			$this->buildLink('warnings/delete', $warning),
			$this->buildLink('warnings/edit', $warning),
			$this->buildLink('warnings'),
			$warning->title
		);
	}

	// underscore prefix to not be confused with actual controller actions
	public function _actionAddEdit(\XF\Entity\WarningAction $action)
	{
		$viewParams = [
			'action' => $action,
			'userGroups' => $this->repository('XF:UserGroup')->getUserGroupTitlePairs()
		];
		return $this->view('XF:Warning\Action\Edit', 'warning_action_edit', $viewParams);
	}

	public function actionActionEdit(ParameterBag $params)
	{
		$action = $this->assertWarningActionExists($params->warning_action_id);
		return $this->_actionAddEdit($action);
	}

	public function actionActionAdd()
	{
		$action = $this->em()->create('XF:WarningAction');
		return $this->_actionAddEdit($action);
	}

	// underscore prefix to not be confused with actual controller actions
	protected function _actionSaveProcess(\XF\Entity\WarningAction $action)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'points' => 'uint',
			'action' => 'str',
			'extra_user_group_ids' => 'array-uint',
		]);

		$actionTypeBase = $this->filter('action_length_type_base', 'str');
		if ($actionTypeBase == 'temporary')
		{
			$input['action_length_type'] = $this->filter('action_length_type', 'str');
			$input['action_length'] = $this->filter('action_length', 'uint');
		}
		else
		{
			$input['action_length_type'] = $actionTypeBase;
			$input['action_length'] = 0;
		}

		$form->basicEntitySave($action, $input);

		return $form;
	}

	public function actionActionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->warning_action_id)
		{
			$action = $this->assertWarningActionExists($params->warning_action_id);
		}
		else
		{
			$action = $this->em()->create('XF:WarningAction');
		}

		$this->_actionSaveProcess($action)->run();

		return $this->redirect($this->buildLink('warnings'));
	}

	public function actionActionDelete(ParameterBag $params)
	{
		$action = $this->assertWarningActionExists($params->warning_action_id);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$action,
			$this->buildLink('warnings/actions/delete', $action),
			$this->buildLink('warnings/actions/edit', $action),
			$this->buildLink('warnings/actions'),
			$action->title
		);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\WarningDefinition
	 */
	protected function assertWarningDefinitionExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:WarningDefinition', $id, $with, $phraseKey);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\WarningAction
	 */
	protected function assertWarningActionExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:WarningAction', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Warning
	 */
	protected function getWarningRepo()
	{
		return $this->repository('XF:Warning');
	}
}