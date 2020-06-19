<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Cli\Command\JobRunnerTrait;

class RebuildCaches extends Command
{
	use RequiresDevModeTrait, JobRunnerTrait;

	protected function configure()
	{
		$this
			->setName('xf-dev:rebuild-caches')
			->setDescription('Rebuilds various caches');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->setupAndRunJob('xfDevCoreCacheRebuild', 'XF:CoreCacheRebuild');

		$output->writeln("Miscellaneous caches rebuilt.");

		return 0;
	}
}