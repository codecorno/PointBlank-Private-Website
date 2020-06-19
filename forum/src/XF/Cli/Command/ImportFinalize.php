<?php

namespace XF\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportFinalize extends Command
{
	use JobRunnerTrait;

	protected function configure()
	{
		$this
			->setName('xf:import-finalize')
			->setDescription('Finalize an import configured via the control panel');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$app = \XF::app();
		$em = $app->em();

		$manager = $app->import()->manager();
		$runner = $manager->getRunner();

		if (!$runner)
		{
			$output->writeln("<error>No valid import session could be found. Configure this via the control panel.</error>");

			return 1;
		}

		$session = $runner->getSession();
		if (!$session->runComplete)
		{
			$output->writeln("<error>The import session is not yet completed.</error>");

			return 1;
		}

		$db = $app->db();
		$db->logQueries(false); // need to limit memory usage

		$importerTitle = $runner->getImporter()->getSourceTitle();
		$output->writeln("Finalizing import from $importerTitle...");

		// TODO: output each job on a single line?

		$jobs = $manager->getImporter($session->importerId)->getFinalizeJobs($session->getStepsRun());
		if ($jobs)
		{
			$this->setupAndRunJob('importFinalize', 'XF:Atomic', ['execute' => $jobs], $output);
		}
		else
		{
			$output->writeln('No jobs...');
		}

		$session->finalized = true;
		$manager->updateCurrentSession($session);

		$output->writeln("The import has been finalized and is ready for use.");

		return 0;
	}
}