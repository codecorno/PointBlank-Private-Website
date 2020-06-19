<?php

namespace XF\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Import\Runner;

class ImportChildProcess extends Command
{
	use JobRunnerTrait;

	protected function configure()
	{
		$this
			->setName('xf:import-child-process')
			->setDescription('Executes part of the import as part of the multi-process import system')
			->setHidden(true)
			->addArgument(
				'step',
				InputArgument::REQUIRED,
				'The name of the step to run.'
			)
			->addArgument(
				'startAfter',
				InputArgument::REQUIRED,
				'The first ID to start importing after.'
			)
			->addArgument(
				'end',
				InputArgument::REQUIRED,
				'The last ID to import.'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$app = \XF::app();

		$manager = $app->import()->manager();

		/** @var \XF\Import\ParallelRunner $runner */
		$runner = $manager->newRunner('parallel');
		// force the parallel runner as this has special methods for this

		if (!$runner)
		{
			$output->writeln("Error: No valid import session could be found.");
			return 1;
		}

		$session = $runner->getSession();
		if ($session->runComplete || !$session->canRunVia('cli'))
		{
			$output->writeln("Error: Import is not in expected state.");
			return 1;
		}

		$step = $input->getArgument('step');
		$startAfter = intval($input->getArgument('startAfter'));
		$end = intval($input->getArgument('end'));

		if (!$runner->validateChildProcessRun($step, $startAfter, $end, $error))
		{
			$output->writeln("Error: $error");
			return 1;
		}

		$messageReceiver = function($message) use ($output)
		{
			$output->writeln($message);
		};

		$runner->runChildProcess($step, $startAfter, $end, $messageReceiver);

		return 0;
	}

	protected function getIntervalString($start, $end)
	{
		$time = $end - $start;

		if ($time <= 0)
		{
			return '00:00:00';
		}
		else
		{
			$t = \XF\Util\Time::getIntervalArray($time, false);

			return sprintf('%02d:%02d:%02d', $t['hours'], $t['minutes'], $t['seconds']);
		}
	}
}