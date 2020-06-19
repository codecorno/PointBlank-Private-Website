<?php

namespace XF\SubContainer;

use XF\Container;

class Import extends AbstractSubContainer
{
	public function initialize()
	{
		$container = $this->container;

		$container['manager'] = function(Container $c)
		{
			return new \XF\Import\Manager($this->app, $c['importers']);
		};

		$container['importers'] = function()
		{
			$importers = [];

			$this->app->fire('import_importer_classes', [$this, $this->parent, &$importers]);

			return $importers;
		};
	}

	/**
	 * @return \XF\Import\Manager
	 */
	public function manager()
	{
		return $this->container['manager'];
	}
}