<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportBbCode extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'bb codes',
			'command' => 'bb-codes',
			'dir' => 'bb_codes',
			'entity' => 'XF:BbCode'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		return \XF::db()->fetchPairs("
			SELECT bb_code_id, bb_code_id
			FROM xf_bb_code
			WHERE addon_id = ?
		", $addOnId);
	}
}