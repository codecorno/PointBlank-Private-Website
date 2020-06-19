<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Export extends Command
{
	use RequiresDevModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-dev:export')
			->setDescription('Exports all data to development files')
			->addOption(
				'addon',
				'a',
				InputOption::VALUE_REQUIRED,
				'Add-on to limit to exporting (default: all)'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$exporters = [
			'xf-dev:export-admin-navigation',
			'xf-dev:export-admin-permissions',
			'xf-dev:export-advertising-positions',
			'xf-dev:export-api-scopes',
			'xf-dev:export-bb-codes',
			'xf-dev:export-bb-code-media-sites',
			'xf-dev:export-class-extensions',
			'xf-dev:export-code-event-listeners',
			'xf-dev:export-code-events',
			'xf-dev:export-content-types',
			'xf-dev:export-cron-entries',
			'xf-dev:export-help-pages',
			'xf-dev:export-member-stats',
			'xf-dev:export-navigation',
			'xf-dev:export-options',
			'xf-dev:export-permissions',
			'xf-dev:export-phrases',
			'xf-dev:export-routes',
			'xf-dev:export-style-properties',
			'xf-dev:export-template-modifications',
			'xf-dev:export-templates',
			'xf-dev:export-widget-definitions',
			'xf-dev:export-widget-positions'
		];

		$addOn = $input->getOption('addon');

		foreach ($exporters AS $exporter)
		{
			$command = $this->getApplication()->find($exporter);

			$i = ['command' => $exporter];
			if ($addOn)
			{
				$i['--addon'] = $addOn;
			}

			$childInput = new ArrayInput($i);
			$command->run($childInput, $output);
		}

		return 0;
	}
}