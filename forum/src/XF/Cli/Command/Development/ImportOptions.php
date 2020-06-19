<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportOptions extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'options',
			'command' => 'options',
			'dir' => 'options',
			'entity' => 'XF:Option'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		if ($typeDir == 'option_groups')
		{
			return \XF::db()->fetchPairs("
				SELECT group_id, group_id
				FROM xf_option_group
				WHERE addon_id = ?
			", $addOnId);
		}
		else
		{
			return \XF::db()->fetchPairs("
				SELECT option_id, option_id
				FROM xf_option
				WHERE addon_id = ?
			", $addOnId);
		}
	}

	public function importData($typeDir, $fileName, $path, $content, $addOnId, array $metadata)
	{
		$id = preg_replace('/\.json$/', '', $fileName);

		if ($typeDir == 'option_groups')
		{
			\XF::app()->developmentOutput()->import('XF:OptionGroup', $id, $addOnId, $content, $metadata, [
				'import' => true
			]);
		}
		else
		{
			\XF::app()->developmentOutput()->import('XF:Option', $id, $addOnId, $content, $metadata, [
				'import' => true
			]);
		}

		return $id;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$returnCode = parent::execute($input, $output);
		if (!$returnCode)
		{
			// success
			$groupType = [
				'name' => 'option groups',
				'dir' => 'option_groups',
				'entity' => 'XF:OptionGroup'
			];
			$this->executeType($groupType, $input, $output);
		}

		return $returnCode;
	}
}