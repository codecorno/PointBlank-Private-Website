<?php

namespace XF\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class AddOnUpgrade extends Command
{
	use AddOnActionTrait, JobRunnerTrait;

	protected function configure()
	{
		$this
			->setName('xf:addon-upgrade')
			->setAliases(['xf-addon:upgrade'])
			->setDescription('Upgrades the specified add-on')
			->addArgument(
				'id',
				InputArgument::REQUIRED,
				'Add-On ID'
			)
			->addOption(
				'force',
				'f',
				InputOption::VALUE_NONE,
				'Skip verifying that the add-on is upgradeable'
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

		if (!$addOn->canUpgrade() && !$input->getOption('force'))
		{
			$output->writeln("<error>" . \XF::phrase('this_add_on_cannot_be_upgraded') . "</error>");
			return 1;
		}

		if (!$this->verifyAddOnAction($input, $output, $addOn))
		{
			return 1;
		}

		/** @var QuestionHelper $helper */
		$helper = $this->getHelper('question');

		$phrase = \XF::phrase('upgrading_x_from_y_to_z', [
			'title' => $addOn->title,
			'old' => $addOn->version_string,
			'new' => $addOn->json_version_string
		]);

		$output->writeln($phrase->render() . '...');

		$question = new ConfirmationQuestion(
			"<question>Confirm upgrade? (y/n)</question>"
		);
		$response = $helper->ask($input, $output, $question);
		if (!$response)
		{
			return 1;
		}

		// make sure any errors get logged here
		\XF::app()->error()->setIgnorePendingUpgrade(true);

		$addOn->preUpgrade();

		$this->runSubAction($output, $addOn, 'upgrade');
		$this->runSubAction($output, $addOn, 'import');
		$this->runSubAction($output, $addOn, 'post-upgrade');

		return 0;
	}
}