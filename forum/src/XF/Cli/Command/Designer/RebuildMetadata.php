<?php

namespace XF\Cli\Command\Designer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RebuildMetadata extends Command
{
	protected function configure()
	{
		$this
			->setName('xf-designer:rebuild-metadata')
			->setDescription('Rebuilds metadata hashes based on file system content')
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
		$hasChanged = false;

		foreach ($designerOutput->getTypes() AS $type)
		{
			$changes = $designerOutput->rebuildTypeMetadata($type, $designerMode);
			if ($changes)
			{
				$hasChanged = true;

				$output->writeln("Rebuilding metadata hashes $type:");
				foreach ($changes AS $addOnId => $files)
				{
					foreach ($files AS $file)
					{
						$output->writeln("\t$addOnId/$file");
					}
				}
			}
		}

		if (!$hasChanged)
		{
			$output->writeln("No changes necessary.");
		}

		return 0;
	}
}