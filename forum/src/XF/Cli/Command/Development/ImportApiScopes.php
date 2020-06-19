<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportApiScopes extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'API scopes',
			'command' => 'api-scopes',
			'dir' => 'api_scopes',
			'entity' => 'XF:ApiScope'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		return \XF::db()->fetchPairs("
			SELECT REPLACE(api_scope_id, ':', '-'), api_scope_id
			FROM xf_api_scope
			WHERE addon_id = ?
		", $addOnId);
	}
}