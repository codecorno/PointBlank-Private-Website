<?php

namespace XF\Cli\Command\Rebuild;

use Symfony\Component\Console\Input\InputOption;

class RebuildSearch extends AbstractRebuildCommand
{
	protected function getRebuildName()
	{
		return 'search';
	}

	protected function getRebuildDescription()
	{
		return 'Rebuilds the search index.';
	}

	protected function getRebuildClass()
	{
		return 'XF:SearchRebuild';
	}

	protected function configureOptions()
	{
		$this
			->addOption(
				'type',
				null,
				InputOption::VALUE_REQUIRED,
				'Content type to rebuild search index for. Default: all'
			)
			->addOption(
				'truncate',
				null,
				InputOption::VALUE_NONE,
				'Delete the existing index before rebuilding. Default: false'
			);
	}
}