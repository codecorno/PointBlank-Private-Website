<?php

namespace XF\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RebuildMasterData extends Command implements CustomAppCommandInterface
{
	use JobRunnerTrait;

	public static function getCustomAppClass()
	{
		return 'XF\Install\App';
	}

	protected function configure()
	{
		$this
			->setName('xf:rebuild-master-data')
			->setDescription('Rebuilds the core XF master data.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$installHelper = new \XF\Install\Helper(\XF::app());
		$installHelper->insertRebuildJob('xfRebuildMaster');

		$startTime = microtime(true);

		$this->runJob('xfRebuildMaster', $output);

		$total = microtime(true) - $startTime;

		$output->writeln(sprintf("Master data rebuilt successfully. Time taken to import and rebuild: %.02fs", $total));

		return 0;
	}
}