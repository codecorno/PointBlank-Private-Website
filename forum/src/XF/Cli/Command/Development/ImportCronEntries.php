<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCronEntries extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'cron entries',
			'command' => 'cron-entries',
			'dir' => 'cron_entries',
			'entity' => 'XF:CronEntry'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		return \XF::db()->fetchPairs("
			SELECT entry_id, entry_id
			FROM xf_cron_entry
			WHERE addon_id = ?
		", $addOnId);
	}
}