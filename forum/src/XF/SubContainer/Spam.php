<?php

namespace XF\SubContainer;

use XF\Container;
use XF\Entity;

class Spam extends AbstractSubContainer
{
	public function initialize()
	{
		$container = $this->container;
		$parent = $this->parent;

		$container->factory('checker', function($class, array $params, Container $c)
		{
			$class = \XF::stringToClass($class, '\%s\Spam\%s');
			$class = $this->extendClass($class);

			return $c->createObject($class, [$this->app]);
		});

		$container->set('userChecker', function(Container $c)
		{
			/** @var \XF\Spam\UserChecker $checker */
			$checker = $c->create('checker', 'XF:UserChecker');
			$params = [$checker, $this->app];

			$providers = $c['userProviders'];
			foreach ($providers AS $className)
			{
				$checker->addProvider($c->create('provider', $className, $params));
			}

			return $checker;
		});

		$container->set('userSubmitter', function(Container $c) use ($parent)
		{
			/** @var \XF\Spam\UserChecker $checker */
			$checker = $c->create('checker', 'XF:UserChecker');
			$params = [$checker, $this->app];

			$options = $parent['options'];
			if ($options->stopForumSpam['submitRejections'] && $options->stopForumSpam['apiKey'])
			{
				$checker->addProvider($c->create('provider', 'XF:StopForumSpam', $params));
			}

			$this->app->fire('spam_user_submitter', [$this, $parent, &$checker]);

			return $checker;
		}, false);

		$container->set('userProviders', function(Container $c) use ($parent)
		{
			/** @var \ArrayObject $options */
			$options = $parent['options'];

			$providers = [];

			$sfsEnabled = !empty($options->stopForumSpam['enabled']);

			if (!empty($options->registrationCheckDnsBl['check']))
			{
				$httpBlKey = $options->registrationCheckDnsBl['projectHoneyPotKey'];

				if (!$sfsEnabled)
				{
					$providers[] = 'XF:Tornevall';
				}

				if ($httpBlKey)
				{
					$providers[] = 'XF:ProjectHoneyPot';
				}
			}

			if ($sfsEnabled)
			{
				$providers[] = 'XF:StopForumSpam';
			}

			if (!empty($options->approveSharedBannedRejectedIp['enabled']))
			{
				$providers[] = 'XF:BannedUsers';
				$providers[] = 'XF:RejectedUsers';
			}

			$this->app->fire('spam_user_providers', [$this, $parent, &$providers]);

			return $providers;
		}, false);

		$container->set('contentChecker', function(Container $c)
		{
			/** @var \XF\Spam\ContentChecker $checker */
			$checker = $c->create('checker', 'XF:ContentChecker');
			$params = [$checker, $this->app];

			$providers = $c['contentProviders'];
			foreach ($providers AS $className)
			{
				$checker->addProvider($c->create('provider', $className, $params));
			}

			return $checker;
		});

		$container->set('contentSubmitter', function(Container $c) use ($parent)
		{
			/** @var \XF\Spam\ContentChecker $checker */
			$checker = $c->create('checker', 'XF:ContentChecker');
			$params = [$checker, $this->app];

			$options = $parent['options'];
			if ($options->akismetKey)
			{
				$checker->addProvider($c->create('provider', 'XF:Akismet', $params));
			}

			$this->app->fire('spam_content_submitter', [$this, $parent, &$checker]);

			return $checker;
		}, false);

		$container->set('contentHamSubmitter', function(Container $c) use ($parent)
		{
			/** @var \XF\Spam\ContentChecker $checker */
			$checker = $c->create('checker', 'XF:ContentChecker');
			$params = [$checker, $this->app];

			$options = $parent['options'];
			if ($options->akismetKey)
			{
				$checker->addProvider($c->create('provider', 'XF:Akismet', $params));
			}

			$this->app->fire('spam_content_submitter_ham', [$this, $parent, &$checker]);

			return $checker;
		}, false);

		$container->set('contentProviders', function(Container $c) use ($parent)
		{
			/** @var \ArrayObject $options */
			$options = $parent['options'];

			$providers = [];

			if (!empty($options->spamPhrases['phrases']))
			{
				$providers[] = 'XF:SpamPhrases';
			}

			if ($options->akismetKey)
			{
				$providers[] = 'XF:Akismet';
			}

			$this->app->fire('spam_content_providers', [$this, $parent, &$providers]);

			return $providers;
		}, false);

		$container->factory('provider', function($class, array $params, Container $c)
		{
			$class = \XF::stringToClass($class, '\%s\Spam\Checker\%s');
			$class = $this->extendClass($class);

			return $c->createObject($class, $params);
		}, false);

		$container->factory('cleaner', function($userId, array $params, Container $c)
		{
			$class = $this->extendClass('XF\Spam\Cleaner');

			array_unshift($params, $this->app);
			return $c->createObject($class, $params);
		});

		$container->factory('restorer', function($logId, array $params, Container $c)
		{
			$class = $this->extendClass('XF\Spam\Restorer');

			array_unshift($params, $this->app);
			return $c->createObject($class, $params);
		});
	}

	/**
	 * @param Entity\User $user
	 *
	 * @return \XF\Spam\Cleaner
	 */
	public function cleaner(Entity\User $user)
	{
		$arguments = func_get_args();
		unset($arguments[0]);
		array_unshift($arguments, $user);
		return $this->container->create('cleaner', $user->user_id, $arguments);
	}

	/**
	 * @param Entity\SpamCleanerLog $log
	 *
	 * @return \XF\Spam\Restorer
	 */
	public function restorer(Entity\SpamCleanerLog $log)
	{
		$arguments = func_get_args();
		unset($arguments[0]);
		array_unshift($arguments, $log);
		return $this->container->create('restorer', $log->spam_cleaner_log_id, $arguments);
	}

	/**
	 * @return \XF\Spam\UserChecker
	 */
	public function userChecker()
	{
		return $this->container['userChecker'];
	}

	/**
	 * @return \XF\Spam\ContentChecker
	 */
	public function contentChecker()
	{
		return $this->container['contentChecker'];
	}
}