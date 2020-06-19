<?php

namespace XF\Cli\Command\Development;

class ExportWidgetDefinitions extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'widget definitions',
			'command' => 'widget-definitions',
			'entity' => 'XF:WidgetDefinition'
		];
	}
}