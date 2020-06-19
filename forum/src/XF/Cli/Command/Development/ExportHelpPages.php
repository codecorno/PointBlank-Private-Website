<?php

namespace XF\Cli\Command\Development;

class ExportHelpPages extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'help pages',
			'command' => 'help-pages',
			'entity' => 'XF:HelpPage'
		];
	}
}