<?php

namespace XF\Admin\Controller;

use XF\Entity\ConnectedAccountProvider;
use XF\Mvc\ParameterBag;

class ConnectedAccount extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('user');
	}

	public function actionIndex()
	{
		$providers = $this->getConnectedAccountRepo()->findProvidersForList()->fetch();

		$activeProviders = $providers->filter(function(ConnectedAccountProvider $provider)
		{
			return $provider->isUsable() === true;
		});
		$inactiveProviders = $providers->filter(function(ConnectedAccountProvider $provider)
		{
			return $provider->isUsable() === false;
		});

		$viewParams = [
			'activeProviders' => $activeProviders,
			'inactiveProviders' => $inactiveProviders
		];
		return $this->view('XF:ConnectedAccount\Listing', 'connected_account_provider_list', $viewParams);
	}

	public function actionTest(ParameterBag $params)
	{
		$provider = $this->assertProviderExists($params->provider_id);
		if (!$provider->canBeTested() || !$provider->isUsable())
		{
			return $this->error(\XF::phrase('it_is_not_possible_to_test_this_provider'));
		}
		$handler = $provider->handler;
		$handler->setTestMode(true);

		$viewParams = [
			'provider' => $provider,
			'handler' => $handler,
			'redirectUri' => $handler->getRedirectUri($provider)
		];
		return $this->view('XF:ConnectedAccount\Test', 'connected_account_provider_test', $viewParams);
	}

	public function actionPerformTest(ParameterBag $params)
	{
		$provider = $this->assertProviderExists($params->provider_id);
		if (!$provider->canBeTested() || !$provider->isUsable())
		{
			return $this->error(\XF::phrase('it_is_not_possible_to_test_this_provider'));
		}
		$handler = $provider->handler;
		$handler->setTestMode(true);

		$storageState = $handler->getStorageState($provider, \XF::visitor());

		if ($this->filter('test', 'bool'))
		{
			$storageState->clearToken();
			return $handler->handleAuthorization($this, $provider, $this->getDynamicRedirect());
		}

		/** @var \XF\Session\Session $session */
		$session = $this->app['session.public'];
		$connectedAccountRequest = $session->get('connectedAccountRequest');

		if (!is_array($connectedAccountRequest) || !isset($connectedAccountRequest['provider']))
		{
			return $this->error(\XF::phrase('there_is_no_valid_connected_account_request_available_at_this_time'));
		}

		if ($connectedAccountRequest['provider'] !== $provider->provider_id)
		{
			$session->remove('connectedAccountRequest');
			$session->save();
			return $this->error(\XF::phrase('stored_account_provider_does_not_match_this_request'));
		}

		if (!$storageState->getProviderToken() || empty($connectedAccountRequest['tokenStored']))
		{
			return $this->error(\XF::phrase('error_occurred_while_connecting_with_x', ['provider' => $provider->title]));
		}

		$providerData = $handler->getProviderData($storageState);

		// Fetch everything from the default endpoint and cache it.
		$providerData->requestFromEndpoint();

		$viewParams = [
			'provider' => $provider,
			'providerData' => $providerData,
			'handler' => $handler
		];
		return $this->view('XF:ConnectedAccount\Test', 'connected_account_provider_test', $viewParams);
	}

	protected function providerAddEdit(ConnectedAccountProvider $provider)
	{
		$viewParams = [
			'provider' => $provider
		];
		return $this->view('XF:ConnectedAccount\Edit', 'connected_account_provider_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$provider = $this->assertProviderExists($params->provider_id);
		return $this->providerAddEdit($provider);
	}

	protected function providerSaveProcess(ConnectedAccountProvider $provider)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'options' => 'array'
		]);
		$form->basicEntitySave($provider, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		$provider = $this->assertProviderExists($params->provider_id);

		$this->providerSaveProcess($provider)->run();

		return $this->redirect($this->buildLink('connected-accounts') . $this->buildLinkHash($provider->provider_id));
	}

	public function actionDeactivate(ParameterBag $params)
	{
		$provider = $this->assertProviderExists($params->provider_id);

		if (!$provider->isUsable())
		{
			return $this->error(\XF::phrase('it_is_not_possible_to_deactivate_this_provider'));
		}

		if ($this->isPost())
		{
			$provider->options = [];
			$provider->save();

			return $this->redirect($this->buildLink('connected-accounts'));
		}
		else
		{
			$viewParams = [
				'provider' => $provider
			];
			return $this->view('XF:ConnectedAccount\Deactivate', 'connected_account_provider_deactivate', $viewParams);
		}
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return ConnectedAccountProvider
	 */
	protected function assertProviderExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:ConnectedAccountProvider', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\ConnectedAccount
	 */
	protected function getConnectedAccountRepo()
	{
		return $this->repository('XF:ConnectedAccount');
	}
}