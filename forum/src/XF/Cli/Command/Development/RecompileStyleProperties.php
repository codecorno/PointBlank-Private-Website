<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecompileStyleProperties extends Command
{
	use RequiresDevModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-dev:recompile-style-properties')
			->setDescription('Recompiles style properties');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$app = \XF::app();
		$start = microtime(true);

		$output->writeln("Recompiling style properties...");

		/** @var \XF\Service\StyleProperty\Rebuild $rebuildService */
		$spRebuildService = $app->service('XF:StyleProperty\Rebuild');
		$spRebuildService->rebuildFullPropertyMap();
		$spRebuildService->rebuildPropertyStyleCache();

		$output->writeln(sprintf("Style properties compiled. (%.02fs)", microtime(true) - $start));

		return 0;
	}
}