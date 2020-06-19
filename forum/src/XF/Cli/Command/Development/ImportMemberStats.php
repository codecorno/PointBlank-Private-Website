<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportMemberStats extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'member stats',
			'command' => 'member-stats',
			'dir' => 'member_stats',
			'entity' => 'XF:MemberStat'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		return \XF::db()->fetchPairs("
			SELECT member_stat_key, member_stat_id
			FROM xf_member_stat
			WHERE addon_id = ?
		", $addOnId);
	}
}