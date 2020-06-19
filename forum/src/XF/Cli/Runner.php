<?php

namespace XF\Cli;

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use XF\Cli\Command\CustomAppCommandInterface;

class Runner
{
	public function run()
	{
		if (PHP_SAPI != 'cli')
		{
			die('This script can only be run via the command line interface.');
		}

		// CLI requests can run for longer than a web request, so we don't want to get tripped up by memory issues
		\XF::setMemoryLimit(-1);
		@set_time_limit(0);

		$console = new ConsoleApplication();

		$this->registerCommands($console);

		$input = new ArgvInput();
		$output = new ConsoleOutput();
		$output->getFormatter()->setStyle('warning', new OutputFormatterStyle('black', 'yellow'));

		$console->setCatchExceptions(false);
		$console->setAutoExit(false);

		try
		{
			$name = $input->getFirstArgument();
			$command = $console->find($name);

			if ($command instanceof CustomAppCommandInterface)
			{
				$appClass = $command::getCustomAppClass();
			}
			else
			{
				$appClass = 'XF\Cli\App';
			}

			$app = \XF::setupApp($appClass);
			$app->start();
		}
		catch (\Symfony\Component\Console\Exception\CommandNotFoundException $e)
		{
			$output->writeln($e->getMessage());
			exit;
		}

		$exitCode = 0;

		try
		{
			$exitCode = $console->run($input, $output);
			$this->postExecutionCleanUp($output);
		}
		catch (\Symfony\Component\Console\Exception\RuntimeException $e)
		{
			// this will usually indicate that they passed unexpected arguments in or had a local problem of some sort
			$output->writeln($e->getMessage());
			$exitCode = 1;
		}
		catch (\Exception $e)
		{
			\XF::logException($e, true); // exiting so rollback
			$console->renderException($e, $output);
			$this->outputAdditionalInfo($e, $output);
			$exitCode = 1;
		}
		catch (\Throwable $e)
		{
			\XF::logException($e, true); // exiting so rollback
			$console->renderException(
				new \ErrorException($e->getMessage(), $e->getCode(), E_ERROR, $e->getFile(), $e->getLine(), $e),
				$output)
			;
			$this->outputAdditionalInfo($e, $output);
			$exitCode = 1;
		}

		if ($exitCode > 255)
		{
			$exitCode = 255;
		}

		exit($exitCode);
	}

	protected function outputAdditionalInfo($e, OutputInterface $output)
	{
		$messages = [];

		if ($e instanceof \XF\Db\Exception && $e->query)
		{
			$messages[] = '<comment>Exception query:</comment>';

			$emptyLine = sprintf('<error>  %s  </error>', str_repeat(' ', strlen($e->query)));

			$messages[] = $emptyLine;
			$messages[] = sprintf('<error>  %s  </error>', $e->query);
			$messages[] = $emptyLine;
			$messages[] = '';
		}

		$output->writeln($messages);
	}

	protected function postExecutionCleanUp(OutputInterface $output = null)
	{
		\XF::triggerRunOnce();

		$app = \XF::app();

		if ($app->container()->isCached('job.manager'))
		{
			$jobManager = $app->jobManager();

			if ($jobManager->hasManualEnqueued())
			{
				$output->writeln(['', 'Running clean up tasks...']);

				$manualJobs = array_keys($jobManager->getManualEnqueued());
				foreach ($manualJobs AS $manualJobId)
				{
					while ($runner = $jobManager->runById($manualJobId, \XF::config('jobMaxRunTime')))
					{
						if ($output)
						{
							$output->writeln(strval($runner->statusMessage));
						}

						// keep the memory limit down on long running jobs
						$app->em()->clearEntityCache();

						if ($runner->continueDate)
						{
							// job is finished but set to resume in the future
							break;
						}
					}
				}
			}
		}
	}

	protected function registerCommands(ConsoleApplication $app)
	{
		$directoryMap = $this->getCommandDirectoryMap();
		$classes = $this->getValidCommandClasses($directoryMap);
		foreach ($classes AS $class)
		{
			$app->add(new $class());
		}
	}

	protected function getCommandDirectoryMap()
	{
		$dirs = [
			\XF::getSourceDirectory() . '/XF/Cli/Command' => 'XF\Cli\Command'
		];

		$addOnIds = [];
		$addOnBaseDir = \XF::getAddOnDirectory();

		foreach (new \DirectoryIterator($addOnBaseDir) AS $entry)
		{
			if (!$this->isPotentialAddOnDirectory($entry))
			{
				continue;
			}

			if ($this->isAddOnRootDirectory($entry))
			{
				$addOnIds[] = $entry->getBasename();
			}
			else
			{
				$vendorPrefix = $entry->getBasename();
				foreach (new \DirectoryIterator($entry->getPathname()) AS $addOnDir)
				{
					if (!$this->isPotentialAddOnDirectory($addOnDir))
					{
						continue;
					}

					if ($this->isAddOnRootDirectory($addOnDir))
					{
						$addOnIds[] = "$vendorPrefix/{$addOnDir->getBasename()}";
					}
				}
			}
		}

		foreach ($addOnIds AS $addOnId)
		{
			$searchPath = $addOnBaseDir . '/' . $addOnId . '/Cli/Command';
			$classBase = '\\' . str_replace('/', '\\', $addOnId) . '\Cli\Command';

			$dirs[$searchPath] = $classBase;
		}

		return $dirs;
	}

	protected function isPotentialAddOnDirectory(\DirectoryIterator $entry)
	{
		if (!$entry->isDir()
			|| $entry->isDot()
			|| !preg_match('/^[a-z0-9_]+$/i', $entry->getBasename())
		)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	protected function isAddOnRootDirectory(\DirectoryIterator $entry)
	{
		$ds = \XF::$DS;

		$pathname = $entry->getPathname();
		$addOnJson = "{$pathname}{$ds}addon.json";
		$outputDir = "{$pathname}{$ds}_output";
		$dataDir = "{$pathname}{$ds}_data";

		if (file_exists($addOnJson) || file_exists($outputDir) || file_exists($dataDir))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	protected function getValidCommandClasses(array $directoryMap)
	{
		$classes = [];

		foreach ($directoryMap AS $dir => $baseClass)
		{
			$fullPath = \XF\Util\File::canonicalizePath($dir);
			if (!file_exists($fullPath) || !is_dir($fullPath))
			{
				continue;
			}

			$iterator = new \RecursiveCallbackFilterIterator(
				new \RecursiveDirectoryIterator($fullPath),
				function(\SplFileInfo $entry, $key, \RecursiveIterator $iterator)
				{
					if ($iterator->hasChildren())
					{
						return true;
					}

					return ($entry->isFile() && $entry->getExtension() == 'php');
				}
			);
			foreach (new \RecursiveIteratorIterator($iterator) AS $file)
			{
				/** @var \DirectoryIterator $file */
				$localPath = str_replace($fullPath, '', $file->getPathname());
				$localPath = trim(str_replace('\\', '/', $localPath), '/');

				$className = $baseClass . '\\' . str_replace('/', '\\', $localPath);
				$className = preg_replace('/\.php$/', '', $className);

				if ($this->isValidCommandClass($className))
				{
					$classes[] = $className;
				}
			}
		}

		return $classes;
	}

	protected function isValidCommandClass($class)
	{
		if (!class_exists($class))
		{
			return false;
		}

		$reflection = new \ReflectionClass($class);
		return (
			$reflection->isInstantiable()
			&& $reflection->isSubclassOf('Symfony\Component\Console\Command\Command')
		);
	}
}