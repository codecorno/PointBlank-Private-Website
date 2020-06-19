<?php

namespace XF\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use XF\Install\Upgrader;
use XF\Mvc\Reply\AbstractReply;

class Upgrade extends Command implements CustomAppCommandInterface
{
	use JobRunnerTrait;

	public static function getCustomAppClass()
	{
		return 'XF\Install\App';
	}

	protected function configure()
	{
		$this
			->setName('xf:upgrade')
			->setDescription('Upgrades XenForo')
			->addOption(
				'skip-statistics',
				null,
				InputOption::VALUE_NONE,
				'If set (and not already configured), the question to opt into anonymous server statistics collection will be skipped.'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		/** @var \XF\Install\App $app */
		$app = \XF::app();
		$upgrader = new Upgrader($app);
		$installHelper = new \XF\Install\Helper($app);

		if ($app->config('legacyExists'))
		{
			$output->writeln("<error>Cannot start upgrade without a src/config.php file.</error>");
			$output->writeln("Please use the web interface to create the correct src/config.php file.");
			return 1;
		}
		if (!$app->config('exists'))
		{
			$output->writeln("<error>Cannot start upgrade without a src/config.php file.</error>");
			$output->writeln("Please create a new src/config.php file.");
			return 1;
		}

		$upgrader->syncUpgradeLogStructure();

		$lastUpgradeVersion = $upgrader->getLatestUpgradeVersion();

		$output->writeln(
			'<info>Current version: '
			. $lastUpgradeVersion['version_id']
			. ($lastUpgradeVersion['last_step'] ? " (step $lastUpgradeVersion[last_step])" : '')
			. '</info>'
		);
		$output->writeln(sprintf('<info>Upgrade target: %s (%s)</info>', \XF::$versionId, \XF::$version));

		$upgradeComplete = ($lastUpgradeVersion['version_id'] === \XF::$versionId && !$lastUpgradeVersion['last_step']);

		/** @var QuestionHelper $helper */
		$helper = $this->getHelper('question');

		if ($upgradeComplete)
		{
			$startConfirm = 'You are already running the latest version. Rebuild the master data?';
		}
		else
		{
			$startConfirm = 'Are you sure you want to continue with the upgrade?';
		}

		$question = new ConfirmationQuestion("<question>$startConfirm [y/n] </question>");

		if (!$helper->ask($input, $output, $question))
		{
			return 1;
		}

		if (!$upgradeComplete)
		{
			$output->writeln('');

			$controller = new \XF\Install\Controller\Upgrade($app, $app->request());

			$runVersionId = null;
			$step = 1;

			if ($lastUpgradeVersion['last_step'])
			{
				$runVersionId = $lastUpgradeVersion['version_id'];
				$step = $lastUpgradeVersion['last_step'] + 1;
			}

			if (!$runVersionId)
			{
				$runVersionId = $upgrader->getNextUpgradeVersionId($lastUpgradeVersion['version_id']);
				$step = 1;
			}

			do
			{
				$upgrade = $runVersionId ? $upgrader->getUpgrade($runVersionId) : false;
				if (!$upgrade)
				{
					break;
				}

				$position = 0;
				$data = [];
				$versionName = $upgrade->getVersionName();

				while (method_exists($upgrade, 'step' . $step))
				{
					if ($position)
					{
						$this->outputStatus($output, "Running upgrade to $versionName, step $step ($position)...");
					}
					else
					{
						$this->outputStatus($output, "Running upgrade to $versionName, step $step...");
					}

					$result = $upgrade->{'step' . $step}($position, $data, $controller);
					if ($result instanceof AbstractReply)
					{
						$this->clearStatus($output, '<error>This step must be completed via the web interface.</error>');
						return 2;
					}
					else if ($result === 'complete')
					{
						$this->clearStatus($output, "Running upgrade to $versionName, step $step... done.");
						break;
					}
					else if ($result === true || $result === null)
					{
						$upgrader->insertUpgradeLog($runVersionId, $step);

						$this->clearStatus($output, "Running upgrade to $versionName, step $step... done.");

						$step++;
						$position = 0;
						$data = [];
					}
					else if (is_array($result))
					{
						// stay on the same step
						$position = $result[0];
						$data = !empty($result[2]) ? $result[2] : [];
					}
					else
					{
						$this->clearStatus($output, '<error>The upgrade step returned an unexpected result.</error>');
						return 3;
					}
				}

				$upgrader->insertUpgradeLog($runVersionId);

				$runVersionId = $upgrader->getNextUpgradeVersionId($runVersionId);
				$step = 1;
			}
			while (true);

			$output->writeln(['<info>All upgrade steps run up to version ' . \XF::$version . '.</info>', '']);

			$upgrader->insertUpgradeLog(\XF::$versionId);
		}

		$originalVersion = $upgrader->getCurrentVersion();

		$devOutput = $app->developmentOutput();
		if ($devOutput->isEnabled() && $devOutput->isCoreXfDataAvailable())
		{
			$command = $this->getApplication()->find('xf-dev:import');
			$childInput = new ArrayInput([
				'command' => 'xf-dev:import',
				'--addon' => 'XF'
			]);
			$command->run($childInput, $output);

			$extraJobs = $upgrader->getExtraUpgradeJobsMap();
			$installHelper->insertRebuildJob(null, $extraJobs, false, $originalVersion);

			$this->runJob($installHelper->getDefaultRebuildJobName(), $output);
		}
		else
		{
			$extraJobs = $upgrader->getExtraUpgradeJobsMap();
			$installHelper->insertRebuildJob(null, $extraJobs, true, $originalVersion);

			$this->runJob($installHelper->getDefaultRebuildJobName(), $output);
		}

		if ($upgrader->isUpgradeComplete())
		{
			$upgrader->completeUpgrade();

			$schemaErrors = $upgrader->getDefaultSchemaErrors();
			if ($schemaErrors)
			{
				$output->writeln("Upgrade completed but errors were found:");
				foreach ($schemaErrors AS $error)
				{
					$output->writeln("\t* $error");
				}
				$output->writeln("This is likely caused by an add-on conflict. You may need to restore a backup, remove the offending add-on data from the database, and retry the upgrade. Contact support if you are not sure how to proceed.");
				return 4;
			}
			else if (!$upgradeComplete)
			{
				$output->writeln("Upgrade completed successfully.");

				$outdatedTemplates = $app->repository('XF:Template')->countOutdatedTemplates();
				if ($outdatedTemplates)
				{
					$output->writeln("");
					$output->writeln(
						"Note: outdated templates have been detected. This is normal after upgrading."
						. " This can be resolved by visiting the outdated templates section of the control panel."
						. " Incorporating template changes is important to ensure new features work properly and bug fixes take effect."
					);
					$output->writeln("");
				}
			}
			else
			{
				$output->writeln("Rebuild completed successfully.");
			}
		}
		else
		{
			$output->writeln("Upgrade failed to complete!");
			return 5;
		}

		$options = \XF::options();
		if (empty($options->collectServerStats['configured']) && !$input->getOption('skip-statistics'))
		{
			$serverStats = [
				'configured' => 1,
				'enabled' => 0
			];

			$question = new ConfirmationQuestion("<question>Send anonymous server statistics (PHP, MySQL, XF versions)? (y/n)</question> ");
			if ($helper->ask($input, $output, $question))
			{
				$serverStats['enabled'] = 1;
			}

			/** @var \XF\Repository\Option $optionRepo */
			$optionRepo = \XF::repository('XF:Option');
			$optionRepo->updateOptions([
				'collectServerStats' => $serverStats
			]);
		}

		return 0;
	}

	protected $lastStatusLength;

	protected function outputStatus(OutputInterface $output, $status)
	{
		if ($this->lastStatusLength && $this->lastStatusLength > strlen($status))
		{
			$status = str_pad($status, $this->lastStatusLength, " ", STR_PAD_RIGHT);
		}

		$output->write("\r$status");
		$this->lastStatusLength = strlen($status);
	}

	protected function clearStatus(OutputInterface $output, $extraMessage = null)
	{
		$this->outputStatus($output, '');
		$output->write("\r");
		if ($extraMessage !== null)
		{
			$output->writeln($extraMessage);
		}
	}
}