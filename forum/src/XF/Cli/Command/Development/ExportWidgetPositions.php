<?php

namespace XF\Cli\Command\Development;

class ExportWidgetPositions extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'widget positions',
			'command' => 'widget-positions',
			'entity' => 'XF:WidgetPosition'
		];
	}
}