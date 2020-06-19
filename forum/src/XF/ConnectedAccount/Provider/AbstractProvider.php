<?php

namespace XF\ConnectedAccount\Provider;

use XF\Entity\ConnectedAccountProvider;
use XF\Entity\User;
use XF\ConnectedAccount\Http\HttpResponseException;
use XF\ConnectedAccount\ProviderData\AbstractProviderData;
use XF\ConnectedAccount\Storage\StorageState;
use XF\Http\Request;
use XF\Mvc\Controller;

abstract class AbstractProvider
{
	protected $providerId;

	protected $oAuthVersion = 2;

	protected $testMode = false;

	/**
	 * Represents the name of the OAuth service.
	 *
	 * This can be given as a class name if there isn't a service already defined within the OAuth library.
	 * e.g. '\XF\ConnectedAccount\Service\ProviderId' or 'XF:Service\ProviderId'
	 * 
	 * You can check if a service already exists by looking in 'src/vendor/lusitanian/oauth/src/OAuth/OAuth(1|2)\Service'
	 *
	 * @return string
	 */
	abstract public function getOAuthServiceName();
	
	abstract public function getDefaultOptions();

	abstract public function getOAuthConfig(ConnectedAccountProvider $provider, $redirectUri = null);

	public function __construct($providerId)
	{
		$this->providerId = $providerId;
	}

	public function getProviderDataClass()
	{
		return 'XF:ProviderData\\' . $this->getOAuthServiceName();
	}

	public function setTestMode($testMode)
	{
		$this->testMode = $testMode;
	}

	public function isUsable(ConnectedAccountProvider $provider)
	{
		if (!$provider->options)
		{
			return false;
		}

		return $this->isConfigured($this->getEffectiveOptions($provider->options));
	}

	protected function isConfigured(array $options)
	{
		foreach ($options AS $key => $value)
		{
			if (is_string($value) && trim($value) === '')
			{
				return false;
			}
		}

		return true;
	}

	public function isValidForRegistration()
	{
		return true;
	}

	public function canBeTested()
	{
		return true;
	}

	public function getTitle()
	{
		return \XF::phrase('con_acc.' . $this->providerId);
	}

	public function getDescription()
	{
		return \XF::phrase('con_acc_desc.' . $this->providerId);
	}

	public function getIconUrl()
	{
		return null;
	}

	public function getRedirectUri(ConnectedAccountProvider $provider)
	{
		return \XF::app()->options()->boardUrl . '/connected_account.php';
	}

	public function handleAuthorization(Controller $controller, ConnectedAccountProvider $provider, $returnUrl)
	{
		$config = $this->getOAuthConfig($provider);
		$oAuth = $this->getOAuth($config);

		$additionalAuthParams = [];
		if ($this->getOAuthVersion() === 1)
		{
			try
			{
				/** @var \OAuth\OAuth1\Service\AbstractService $oAuth */
				$requestToken = $oAuth->requestRequestToken();
				$additionalAuthParams['oauth_token'] = $requestToken->getRequestToken();
			}
			catch (HttpResponseException $e)
			{
				$error = \XF::phraseDeferred('error_occurred_while_connecting_with_x', ['provider' => $this->getTitle()]);
				if ($this->testMode && $e->getResponseContent())
				{
					$this->parseProviderError($e, $error);
				}
				throw $controller->exception($controller->error($error));
			}
		}

		/** @var \XF\Session\Session $session */
		$session = \XF::app()['session.public'];

		$session->set('connectedAccountRequest', [
			'provider' => $this->providerId,
			'returnUrl' => $returnUrl,
			'test' => $this->testMode
		]);
		$session->save();

		return $controller->redirect($oAuth->getAuthorizationUri($additionalAuthParams));
	}

	public function getOAuthVersion()
	{
		return $this->oAuthVersion;
	}

	public function getOAuth(array $config)
	{
		$config = array_replace([
			'storageType' => null
		], $config);

		$provider = \XF::app()->oAuth()->provider($this->getOAuthServiceName(), $config);
		if (!$provider)
		{
			throw new \InvalidArgumentException(
				"Cannot find a valid OAuth Service for provider '{$this->getOAuthServiceName()}'"
			);
		}
		return $provider;
	}

	/**
	 * @param StorageState $storageState
	 *
	 * @return \XF\ConnectedAccount\ProviderData\AbstractProviderData
	 */
	public function getProviderData(StorageState $storageState)
	{
		$providerData = \XF::app()->oAuth()->providerData($this->getProviderDataClass(), $this->providerId, $storageState);
		if (!$providerData)
		{
			throw new \InvalidArgumentException(
				"Cannot find a valid ProviderData object for class '{$this->getProviderDataClass()}'"
			);
		}
		return $providerData;
	}

	/**
	 * @param ConnectedAccountProvider $provider
	 * @param User $user
	 *
	 * @return StorageState
	 */
	public function getStorageState(ConnectedAccountProvider $provider, User $user)
	{
		return \XF::app()->oAuth()->storageState($provider, $user);
	}

	public function requestProviderToken(StorageState $storageState, Request $request, &$error = null, $skipStoredToken = false)
	{
		$version = $this->getOAuthVersion();
		$token = false;
		if (!$skipStoredToken)
		{
			$token = $storageState->getProviderToken();
			if ($token && $version == 2)
			{
				return $token;
			}
		}

		if ($request->filter('error', 'str') == 'access_denied' || $request->filter('denied', 'str'))
		{
			$error = \XF::phraseDeferred('you_did_not_grant_permission_to_access_connected_account');
			return false;
		}

		switch ($version)
		{
			case 2:
				$code = $request->filter('code', 'str');

				try
				{
					/** @var \OAuth\OAuth2\Service\ServiceInterface $oAuth */
					$oAuth = $this->getOAuth($this->getOAuthConfig($storageState->getProvider()));
					$token = $oAuth->requestAccessToken($code);
				}
				catch (\Exception $e)
				{
					$error = \XF::phraseDeferred('error_occurred_while_connecting_with_x', ['provider' => $this->getTitle()]);
					if (!($e instanceof HttpResponseException))
					{
						// Token response exception can be thrown in the internals of the library
						$e = new HttpResponseException($e->getMessage());
						$e->setResponseContent($e->getMessage());
					}
					else
					{
						if ($this->testMode && $e->getResponseContent())
						{
							$this->parseProviderError($e, $error);
						}
					}
					return false;
				}
				break;

			case 1:
				$oToken = $request->filter('oauth_token', 'str');
				$oVerifier = $request->filter('oauth_verifier', 'str');
				if ($oToken && $oVerifier)
				{
					try
					{
						if (!$token)
						{
							$token = $storageState->getProviderToken();
						}

						/** @var \OAuth\OAuth1\Service\ServiceInterface $oAuth */
						$oAuth = $this->getOAuth($this->getOAuthConfig($storageState->getProvider()));
						$token =  $oAuth->requestAccessToken($oToken, $oVerifier, $token->getRequestTokenSecret());
					}
					catch (HttpResponseException $e)
					{
						$error = \XF::phraseDeferred('error_occurred_while_connecting_with_x', ['provider' => $this->getTitle()]);
						if ($this->testMode && $e->getResponseContent())
						{
							$this->parseProviderError($e, $error);
						}
						return false;
					}
				}
				else
				{
					$error = \XF::phraseDeferred('error_occurred_while_connecting_with_x', ['provider' => $this->getTitle()]);
					return false;
				}
				break;

			default:
				throw new \InvalidArgumentException("Unknown OAuth version '$version'");
				break;
		}

		$storageState->storeToken($token);
		return $token;
	}

	protected function parseProviderError(HttpResponseException $e, &$error = null)
	{
		$error = \XF::phraseDeferred('error_occurred_while_connecting_with_x_error_y', [
			'provider' => $this->getTitle(),
			'error' => $e->getMessage()
		]);
	}

	public function verifyConfig(array &$options, &$error = null)
	{
		$finalOptions = [];

		foreach ($this->getDefaultOptions() AS $key => $null)
		{
			$valid = true;

			if (!isset($options[$key]))
			{
				$valid = false;
			}
			else
			{
				$value = $options[$key];
				if (!is_string($value) || trim($value) === '')
				{
					$valid = false;
				}
				else
				{
					$finalOptions[$key] = $value;
				}
			}

			if (!$valid)
			{
				$error = \XF::phrase('please_complete_required_fields');
				return false;
			}
		}

		$options = $finalOptions;
		return true;
	}

	public function renderConfig(ConnectedAccountProvider $provider)
	{
		return \XF::app()->templater()->renderTemplate('admin:connected_account_provider_' . $provider->provider_id, [
			'options' => $this->getEffectiveOptions($provider->options)
		]);
	}

	public function renderTest(ConnectedAccountProvider $provider, AbstractProviderData $providerData = null)
	{
		return \XF::app()->templater()->renderTemplate($this->getTestTemplateName(), [
			'provider' => $provider,
			'providerData' => $providerData
		]);
	}

	public function getTestTemplateName()
	{
		return 'admin:connected_account_provider_test_' . $this->providerId;
	}

	public function renderAssociated(ConnectedAccountProvider $provider, \XF\Entity\User $user)
	{
		return \XF::app()->templater()->renderTemplate('public:connected_account_associated_' . $provider->provider_id, [
			'provider' => $provider,
			'user' => $user,
			'providerData' => $provider->getUserInfo($user),
			'connectedAccounts' => $user->Profile->connected_accounts
		]);
	}

	protected function getEffectiveOptions(array $options)
	{
		return array_replace($this->getDefaultOptions(), $options);
	}
}