<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportPermissions extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'Permissions',
			'command' => 'permissions',
			'entity' => 'XF:Permission'
		];
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$returnCode = parent::execute($input, $output);
		if (!$returnCode)
		{
			// success
			$write = function($entity)
			{
				\XF::app()->developmentOutput()->export($entity);
			};
			$this->exportData($input, $output, 'permission interface groups', 'XF:PermissionInterfaceGroup', $write);
		}

		return $returnCode;
	}
}