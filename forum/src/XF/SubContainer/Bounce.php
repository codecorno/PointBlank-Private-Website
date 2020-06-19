<?php

namespace XF\SubContainer;

use XF\Container;

class Bounce extends AbstractSubContainer
{
	public function initialize()
	{
		$container = $this->container;

		$container->set('storage', function(Container $c)
		{
			return \XF\EmailBounce\Processor::getDefaultBounceHandlerStorage($this->app);
		}, false);

		$container['parser'] = function(Container $c)
		{
			$options = $this->app->options();

			$class = $this->app->extendClass('XF\EmailBounce\Parser');

			return new $class(
				$options->enableVerp ? $options->bounceEmailAddress : null,
				$this->app->config('globalSalt')
			);
		};

		$container['processor'] = function(Container $c)
		{
			$class = $this->app->extendClass('XF\EmailBounce\Processor');

			return new $class($this->app, $c['parser']);
		};
	}

	/**
	 * @return \Zend\Mail\Storage\AbstractStorage
	 */
	public function storage()
	{
		return $this->container['storage'];
	}

	/**
	 * @return \XF\EmailBounce\Parser
	 */
	public function parser()
	{
		return $this->container['parser'];
	}

	/**
	 * @return \XF\EmailBounce\Processor
	 */
	public function processor()
	{
		return $this->container['processor'];
	}
}