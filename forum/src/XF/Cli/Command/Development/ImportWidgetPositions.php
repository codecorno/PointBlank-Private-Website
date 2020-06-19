<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportWidgetPositions extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'widget positions',
			'command' => 'widget-positions',
			'dir' => 'widget_positions',
			'entity' => 'XF:WidgetPosition'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		return \XF::db()->fetchPairs("
			SELECT position_id, position_id
			FROM xf_widget_position
			WHERE addon_id = ?
		", $addOnId);
	}
}