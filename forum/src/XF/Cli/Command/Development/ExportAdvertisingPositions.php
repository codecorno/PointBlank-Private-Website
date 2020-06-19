<?php

namespace XF\Cli\Command\Development;

class ExportAdvertisingPositions extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'advertising positions',
			'command' => 'advertising-positions',
			'entity' => 'XF:AdvertisingPosition'
		];
	}
}