<?php

namespace XF\Cli\Command\Development;

use XF\Mvc\Entity\Finder;

class ExportTemplates extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'templates',
			'command' => 'templates',
			'entity' => 'XF:Template'
		];
	}

	protected function extraFinderConditions(Finder $finder)
	{
		$finder->where('style_id', 0)
			->order(['type', 'title']);
	}
}