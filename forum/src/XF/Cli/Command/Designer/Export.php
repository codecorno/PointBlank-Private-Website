<?php

namespace XF\Cli\Command\Designer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Export extends Command
{
	use RequiresDesignerModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-designer:export')
			->setDescription('Exports modified templates from the database to the file system for the specified designer mode.')
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

		$exporters = [
			'xf-designer:export-style-properties',
			'xf-designer:export-templates'
		];

		foreach ($exporters AS $exporter)
		{
			$command = $this->getApplication()->find($exporter);

			$i = [
				'command' => $exporter,
				'designer-mode' => $designerMode
			];

			$childInput = new ArrayInput($i);
			$command->run($childInput, $output);
		}

		return 0;
	}
}