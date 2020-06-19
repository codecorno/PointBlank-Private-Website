<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportAdvertisingPositions extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'advertising positions',
			'command' => 'advertising-positions',
			'dir' => 'advertising_positions',
			'entity' => 'XF:AdvertisingPosition'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		return \XF::db()->fetchPairs("
			SELECT position_id, position_id
			FROM xf_advertising_position
			WHERE addon_id = ?
		", $addOnId);
	}
}