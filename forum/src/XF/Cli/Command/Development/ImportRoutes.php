<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportRoutes extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'routes',
			'command' => 'routes',
			'dir' => 'routes',
			'entity' => 'XF:Route'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		return \XF::db()->fetchPairs("
			SELECT CONCAT(route_type, '/', route_prefix, '/', sub_name), route_id
			FROM xf_route
			WHERE addon_id = ?
		", $addOnId);
	}

	public function importData($typeDir, $fileName, $path, $content, $addOnId, array $metadata)
	{
		$metadata = isset($allMetadata[$addOnId][$fileName]) ? $allMetadata[$addOnId][$fileName] : [];
		$route = \XF::app()->developmentOutput()->import('XF:Route', $fileName, $addOnId, $content, $metadata, [
			'import' => true
		]);

		return "$route->route_type/$route->route_prefix/$route->sub_name";
	}
}