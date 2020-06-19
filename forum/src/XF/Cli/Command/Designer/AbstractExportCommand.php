<?php

namespace XF\Cli\Command\Designer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractExportCommand extends Command
{
	use RequiresDesignerModeTrait;

	// [command, name, entity]
	abstract protected function getContentTypeDetails();

	protected function writeContent(\XF\Mvc\Entity\Entity $entity)
	{
		\XF::app()->designerOutput()->export($entity);
	}

	protected function configure()
	{
		$contentType = $this->getContentTypeDetails();

		$this
			->setName("xf-designer:export-$contentType[command]")
			->setDescription("Exports $contentType[name] for the specified designer mode ID.")
			->addArgument(
				'designer-mode',
				InputArgument::REQUIRED,
				'Designer mode ID'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em = \XF::em();

		/** @var \XF\Entity\Style $style */
		$designerMode = $input->getArgument('designer-mode');
		$style = $em->findOne('XF:Style', ['designer_mode' => $designerMode]);

		if (!$style)
		{
			$output->writeln("No style with designer mode ID '$designerMode' could be found.");
			return 1;
		}

		$contentType = $this->getContentTypeDetails();

		$write = function($entity)
		{
			$this->writeContent($entity);
		};

		$this->exportData($input, $output, $contentType['name'], $contentType['entity'], $style, $write);

		return 0;
	}

	protected function exportData(InputInterface $input, OutputInterface $output, $name, $entityName, \XF\Entity\Style $style, \Closure $write)
	{
		$start = microtime(true);

		$output->writeln("Exporting $name...");

		$designerOutput = \XF::app()->designerOutput();

		$finder = \XF::em()->getFinder($entityName)
			->where('style_id', $style->style_id);

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

		$designerOutput->enableBatchMode();

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

		$designerOutput->clearBatchMode();

		if ($progress)
		{
			$progress->finish();
			$output->writeln("");
		}

		$output->writeln(sprintf(ucfirst($name) . " exported. (%.02fs)", microtime(true) - $start));
	}
}