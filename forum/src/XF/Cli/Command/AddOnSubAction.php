<?php

namespace XF\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Application as ConsoleApplication;

class AddOnSubAction extends Command
{
	use AddOnActionTrait, JobRunnerTrait;

	protected function configure()
	{
		$this
			->setName('xf:addon-sub-action')
			->setDescription('Runs a add-on sub-action in a separate process. Do not run directly!')
			->setHidden(true)
			->addArgument(
				'id',
				InputArgument::REQUIRED,
				'Add-On ID'
			)
			->addArgument(
				'action',
				InputArgument::REQUIRED
			)
			->addOption(
				'k',
				null,
				InputOption::VALUE_REQUIRED,
				'Confirmation key (to allow this to be run)'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$id = $input->getArgument('id');
		$action = $input->getArgument('action');

		$expectedKey = $this->getSubActionKey($id, $action);
		if ($expectedKey !== $input->getOption('k'))
		{
			$output->writeln("<error>Invalid sub-action confirmation key. Run from parent command.</error>");
			return 1;
		}

		$addOnManager = \XF::app()->addOnManager();
		$addOn = $addOnManager->getById($id);
		if (!$addOn)
		{
			$output->writeln("<error>No add-on with ID '$id' could be found.</error>");
			return 1;
		}

		// make sure any errors get logged here
		\XF::app()->error()->setIgnorePendingUpgrade(true);

		try
		{
			switch ($action)
			{
				case 'import':
					$this->importAddOnData($input, $output, $addOn);
					break;

				case 'install':
					$this->performAddOnAction($input, $output, $addOn, \XF::phrase('installing'), 'install');
					break;

				case 'post-install':
					$stateChanges = [];
					$addOn->postInstall($stateChanges);
					break;

				case 'upgrade':
					$this->performAddOnAction($input, $output, $addOn, \XF::phrase('upgrading'), 'upgrade');
					break;

				case 'post-upgrade':
					$stateChanges = [];
					$addOn->postUpgrade($stateChanges);
					break;

				case 'uninstall':
					$this->performAddOnAction($input, $output, $addOn, \XF::phrase('uninstalling'), 'uninstall');
					break;

				case 'uninstall-data':
					$installed = $addOn->getInstalledAddOn();
					if ($installed)
					{
						$installed->delete();
					}
					break;

				case 'post-uninstall':
					$addOn->postUninstall();
					break;

				case 'post-rebuild':
					$addOn->postRebuild();
					break;

				case 'active-change':
					\XF::app()->addOnDataManager()->triggerRebuildActiveChange($addOn->getInstalledAddOn());
					break;

				default:
					throw new \InvalidArgumentException("Unknown action '$action'");
			}
		}
		catch (\Exception $e)
		{
			// taking this approach so that we don't get double logging
			\XF::logException($e, true, '', true);

			$output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
			$this->getApplication()->renderException($e, $output);

			return 222;
		}
		catch (\Throwable $e)
		{
			// taking this approach so that we don't get double logging
			\XF::logException($e, true, '', true);

			$output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
			$this->getApplication()->renderException($e, $output);

			return 222;
		}

		return 0;
	}
}