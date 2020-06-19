<?php

namespace XF\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class AddOnRebuild extends Command
{
	use AddOnActionTrait, JobRunnerTrait;

	protected function configure()
	{
		$this
			->setName('xf:addon-rebuild')
			->setAliases(['xf-addon:rebuild'])
			->setDescription('Rebuilds the specified add-on data.')
			->addArgument(
				'id',
				InputArgument::REQUIRED,
				'Add-On ID'
			)
			->addOption(
				'force',
				'f',
				InputOption::VALUE_NONE,
				'Skip verifying that the add-on is rebuildable'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$id = $input->getArgument('id');

		$addOn = $this->checkInstalledAddOn($id, $error);
		if (!$addOn)
		{
			$output->writeln('<error>' . $error . '</error>');
			return 1;
		}

		if (!$addOn->canRebuild() && !$input->getOption('force'))
		{
			$output->writeln("<error>" . \XF::phrase('this_add_on_cannot_be_rebuilt') . "</error>");
			return 1;
		}

		if (!$this->verifyAddOnAction($input, $output, $addOn))
		{
			return 1;
		}

		/** @var QuestionHelper $helper */
		$helper = $this->getHelper('question');

		$question = new ConfirmationQuestion("<question>" . \XF::phrase('please_confirm_that_you_want_to_rebuild_following_add_on') . ': (' . $addOn->title . ' ' . $addOn->version_string . ") (y/n)</question>");
		$response = $helper->ask($input, $output, $question);
		if (!$response)
		{
			return 1;
		}

		$addOn->preRebuild();

		$this->runSubAction($output, $addOn, 'import');
		$this->runSubAction($output, $addOn, 'post-rebuild');

		return 0;
	}
}