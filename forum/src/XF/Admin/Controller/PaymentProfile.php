<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class PaymentProfile extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('payment');
	}

	public function actionIndex()
	{
		$paymentRepo = $this->getPaymentRepo();

		$profiles = $paymentRepo->findPaymentProfilesForList()->fetch();
		$providers = $profiles->pluckNamed('Provider', 'provider_id');

		$viewParams = [
			'totalProfiles' => $profiles->count(),
			'groupedProfiles' => $profiles->groupBy('provider_id'),
			'providers' => $providers
		];
		return $this->view('XF:PaymentProfile\Listing', 'payment_profile_list', $viewParams);
	}

	public function profileAddEdit(\XF\Entity\PaymentProfile $profile, \XF\Entity\PaymentProvider $provider)
	{
		$viewParams = [
			'profile' => $profile,
			'provider' => $provider
		];
		return $this->view('XF:PaymentProfile\Edit', 'payment_profile_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$profile = $this->assertProfileExists($params->payment_profile_id);
		return $this->profileAddEdit($profile, $profile->Provider);
	}

	public function actionAdd()
	{
		$providerId = $this->filter('provider_id', 'str');

		if (!$providerId)
		{
			if ($this->isPost())
			{
				return $this->error(\XF::phrase('you_must_select_payment_provider_to_continue'));
			}
			else
			{
				$providers = $this->getPaymentRepo()
					->findPaymentProvidersForList()
					->pluckFrom('title', 'provider_id');

				if (!$providers)
				{
					throw $this->exception(
						$this->notFound(\XF::phrase('you_cannot_create_payment_profile_as_there_no_valid_payment_providers'))
					);
				}

				$viewParams = [
					'providers' => $providers
				];
				return $this->view('XF:PaymentProfile\ChooseProvider', 'payment_profile_choose_provider', $viewParams);
			}
		}

		/** @var \XF\Entity\PaymentProfile $profile */
		$profile = $this->em()->create('XF:PaymentProfile');
		$provider = $this->assertProviderExists($providerId);
		$profile->provider_id = $provider->provider_id;

		if ($this->isPost())
		{
			return $this->redirect(
				$this->buildLink('payment-profiles/add', null, ['provider_id' => $provider->provider_id]), ''
			);
		}

		return $this->profileAddEdit($profile, $provider);
	}

	protected function profileSaveProcess(\XF\Entity\PaymentProfile $profile, \XF\Entity\PaymentProvider $provider)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'provider_id' => 'str',
			'title' => 'str',
			'display_title' => 'str'
		]);

		$options = $this->filter('options', 'array-str');

		$form->validate(function(FormAction $form) use ($profile, $provider, $options)
		{
			$provider->getHandler()->verifyConfig($options, $errors);
			if ($errors)
			{
				$form->logErrors($errors);
			}
			$profile->options = $options;
		});

		$form->basicEntitySave($profile, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->payment_profile_id)
		{
			$profile = $this->assertProfileExists($params->payment_profile_id);
		}
		else
		{
			$profile = $this->em()->create('XF:PaymentProfile');

			$providerId = $this->filter('provider_id', 'str');
			$provider = $this->assertProviderExists($providerId);

			$profile->provider_id = $provider->provider_id;
		}

		$this->profileSaveProcess($profile, $profile->Provider)->run();

		return $this->redirect($this->buildLink('payment-profiles') . $this->buildLinkHash($profile->payment_profile_id));
	}

	public function actionDelete(ParameterBag $params)
	{
		$profile = $this->assertProfileExists($params->payment_profile_id);

		$profileUsed = [];

		$purchasableTypes = $this->finder('XF:Purchasable')->fetch();
		foreach ($purchasableTypes AS $purchasableType)
		{
			/** @var \XF\Purchasable\AbstractPurchasable $handler */
			$handler = $purchasableType->handler;
			if ($handler)
			{
				$purchasableItems = $handler->getPurchasablesByProfileId($profile->payment_profile_id);
				$profileUsed = array_merge($profileUsed, $purchasableItems ?: []);
			}
		}

		if ($this->isPost())
		{
			$profile->active = false;
			$profile->save();

			return $this->redirect($this->buildLink('payment-profiles'));
		}
		else
		{
			$viewParams = [
				'profile' => $profile,
				'profileUsed' => $profileUsed
			];
			return $this->view('XF:PaymentProfile\Delete', 'payment_profile_delete', $viewParams);
		}
	}

	public function actionToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		return $plugin->actionToggle('XF:PaymentProfile');
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\PaymentProvider
	 */
	protected function assertProviderExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:PaymentProvider', $id, $with, $phraseKey);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\PaymentProfile
	 */
	protected function assertProfileExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:PaymentProfile', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Payment
	 */
	protected function getPaymentRepo()
	{
		return $this->repository('XF:Payment');
	}
}