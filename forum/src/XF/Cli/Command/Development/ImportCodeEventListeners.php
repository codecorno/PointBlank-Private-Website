<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCodeEventListeners extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'code event listeners',
			'command' => 'code-event-listeners',
			'dir' => 'code_event_listeners',
			'entity' => 'XF:CodeEventListener'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		return \XF::db()->fetchPairs("
			SELECT CONCAT(addon_id, '/', event_id, '/', callback_class, '/', callback_method, '/', hint), event_listener_id
			FROM xf_code_event_listener
			WHERE addon_id = ?
		", $addOnId);
	}

	public function importData($typeDir, $fileName, $path, $content, $addOnId, array $metadata)
	{
		$listener = \XF::app()->developmentOutput()->import('XF:CodeEventListener', $fileName, $addOnId, $content, $metadata, [
			'import' => true
		]);

		return "$listener->addon_id/$listener->event_id/$listener->callback_class/$listener->callback_method/$listener->hint";
	}
}