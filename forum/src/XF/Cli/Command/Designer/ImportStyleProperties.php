<?php

namespace XF\Cli\Command\Designer;

use Symfony\Component\Console\Input\InputInterface;
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

	protected function getTitleIdMap($typeDir, $styleId)
	{
		if ($typeDir == 'style_property_groups')
		{
			return \XF::db()->fetchPairs("
				SELECT group_name, property_group_id
				FROM xf_style_property_group
				WHERE style_id = ?
			", $styleId);
		}
		else
		{
			return \XF::db()->fetchPairs("
				SELECT property_name, property_id
				FROM xf_style_property
				WHERE style_id = ?
			", $styleId);
		}
	}

	public function importData($typeDir, $fileName, $path, $content, \XF\Entity\Style $style, array $metadata)
	{
		$id = preg_replace('/\.json$/', '', $fileName);

		if ($typeDir == 'style_property_groups')
		{
			\XF::app()->designerOutput()->import('XF:StylePropertyGroup', $id, $style->style_id, $content, $metadata, [
				'import' => true
			]);
		}
		else
		{
			\XF::app()->designerOutput()->import('XF:StyleProperty', $id, $style->style_id, $content, $metadata, [
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
			$returnCode = $this->executeType($groupType, $input, $output);
		}

		return $returnCode;
	}
}