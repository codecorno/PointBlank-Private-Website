<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncTemplates extends Command
{
	use RequiresDevModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-dev:sync-templates')
			->setDescription('Syncs the contents of the template files to the DB, applying version number updates')
			->addOption(
				'addon',
				null,
				InputOption::VALUE_REQUIRED,
				'Add-on to limit to importing (default: all)'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$onlyAddOn = $input->getOption('addon');

		$devOutput = \XF::app()->developmentOutput();
		$addOns = $devOutput->getAvailableTypeFilesByAddOn('templates');
		$installedAddOns = \XF::repository('XF:AddOn')->getInstalledAddOnData();

		/** @var \XF\DevelopmentOutput\Template $templateOutputHandler */
		$templateOutputHandler = \XF::app()->developmentOutput()->getHandler('XF:Template');
		$templater = \XF::app()->templater();

		$totalUpdated = 0;

		foreach ($addOns AS $addOnId => $files)
		{
			if ($onlyAddOn && $onlyAddOn != $addOnId)
			{
				continue;
			}

			if (!isset($installedAddOns[$addOnId]))
			{
				$output->writeln("Skipping $addOnId - not installed.");
				continue;
			}

			$output->writeln("Syncing $addOnId templates...");

			foreach ($files AS $fileName => $path)
			{
				$name = $templateOutputHandler->convertTemplateFileToName($fileName);
				$parts = preg_split('#[:/\\\\]#', $name, 2);
				if (count($parts) == 1)
				{
					throw new \InvalidArgumentException("Template $name does not contain a type component");
				}

				list($type, $title) = $parts;

				if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE)
				{
					$output->writeln("Checking $type:$title...");
				}

				if ($templateOutputHandler->watchTemplate($templater, $type, $title))
				{
					$output->writeln("\tUpdated $type:$title.");
					$totalUpdated++;
				}
			}

			$output->writeln("Synced $addOnId.");
		}

		$output->writeln("");
		$output->writeln("Done. Total templates updated: $totalUpdated.");

		return 0;
	}
}