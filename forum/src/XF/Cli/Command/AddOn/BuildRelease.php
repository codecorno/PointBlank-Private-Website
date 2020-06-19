<?php

namespace XF\Cli\Command\AddOn;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Cli\Command\AddOnActionTrait;

class BuildRelease extends Command
{
	use AddOnActionTrait;

	protected function configure()
	{
		$this
			->setName('xf-addon:build-release')
			->setDescription(
				'Performs an export of the current XML data and saves a ZIP file to _releases'
			)
			->addArgument(
				'id',
				InputArgument::REQUIRED,
				'Add-on ID'
			)
			->addOption(
				'skip-hashes',
				null,
				InputOption::VALUE_NONE,
				'Skips generating hashes and including them in the built release.'
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

		if (!class_exists('ZipArchive'))
		{
			$output->writeln('<error>' . \XF::phrase('required_php_extension_x_not_found', ['extension' => 'ZipArchive']) . '</error>');
			return 1;
		}

		$output->writeln("Performing add-on export.");

		$command = $this->getApplication()->find('xf-addon:export');
		$childInput = new ArrayInput([
			'command' => 'xf-addon:export',
			'id' => $addOn->addon_id
		]);
		$command->run($childInput, $output);

		$command = $this->getApplication()->find('xf-addon:validate-json');
		$childInput = new ArrayInput([
			'command' => 'xf-addon:validate-json',
			'id' => $addOn->addon_id
		]);
		if ($command->run($childInput, $output) !== 0)
		{
			return 1;
		}

		/** @var \XF\Service\AddOn\ReleaseBuilder $builderService */
		$builderService = \XF::app()->service('XF:AddOn\ReleaseBuilder', $addOn);

		$skipHashes = $input->getOption('skip-hashes');
		if ($skipHashes)
		{
			$output->writeln(["", "Skipping generating hashes."]);
			$builderService->setGenerateHashes(false);
		}

		$output->writeln(["", "Building release ZIP."]);
		$builderService->build();
		$output->writeln(["", "Writing release ZIP to {$addOn->getReleasesDirectory()}."]);
		$builderService->finalizeRelease();
		$output->writeln(["", "Release written successfully."]);

		return 0;
	}
}