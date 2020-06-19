<?php

namespace XF\Cli\Command\Designer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use XF\Util\File;

class Enable extends Command
{
	use RequiresDesignerModeTrait;

	protected function configure()
	{
		$this
			->setName('xf-designer:enable')
			->setDescription('Enables designer mode on the specified style')
			->addArgument(
				'style-id',
				InputArgument::REQUIRED,
				'Style ID'
			)
			->addArgument(
				'designer-mode',
				InputArgument::REQUIRED,
				'Designer mode ID'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em = \XF::em();

		$styleId = $input->getArgument('style-id');
		$style = $em->find('XF:Style', $styleId);

		if (!$style)
		{
			$output->writeln("No style with ID '$styleId' could be found.");
			return 1;
		}

		if ($style->designer_mode)
		{
			$output->writeln(\XF::phrase('once_enabled_it_is_not_possible_to_change_designer_mode_id')->render());
			return 1;
		}

		$designerMode = $input->getArgument('designer-mode');
		$style->designer_mode = $designerMode;

		if (!$style->preSave())
		{
			$output->writeln($style->getErrors());
			return 1;
		}

		$style->save();

		$designerModePath = \XF::app()->designerOutput()->getDesignerModePath($style->designer_mode);
		$printablePath = str_replace(\XF::getRootDirectory() . \XF::$DS, '', $designerModePath);

		if (file_exists($designerModePath))
		{
			/** @var QuestionHelper $helper */
			$helper = $this->getHelper('question');

			$question = new ChoiceQuestion(
				"<question>The designer mode path '$printablePath' already exists. How should this be treated?</question>",
				[
					'dir' => 'Treat the directory as the master version. (Overwrite style from directory.)',
					'db' => 'Treat the database as the master version. (Overwrite directory from style.)',
					'' => 'Do nothing. (You will need to resolve this manually.)'
				]
			);

			$action = $helper->ask($input, $output, $question);
			switch ($action)
			{
				case 'dir':
					$this->runImport($designerMode, $output);
					break;

				case 'db':
					File::deleteDirectory($designerModePath);
					File::createDirectory($designerModePath, false);
					$this->runExport($designerMode, $output);
			}
		}
		else
		{
			File::createDirectory($designerModePath, false);
			$this->runExport($designerMode, $output);
		}

		$output->writeln(["", "Designer mode enabled for '$style->title' in path '$printablePath'", ""]);

		return 0;
	}

	protected function runExport($designerMode, OutputInterface $output)
	{
		$command = $this->getApplication()->find('xf-designer:export');

		$i = [
			'command' => 'xf-designer:export',
			'designer-mode' => $designerMode
		];

		$childInput = new ArrayInput($i);
		$command->run($childInput, $output);
	}

	protected function runImport($designerMode, OutputInterface $output)
	{
		$command = $this->getApplication()->find('xf-designer:import');

		$i = [
			'command' => 'xf-designer:import',
			'designer-mode' => $designerMode
		];

		$childInput = new ArrayInput($i);
		$command->run($childInput, $output);
	}
}