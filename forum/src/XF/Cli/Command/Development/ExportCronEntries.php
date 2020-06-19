<?php

namespace XF\Cli\Command\Development;

class ExportCronEntries extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'cron entries',
			'command' => 'cron-entries',
			'entity' => 'XF:CronEntry'
		];
	}
}