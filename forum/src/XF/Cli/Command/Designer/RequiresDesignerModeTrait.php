<?php

namespace XF\Cli\Command\Designer;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait RequiresDesignerModeTrait
{
	public function run(InputInterface $input, OutputInterface $output)
	{
		$config = \XF::config();

		if (!$config['designer']['enabled'])
		{
			$output->writeln("<error>Designer mode is not enabled</error>");
			return 1;
		}

		return parent::run($input, $output);
	}
}