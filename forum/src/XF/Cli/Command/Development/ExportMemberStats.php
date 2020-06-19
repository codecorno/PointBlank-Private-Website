<?php

namespace XF\Cli\Command\Development;

class ExportMemberStats extends AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'member stats',
			'command' => 'member-stats',
			'entity' => 'XF:MemberStat'
		];
	}
}