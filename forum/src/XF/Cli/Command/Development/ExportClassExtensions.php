<?php

namespace XF\Cli\Command\Development;

class ExportClassExtensions extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'class extensions',
			'command' => 'class-extensions',
			'entity' => 'XF:ClassExtension'
		];
	}
}