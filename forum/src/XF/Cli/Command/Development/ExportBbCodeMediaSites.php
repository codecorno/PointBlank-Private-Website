<?php

namespace XF\Cli\Command\Development;

class ExportBbCodeMediaSites extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'bb code media sites',
			'command' => 'bb-code-media-sites',
			'entity' => 'XF:BbCodeMediaSite'
		];
	}
}