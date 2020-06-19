<?php

namespace XF\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class AddOnUninstall extends Command
{
	use AddOnActionTrait;

	protected function configure()
	{
		$this
			->setName('xf:addon-uninstall')
			->setAliases(['xf-addon:uninstall'])
			->setDescription('Uninstalls the specified add-on')
			->addArgument(
				'id',
				InputArgument::REQUIRED,
				'Add-On ID'
			)
			->addOption(
				'force',
				'f',
				InputOption::VALUE_NONE,
				'Skip verifying that the add-on is uninstallable'
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

		if (!$addOn->canUninstall() && !$input->getOption('force'))
		{
			$output->writeln("<error>" . \XF::phrase('this_add_on_cannot_be_uninstalled_like_files_missing') . "</error>");
			return 1;
		}

		/** @var QuestionHelper $helper */
		$helper = $this->getHelper('question');

		$question = new ConfirmationQuestion("<question>" . \XF::phrase('please_confirm_that_you_want_to_uninstall_following_add_on') . ': (' . $addOn->title . ' ' . $addOn->version_string . ") (y/n)</question>");
		$response = $helper->ask($input, $output, $question);
		if (!$response)
		{
			return 1;
		}

		// make sure any errors get logged here
		\XF::app()->error()->setIgnorePendingUpgrade(true);

		$addOn->preUninstall();

		$this->runSubAction($output, $addOn, 'uninstall');
		$this->runSubAction($output, $addOn, 'uninstall-data');
		$this->runSubAction($output, $addOn, 'post-uninstall');

		return 0;
	}
}