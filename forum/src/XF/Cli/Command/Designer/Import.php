<?php

namespace XF\Cli\Command\Designer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Import extends Command
{
	use RequiresDesignerModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-designer:import')
			->setDescription('Imports designer files from the file system for the specified designer mode.')
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

		$importers = [
			'xf-designer:import-style-properties',
			'xf-designer:import-templates'
		];

		$start = microtime(true);

		foreach ($importers AS $importer)
		{
			$command = $this->getApplication()->find($importer);

			$i = [
				'command' => $importer,
				'designer-mode' => $designerMode
			];

			$childInput = new ArrayInput($i);
			$command->run($childInput, $output);
			$output->writeln("");
		}

		$total = microtime(true) - $start;
		$output->writeln(sprintf("All data imported. (%.02fs)", $total));

		return 0;
	}
}