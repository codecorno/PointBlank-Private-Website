<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Recompile extends Command
{
	use RequiresDevModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-dev:recompile')
			->setDescription('Recompiles all template/phrase data');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$recompilers = [
			'xf-dev:recompile-phrases',
			'xf-dev:recompile-templates'
		];

		foreach ($recompilers AS $recompiler)
		{
			$command = $this->getApplication()->find($recompiler);

			$i = ['command' => $recompiler];

			$childInput = new ArrayInput($i);
			$command->run($childInput, $output);
		}

		return 0;
	}
}