<?php

namespace XF\Cli\Command\AddOn;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use XF\Cli\Command\AddOnActionTrait;
use XF\Util\File;
use XF\Util\Json;

class ValidateJson extends Command
{
	use AddOnActionTrait;

	protected function configure()
	{
		$this
			->setName('xf-addon:validate-json')
			->setDescription(
				'Validates the contents of the add-on JSON file to ensure all of the required values exist and are of the correct type.'
			)
			->addArgument(
				'id',
				InputArgument::REQUIRED,
				'Add-on ID'
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

		$output->writeln("Attempting to validate addon.json file...");

		/** @var QuestionHelper $helper */
		$helper = $this->getHelper('question');

		$jsonPath = $addOn->getJsonPath();
		if (!file_exists($jsonPath))
		{
			$question = new ConfirmationQuestion("<question>No JSON path can be found, create the default one? (y/n)</question> ");
			if ($helper->ask($input, $output, $question))
			{
				$json = $addOn->prepareJsonFile([
					'title' => $addOn->title,
					'version_id' => intval($addOn->version_id),
					'version_string' => $addOn->version_string
				]);
				$written = File::writeFile($jsonPath, Json::jsonEncodePretty(
					$addOn->prepareJsonFile($json)
				), false);

				if ($written)
				{
					$addOn->getInstalledAddOn()->fastUpdate('json_hash', \XF\Util\Hash::hashTextFile($jsonPath, 'sha256'));
					$output->writeln("The addon.json file was successfully written out to $jsonPath");
				}
				else
				{
					$output->writeln("<error>The addon.json file could not be written out to $jsonPath. Please create the addon.json file manually before continuing.</error>");
					return 1;
				}
			}
			else
			{
				$output->writeln("<error>Please create the addon.json file before continuing.</error>");
				return 1;
			}
		}
		else
		{
			$json = @json_decode(file_get_contents($addOn->getJsonPath()), true);
		}

		if (!is_array($json))
		{
			$output->writeln("<error>The contents of the addon.json file could not be decoded.</error>");
			return 1;
		}

		$requiredKeys = [
			'title',
			['version_id', 'is_int'],
			'version_string'
		];
		$optionalKeys = [
			'legacy_addon_id',
			'description',
			'dev',
			'dev_url',
			'faq_url',
			'support_url',
			['extra_urls', 'is_array'],
			['require', 'is_array'],
			'icon'
		];

		// keys which are uncommon or have a different meaning if unset
		$ignoreKeys = [
			'options',
			'composer_autoload'
		];

		$hasMissingKeys = false;
		$checkedKeys = [];
		$errors = [];
		$warnings = [];

		foreach ($requiredKeys AS $key)
		{
			if (is_array($key))
			{
				list($key, $f) = $key;
			}
			else
			{
				$f = 'is_string';
			}

			$checkedKeys[] = $key;

			if (!isset($json[$key]))
			{
				$errors[] = "Key '{$key}' is missing from your JSON file.";
				$hasMissingKeys = true;
				continue;
			}

			$actualType = gettype($json[$key]);
			if (!$f($json[$key]))
			{
				$type = str_replace('is_', '', $f);
				$errors[] = "Expected type '{$type}' for '{$key}' but is type '{$actualType}'.";
			}
		}

		foreach ($optionalKeys AS $key)
		{
			if (is_array($key))
			{
				list($key, $f) = $key;
			}
			else
			{
				$f = 'is_string';
			}

			$checkedKeys[] = $key;

			if (!isset($json[$key]))
			{
				$warnings[] = "Key '{$key}' is missing from your JSON file.";
				$hasMissingKeys = true;
				continue;
			}

			$actualType = gettype($json[$key]);
			if (!$f($json[$key]))
			{
				$type = str_replace('is_', '', $f);
				$errors[] = "Expected type '{$type}' for '{$key}' but is type '{$actualType}'.";
			}
		}

		$unexpectedKeys = [];
		foreach (array_keys($json) AS $key)
		{
			if (in_array($key, $checkedKeys) || in_array($key, $ignoreKeys))
			{
				continue;
			}
			$unexpectedKeys[] = $key;
		}

		if ($unexpectedKeys)
		{
			$warnings[] = "The following keys were found which were unexpected: '" . implode('\', \'', $unexpectedKeys) . "' these may be safe to ignore or they may represent mistakes.";
		}

		$fixedMissingKeys = false;
		if ($hasMissingKeys)
		{
			$question = new ConfirmationQuestion("<question>Missing keys were detected while validating your addon.json file. Would you like to set them to their default values? (y/n)</question> ");
			if ($helper->ask($input, $output, $question))
			{
				$written = File::writeFile($jsonPath, Json::jsonEncodePretty(
					$addOn->prepareJsonFile($json)
				), false);
				if ($written)
				{
					$fixedMissingKeys = true;
					$addOn->getInstalledAddOn()->fastUpdate('json_hash', \XF\Util\Hash::hashTextFile($jsonPath, 'sha256'));
					$output->writeln("The addon.json file was successfully written out to $jsonPath");
				}
			}
		}

		if ($errors)
		{
			$output->writeln(["", "<error>Please rectify the following error/s we found while validating your addon.json file before continuing:</error>"]);
			foreach ($errors AS $error)
			{
				$output->writeln("<info>\t * $error</info>");
			}
			if ($warnings)
			{
				$output->writeln(["", "<warning>Additionally, please review the following warning/s:</warning>"]);
				foreach ($warnings AS $warning)
				{
					$output->writeln("<info>\t * $warning</info>");
				}
			}
			if ($fixedMissingKeys)
			{
				$output->writeln(["", "The addon.json file was updated to repair missing keys, so no action required."]);
				// resolved so we won't error out (this may leave some unresolved errors, notably type errors, but
				// they shouldn't be significant and could get sorted in subsequent runs).
			}
			else
			{
				return 1;
			}
		}

		if ($warnings && !$errors)
		{
			$output->writeln(["", "<warning>Please review the following warning/s we found while validating your addon.json file. These are safe to ignore:</warning>"]);
			foreach ($warnings AS $warning)
			{
				$output->writeln("<info>\t * $warning</info>");
			}
			if ($fixedMissingKeys)
			{
				$output->writeln(["", "The addon.json file was updated to repair missing keys, so no action required."]);
			}
			// We won't block warnings only, consider it successful.
		}

		$output->writeln("JSON file validates successfully!");

		return 0;
	}
}