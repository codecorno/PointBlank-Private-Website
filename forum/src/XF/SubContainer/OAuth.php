<?php

namespace XF\SubContainer;

use OAuth\Common\Service\ServiceInterface;
use XF\ConnectedAccount\Storage\Local;
use XF\Container;
use XF\Entity\ConnectedAccountProvider;
use XF\Entity\User;
use XF\ConnectedAccount\Http\Client;
use XF\ConnectedAccount\ProviderData\AbstractProviderData;
use XF\ConnectedAccount\Storage\Session;
use XF\ConnectedAccount\Storage\StorageState;

class OAuth extends AbstractSubContainer
{
	public function initialize()
	{
		$container = $this->container;

		$container['client'] = function(Container $c)
		{
			$class = '\XF\ConnectedAccount\Http\Client';
			$class = $this->extendClass($class);

			return $c->createObject($class);
		};

		$container['storage.session'] = function(Container $c)
		{
			$class = '\XF\ConnectedAccount\Storage\Session';
			$class = $this->extendClass($class);

			return $c->createObject($class, [$this->parent['session.public']]);
		};

		$container['storage.local'] = function(Container $c)
		{
			$class = '\XF\ConnectedAccount\Storage\Local';
			$class = $this->extendClass($class);

			return $c->createObject($class);
		};

		$container->factory('provider', function($serviceName, array $config, Container $c)
		{
			/** @var \OAuth\Common\Consumer\Credentials $credentials */
			$credentials = $c->createObject('\OAuth\Common\Consumer\Credentials', [
				$config['key'], $config['secret'], $config['redirect']
			]);

			/** @var \OAuth\ServiceFactory $serviceFactory */
			$serviceFactory = $c->createObject('\OAuth\ServiceFactory');

			$isClass = false;
			if (strpos($serviceName, ':') !== false)
			{
				$serviceName = \XF::stringToClass($serviceName, '\%s\ConnectedAccount\%s');
				$isClass = true;
			}
			else if (strpos($serviceName, '\\') !== false)
			{
				$isClass = true;
			}
			if ($isClass)
			{
				$serviceName = $this->extendClass($serviceName);
				$serviceFactory->registerService($serviceName, $serviceName);
			}

			/** @var Client $client */
			$client = $c['client'];
			$serviceFactory->setHttpClient($client);

			$storage = $this->storage($config['storageType']);

			return $serviceFactory->createService($serviceName, $credentials, $storage, $config['scopes']);
		});

		$container->factory('providerData', function($class, array $params, Container $c)
		{
			$class = \XF::stringToClass($class, '%s\ConnectedAccount\%s');
			$class = $this->extendClass($class);
			return $c->createObject($class, $params);
		});
	}

	/**
	 * @return Session|Local
	 */
	public function storage($type = null)
	{
		return $type ? $this->container['storage.' . $type] : $this->container['storage.session'];
	}

	/**
	 * @param $serviceName
	 * @param array $config
	 *
	 * @return ServiceInterface
	 */
	public function provider($serviceName, array $config = [])
	{
		return $this->container->create('provider', $serviceName, $config);
	}

	/**
	 * @param $class
	 * @param $providerId
	 * @param StorageState $storageState
	 *
	 * @return AbstractProviderData
	 */
	public function providerData($class, $providerId, StorageState $storageState)
	{
		return $this->container->create('providerData', $class, [$providerId, $storageState]);
	}

	/**
	 * @param ConnectedAccountProvider $provider
	 * @param User $user
	 *
	 * @return StorageState
	 */
	public function storageState(ConnectedAccountProvider $provider, User $user)
	{
		$class = $this->extendClass('\XF\ConnectedAccount\Storage\StorageState');
		return new $class($provider, $user);
	}

	/**
	 * @return Client
	 */
	public function client()
	{
		return $this->container['client'];
	}
}