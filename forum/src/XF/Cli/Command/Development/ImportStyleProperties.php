<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportStyleProperties extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'style properties',
			'command' => 'style-properties',
			'dir' => 'style_properties',
			'entity' => 'XF:StyleProperty'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		if ($typeDir == 'style_property_groups')
		{
			return \XF::db()->fetchPairs("
				SELECT group_name, property_group_id
				FROM xf_style_property_group
				WHERE addon_id = ?
					AND style_id = 0
			", $addOnId);
		}
		else
		{
			return \XF::db()->fetchPairs("
				SELECT property_name, property_id
				FROM xf_style_property
				WHERE addon_id = ?
					AND style_id = 0
			", $addOnId);
		}
	}

	public function importData($typeDir, $fileName, $path, $content, $addOnId, array $metadata)
	{
		$id = preg_replace('/\.json$/', '', $fileName);

		if ($typeDir == 'style_property_groups')
		{
			\XF::app()->developmentOutput()->import('XF:StylePropertyGroup', $id, $addOnId, $content, $metadata, [
				'import' => true
			]);
		}
		else
		{
			\XF::app()->developmentOutput()->import('XF:StyleProperty', $id, $addOnId, $content, $metadata, [
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
				'name' => 'style property groups',
				'dir' => 'style_property_groups',
				'entity' => 'XF:StylePropertyGroup'
			];
			$this->executeType($groupType, $input, $output);
		}

		return $returnCode;
	}
}