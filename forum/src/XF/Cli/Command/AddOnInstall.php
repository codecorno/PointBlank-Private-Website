<?php

namespace XF\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class AddOnInstall extends Command
{
	use AddOnActionTrait, JobRunnerTrait;

	protected function configure()
	{
		$this
			->setName('xf:addon-install')
			->setAliases(['xf-addon:install'])
			->setDescription('Installs the specified add-on')
			->addArgument(
				'id',
				InputArgument::REQUIRED,
				'Add-On ID'
			)
			->addOption(
				'force',
				'f',
				InputOption::VALUE_NONE,
				'Skip verifying that the add-on is installable'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$id = $input->getArgument('id');

		$addOnManager = \XF::app()->addOnManager();
		$addOn = $addOnManager->getById($id);

		if (!$addOn)
		{
			$output->writeln("<error>" . "No add-on with ID '$id' could be found." . "</error>");
			return 1;
		}

		if (!$addOn->canInstall())
		{
			if ($addOn)
			{
				$output->writeln("<error>" . \XF::phrase('this_add_on_already_installed_upgrade_instead') . "</error>");
			}
			else
			{
				$output->writeln("<error>" . \XF::phrase('this_add_on_cannot_be_installed') . "</error>");
			}
			return 1;
		}

		if (!$this->verifyAddOnAction($input, $output, $addOn))
		{
			return 1;
		}

		/** @var QuestionHelper $helper */
		$helper = $this->getHelper('question');

		$question = new ConfirmationQuestion("<question>" . \XF::phrase('please_confirm_that_you_want_to_install_following_add_on') . ': (' . $addOn->title . ' ' . $addOn->version_string . ") (y/n)</question>");
		$response = $helper->ask($input, $output, $question);
		if (!$response)
		{
			return 1;
		}

		// make sure any errors get logged here
		\XF::app()->error()->setIgnorePendingUpgrade(true);

		$addOn->preInstall();

		$this->runSubAction($output, $addOn, 'install');
		$this->runSubAction($output, $addOn, 'import');
		$this->runSubAction($output, $addOn, 'post-install');

		return 0;
	}
}