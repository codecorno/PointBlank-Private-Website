<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class RecompileTemplates extends Command
{
	use RequiresDevModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-dev:recompile-templates')
			->setDescription('Recompiles parsed templates');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$db = \XF::db();
		$em = \XF::em();
		$app = \XF::app();
		$start = microtime(true);

		$output->writeln("Rebuilding style properties...");

		/** @var \XF\Service\StyleProperty\Rebuild $rebuildService */
		$spRebuildService = $app->service('XF:StyleProperty\Rebuild');
		$spRebuildService->rebuildFullPropertyMap();
		$spRebuildService->rebuildPropertyStyleCache();

		$output->writeln("Rebuilding template map...");

		/** @var \XF\Service\Template\Rebuild $rebuildService */
		$rebuildService = $app->service('XF:Template\Rebuild');
		$rebuildService->rebuildFullTemplateMap();

		$output->writeln("Recompiling templates...");

		$templateIds = $db->fetchAllColumn("
			SELECT template_id
			FROM xf_template
			ORDER BY template_id
		");

		$outputName = $output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;

		$progress = new ProgressBar($output, count($templateIds));
		if (!$outputName)
		{
			$progress->start();
		}

		/** @var \XF\Service\Template\Compile $compileService */
		$compileService = $app->service('XF:Template\Compile');

		foreach ($templateIds AS $templateId)
		{
			if (!$outputName)
			{
				$progress->advance();
			}

			/** @var \XF\Entity\Template $template */
			$template = $em->find('XF:Template', $templateId);
			if (!$template)
			{
				continue;
			}

			if ($outputName)
			{
				$output->writeln("$template->type:$template->title");
			}

			$template->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);

			$needsSave = $template->reparseTemplate(false);
			if ($needsSave)
			{
				// this will recompile
				$template->save();
			}
			else
			{
				$compileService->recompile($template);
				$compileService->updatePhrasesUsed($template);
			}

			$em->clearEntityCache(); // workaround memory issues
		}

		if (!$outputName)
		{
			$progress->finish();
			$output->writeln("");
		}

		$output->writeln(sprintf("Templates compiled. (%.02fs)", microtime(true) - $start));

		return 0;
	}
}