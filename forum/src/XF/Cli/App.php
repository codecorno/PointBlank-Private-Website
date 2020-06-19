<?php

namespace XF\Cli;

use XF\Container;

class App extends \XF\App
{
	public function initializeExtra()
	{
		$container = $this->container;

		$container['app.classType'] = 'Cli';
		$container['app.defaultType'] = 'public';
		$container['job.manual.allow'] = true;

		$container['session'] = function (Container $c)
		{
			return $c['session.public'];
		};
	}

	public function setup(array $options = [])
	{
		parent::setup();

		$this->fire('app_cli_setup', [$this]);
	}

	public function start($allowShortCircuit = false)
	{
		parent::start($allowShortCircuit);

		$this->fire('app_cli_start_begin', [$this]);
		$this->fire('app_cli_start_end', [$this]);
	}

	public function run()
	{
		throw new \LogicException("The CLI app is not runnable. Use \\XF\\Cli\\Runner.");
	}
}