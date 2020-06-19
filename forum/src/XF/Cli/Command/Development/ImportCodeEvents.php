<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCodeEvents extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'code events',
			'command' => 'code-events',
			'dir' => 'code_events',
			'entity' => 'XF:CodeEvent'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		return \XF::db()->fetchPairs("
			SELECT event_id, event_id
			FROM xf_code_event
			WHERE addon_id = ?
		", $addOnId);
	}
}