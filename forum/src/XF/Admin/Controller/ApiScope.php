<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class ApiScope extends AbstractController
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
			'scopes' => $this->getApiRepo()->findApiScopesForList()->fetch()
		];
		return $this->view('XF:ApiScope\Listing', 'api_scope_list', $viewParams);
	}

	protected function scopeAddEdit(\XF\Entity\ApiScope $scope)
	{
		$viewParams = [
			'scope' => $scope
		];
		return $this->view('XF:ApiScope\Edit', 'api_scope_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$scope = $this->assertScopeExists($params->api_scope_id_url, 'MasterDescription');
		return $this->scopeAddEdit($scope);
	}

	public function actionAdd()
	{
		$scope = $this->em()->create('XF:ApiScope');
		return $this->scopeAddEdit($scope);
	}

	protected function scopeSaveProcess(\XF\Entity\ApiScope $scope)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'api_scope_id' => 'str',
			'addon_id' => 'str'
		]);
		$form->basicEntitySave($scope, $input);

		$phraseInput = $this->filter([
			'description' => 'str'
		]);
		$form->apply(function() use ($phraseInput, $scope)
		{
			$title = $scope->getMasterPhrase();
			$title->phrase_text = $phraseInput['description'];
			$title->save();
		});

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->api_scope_id_url)
		{
			$scope = $this->assertScopeExists($params->api_scope_id_url);
		}
		else
		{
			$scope = $this->em()->create('XF:ApiScope');
		}

		$this->scopeSaveProcess($scope)->run();

		return $this->redirect($this->buildLink('api-scopes') . $this->buildLinkHash($scope->api_scope_id_url));
	}

	public function actionDelete(ParameterBag $params)
	{
		$scope = $this->assertScopeExists($params->api_scope_id_url);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$scope,
			$this->buildLink('api-scopes/delete', $scope),
			$this->buildLink('api-scopes/edit', $scope),
			$this->buildLink('api-scopes'),
			$scope->api_scope_id
		);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\ApiScope
	 */
	protected function assertScopeExists($id, $with = null, $phraseKey = null)
	{
		$id = str_replace('-', ':', $id);

		return $this->assertRecordExists('XF:ApiScope', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Api
	 */
	protected function getApiRepo()
	{
		return $this->repository('XF:Api');
	}
}