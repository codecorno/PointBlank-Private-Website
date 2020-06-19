<?php

namespace XF\Import;

class Manager
{
	const SESSION_KEY = 'importSession';

	protected $app;

	protected $importerIdentifiers;

	public function __construct(\XF\App $app, array $importerIdentifiers)
	{
		$this->app = $app;
		$this->importerIdentifiers = $importerIdentifiers;
	}

	public function getImporterList()
	{
		$map = [];
		foreach ($this->importerIdentifiers AS $identifier)
		{
			$class = $this->getClassFromIdentifier($identifier);
			if (class_exists($class))
			{
				$map[$identifier] = $class::getListInfo();
			}
		}

		$map = \XF\Util\Arr::columnSort($map, 'source', 'strnatcasecmp');

		return $map;
	}

	public function getImporterListTargetGrouped()
	{
		$grouped = [];
		foreach ($this->getImporterList() AS $k => $value)
		{
			if (!empty($value['beta']))
			{
				$value['source'] .= ' ' . \XF::phrase('(beta)');
			}
			$grouped[$value['target']][$k] = $value;
		}

		ksort($grouped, SORT_NATURAL | SORT_FLAG_CASE);

		return $grouped;
	}

	/**
	 * @param string $identifier
	 *
	 * @return \XF\Import\Importer\AbstractImporter|null
	 */
	public function getImporter($identifier)
	{
		if (!$identifier)
		{
			return null;
		}

		if (!in_array($identifier, $this->importerIdentifiers))
		{
			return null;
		}

		$class = $this->getClassFromIdentifier($identifier);
		return (class_exists($class) ? new $class($this->app) : null);
	}

	public function getImporterSourceTitle($identifier)
	{
		$importer = $this->getImporter($identifier);
		return $importer ? $importer->getSourceTitle() : null;
	}

	protected function getClassFromIdentifier($identifier)
	{
		$class = \XF::stringToClass($identifier, '%s\Import\Importer\%s');
		return \XF::extendClass($class);
	}

	public function resolveStepDependencies(array $runSteps, array $stepDefinitions)
	{
		// To resolve dependencies, we need to run until there aren't any changes. One loop isn't sufficient
		// as a single loop will generally only resolve one level of dependencies.
		$i = 0;

		do
		{
			if ($i++ > 100)
			{
				throw new \LogicException("Infinite loop in import step dependency resolution. Please make a bug report.");
			}

			$baseSelectedSteps = array_fill_keys($runSteps, true);
			$newSelectedSteps = [];

			foreach ($stepDefinitions AS $step => $definition)
			{
				if (isset($baseSelectedSteps[$step]))
				{
					$newSelectedSteps[$step] = true;

					if (!empty($definition['depends']))
					{
						// if this step is selected, we need to select any steps it depends on
						foreach ($definition['depends'] AS $dependStep)
						{
							$newSelectedSteps[$dependStep] = true;
						}
					}
					if (!empty($definition['force']))
					{
						// if this step is selected, force any subsequent steps to be selected.
						// works out the same as depend at this point, though semantically different
						foreach ($definition['force'] AS $forceStep)
						{
							$newSelectedSteps[$forceStep] = true;
						}
					}
				}
			}

			ksort($baseSelectedSteps);
			ksort($newSelectedSteps);

			$runSteps = array_keys($newSelectedSteps); // for the next loop
		}
		while ($newSelectedSteps !== $baseSelectedSteps);

		// all dependencies have been resolved, we just need to make sure our selections are in the right order
		$finalSteps = [];

		foreach (array_keys($stepDefinitions) AS $step)
		{
			if (!empty($newSelectedSteps[$step]))
			{
				$finalSteps[] = $step;
			}
		}

		return $finalSteps;
	}

	public function initializeNewImport(
		$importerId, $logTable, $retainIds, array $baseConfig, array $stepConfig, array $steps
	)
	{
		$log = $this->getLog($logTable);
		if (!$log->isValidLogName())
		{
			throw new \InvalidArgumentException("Invalid log table '$logTable'");
		}

		$importer = $this->getImporter($importerId);
		if (!$importer)
		{
			throw new \InvalidArgumentException("Could not find importer $importerId");
		}
		if ($retainIds)
		{
			if (!$importer->canRetainIds())
			{
				throw new \InvalidArgumentException("Attempting to retain IDs when not possible");
			}

			$importer->resetDataForRetainIds();
		}

		$log->initializeIfNeeded();

		$session = new Session();
		$session->importerId = $importerId;
		$session->logTable = $logTable;
		$session->retainIds = $retainIds;
		$session->baseConfig = $baseConfig;
		$session->stepConfig = $stepConfig;
		$session->remainingSteps = $steps;

		$this->app->registry()->set(self::SESSION_KEY, $session);

		return $session;
	}

	public function updateCurrentSession(Session $session)
	{
		if (!$this->getCurrentSession())
		{
			throw new \LogicException("No previous session found");
		}

		$this->app->registry()->set(self::SESSION_KEY, $session);
	}

	public function clearCurrentSession()
	{
		$this->app->registry()->delete(self::SESSION_KEY);
	}

	/**
	 * @return Session|null
	 */
	public function getCurrentSession()
	{
		$session = $this->app->registry()->get(self::SESSION_KEY);
		if ($session && $session instanceof Session)
		{
			return $session;
		}
		else
		{
			return null;
		}
	}

	public function getLog($table)
	{
		return new Log($this->app->db(), $table);
	}

	public function getRunner(Session $session = null)
	{
		return $this->newRunner(null, [], $session);
	}

	public function newRunner($type = null, array $options = [], Session $session = null)
	{
		if (!$session)
		{
			$session = $this->getCurrentSession();
			if (!$session)
			{
				return null;
			}
		}

		$importer = $this->getImporter($session->importerId);
		if (!$importer)
		{
			return null;
		}

		$log = $this->getLog($session->logTable);
		$dataManager = new DataManager($this->app, $log, $session->retainIds, $this->app->config('fullUnicode'));

		$importer->initialize($session, $dataManager, $session->baseConfig);

		switch ($type)
		{
			case 'parallel':
				return ParallelRunner::instantiate($importer, $session, $options);

			case 'base':
			case null:
				return new Runner($importer, $session);

			default:
				throw new \LogicException("Unknown runner type '$type'");
		}
	}

	public static function getImporterShortNamesForType($type)
	{
		if ($type == 'XF')
		{
			$baseDir = \XF::getSourceDirectory() . '/XF';
		}
		else
		{
			$baseDir = \XF::getAddOnDirectory() . '/' . $type;
		}

		$fullPath = $baseDir . '/Import/Importer';

		if (!file_exists($fullPath) || !is_dir($fullPath))
		{
			return [];
		}

		$importers = [];
		foreach (glob($fullPath . '/*.php') AS $file)
		{
			$className = substr(basename($file), 0, -4);
			if (preg_match('#(^Abstract|Interface$)#', $className))
			{
				continue;
			}

			if (strpos($type, '/') !== false)
			{
				$type = str_replace('/', '\\', $type);
			}

			$importers[] = "$type:$className";
		}

		return $importers;
	}
}