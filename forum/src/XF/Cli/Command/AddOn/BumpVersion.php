<?php

namespace XF\Cli\Command\AddOn;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use XF\Cli\Command\AddOnActionTrait;
use XF\Util\File;
use XF\Util\Json;

class BumpVersion extends Command
{
	use AddOnActionTrait;

	protected function configure()
	{
		$this
			->setName('xf-addon:bump-version')
			->setDescription('Bumps the version of the specified add-on')
			->addArgument(
				'id',
				InputArgument::REQUIRED,
				'Add-On ID'
			)
			->addOption(
				'version-id',
				null,
				InputOption::VALUE_REQUIRED,
				'If provided, this will be used as the new version ID'
			)
			->addOption(
				'version-string',
				null,
				InputOption::VALUE_REQUIRED,
				'If provided, this will be used as the new version string'
			)
			->addOption(
				'from-json',
				null,
				InputOption::VALUE_NONE,
				'Bump the add-on version in the database from the specified addon.json'
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

		/** @var \XF\Repository\AddOn $addOnRepo */
		$addOnRepo = \XF::repository('XF:AddOn');

		/** @var QuestionHelper $helper */
		$helper = $this->getHelper('question');

		$fromJson = $input->getOption('from-json');
		if ($fromJson)
		{
			$json = $addOn->getJson();
			$versionId = $json['version_id'];
			$versionString = $json['version_string'];
		}
		else
		{
			$versionString = $input->getOption('version-string');
			$versionId = $input->getOption('version-id');

			if ($versionId)
			{
				if (!$versionString)
				{
					$versionString = $addOnRepo->inferVersionStringFromId($versionId);
				}
			}
			else
			{
				$question = new Question("<question>Enter a version ID.</question><info> (Current version ID: {$addOn->version_id}): </info>");
				$question->setValidator(function($value)
				{
					if (!preg_match('/^[0-9]+$/', $value) || !trim($value))
					{
						throw new \InvalidArgumentException("The version ID should contain numeric values only.");
					}
					return $value;
				});
				$versionId = $helper->ask($input, $output, $question);
				$output->writeln("");

				if (!$versionString)
				{
					$versionString = $addOnRepo->inferVersionStringFromId($versionId);
				}
			}
		}

		$installed = $addOn->getInstalledAddOn();

		if ($versionId)
		{
			if (!$input->getOption('force'))
			{
				if ($addOn->version_id > $versionId)
				{
					$output->writeln("<error>Downgrading an existing add-on is not supported.</error>");
					return 1;
				}
			}
			$installed->version_id = $versionId;
			$output->writeln("<info>Version ID set to: {$versionId}</info>");
		}

		if (!$versionString)
		{
			$question = new Question("<question>Enter a version string.</question><info> (Current version string: {$addOn->version_string}): </info>");
			$question->setValidator(function($value)
			{
				if (!trim($value))
				{
					throw new \InvalidArgumentException("A version string is required.");
				}
				return $value;
			});
			$versionString = $helper->ask($input, $output, $question);
			$output->writeln("");
		}

		if ($versionString)
		{
			$installed->version_string = $versionString;
			$output->writeln("<info>Version string set to: {$versionString}</info>");
		}

		if ($installed->save())
		{
			if (!$fromJson)
			{
				$jsonPath = $addOn->getJsonPath();
				$json = $addOn->getJson();
				$json['version_id'] = intval($versionId);
				$json['version_string'] = $versionString;

				$written = File::writeFile($jsonPath, Json::jsonEncodePretty($addOn->prepareJsonFile($json)), false);

				if ($written)
				{
					$installed->fastUpdate('json_hash', \XF\Util\Hash::hashTextFile($jsonPath, 'sha256'));
					$output->writeln("The addon.json file was successfully written out to $jsonPath");
				}
				else
				{
					$output->writeln("<error>The addon.json file could not be written out to $jsonPath</error>");
				}
			}
		}
		else
		{
			$output->writeln("<error>An unexpected error occurred while updating the version details for this add-on.</error>");
			return 1;
		}

		$output->writeln("Add-on successfully updated to version $versionString ($versionId).");

		return 0;
	}
}