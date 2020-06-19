<?php

namespace XF\Cli\Command\Development;

use XF\Mvc\Entity\Finder;

class ExportTemplateModifications extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'template modifications',
			'command' => 'template-modifications',
			'entity' => 'XF:TemplateModification'
		];
	}
}