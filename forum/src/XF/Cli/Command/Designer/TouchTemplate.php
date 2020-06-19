<?php

namespace XF\Cli\Command\Designer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TouchTemplate extends Command
{
	use RequiresDesignerModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-designer:touch-template')
			->setDescription('Marks the specified template as modified in the specified style and exports it.')
			->addArgument(
				'designer-mode',
				InputArgument::REQUIRED,
				'Designer mode ID'
			)
			->addArgument(
				'template',
				InputArgument::REQUIRED,
				'Template to mark as modified. Must include the type prefix, e.g. \'public:template_name\''
			)
			->addOption(
				'custom',
				'c',
				InputOption::VALUE_NONE,
				'If specified, allows the creation of a custom template.'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em = \XF::em();

		/** @var \XF\Entity\Style $style */
		$designerMode = $input->getArgument('designer-mode');
		$style = $em->findOne('XF:Style', ['designer_mode' => $designerMode]);

		if (!$style)
		{
			$output->writeln("No style with designer mode ID '$designerMode' could be found.");
			return 1;
		}

		$templater = \XF::app()->templater();

		$title = $input->getArgument('template');
		list ($type, $title) = $templater->getTemplateTypeAndName($title);

		if (!$type)
		{
			$type = 'public';
		}

		if (!$input->getOption('custom'))
		{
			$templateFinder = $em->getFinder('XF:Template')
				->where([
					'type' => $type,
					'title' => $title
				]);

			if (!$templateFinder->total())
			{
				$output->writeln("The template '$type:$title' does not exist in any style. Pass the --custom option to create it.");
				return 1;
			}
		}

		$templateMap = $em->getFinder('XF:TemplateMap')
			->where([
				'style_id' => $style->style_id,
				'type' => $type,
				'title' => $title
			])
			->with('Template', true)
			->fetchOne();

		if (!$templateMap)
		{
			// entirely custom template
			$newTemplate = $em->create('XF:Template');
			$newTemplate->style_id = $style->style_id;
			$newTemplate->type = $type;
			$newTemplate->title = $title;
			$newTemplate->template = '';
			$newTemplate->addon_id = '';

			if (!$newTemplate->preSave())
			{
				$output->writeln($newTemplate->getErrors());
				return 1;
			}
			$newTemplate->save();
		}
		else
		{
			$baseTemplate = $templateMap->Template;

			if ($baseTemplate->style_id == $style->style_id)
			{
				// template already exists in this style
				$output->writeln(["", "The template '$type:$title' has already been modified. Run the 'xf-designer:export-templates' command to export."]);
				return 1;
			}
			else
			{
				// template only exists in a parent; duplicate it here
				$newTemplate = \XF::em()->create('XF:Template');
				$newTemplate->style_id = $style->style_id;
				$newTemplate->type = $baseTemplate->type;
				$newTemplate->title = $baseTemplate->title;
				$newTemplate->template = $baseTemplate->template;
				$newTemplate->addon_id = $baseTemplate->addon_id;

				if (!$newTemplate->preSave())
				{
					$output->writeln($newTemplate->getErrors());
					return 1;
				}
				$newTemplate->save();
			}
		}

		$output->writeln(["", "Template '{$newTemplate->type}:{$newTemplate->title}' modified in style '$style->title'", ""]);

		return 0;
	}
}