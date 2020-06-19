<?php

namespace XF\Cli\Command\AddOn;

use Symfony\Component\Console\Input\InputArgument;

class InstallStep extends AbstractSetupStep
{
	protected function getStepType()
	{
		return 'install';
	}

	protected function getCommandArguments()
	{
		$this->addArgument(
			'step',
			InputArgument::REQUIRED,
			'The step number to run.'
		);
	}
}