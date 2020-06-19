<?php

namespace XF\Cli\Command\Development;

use XF\Mvc\Entity\Finder;

class ExportPhrases extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'phrases',
			'command' => 'phrases',
			'entity' => 'XF:Phrase'
		];
	}

	protected function extraFinderConditions(Finder $finder)
	{
		$finder->where('language_id', 0);
	}
}