<?php

namespace XF\Cli\Command\AddOn;

use Symfony\Component\Console\Input\InputArgument;

class UpgradeStep extends AbstractSetupStep
{
	protected function getStepType()
	{
		return 'upgrade';
	}

	protected function getCommandArguments()
	{
		$this
			->addArgument(
				'version',
				InputArgument::REQUIRED,
				'The version number to run.'
			)
			->addArgument(
				'step',
				InputArgument::REQUIRED,
				'The step number to run.'
			);
	}
}