<?php

namespace XF\Cli\Command\Designer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncTemplates extends Command
{
	use RequiresDesignerModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-designer:sync-templates')
			->setDescription('Syncs the contents of the template files to the DB for the specified designer mode, applying version number updates')
			->addArgument(
				'designer-mode',
				InputArgument::REQUIRED,
				'Designer mode ID'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em = \XF::em();

		$designerMode = $input->getArgument('designer-mode');
		$style = $em->findOne('XF:Style', ['designer_mode' => $designerMode]);

		if (!$style)
		{
			$output->writeln("No style with designer mode ID '$designerMode' could be found.");
			return 1;
		}

		$designerOutput = \XF::app()->designerOutput();
		$files = $designerOutput->getAvailableTypeFiles('templates', $designerMode);

		/** @var \XF\DesignerOutput\Template $templateOutputHandler */
		$templateOutputHandler = $designerOutput->getHandler('XF:Template');
		$templater = \XF::app()->templater();

		$totalUpdated = 0;

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

		$output->writeln("");
		$output->writeln("Done. Total templates updated: $totalUpdated.");

		return 0;
	}
}