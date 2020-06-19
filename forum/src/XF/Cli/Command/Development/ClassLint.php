<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClassLint extends Command
{
	use RequiresDevModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-dev:class-lint')
			->setDescription('Checks that all classes can be loaded without conflicts');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$srcDir = \XF::getSourceDirectory() . \XF::$DS . 'XF';
		$dirIterator = \XF\Util\File::getRecursiveDirectoryIterator($srcDir, null, null);
		$iterator = new \RegexIterator($dirIterator, '/^.+\.php$/');

		foreach ($iterator AS $file)
		{
			if (preg_match('#(^|\\\\|/)(tests|test)(\\\\|/|$)#i', $file->getPath()))
			{
				continue;
			}
			if (!preg_match('#^[A-Z].+\.php$#', $file->getFilename()))
			{
				continue;
			}

			require_once($file->getPathname());
		}

		return 0;
	}
}