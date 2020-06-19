<?php

namespace XF\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FileCheck extends Command
{
	protected function configure()
	{
		$this
			->setName('xf:file-check')
			->setDescription('Performs a file health check')
			->addOption(
				'addon',
				'a',
				InputOption::VALUE_REQUIRED,
				'Add-on to limit to checking (default: all)'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$addOnInfo = [];

		$ds = \XF::$DS;
		$addOnId = $input->getOption('addon');
		if ($addOnId == 'XF')
		{
			$addOnInfo['XF'] = [
				'title' => 'XenForo',
				'hash' => \XF::getAddOnDirectory() . $ds . 'XF' . $ds . 'hashes.json'
			];
		}
		else if ($addOnId)
		{
			/** @var \XF\AddOn\AddOn $addOn */
			$addOn = \XF::app()->addOnManager()->getById($addOnId);
			if (!$addOn)
			{
				$output->writeln('<error>' . \XF::phrase('please_enter_valid_addon_id') . '</error>');
				return 1;
			}
			$addOnInfo[$addOnId] = [
				'title' => $addOn->title,
				'hash' => $addOn->getHashesPath()
			];
		}
		else
		{
			$addOns = \XF::app()->addOnManager()->getAllAddOns();
			foreach ($addOns AS $addOnId => $addOn)
			{
				if (!$addOn->isInstalled())
				{
					continue;
				}
				$addOnInfo[$addOnId] = [
					'title' => $addOn->title,
					'hash' => $addOn->getHashesPath()
				];
			}
			$addOnInfo['XF'] = [
				'title' => 'XenForo',
				'hash' => \XF::getAddOnDirectory() . $ds . 'XF' . $ds . 'hashes.json'
			];
		}

		$results = [
			'missing' => [],
			'inconsistent' => [],
			'total_missing' => 0,
			'total_inconsistent' => 0,
			'total_checked' => 0
		];

		$rootPrefix = \XF::getRootDirectory() . $ds;

		$hashesMissing = [];

		foreach ($addOnInfo AS $addOnId => $info)
		{
			if (!file_exists($info['hash']))
			{
				$hashesMissing[$addOnId] = $info;
				continue;
			}

			$json = json_decode(file_get_contents($info['hash']), true);
			foreach ((array)$json AS $info => $hash)
			{
				$path = $rootPrefix . $info;

				$results['total_checked']++;
				if (!file_exists($path))
				{
					$results['missing'][$addOnId][] = $info;
					$results['total_missing']++;
				}
				else if (\XF\Util\Hash::hashTextFile($path, 'sha256') !== $hash)
				{
					$results['inconsistent'][$addOnId][] = $info;
					$results['total_inconsistent']++;
				}
			}
		}

		if ($hashesMissing)
		{
			$skipMissingHashes = \XF::app()->config['development']['enabled'];

			$output->writeln('<error>' . \XF::phrase('following_add_ons_missing_necessary_files_for_health_checking') . '</error>');
			foreach ($hashesMissing AS $addOn)
			{
				$output->writeln("\t * $addOn[title]");
			}

			if ($skipMissingHashes)
			{
				$output->writeln(\XF::phrase('execution_has_been_allowed_to_continue')->render());
			}
			else
			{
				return 1;
			}
		}

		if (!$results['total_missing'] && !$results['total_inconsistent'])
		{
			if (!$hashesMissing)
			{
				$output->writeln('<info>' . \XF::phrase('file_health_check_okay', ['numFiles' => $results['total_checked']]) . '</info>');
			}

			return 0;
		}
		$table = new Table($output);

		foreach ($addOnInfo AS $addOnId => $info)
		{
			if (isset($results['missing'][$addOnId]) || isset($results['inconsistent'][$addOnId]))
			{
				$table->setHeaders([
					[new TableCell($info['title'], ['colspan' => 2])],
					['File path', 'Status']
				]);

				if (isset($results['inconsistent'][$addOnId]))
				{
					foreach ($results['inconsistent'][$addOnId] AS $path)
					{
						$table->addRow([$path, 'Inconsistent']);
					}

					if (isset($results['missing'][$addOnId]))
					{
						$table->addRow(new TableSeparator());
					}
				}
				if (isset($results['missing'][$addOnId]))
				{
					foreach ($results['missing'][$addOnId] AS $path)
					{
						$table->addRow([$path, 'Missing']);
					}
				}
			}
		}

		$output->writeln('<error>' . \XF::phrase('check_completed_on_x_files', ['total_checked' => $results['total_checked']]) . '</error>');

		$table->render();

		return 1;
	}
}