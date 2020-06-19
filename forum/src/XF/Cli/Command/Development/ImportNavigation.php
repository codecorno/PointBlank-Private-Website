<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportNavigation extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'navigation',
			'command' => 'navigation',
			'dir' => 'navigation',
			'entity' => 'XF:Navigation'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		return \XF::db()->fetchPairs("
			SELECT navigation_id, navigation_id
			FROM xf_navigation
			WHERE addon_id = ?
		", $addOnId);
	}
}