<?php

namespace XF\SubContainer;

class Oembed extends AbstractSubContainer
{
	public function initialize()
	{
		$container = $this->container;

		$container['controller'] = function($c)
		{
			return new \XF\Oembed\Controller($this->app, $this->app->request());
		};
	}

	/**
	 * @return \XF\Proxy\Controller
	 */
	public function controller()
	{
		return $this->container['controller'];
	}
}