<?php

namespace XF\Cli\Command\Development;

class ExportAdminPermissions extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'Admin permissions',
			'command' => 'admin-permissions',
			'entity' => 'XF:AdminPermission'
		];
	}
}