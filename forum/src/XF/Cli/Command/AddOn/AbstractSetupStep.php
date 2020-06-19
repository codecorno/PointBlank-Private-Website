<?php

namespace XF\Cli\Command\AddOn;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepResult;
use XF\Cli\Command\AddOnActionTrait;

abstract class AbstractSetupStep extends Command
{
	use AddOnActionTrait;

	abstract protected function getStepType();

	abstract protected function getCommandArguments();

	protected function configure()
	{
		$command = $this->getStepType();

		$this
			->setName('xf-addon:' . $command . '-step')
			->setDescription('Runs a specific step from the specified add-on Setup class.')
			->addArgument(
				'id',
				InputArgument::REQUIRED,
				'Add-on ID'
			);

		$this->getCommandArguments();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$id = $input->getArgument('id');

		$addOn = $this->checkEditableAddOn($id, $error);
		if (!$addOn)
		{
			$output->writeln('<error>' . $error . '</error>');
			return 1;
		}

		$setup = $addOn->getSetup();

		if (!$setup)
		{
			$output->writeln("<error>Setup class for add-on could not be found.</error>");
			return 1;
		}

		$step = $input->getArgument('step');
		$version = null;
		$params = [];

		$stepType = $this->getStepType();
		if ($stepType == 'upgrade')
		{
			$version = $input->getArgument('version');
		}

		$setup->prepareForAction($stepType);

		do
		{
			$result = $this->runStep($setup, $output, $stepType, $step, $version, $params);
			if (!($result instanceof StepResult))
			{
				$this->clearStatus($output,"<error>Must return a StepResult object.</error>");
				return 1;
			}
			else
			{
				if ($result->complete)
				{
					$loop = false;
				}
				else if (is_int($result))
				{
					$loop = false;
				}
				else
				{
					$params = $result->params;
					$loop = true;
				}
			}
		}
		while ($loop);

		$output->writeln("");

		return 0;
	}

	protected function runStep(AbstractSetup $setup, OutputInterface $output, $stepType, $step, $version = null, array $params = [])
	{
		$method = $stepType;

		switch ($method)
		{
			case 'install':
			case 'uninstall':
				if ($step)
				{
					$method .= 'Step' . $step;
				}
				break;

			case 'upgrade':
				if ($version && $step)
				{
					$method .= $version . 'Step' . $step;
				}
				break;
		}

		if (!method_exists($setup, $method))
		{
			$this->clearStatus($output, "<error>Setup class does not have a $method() method.</error>");
			return new StepResult(true);
		}

		$this->outputStatus($output, "Running Setup class method $method()...");

		$result = $setup->$method($params);
		if ($result === null || $result === true)
		{
			$result = new StepResult(true);
			$this->outputStatus($output, "Running Setup class method $method()... done.");
		}
		else if (is_array($result))
		{
			$result = new StepResult(false, $result);
		}

		return $result;
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