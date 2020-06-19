<?php

namespace XF\Cli\Command\Development;

class ExportBbCode extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'bb codes',
			'command' => 'bb-codes',
			'entity' => 'XF:BbCode'
		];
	}
}