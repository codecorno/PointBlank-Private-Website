<?php

namespace XF\Install;

use XF\Install\Upgrade\AbstractUpgrade;

class Upgrader
{
	/**
	 * @var \XF\App
	 */
	protected $app;

	protected $currentVersion = null;

	public function __construct(\XF\App $app)
	{
		$this->app = $app;
	}

	protected function db()
	{
		return $this->app->db();
	}

	protected function helper()
	{
		return new Helper($this->app);
	}

	public function insertUpgradeLog($versionId, $lastStep = null)
	{
		$helper = $this->helper();
		$helper->insertUpgradeLog($versionId, $lastStep);
	}

	public function syncUpgradeLogStructure()
	{
		$sm = $this->db()->getSchemaManager();

		if (!$sm->columnExists('xf_upgrade_log', 'last_step'))
		{
			$sm->alterTable('xf_upgrade_log', function (\XF\Db\Schema\Alter $table)
			{
				$table->addColumn('last_step', 'smallint')->nullable();
				$table->dropColumns('user_id');
			});
		}
	}

	public function completeUpgrade()
	{
		$helper = $this->helper();
		$helper->updateVersion();

		$this->enqueuePostUpgradeJobs();
		$this->clearUpgradeJobs();

		$this->enqueueUpgradeCheck();
		$this->enqueueStatsCollection();
	}

	public function getLatestUpgradeVersion()
	{
		return $this->db()->fetchRow('
			SELECT *
			FROM xf_upgrade_log
			ORDER BY version_id DESC
			LIMIT 1
		');
	}

	public function isUpgradeComplete()
	{
		$lastUpgradeVersion = $this->getLatestUpgradeVersion();

		return ($lastUpgradeVersion['version_id'] === \XF::$versionId && !$lastUpgradeVersion['last_step']);
	}

	public function getPossibleUpgradeFileNames()
	{
		$searchDir = \XF::getSourceDirectory() . '/XF/Install/Upgrade';

		$upgrades = [];
		foreach (glob($searchDir . '/*.php') AS $file)
		{
			$file = basename($file);

			switch ($file)
			{
				case '1010031-100b1.php': // this was badly named - make sure it's always skipped so the right one runs
					continue 2;
			}

			$versionId = intval($file);
			if (!$versionId)
			{
				continue;
			}

			$upgrades[$versionId] = $searchDir . '/' . $file;
		}

		ksort($upgrades, SORT_NUMERIC);

		return $upgrades;
	}

	public function getRemainingUpgradeVersionIds($lastCompletedVersion)
	{
		$upgrades = $this->getPossibleUpgradeFileNames();
		$offset = 0;

		foreach ($upgrades AS $upgrade => $file)
		{
			if ($upgrade > $lastCompletedVersion)
			{
				return array_slice($upgrades, $offset, null, true);
			}

			$offset++;
		}

		return [];
	}

	public function getNextUpgradeVersionId($lastCompletedVersion)
	{
		$upgrades = $this->getRemainingUpgradeVersionIds($lastCompletedVersion);
		reset($upgrades);
		return key($upgrades);
	}

	public function getNewestUpgradeVersionId()
	{
		$upgrades = $this->getRemainingUpgradeVersionIds(0);
		end($upgrades);
		return key($upgrades);
	}

	/**
	 * @param integer $versionId
	 * @param App $app
	 *
	 * @return AbstractUpgrade
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getUpgrade($versionId)
	{
		$versionId = intval($versionId);
		if (!$versionId)
		{
			throw new \InvalidArgumentException('No upgrade version ID specified.');
		}

		$upgrades = $this->getPossibleUpgradeFileNames();
		if (isset($upgrades[$versionId]))
		{
			require_once($upgrades[$versionId]);
			$class = '\XF\Install\Upgrade\Version' . $versionId;
			return new $class($this->app);
		}

		throw new \InvalidArgumentException('Could not find the specified upgrade.');
	}

	public function getCurrentVersion()
	{
		if ($this->currentVersion === null)
		{
			$existingVersion = $this->db()->fetchOne("
				SELECT option_value
				FROM xf_option
				WHERE option_id = 'currentVersionId'
			");

			$this->currentVersion = $existingVersion ? $existingVersion : 0;
		}

		return $this->currentVersion;
	}

	public function getAddOnConflicts($fromVersion)
	{
		$conflicts = [];

		if ($fromVersion < 2000010)
		{
			$sm = $this->db()->getSchemaManager();

			if ($sm->tableExists('xf_forum_field'))
			{
				$conflicts[] = '[TH] Custom Fields';
			}
			if ($sm->tableExists('xf_widget'))
			{
				$conflicts[] = '[bd] Widget Framework';
			}
			if ($sm->columnExists('xf_conversation_message', 'like_users'))
			{
				$conflicts[] = 'Conversation Improvements by Xon';
			}
			if ($sm->columnExists('xf_thread', 'prefix_id', $prefixDef) && !empty($prefixDef['Type']))
			{
				if (!preg_match('#(int|integer)(\s|\(|$)#i', $prefixDef['Type']))
				{
					$conflicts[] = 'Multi Prefix';
				}
			}
		}

		return $conflicts;
	}

	public function getCliCommand()
	{
		return 'php cmd.php xf:install';
	}

	public function getDefaultSchemaErrors()
	{
		$ds = \XF::$DS;
		$hashesPath = \XF::getAddOnDirectory() . $ds . 'XF' . $ds . 'hashes.json';
		if (file_exists($hashesPath))
		{
			$hashes = json_decode(file_get_contents($hashesPath), true);
		}
		else
		{
			$hashes = null;
		}

		return $this->runSchemaCompare(
			'XF\Entity',
			\XF::getSourceDirectory() . $ds . 'XF' . $ds . 'Entity',
			$hashes
		);
	}

	public function runSchemaCompare($entityClassPrefix, $entityClassDir, array $fileLookup = null)
	{
		$db = $this->app->db();
		$tables = array_fill_keys($db->fetchAllColumn('SHOW TABLES'), true);
		$errors = [];

		$entityClasses = $this->_findSchemaClasses($entityClassPrefix, $entityClassDir, $fileLookup);
		foreach ($entityClasses AS $class)
		{
			if (!class_exists($class))
			{
				continue;
			}

			$reflection = new \ReflectionClass($class);
			if (!$reflection->isInstantiable() || !$reflection->isSubclassOf('XF\Mvc\Entity\Entity'))
			{
				continue;
			}

			$entity = $this->app->em()->create($class);
			$structure = $entity->structure();

			if (!isset($tables[$structure->table]))
			{
				$errors[$structure->table] = "Table $structure->table missing.";
			}

			$columns = $db->fetchAllKeyed('
				SHOW COLUMNS FROM `' . $structure->table . '`
			', 'Field');

			foreach ($structure->columns AS $column => $definition)
			{
				if (!isset($columns[$column]))
				{
					$errors["$structure->table.$column"] = "Column $structure->table.$column missing.";
				}
			}
		}

		return $errors;
	}

	protected function _findSchemaClasses($classPrefix, $searchDir, array $fileLookup = null)
	{
		$searchDir = rtrim($searchDir, '/\\');
		$dir = opendir($searchDir);
		if (!$dir)
		{
			return array();
		}

		$output = array();
		while (($entry = readdir($dir)) !== false)
		{
			if ($entry == '.' || $entry == '..')
			{
				continue;
			}

			$fullPath = "$searchDir/$entry";

			if (is_dir($fullPath))
			{
				continue;
			}

			if ($fileLookup !== null)
			{
				$testFile = str_replace(\XF::getSourceDirectory(), 'src', $fullPath);
				if (!isset($fileLookup[$testFile]))
				{
					// this file doesn't exist any more - likely a left over from a previous version
					continue;
				}
			}

			if (preg_match('#^([a-z0-9_]+)\.php$#i', $entry, $match))
			{
				$output[] = $classPrefix . '\\' . $match[1];
			}
		}

		return $output;
	}

	public function isSignificantUpgrade()
	{
		$currentVersion = $this->getCurrentVersion();
		if ($currentVersion)
		{
			$diff = floor(\XF::$versionId / 10000) - floor($currentVersion / 10000);
			if ($diff == 0)
			{
				// upgrading in the same branch (1.3.0 -> 1.3.1 for example). Web upgrader should be fine in general
				return false;
			}
		}

		return true;
	}

	public function isCliRecommended()
	{
		if (!$this->isSignificantUpgrade())
		{
			return false;
		}

		$totals = $this->app->db()->fetchOne("
			SELECT data_value
			FROM xf_data_registry
			WHERE data_key IN ('boardTotals', 'forumStatistics')
			LIMIT 1
		");
		if (!$totals)
		{
			return false;
		}

		$totals = @unserialize($totals);
		if (!$totals)
		{
			return false;
		}

		if (!empty($totals['messages']) && $totals['messages'] >= 500000)
		{
			return true;
		}

		if (!empty($totals['users']) && $totals['users'] >= 50000)
		{
			return true;
		}

		return false;
	}

	public function translateLegacyConfig(array $config)
	{
		$new = \XF\Util\Arr::arrayFilterKeys($config, [
			'db',
			'debug',
			'cookie',
			'enableMail',
			'enableMailQueue',
			'enableListeners',
			'globalSalt',
			'superAdmins',
			'internalDataPath',
			'externalDataPath',
			'externalDataUrl',
			'javaScriptUrl',
			'passwordIterations',
			'enableTemplateModificationCallbacks',
			'enableClickjackingProtection',
			'enableReverseTabnabbingProtection',
			'enableTfa',
			'maxImageResizePixelCount',
			'adminLogLength',
			'checkVersion',
			'chmodWritableValue'
		], true);

		unset($new['db']['adapter'], $new['db']['adapterNamespace']);

		if (isset($config['rebuildMaxExecution']))
		{
			$new['jobMaxRunTime'] = $config['rebuildMaxExecution'];
		}

		if (!isset($config['superAdmins']))
		{
			$new['superAdmins'] = '1';
		}

		return $new;
	}

	public function migrateLegacyConfigIfNeeded(&$written = false)
	{
		// load only the actual config values and filter down to the ones we want to keep
		$config = [];
		$container = $this->app->container();
		include($container['config.legacyFile']);

		$config = $this->translateLegacyConfig($config);
		$configPhp = $this->helper()->generateConfig($config);
		$configFile = $container['config.file'];

		if (!file_exists($configFile) && is_writable(dirname($configFile)))
		{
			try
			{
				file_put_contents($configFile, $configPhp);
				\XF\Util\File::makeWritableByFtpUser($configFile);

				$written = true;
			}
			catch (\Exception $e)
			{
				$written = false;
			}
		}
		else
		{
			$written = false;
		}

		return $config;
	}

	public function renameLegacyConfigIfNeeded()
	{
		$legacyFile = $this->app->container('config.legacyFile');

		if (file_exists($legacyFile) && is_writable($legacyFile))
		{
			@rename($legacyFile, \XF::getRootDirectory() . '/library/xf1-config.php');
		}
	}

	public function getUpgradeJobs($immediate = true)
	{
		return $this->db()->fetchAllKeyed("
			SELECT *
			FROM xf_upgrade_job
			WHERE immediate = ?
		", 'unique_key', [$immediate ? 1 : 0]);
	}

	public function getExtraUpgradeJobsMap($immediate = true)
	{
		$extra = [];
		foreach ($this->getUpgradeJobs($immediate) AS $job)
		{
			$extra[] = [$job['execute_class'], unserialize($job['execute_data'])];
		}

		return $extra;
	}

	public function clearUpgradeJobs($immediate = true)
	{
		return $this->db()->delete('xf_upgrade_job', 'immediate = ?', [$immediate ? 1 : 0]);
	}

	public function getPostUpgradeJobs()
	{
		return $this->getUpgradeJobs(true);
	}

	public function enqueuePostUpgradeJobs($clear = true)
	{
		$jobs = $this->getUpgradeJobs(false);
		$jobManager = $this->app->jobManager();

		foreach ($jobs AS $job)
		{
			$params = unserialize($job['execute_data']);
			$jobManager->enqueueUnique($job['unique_key'], $job['execute_class'], $params, false);
		}

		if ($jobs && $clear)
		{
			$this->clearUpgradeJobs(false);
		}
	}

	public function enqueueUpgradeCheck()
	{
		$this->app->jobManager()->enqueueUnique('xfUpgradeCheck', 'XF:UpgradeCheck', [], false);
	}

	public function enqueueStatsCollection()
	{
		/** @var \XF\Repository\CollectStats $collectStatsRepo */
		$collectStatsRepo = $this->app->repository('XF:CollectStats');

		if ($collectStatsRepo->isEnabled())
		{
			$this->app->jobManager()->enqueueUnique('xfCollectStats', 'XF:CollectStats', [], false);
		}
	}
}