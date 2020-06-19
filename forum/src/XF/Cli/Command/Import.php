<?php

namespace XF\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Import\ParallelRunner;
use XF\Import\Runner;
use XF\Import\StepState;

class Import extends Command
{
	use JobRunnerTrait;

	protected function configure()
	{
		$this
			->setName('xf:import')
			->setDescription('Executes an import configured via the control panel')
			->addOption(
				'processes',
				null,
				InputOption::VALUE_REQUIRED,
				'If provided and set to a value more than 1, the importer will execute in parallel with this many processes.'
			)
			->addOption(
				'finalize',
				null,
				InputOption::VALUE_NONE,
				'If set, the import will be automatically finalized when the data import step completes.'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$app = \XF::app();

		$manager = $app->import()->manager();

		$processes = intval($input->getOption('processes'));
		if ($processes > 1)
		{
			$runner = $manager->newRunner('parallel', ['processes' => $processes]);
		}
		else
		{
			$runner = $manager->newRunner();
		}

		if (!$runner)
		{
			$output->writeln("<error>No valid import session could be found. Configure this via the control panel.</error>");
			return 1;
		}

		$session = $runner->getSession();
		if ($session->runComplete)
		{
			$output->writeln("<error>The import session has already been completed. Continue via the control panel.</error>");
			return 1;
		}

		if (!$session->canRunVia('cli'))
		{
			$output->writeln("<error>This import has been started through another method.</error>");
			return 1;
		}

		$db = $app->db();
		$db->logQueries(false); // need to limit memory usage

		$isParallel = ($runner instanceof ParallelRunner);
		$importerTitle = $runner->getImporter()->getSourceTitle();

		$output->writeln("Starting import from $importerTitle...");

		if ($isParallel)
		{
			$output->writeln("Using parallel runner with up to " . $runner->getMaxProcesses() . " processes.");
			$output->writeln("<info>Note: completion percentage is approximate and only updated periodically.</info>");
		}

		$progressIndicator = new ProgressIndicator($output);
		$progressIndicator->start('Importing...');
		$progressStarted = true;

		$session->runType = 'cli';
		$manager->updateCurrentSession($session);

		$runner->runUntilComplete(
			$manager,
			function($isComplete, $step, StepState $stepState, $importCompletion)
				use ($progressIndicator, &$progressStarted, $isParallel)
			{
				$stepTitle = $stepState->title;
				if (!$stepTitle)
				{
					// nothing to output
					return;
				}
				if ($isComplete && !$progressStarted)
				{
					// don't bother outputting
					return;
				}

				if (!$progressStarted)
				{
					$progressIndicator->start('Importing...');
					$progressStarted = true;
				}

				$stepNumberStrlen = strlen((string)$importCompletion['total']);
				$stepNameStrlen = 25; // just arbitrary for now, but could be based on the strlen of the longest step title

				$stepInfo = sprintf(
					"Step %{$stepNumberStrlen}d of %d: %-{$stepNameStrlen}s ",
					$importCompletion['current'],
					$importCompletion['total'],
					$stepTitle
				);

				if ($stepState->complete)
				{
					$progressIndicator->finish(
						$stepInfo
						. $this->getIntervalString($stepState->startDate, $stepState->completeDate)
						. ' [' . \XF::language()->numberFormat($stepState->imported) . ']'
					);
					$progressStarted = false;
				}
				else
				{
					$progressIndicator->setMessage(
						$stepInfo
						. $this->getIntervalString($stepState->startDate, time()) . ' '
						. $stepState->getCompletionOutput()
					);
				}
			}
		);

		if ($progressStarted)
		{
			$progressIndicator->finish('Done!');
		}

		$output->writeln("The data has been imported successfully.");

		if ($input->getOption('finalize'))
		{
			$command = $this->getApplication()->find('xf:import-finalize');
			$childInput = new ArrayInput(['command' => 'xf:import-finalize']);
			$command->run($childInput, $output);
			$output->writeln("");
		}
		else
		{
			$output->writeln("To finalize this import, you must continue via the control panel, or run the xf:import-finalize command.");
		}

		// TODO: we can probably display any notes about the import here (changed users, etc)

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