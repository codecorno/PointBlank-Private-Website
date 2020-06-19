<?php

namespace XF\Cli\Command\Development;

class ExportCodeEventListeners extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'code event listeners',
			'command' => 'code-event-listeners',
			'entity' => 'XF:CodeEventListener'
		];
	}
}