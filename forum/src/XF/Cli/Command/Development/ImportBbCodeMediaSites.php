<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportBbCodeMediaSites extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'bb code media sites',
			'command' => 'bb-code-media-sites',
			'dir' => 'bb_code_media_sites',
			'entity' => 'XF:BbCodeMediaSite'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		return \XF::db()->fetchPairs("
			SELECT media_site_id, media_site_id
			FROM xf_bb_code_media_site
			WHERE addon_id = ?
		", $addOnId);
	}
}