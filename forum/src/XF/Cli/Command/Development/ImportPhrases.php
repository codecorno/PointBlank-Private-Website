<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPhrases extends AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'phrases',
			'command' => 'phrases',
			'dir' => 'phrases',
			'entity' => 'XF:Phrase'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		return \XF::db()->fetchPairs("
			SELECT title, phrase_id
			FROM xf_phrase
			WHERE addon_id = ? AND language_id = 0
		", $addOnId);
	}

	public function importData($typeDir, $fileName, $path, $content, $addOnId, array $metadata)
	{
		$title = preg_replace('/\.txt$/', '', $fileName);
		\XF::app()->developmentOutput()->import('XF:Phrase', $title, $addOnId, $content, $metadata, [
			'import' => true
		]);
		return $title;
	}

	protected function afterExecuteType(array $contentType, InputInterface $input, OutputInterface $output)
	{
		/** @var \XF\Service\Phrase\Rebuild $rebuilder */
		$rebuilder = \XF::app()->service('XF:Phrase\Rebuild');
		$rebuilder->rebuildFullPhraseMap();

		/** @var \XF\Service\Phrase\Group $groupService */
		$groupService = \XF::app()->service('XF:Phrase\Group');
		$groupService->compileAllPhraseGroups();

		// TODO: how to handle rebuild of templates including phrases?
	}
}