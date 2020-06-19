<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class ApiKey extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertSuperAdmin();
		$this->assertPasswordVerified(1800); // 30 minutes
	}

	public function actionIndex()
	{
		$repo = $this->getApiRepo();
		$apiKeys = $repo->findApiKeysForList()->fetch();

		$newKeyId = $this->filter('new_key_id', 'uint');
		if ($newKeyId)
		{
			$newKey = $this->em()->find('XF:ApiKey', $newKeyId);
		}
		else
		{
			$newKey = null;
		}

		$viewParams = [
			'apiKeys' => $apiKeys,
			'newKey' => $newKey
		];
		return $this->view('XF:ApiKey\List', 'api_key_list', $viewParams);
	}

	protected function apiKeyAddEdit(\XF\Entity\ApiKey $apiKey)
	{
		$viewParams = [
			'apiKey' => $apiKey,
			'scopes' => $this->getApiRepo()->findApiScopesForList()->fetch()
		];
		return $this->view('XF:ApiKey\Edit', 'api_key_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$apiKey = $this->assertApiKeyExists($params->api_key_id, ['User', 'Creator']);
		return $this->apiKeyAddEdit($apiKey);
	}

	public function actionAdd()
	{
		$apiKey = $this->em()->create('XF:ApiKey');
		return $this->apiKeyAddEdit($apiKey);
	}

	protected function apiKeySaveProcess(\XF\Service\ApiKey\Manager $keyManager)
	{
		$form = $this->formAction();

		$form->basicValidateServiceSave($keyManager, function() use ($keyManager)
		{
			$input = $this->filter([
				'title' => 'str',
				'active' => 'bool',
				'key_type' => 'str',
				'username' => 'str',
				'allow_all_scopes' => 'bool',
				'scopes' => 'array-str',
			]);

			$keyManager->setTitle($input['title']);
			$keyManager->setActive($input['active']);
			$keyManager->setScopes($input['allow_all_scopes'], $input['scopes']);
			$keyManager->setKeyType($input['key_type'], $input['username']);
		});

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->api_key_id)
		{
			$apiKey = $this->assertApiKeyExists($params->api_key_id);
			$newKey = false;
		}
		else
		{
			$apiKey = $this->em()->create('XF:ApiKey');
			$newKey = true;
		}

		/** @var \XF\Service\ApiKey\Manager $keyManager */
		$keyManager = $this->service('XF:ApiKey\Manager', $apiKey);

		$this->apiKeySaveProcess($keyManager)->run();

		if ($newKey)
		{
			$params = ['new_key_id' => $apiKey->api_key_id];
		}
		else
		{
			$params = [];
		}

		return $this->redirect($this->buildLink('api-keys', null, $params));
	}

	public function actionRegenerate(ParameterBag $params)
	{
		$apiKey = $this->assertApiKeyExists($params->api_key_id);

		if ($this->isPost())
		{
			/** @var \XF\Service\ApiKey\Manager $keyManager */
			$keyManager = $this->service('XF:ApiKey\Manager', $apiKey);
			$keyManager->regenerate();

			if (!$keyManager->validate($errors))
			{
				return $this->error($errors);
			}

			$keyManager->save();

			return $this->redirect($this->buildLink('api-keys', null, ['new_key_id' => $apiKey->api_key_id]));
		}
		else
		{
			$viewParams = [
				'apiKey' => $apiKey
			];
			return $this->view('XF:ApiKey\Regenerate', 'api_key_regenerate', $viewParams);
		}
	}

	public function actionDelete(ParameterBag $params)
	{
		$apiKey = $this->assertApiKeyExists($params->api_key_id);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$apiKey,
			$this->buildLink('api-keys/delete', $apiKey),
			$this->buildLink('api-keys/edit', $apiKey),
			$this->buildLink('api-keys'),
			$apiKey->title
		);
	}

	public function actionViewKey(ParameterBag $params)
	{
		$apiKey = $this->assertApiKeyExists($params->api_key_id);

		$viewParams = [
			'apiKey' => $apiKey
		];
		return $this->view('XF:ApiKey\View', 'api_key_view', $viewParams);
	}

	public function actionToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		return $plugin->actionToggle('XF:ApiKey');
	}

	/**
	 * @param string $apiKeyId
	 * @param null|string|array $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\ApiKey
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertApiKeyExists($apiKeyId, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:ApiKey', $apiKeyId, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Api
	 */
	protected function getApiRepo()
	{
		return $this->repository('XF:Api');
	}
}