<?php

namespace XF\Cli\Command\Development;

class ExportRoutes extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'Routes',
			'command' => 'routes',
			'entity' => 'XF:Route'
		];
	}
}