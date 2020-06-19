<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MetadataClean extends Command
{
	use RequiresDevModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-dev:metadata-clean')
			->setDescription('Cleans up the metadata JSON files and removes missing entries');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$devOutput = \XF::app()->developmentOutput();
		$hasChanged = false;

		foreach ($devOutput->getTypes() AS $type)
		{
			$changes = $devOutput->cleanTypeMetadata($type);
			if ($changes)
			{
				$hasChanged = true;

				$output->writeln("Cleaned up $type:");
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