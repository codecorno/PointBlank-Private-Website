<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class RecompilePhrases extends Command
{
	use RequiresDevModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-dev:recompile-phrases')
			->setDescription('Recompiles phrases');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$db = \XF::db();
		$em = \XF::em();
		$app = \XF::app();
		$start = microtime(true);

		$output->writeln("Rebuilding phrase map...");

		/** @var \XF\Service\Phrase\Rebuild $rebuildService */
		$rebuildService = $app->service('XF:Phrase\Rebuild');
		$rebuildService->rebuildFullPhraseMap();

		/** @var \XF\Service\Phrase\Group $groupService */
		$groupService = $app->service('XF:Phrase\Group');
		$groupService->compileAllPhraseGroups();

		$output->writeln("Recompiling phrases...");

		$phraseIds = $db->fetchAllColumn("
			SELECT phrase_id
			FROM xf_phrase
			ORDER BY phrase_id
		");

		$progress = new ProgressBar($output, count($phraseIds));
		$progress->start();

		/** @var \XF\Service\Phrase\Compile $compileService */
		$compileService = $app->service('XF:Phrase\Compile');

		foreach ($phraseIds AS $phraseId)
		{
			$progress->advance();

			/** @var \XF\Entity\Phrase $phrase */
			$phrase = $em->find('XF:Phrase', $phraseId);
			if (!$phrase)
			{
				continue;
			}

			$phrase->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);

			$compileService->recompile($phrase);

			$em->clearEntityCache(); // workaround memory issues
		}

		$progress->finish();
		$output->writeln("");

		$output->writeln(sprintf("Phrases compiled. (%.02fs)", microtime(true) - $start));

		return 0;
	}
}