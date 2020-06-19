<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportClassExtensions extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'class extensions',
			'command' => 'class-extensions',
			'dir' => 'class_extensions',
			'entity' => 'XF:ClassExtension'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		return \XF::db()->fetchPairs("
			SELECT CONCAT(from_class, '/', to_class), extension_id
			FROM xf_class_extension
			WHERE addon_id = ?
		", $addOnId);
	}

	public function importData($typeDir, $fileName, $path, $content, $addOnId, array $metadata)
	{
		$extension = \XF::app()->developmentOutput()->import('XF:ClassExtension', $fileName, $addOnId, $content, $metadata, [
			'import' => true
		]);

		return "$extension->from_class/$extension->to_class";
	}
}