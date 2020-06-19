<?php

namespace XF\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Cli\Command\CustomAppCommandInterface;
use XF\Install\Controller\Upgrade;
use XF\Install\Upgrade\AbstractUpgrade;
use XF\Mvc\Reply\AbstractReply;

class UpgradeStep extends Command implements CustomAppCommandInterface
{
	use RequiresDevModeTrait;

	public static function getCustomAppClass()
	{
		return 'XF\Install\App';
	}

	protected function configure()
	{
		$this
			->setName('xf-dev:upgrade-step')
			->setDescription('Runs a specific upgrade step from a specified upgrade class.')
			->addArgument(
				'version',
				InputArgument::REQUIRED,
				'The upgrade version to execute, e.g. 2000010'
			)
			->addArgument(
				'step',
				InputArgument::REQUIRED,
				'The step number to run'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		/** @var \XF\Install\App $app */
		$app = \XF::app();
		$upgrader = new \XF\Install\Upgrader($app);

		$controller = new \XF\Install\Controller\Upgrade($app, $app->request());
		$upgrade = $upgrader->getUpgrade($input->getArgument('version'));

		$step = $input->getArgument('step');
		$position = 0;
		$data = [];

		do
		{
			$returnCode = $this->runStep($upgrade, $output, $controller, $step, $position, $data);
			if (is_int($returnCode))
			{
				$loop = false;
			}
			else if (is_array($returnCode))
			{
				$loop = true;
				$position = $returnCode[0];
				$data = isset($returnCode[2]) ? $returnCode[2] : [];
			}
			else
			{
				throw new \LogicException("Step did not return an expected result");
			}
		}
		while ($loop);

		return $returnCode;
	}

	protected function runStep(AbstractUpgrade $upgrade, OutputInterface $output, Upgrade $controller, $step, $position = 0, $data = [])
	{
		$versionName = $upgrade->getVersionName();

		if (!method_exists($upgrade, 'step' . $step))
		{
			$this->clearStatus($output, "<error>Upgrade to version $versionName does not have a step$step() method.</error>");
			return 2;
		}

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
			$this->clearStatus($output, "<error>This step must be completed via the web interface.</error>");
			return 2;
		}
		else if ($result === 'complete' || $result === true || $result === null)
		{
			$this->clearStatus($output, "Running upgrade to $versionName, step $step... done.");
			return 0;
		}
		else if (is_array($result))
		{
			// stay on the same step
			return $result;
		}
		else
		{
			$this->clearStatus($output, "<error>The upgrade step returned an unexpected result.</error>");
			return 3;
		}
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