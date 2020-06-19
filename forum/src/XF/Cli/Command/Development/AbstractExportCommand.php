<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use XF\Mvc\Entity\Finder;

abstract class AbstractExportCommand extends Command
{
	use RequiresDevModeTrait;

	// [command, name, entity]
	abstract protected function getContentTypeDetails();

	protected function writeContent(\XF\Mvc\Entity\Entity $entity)
	{
		\XF::app()->developmentOutput()->export($entity);
	}

	protected function configure()
	{
		$contentType = $this->getContentTypeDetails();

		$this
			->setName("xf-dev:export-$contentType[command]")
			->setDescription("Exports $contentType[name] to development files")
			->addOption(
				'addon',
				null,
				InputOption::VALUE_REQUIRED,
				'Add-on to limit to exporting (default: all)'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$contentType = $this->getContentTypeDetails();

		$write = function($entity)
		{
			$this->writeContent($entity);
		};

		$this->exportData($input, $output, $contentType['name'], $contentType['entity'], $write);

		return 0;
	}

	protected function exportData(InputInterface $input, OutputInterface $output, $name, $entityName, \Closure $write)
	{
		$start = microtime(true);
		$output->writeln("Exporting $name...");

		$devOutput = \XF::app()->developmentOutput();

		$finder = \XF::em()->getFinder($entityName)
			->where('addon_id', '!=', '');

		$skippedAddOns = $devOutput->getSkippedAddOns();
		if ($skippedAddOns)
		{
			$finder->where('addon_id', '!=', $skippedAddOns);
		}

		$onlyAddOn = $input->getOption('addon');
		if ($onlyAddOn)
		{
			$finder->where('addon_id', $onlyAddOn);
		}

		$this->extraFinderConditions($finder);

		$printName = ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE);

		if (!$printName)
		{
			$progress = new ProgressBar($output, $finder->total());
			$progress->start();
		}
		else
		{
			$progress = null;
		}

		$devOutput->enableBatchMode();

		foreach ($finder->fetch() AS $entity)
		{
			if ($printName)
			{
				$output->writeln("\t" . strval($entity));
			}
			else
			{
				$progress->advance();
			}

			$write($entity);
		}

		$devOutput->clearBatchMode();

		if ($progress)
		{
			$progress->finish();
			$output->writeln("");
		}

		$output->writeln(sprintf(ucfirst($name) . " exported. (%.02fs)", microtime(true) - $start));
	}

	protected function extraFinderConditions(Finder $finder)
	{
	}
}