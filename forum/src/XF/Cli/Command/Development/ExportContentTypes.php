<?php

namespace XF\Cli\Command\Development;

class ExportContentTypes extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'content types',
			'command' => 'content-types',
			'entity' => 'XF:ContentTypeField'
		];
	}
}