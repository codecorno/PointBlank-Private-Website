<?php

namespace XF\Admin\Controller;

use XF\Entity\TfaProvider;
use XF\Mvc\Entity\Entity;
use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class TwoStep extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('user');
	}

	public function actionIndex()
	{
		$providers = $this->getTfaRepo()->findProvidersForList()->fetch();

		$activeProviders = $providers->filter(function(TfaProvider $provider)
		{
			return $provider->isValid() === true;
		});
		$inactiveProviders = $providers->filter(function(TfaProvider $provider)
		{
			return $provider->isValid() === false;
		});

		$viewParams = [
			'activeProviders' => $activeProviders,
			'inactiveProviders' => $inactiveProviders
		];
		return $this->view('XF:TwoStep\Listing', 'two_step_provider_list', $viewParams);
	}

	protected function providerAddEdit(TfaProvider $provider)
	{
		$viewParams = [
			'provider' => $provider
		];
		return $this->view('XF:TwoStep\Edit', 'two_step_provider_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$provider = $this->assertProviderExists($params->provider_id);
		return $this->providerAddEdit($provider);
	}

	protected function providerSaveProcess(TfaProvider $provider)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'priority' => 'uint'
		]);

		$form->validate(function(FormAction $form) use ($provider)
		{
			$options = $this->filter('options', 'array');
			$request = new \XF\Http\Request($this->app->inputFilterer(), $options, [], []);
			$handler = $provider->getHandler();
			if ($handler && !$handler->verifyOptions($request, $options, $error))
			{
				$form->logError($error);
			}
			$provider->options = $options;
		});

		$form->basicEntitySave($provider, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		$provider = $this->assertProviderExists($params->provider_id);

		$this->providerSaveProcess($provider)->run();

		return $this->redirect($this->buildLink('two-step') . $this->buildLinkHash($provider->provider_id));
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return Entity|TfaProvider
	 */
	protected function assertProviderExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:TfaProvider', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Mvc\Entity\Repository|\XF\Repository\Tfa
	 */
	protected function getTfaRepo()
	{
		return $this->repository('XF:Tfa');
	}
}