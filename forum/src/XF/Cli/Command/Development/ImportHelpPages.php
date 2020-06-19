<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportHelpPages extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'help pages',
			'command' => 'help-pages',
			'dir' => 'help_pages',
			'entity' => 'XF:HelpPage'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		return \XF::db()->fetchPairs("
			SELECT page_id, page_id
			FROM xf_help_page
			WHERE addon_id = ?
		", $addOnId);
	}
}