<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait RequiresDevModeTrait
{
	public function run(InputInterface $input, OutputInterface $output)
	{
		$config = \XF::config();

		if (!$config['development']['enabled'])
		{
			$output->writeln("<error>Development mode is not enabled</error>");
			return 1;
		}

		return parent::run($input, $output);
	}
}