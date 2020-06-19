<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateApiDocs extends Command
{
	use RequiresDevModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-dev:generate-api-docs')
			->setDescription('Generates the REST API documentation')
			->addOption(
				'renderer',
				null,
				InputOption::VALUE_REQUIRED,
				'The type of renderer to use'
			)
			->addOption(
				'target',
				null,
				InputOption::VALUE_REQUIRED,
				'Target location to write the output to'
			)
			->addOption(
				'force',
				null,
				InputOption::VALUE_NONE,
				'Force writing to the target'
			)
			->addArgument(
				'ids',
				InputArgument::IS_ARRAY | InputArgument::REQUIRED,
				'List of add-on IDs to include (space separated'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$ids = $input->getArgument('ids');

		$addOnIds = [];
		foreach ($ids AS $id)
		{
			if ($id == 'XF+')
			{
				$addOnIds[] = 'XF';
				$addOnIds[] = 'XFMG';
				$addOnIds[] = 'XFRM';
			}
			else
			{
				$addOnIds[] = $id;
			}
		}

		$addOnIds = array_unique($addOnIds);

		$apiDocs = \XF::app()->apiDocs();
		$compiler = $apiDocs->compiler();

		$rendererName = $input->getOption('renderer') ?: 'simpleHtml';
		$renderer = $apiDocs->renderer($rendererName);

		$target = $input->getOption('target');
		if ($target)
		{
			$target = \XF\Util\File::canonicalizePath($target);

			$success = $renderer->setTarget($target, $error, $input->getOption('force'));
			if (!$success)
			{
				$output->write('<error>' . $error . '</error>');
				return 1;
			}
		}

		foreach ($addOnIds AS $id)
		{
			$compiler->compileForAddOn($id);
		}

		if ($target)
		{
			// TODO: output unknown lines
		}

		$output->write($compiler->render($renderer));

		return 0;
	}
}