<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Import extends Command
{
	use RequiresDevModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-dev:import')
			->setDescription('Imports all data from development files')
			->addOption(
				'addon',
				'a',
				InputOption::VALUE_REQUIRED,
				'Add-on to limit to importing (default: all)'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// phrases go first as certain things (like templates) depend on the new values
		$importers = [
			'xf-dev:import-phrases',
			'xf-dev:import-admin-navigation',
			'xf-dev:import-admin-permissions',
			'xf-dev:import-advertising-positions',
			'xf-dev:import-api-scopes',
			'xf-dev:import-bb-codes',
			'xf-dev:import-bb-code-media-sites',
			'xf-dev:import-class-extensions',
			'xf-dev:import-code-events',
			'xf-dev:import-code-event-listeners',
			'xf-dev:import-content-types',
			'xf-dev:import-cron-entries',
			'xf-dev:import-help-pages',
			'xf-dev:import-member-stats',
			'xf-dev:import-navigation',
			'xf-dev:import-options',
			'xf-dev:import-permissions',
			'xf-dev:import-routes',
			'xf-dev:import-style-properties',
			'xf-dev:import-template-modifications',
			'xf-dev:import-templates',
			'xf-dev:import-widget-definitions',
			'xf-dev:import-widget-positions'
		];

		$addOn = $input->getOption('addon');

		$version = \XF::$version;
		$output->writeln("XenForo {$version}");

		$start = microtime(true);

		foreach ($importers AS $importer)
		{
			$command = $this->getApplication()->find($importer);

			$i = ['command' => $importer];
			if ($addOn)
			{
				$i['--addon'] = $addOn;
			}

			$childInput = new ArrayInput($i);
			$command->run($childInput, $output);
			$output->writeln("");

			// keep the memory limit down on long running jobs
			\XF::em()->clearEntityCache();
		}

		$command = $this->getApplication()->find('xf-dev:rebuild-caches');
		$childInput = new ArrayInput(['command' => 'xf-dev:rebuild-caches']);
		$command->run($childInput, $output);
		$output->writeln("");

		$total = microtime(true) - $start;
		$output->writeln(sprintf("All data imported. (%.02fs)", $total));

		return 0;
	}
}