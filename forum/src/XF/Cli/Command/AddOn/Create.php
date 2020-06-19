<?php

namespace XF\Cli\Command\AddOn;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use XF\Util\File;
use XF\Util\Json;

class Create extends Command
{
	protected function configure()
	{
		$this
			->setName('xf-addon:create')
			->setDescription('Creates an XF add-on and writes out the basic addon.json file.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		/** @var QuestionHelper $helper */
		$helper = $this->getHelper('question');
		
		$question = new Question("<question>Enter an ID for this add-on:</question> ");
		$question->setValidator($this->getAddOnQuestionFieldValidator('addon_id'));
		$addOnId = $helper->ask($input, $output, $question);
		$output->writeln("");

		$devOutput = \XF::app()->developmentOutput();
		if ($devOutput->isAddOnSkipped($addOnId))
		{
			$output->writeln("<error>Development output for this add-on ID has been disabled. Cannot continue.</error>");
			if (strtolower(substr($addOnId, 0, 2)) == 'xf')
			{
				$output->writeln("Note: use of add-on IDs starting with 'xf' is strongly discouraged.");
			}
			return 1;
		}

		$addOnObj = new \XF\AddOn\AddOn($addOnId, \XF::app()->addOnManager());

		$jsonPath = $addOnObj->getJsonPath();

		if (file_exists($jsonPath))
		{
			$output->writeln("<error>The addon.json file already exists at {$jsonPath}. You can create the add-on by installing it from the Admin CP.</error>");
			return 1;
		}

		$question = new Question("<question>Enter a title:</question> ");
		$question->setValidator($this->getAddOnQuestionFieldValidator('title'));
		$title = $helper->ask($input, $output, $question);
		$output->writeln("");

		$question = new Question("<question>Enter a version ID.</question><info> This integer will be used for internal version comparisons. Each release of your add-on should increase this number: </info> ");
		$question->setValidator(function($value)
		{
			if (!preg_match('/^[0-9]+$/', $value))
			{
				throw new \InvalidArgumentException("The version ID should contain numeric values only.");
			}
			return $value;
		});
		$versionId = $helper->ask($input, $output, $question);
		$output->writeln("");

		$versionString = \XF::repository('XF:AddOn')->inferVersionStringFromId($versionId);
		if ($versionString)
		{
			$output->writeln("<info>Version string set to: {$versionString}</info>");
			$output->writeln("");
		}
		else
		{
			$question = new Question("<question>Enter the version string.</question><info> e.g. 1.0.0 Alpha</info> ");
			$question->setValidator($this->getAddOnQuestionFieldValidator('version_string'));
			$versionString = $helper->ask($input, $output, $question);
			$output->writeln("");
		}

		$question = new ConfirmationQuestion("<question>Does this add-on supersede a XenForo 1 add-on? (y/n)</question> ");
		$legacyAddOnId = null;
		if ($helper->ask($input, $output, $question))
		{
			$question = new Question("<question>What is the old add-on ID?</question> (Leave blank if unchanged.) ", $addOnId);
			$legacyAddOnId = $helper->ask($input, $output, $question);
		}

		$addOn = null;
		$renamedLegacy = false;

		if ($legacyAddOnId)
		{
			$addOn = \XF::em()->find('XF:AddOn', $legacyAddOnId);
			if ($addOn)
			{
				$renamedLegacy = true;
			}
			else
			{
				$output->writeln("<warning>No legacy add-on could be found with ID {$legacyAddOnId}. No data will be updated to be associated with this add-on.</warning>");
				$addOn = \XF::em()->create('XF:AddOn');
			}
		}
		else
		{
			$addOn = \XF::em()->create('XF:AddOn');
		}

		$addOn->bulkSet([
			'addon_id' => $addOnId,
			'title' => $title,
			'version_id' => $versionId,
			'version_string' => $versionString,
			'active' => true
		]);

		$output->writeln("");

		$addOn->preSave();
		if ($errors = $addOn->getErrors())
		{
			$output->writeln("<error>An unexpected error occurred while saving the add-on: " . reset($errors) . "</error>");
			return 1;
		}

		$checkPath = \XF::getAddOnDirectory();

		if (strpos($addOnId, '/') !== false)
		{
			$addOnIdParts = explode('/', $addOnId);
			$vendor = reset($addOnIdParts);

			if (file_exists(\XF::getAddOnDirectory() . \XF::$DS . $vendor))
			{
				$checkPath = \XF::getAddOnDirectory() . \XF::$DS . $vendor;
			}
		}

		if (!is_writable($checkPath))
		{
			$checkPathPrintable = str_replace(\XF::getRootDirectory() . \XF::$DS, '', $checkPath);
			$output->writeln("<error>The '{$checkPathPrintable}' directory is not writable.</error>");
			return 1;
		}

		$addOn->save();

		File::createDirectory($addOnObj->getAddOnDirectory(), false);

		$json = [
			'title' => $addOn->title,
			'version_string' => $addOn->version_string,
			'version_id' => intval($addOn->version_id)
		];
		if ($legacyAddOnId)
		{
			$json['legacy_addon_id'] = $legacyAddOnId;
		}

		$written = File::writeFile($jsonPath, Json::jsonEncodePretty(
			$addOnObj->prepareJsonFile($json)
		), false);

		if ($written)
		{
			$addOn->fastUpdate('json_hash', \XF\Util\Hash::hashTextFile($jsonPath, 'sha256'));
			$output->writeln("The addon.json file was successfully written out to $jsonPath");
		}
		else
		{
			$output->writeln("<error>The addon.json file could not be written out to $jsonPath</error>");
		}

		$output->writeln("");

		$setupPath = $addOnObj->getSetupPath();

		$question = new ConfirmationQuestion("<question>Does your add-on need a Setup file? (y/n)</question> ");
		if ($helper->ask($input, $output, $question))
		{
			$output->writeln("");

			$question = new ConfirmationQuestion("<question>Does your Setup need to support running multiple steps? (y/n)</question> ");
			if ($helper->ask($input, $output, $question))
			{
				$setupContent = <<< SETUP
<?php

namespace {$addOnObj->prepareAddOnIdForClass()};

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;

class Setup extends AbstractSetup
{
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;
}
SETUP;

			}
			else
			{
				$setupContent = <<< SETUP
<?php

namespace {$addOnObj->prepareAddOnIdForClass()};

use XF\AddOn\AbstractSetup;

class Setup extends AbstractSetup
{
	public function install(array \$stepParams = [])
	{
		// TODO: Implement install() method.
	}

	public function upgrade(array \$stepParams = [])
	{
		// TODO: Implement upgrade() method.
	}

	public function uninstall(array \$stepParams = [])
	{
		// TODO: Implement uninstall() method.
	}
}
SETUP;
			}

			$output->writeln("");

			$written = File::writeFile($setupPath, $setupContent, false);
			if ($written)
			{
				$output->writeln("The Setup.php file was successfully written out to $setupPath");
			}
			else
			{
				$output->writeln("The Setup.php file could not be written out to $setupPath");
			}
		}
		else
		{
			$output->writeln("");
			$output->writeln("If you change your mind, create a file named Setup.php in " . dirname($setupPath) . " which should extend AbstractSetup and optionally use one of the StepRunnerX traits.");
		}

		$config = \XF::config();
		if ($renamedLegacy && $config['development']['enabled'])
		{
			$output->writeln("");

			$question = new ConfirmationQuestion("<question>Existing legacy data was associated with this add-on. Would you like to export the development data now? (y/n)</question> ");
			if ($helper->ask($input, $output, $question))
			{
				$command = $this->getApplication()->find('xf-dev:export');
				$childInput = new ArrayInput([
					'command' => 'xf-dev:export',
					'--addon' => $addOn->addon_id
				]);
				$command->run($childInput, $output);
			}
		}

		return 0;
	}

	/**
	 * @param $key
	 *
	 * @return \Closure
	 */
	protected function getAddOnQuestionFieldValidator($key)
	{
		return function($value) use($key)
		{
			$addOn = \XF::em()->create('XF:AddOn');

			$valid = $addOn->set($key, $value);
			if (!$valid)
			{
				$errors = $addOn->getErrors();
				if (isset($errors[$key]))
				{
					throw new \InvalidArgumentException($errors[$key]);
				}
			}
			return $value;
		};
	}
}