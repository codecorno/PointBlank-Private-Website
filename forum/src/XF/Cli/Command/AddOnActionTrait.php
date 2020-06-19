<?php

namespace XF\Cli\Command;

use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use XF\AddOn\AddOn;
use XF\AddOn\StepResult;
use XF\Phrase;

trait AddOnActionTrait
{
	protected function verifyAddOnAction(InputInterface $input, OutputInterface $output, AddOn $addOn)
	{
		if (!$input->getOption('force'))
		{
			$addOn->checkRequirements($errors, $warnings);
			$addOn->passesHealthCheck($missing, $inconsistent);

			if ($missing)
			{
				if (count($missing) > 5)
				{
					$errors[] = \XF::phrase('this_add_on_cannot_be_installed_because_x_files_are_missing', [
						'missing' => count($missing)
					]);
				}
				else
				{
					$errors[] = \XF::phrase('this_add_on_cannot_be_installed_because_following_files_are_missing_x', [
						'missing' => implode(', ', $missing)
					]);
				}
			}
			if ($inconsistent)
			{
				if (count($inconsistent) > 5)
				{
					$warnings[] = \XF::phrase('this_add_on_contains_x_files_which_have_unexpected_contents', [
						'inconsistent' => count($inconsistent)
					]);
				}
				else
				{
					$warnings[] = \XF::phrase('this_add_on_contains_following_files_which_have_unexpected_contents_x', [
						'inconsistent' => implode(', ', $inconsistent)
					]);
				}
			}

			if ($errors || $warnings)
			{
				if ($errors)
				{
					$output->writeln('<error>' . \XF::phrase('following_errors_must_be_resolved_before_continuing') . ':</error>');
					foreach ($errors AS $error)
					{
						$output->writeln("<info>\t * " . (string)$error . '</info>');
					}
				}
				if ($warnings)
				{
					$output->writeln('<warning>' . \XF::phrase('following_errors_must_be_resolved_before_continuing') . ':</warning>');
					foreach ($warnings AS $warning)
					{
						$output->writeln("<info>\t * " . (string)$warning . '</info>');
					}

					if (!$errors)
					{
						/** @var QuestionHelper $helper */
						$helper = $this->getHelper('question');

						$question = new ConfirmationQuestion("<question>" . \XF::phrase('i_have_reviewed_warnings_and_i_still_wish_to_proceed') . " (y/n)</question>");
						if ($helper->ask($input, $output, $question))
						{
							return true;
						}
					}
				}

				return false;
			}
		}

		return true;
	}

	protected function performAddOnAction(InputInterface $input, OutputInterface $output, AddOn $addOn, Phrase $actionPhrase, $methodName)
	{
		$setup = $addOn->getSetup();
		$params = [];
		$count = 0;

		$output->write($actionPhrase->render());

		if ($setup)
		{
			$setup->prepareForAction($methodName);

			while (($result = $setup->$methodName($params)) !== null)
			{
				/** @var StepResult $result */

				if (!$result)
				{
					$finished = true;
					$params = [];
				}
				else
				{
					$count++;

					$finished = false;
					$params = $result->params;
					$params['step'] = $result->step;
					if ($result->version)
					{
						$params['version_id'] = $result->version;
					}

					$output->write(' .');
				}

				if ($finished)
				{
					$result = null;
				}
			}
		}

		$output->writeln(["", "Complete."]);
	}

	protected function importAddOnData(InputInterface $input, OutputInterface $output, AddOn $addOn)
	{
		$app = \XF::app();

		$devOutput = $app->developmentOutput();

		if ($devOutput->isAddOnOutputAvailable($addOn->addon_id))
		{
			$command = $this->getApplication()->find('xf-dev:import');
			$childInput = new ArrayInput([
				'command' => 'xf-dev:import',
				'--addon' => $addOn->addon_id
			]);
			$command->run($childInput, $output);

			$addOn->postDataImport();
		}
		else
		{
			$output->writeln(["", "Importing add-on data"]);
			$this->setupAndRunJob(
				'xfAddOnData-' . $addOn->addon_id,
				'XF:AddOnData',
				['addon_id' => $addOn->addon_id],
				$output
			);

			// the job will trigger the post data import
		}
	}

	public function checkInstalledAddOn($id, &$error = null)
	{
		$addOnManager = \XF::app()->addOnManager();
		$addOn = $addOnManager->getById($id);

		if (!$addOn || !$addOn->isInstalled())
		{
			$error = "No add-on with ID '$id' could be found.";
			return null;
		}

		return $addOn;
	}

	public function checkEditableAddOn($id, &$error = null)
	{
		$addOn = $this->checkInstalledAddOn($id, $error);
		if (!$addOn)
		{
			return null;
		}

		if (!$addOn->canEdit())
		{
			$error = "The add-on '$id' is not editable.";
			return null;
		}

		return $addOn;
	}

	public function runSubAction(OutputInterface $output, AddOn $addOn, $action)
	{
		$execFinder = new PhpExecutableFinder();

		$builderOptions = [
			$execFinder->find(false),
			\XF::getRootDirectory() . \XF::$DS .'cmd.php',
			'xf:addon-sub-action',
			$addOn->addon_id,
			$action,
			'--k=' . $this->getSubActionKey($addOn->addon_id, $action)
		];

		if ($verbosityOption = $this->getVerbosityOption($output->getVerbosity()))
		{
			$builderOptions[] = $verbosityOption;
		}

		$process = new Process($builderOptions);
		$process->setTimeout(null);

		/** @var ProcessHelper $processHelper */
		$processHelper = $this->getHelper('process');

		try
		{
			$processHelper->mustRun($output, $process, null, function($type, $data) use ($output)
			{
				if ($type == Process::OUT)
				{
					$output->write($data);
				}
				// Note that progress bar output is in Process::ERR/stderr, but they get streamed to this callback
				// interleaved, so displaying both is difficult. Thus, we need to only display stuff sent stdout.
			});
		}
		catch (ProcessFailedException $e)
		{
			$process = $e->getProcess();
			if ($process->getExitCode() !== 0)
			{
				// This indicates that the sub-process threw an exception. It will have been printed and logged
				// so don't trigger the normal exception handling. However, we can't continue so exit.
				exit(1);
			}
		}

	}

	protected function getVerbosityOption($verbosity)
	{
		switch ($verbosity)
		{
			case OutputInterface::VERBOSITY_QUIET:
				return '-q';

			case OutputInterface::VERBOSITY_VERBOSE:
				return '-v';

			case OutputInterface::VERBOSITY_VERY_VERBOSE:
				return '-vv';

			case OutputInterface::VERBOSITY_DEBUG:
				return '-vvv';

			default:
				return '';
		}
	}

	public function getSubActionKey($addOnId, $action)
	{
		return hash_hmac('sha1', "$addOnId-$action", \XF::config('globalSalt'));
	}
}