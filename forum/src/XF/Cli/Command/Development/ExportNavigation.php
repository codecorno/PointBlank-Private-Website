<?php

namespace XF\Cli\Command\Development;

class ExportNavigation extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'Navigation',
			'command' => 'navigation',
			'entity' => 'XF:Navigation'
		];
	}
}