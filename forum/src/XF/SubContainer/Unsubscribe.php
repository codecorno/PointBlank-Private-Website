<?php

namespace XF\SubContainer;

use XF\Container;

class Unsubscribe extends AbstractSubContainer
{
	public function initialize()
	{
		$container = $this->container;

		$container->set('storage', function(Container $c)
		{
			return \XF\EmailUnsubscribe\Processor::getDefaultUnsubscribeHandlerStorage($this->app);
		}, false);

		$container['processor'] = function(Container $c)
		{
			$options = $this->app->options();

			$class = $this->app->extendClass('XF\EmailUnsubscribe\Processor');

			return new $class($this->app, $options->enableVerp ? $options->unsubscribeEmailAddress : null);
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
	 * @return \XF\EmailUnsubscribe\Processor
	 */
	public function processor()
	{
		return $this->container['processor'];
	}
}