<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportTemplates extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'templates',
			'command' => 'templates',
			'dir' => 'templates',
			'entity' => 'XF:Template'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		return \XF::db()->fetchPairs("
			SELECT CONCAT(type, '/', title), template_id
			FROM xf_template
			WHERE addon_id = ? AND style_id = 0
		", $addOnId);
	}

	public function importData($typeDir, $fileName, $path, $content, $addOnId, array $metadata)
	{
		/** @var \XF\DevelopmentOutput\Template $devOutputHandler */
		$devOutputHandler = \XF::app()->developmentOutput()->getHandler('XF:Template');
		$title = $devOutputHandler->convertTemplateFileToName($fileName);
		$template = $devOutputHandler->import($title, $addOnId, $content, $metadata, [
			'import' => true
		]);
		return "$template->type/$template->title";
	}
}