<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractImportCommand extends Command
{
	use RequiresDevModeTrait;

	// [command, name, entity, dir]
	abstract protected function getContentTypeDetails();

	abstract protected function getTitleIdMap($typeDir, $addOnId);

	public function importData($typeDir, $fileName, $path, $content, $addOnId, array $metadata)
	{
		$shortName = $this->getContentTypeDetails()['entity'];
		$id = preg_replace('/\.json$/', '', $fileName);
		\XF::app()->developmentOutput()->import($shortName, $id, $addOnId, $content, $metadata, [
			'import' => true
		]);
		return $id;
	}

	protected function configure()
	{
		$contentType = $this->getContentTypeDetails();

		$this
			->setName("xf-dev:import-$contentType[command]")
			->setDescription("Imports $contentType[name] from development files")
			->addOption(
				'addon',
				null,
				InputOption::VALUE_REQUIRED,
				'Add-on to limit to importing (default: all)'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->executeType($this->getContentTypeDetails(), $input, $output);

		return 0;
	}

	protected function executeType(array $contentType, InputInterface $input, OutputInterface $output)
	{
		$onlyAddOn = $input->getOption('addon');
		$start = microtime(true);

		$output->writeln("Importing $contentType[name]...");

		$db = \XF::db();
		$db->logQueries(false);
		$db->beginTransaction();

		$this->importDataForType(
			$contentType['dir'], $contentType['entity'], $onlyAddOn, $output
		);

		$this->afterExecuteType($contentType, $input, $output);

		$db->commit();

		\XF::triggerRunOnce();

		$output->writeln(sprintf(ucfirst($contentType['name']) . " imported. (%.02fs)", microtime(true) - $start));
	}

	protected function importDataForType($typeDir, $entity, $onlyAddOn = '', OutputInterface $output)
	{
		$devOutput = \XF::app()->developmentOutput();
		$allMetadata = $devOutput->getTypeMetadata($typeDir);
		$addOns = $devOutput->getAvailableTypeFilesByAddOn($typeDir);
		$installedAddOns = \XF::repository('XF:AddOn')->getInstalledAddOnData();

		foreach ($addOns AS $addOnId => $files)
		{
			if ($onlyAddOn && $onlyAddOn != $addOnId)
			{
				continue;
			}

			if (!isset($installedAddOns[$addOnId]))
			{
				$output->writeln("\tSkipping $addOnId - not installed.");
				continue;
			}

			$output->writeln("\tImporting $addOnId...");

			$metadata = isset($allMetadata[$addOnId]) ? $allMetadata[$addOnId] : [];
			$map = $this->importDataForAddOn($output, $typeDir, $files, $addOnId, $metadata);
			$this->deleteRemaining($typeDir, $map, $entity);

			$output->writeln("\tImported $addOnId.");
			$output->writeln("");
		}
	}

	protected function importDataForAddOn(OutputInterface $output, $typeDir, array $files, $addOnId, array $typeMetadata)
	{
		$map = $this->getTitleIdMap($typeDir, $addOnId);

		$printName = ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE);

		if (!$printName && $files)
		{
			$progress = new ProgressBar($output, count($files));
			$progress->start();
		}
		else
		{
			$progress = null;
		}

		foreach ($files AS $fileName => $path)
		{
			if ($printName)
			{
				$output->writeln("\t$fileName");
			}
			else if ($progress)
			{
				$progress->advance();
			}

			$metadata = isset($typeMetadata[$fileName]) ? $typeMetadata[$fileName] : [];
			$content = file_get_contents($path);

			$title = $this->importData($typeDir, $fileName, $path, $content, $addOnId, $metadata);

			unset($map[$title]);
		}

		if ($progress)
		{
			$progress->finish();
			$output->writeln("");
		}

		return $map;
	}

	protected function deleteRemaining($typeDir, array $map, $entity)
	{
		if ($map)
		{
			$old = \XF::em()->findByIds($entity, $map);
			foreach ($old AS $entity)
			{
				$entity->delete();
			}
		}
	}

	protected function afterExecuteType(array $contentType, InputInterface $input, OutputInterface $output)
	{

	}
}