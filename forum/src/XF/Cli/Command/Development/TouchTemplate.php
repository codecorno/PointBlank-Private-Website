<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TouchTemplate extends Command
{
	use RequiresDevModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-dev:touch-template')
			->setDescription('Updates the version number of the specified template')
			->addArgument(
				'template',
				InputArgument::REQUIRED,
				'Template name (in type:name format)'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$templateInput = $input->getArgument('template');

		$parts = explode(':', $templateInput, 2);
		if (count($parts) == 1)
		{
			$type = 'public';
			$templateName = $parts[0];
		}
		else
		{
			$type = $parts[0];
			$templateName = $parts[1];
		}

		/** @var \XF\Entity\Template|null $template */
		$template = \XF::em()->findOne('XF:Template', [
			'style_id' => 0,
			'type' => $type,
			'title' => $templateName
		]);
		if (!$template)
		{
			$output->writeln("<error>Template '$type:$templateName' not found.</error>");
			return 1;
		}

		$template->updateVersionId();
		$template->save();

		$output->writeln("Template '$type:$templateName' version updated to {$template->version_id} ({$template->version_string}).");

		return 0;
	}
}