<?php

namespace XF\Cli\Command\Development;

class ExportAdminNavigation extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'Admin navigation',
			'command' => 'admin-navigation',
			'entity' => 'XF:AdminNavigation'
		];
	}
}