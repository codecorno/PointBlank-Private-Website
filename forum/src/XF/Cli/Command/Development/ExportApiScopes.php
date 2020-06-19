<?php

namespace XF\Cli\Command\Development;

class ExportApiScopes extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'API scopes',
			'command' => 'api-scopes',
			'entity' => 'XF:ApiScope'
		];
	}
}