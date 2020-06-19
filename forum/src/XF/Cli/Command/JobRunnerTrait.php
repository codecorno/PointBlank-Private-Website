<?php

namespace XF\Cli\Command;

use Symfony\Component\Console\Output\OutputInterface;

trait JobRunnerTrait
{
	public function setupAndRunJob($uniqueId, $jobClass, array $params = [], OutputInterface $output = null)
	{
		\XF::app()->jobManager()->enqueueUnique($uniqueId, $jobClass, $params);

		$this->runJob($uniqueId, $output);
	}

	public function runJob($uniqueId, OutputInterface $output = null)
	{
		$jobManager = \XF::app()->jobManager();
		$em = \XF::em();

		if (function_exists('pcntl_signal') && defined('SIGINT'))
		{
			// Where possible, register a signal handler to run on interrupt to cancel the unique job
			pcntl_signal(SIGINT, function() use($uniqueId)
			{
				\XF::app()->jobManager()->cancelUniqueJob($uniqueId);
			});
		}

		while ($runner = $jobManager->runUnique($uniqueId, \XF::config('jobMaxRunTime')))
		{
			if ($output)
			{
				$output->writeln($runner->statusMessage);
			}

			// keep the memory limit down on long running jobs
			$em->clearEntityCache();

			if (function_exists('pcntl_signal_dispatch'))
			{
				// Dispatch any registered signal handlers for pending signals
				pcntl_signal_dispatch();
			}
		}
	}
}