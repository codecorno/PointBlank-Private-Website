<?php

namespace XF\Cli\Command\AddOn;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Cli\Command\AddOnActionTrait;

class SyncJson extends Command
{
	use AddOnActionTrait;

	protected function configure()
	{
		$this
			->setName('xf-addon:sync-json')
			->setDescription(
				'Syncs the contents of the add-on JSON file to the database, updating the title, version and JSON hash as necessary'
			)
			->addArgument(
				'id',
				InputArgument::REQUIRED,
				'Add-on ID'
			)
			->addOption(
				'force',
				'f',
				InputOption::VALUE_NONE,
				'Skip validation of version ID (ignores downgrades)'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$id = $input->getArgument('id');

		$addOn = $this->checkEditableAddOn($id, $error);
		if (!$addOn)
		{
			$output->writeln('<error>' . $error . '</error>');
			return 1;
		}

		$json = $addOn->getJson();

		if (!$input->getOption('force'))
		{
			if (!$addOn->hasPendingChanges())
			{
				$output->writeln("<error>There do not appear to be any JSON changes to sync.</error>");
				return 1;
			}

			if ($addOn->version_id > $json['version_id'])
			{
				$output->writeln("<error>" . \XF::phrase('downgrading_existing_add_on_is_not_supported') . "</error>");
				return 1;
			}
		}

		$installed = $addOn->getInstalledAddOn();
		$installed->bulkSet([
			'title' => $json['title'],
			'version_string' => $json['version_string'],
			'version_id' => $json['version_id'],
			'json_hash' => $addOn->getJsonHash()
		]);
		$installed->save();

		$output->writeln("JSON contents synced to the database successfully.");
		return 0;
	}
}