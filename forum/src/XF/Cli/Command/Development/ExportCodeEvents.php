<?php

namespace XF\Cli\Command\Development;

class ExportCodeEvents extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'code events',
			'command' => 'code-events',
			'entity' => 'XF:CodeEvent'
		];
	}
}