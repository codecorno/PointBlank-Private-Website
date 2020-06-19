<?php

namespace XF\Install;

use XF\Entity\Page;
use XF\Install\Data\MySql;

class Helper
{
	/**
	 * @var \XF\App
	 */
	protected $app;

	/**
	 * @var array PHP functions required for XenForo functionality
	 */
	protected $requiredFunctions = [
		'ini_set',
		'fpassthru',
	];

	/**
	 * @var array PHP functions recommended for XenForo functionality
	 */
	protected $recommendedFunctions = [
		'fsockopen',
		'exec',
		'exif_read_data',
		'escapeshellarg',
		'proc_open',
		'popen',
		'set_time_limit',
	];

	public function __construct(\XF\App $app)
	{
		$this->app = $app;
	}

	public function getRequirementErrors(\XF\Db\AbstractAdapter $db = null)
	{
		$errors = [];

		$phpVersion = phpversion();
		if (version_compare($phpVersion, '5.6.0', '<'))
		{
			$errors['phpVersion'] = \XF::phrase('php_version_x_does_not_meet_requirements', ['version' => $phpVersion]);
		}

		if (!function_exists('mysqli_connect'))
		{
			$errors['mysqlPhp'] = \XF::phrase('required_php_extension_x_not_found', ['extension' => 'MySQLi']);
		}

		if (!function_exists('gd_info'))
		{
			$errors['gd'] = \XF::phrase('required_php_extension_x_not_found', ['extension' => 'GD']);
		}
		else if (!function_exists('imagecreatefromjpeg'))
		{
			$errors['gdJpeg'] = \XF::phrase('gd_jpeg_support_missing');
		}

		if (!function_exists('iconv'))
		{
			$errors['iconv'] = \XF::phrase('required_php_extension_x_not_found', ['extension' => 'Iconv']);
		}

		if (!function_exists('ctype_alnum'))
		{
			$errors['ctype'] = \XF::phrase('required_php_extension_x_not_found', ['extension' => 'Ctype']);
		}

		if (!function_exists('fpassthru'))
		{
			$errors['fpassthru'] = \XF::phrase('required_php_function_x_not_found', ['function' => 'fpassthru']);
		}

		if (!function_exists('preg_replace'))
		{
			$errors['pcre'] = \XF::phrase('required_php_extension_x_not_found', ['extension' => 'PCRE']);
		}
		else
		{
			try
			{
				preg_match('/./su', 'x');
			}
			catch (\Exception $e)
			{
				$errors['pcre'] = \XF::phrase('pcre_unicode_support_missing');
			}

			try
			{
				preg_match('/\p{C}/u', 'x');
			}
			catch (\Exception $e)
			{
				$errors['pcre'] = \XF::phrase('pcre_unicode_property_support_missing');
			}
		}

		if (!function_exists('spl_autoload_register'))
		{
			$errors['spl'] = \XF::phrase('required_php_extension_x_not_found', ['extension' => 'SPL']);
		}

		if (!function_exists('json_encode'))
		{
			$errors['json'] = \XF::phrase('required_php_extension_x_not_found', ['extension' => 'JSON']);
		}

		if (!extension_loaded('curl'))
		{
			$errors['curl'] = \XF::phrase('required_php_extension_x_not_found', ['extension' => 'cURL']);
		}

		if (!class_exists('DOMDocument') || !class_exists('SimpleXMLElement'))
		{
			$errors['xml'] = \XF::phrase('required_php_xml_extensions_not_found');
		}

		if ($db)
		{
			$mySqlVersion = $db->getServerVersion();
			if ($mySqlVersion && version_compare(strtolower($mySqlVersion), '5.5', '<'))
			{
				$errors['mysqlVersion'] = \XF::phrase('mysql_version_x_does_not_meet_requirements', ['version' => $mySqlVersion]);
			}
		}

		$dataDir = \XF\Util\File::canonicalizePath($this->app->config('externalDataPath'));
		if (!is_dir($dataDir) || !is_writable($dataDir))
		{
			$errors['dataDir'] = \XF::phrase('directory_x_must_be_writable', ['directory' => $dataDir]);
		}
		else
		{
			foreach (scandir($dataDir) AS $file)
			{
				if ($file[0] == '.')
				{
					continue;
				}

				$fullPath = "$dataDir/$file";
				if (is_dir($fullPath) && !is_writable($fullPath))
				{
					$errors['dataDir'] = \XF::phrase('all_directories_under_x_must_be_writable', ['directory' => $dataDir]);
				}
			}
		}

		$internalDataDir = \XF\Util\File::canonicalizePath($this->app->config('internalDataPath'));
		if (!is_dir($internalDataDir) || !is_writable($internalDataDir))
		{
			$errors['internalDataDir'] = \XF::phrase('directory_x_must_be_writable', ['directory' => $internalDataDir]);
		}
		else
		{
			foreach (scandir($internalDataDir) AS $file)
			{
				if ($file[0] == '.')
				{
					continue;
				}

				$fullPath = "$internalDataDir/$file";
				if (is_dir($fullPath) && !is_writable($fullPath))
				{
					$errors['internalDataDir'] = \XF::phrase('all_directories_under_x_must_be_writable', ['directory' => $internalDataDir]);
				}
			}
		}

		$disabled = $this->getDisabledFunctions($errors);
		if (is_array($disabled))
		{
			foreach ($this->requiredFunctions AS $fn)
			{
				if (in_array($fn, $disabled))
				{
					$errors[$fn] = \XF::phrase('php_function_x_disabled_fundamental', ['function' => $fn]);
				}
			}
		}
		else
		{
			$errors['ini_get'] = \XF::phrase('php_functions_disabled_impossible_check');
		}

		return $errors;
	}

	public function getRequirementWarnings(\XF\Db\AbstractAdapter $db = null)
	{
		$warnings = [];

		$disabled = $this->getDisabledFunctions();
		if (is_array($disabled))
		{
			foreach ($this->recommendedFunctions AS $fn)
			{
				if (in_array($fn, $disabled))
				{
					$warnings[$fn] = \XF::phrase('php_function_x_disabled_warning', ['function' => $fn]);
				}
			}
		}
		else
		{
			$warnings['ini_get'] = \XF::phrase('php_functions_disabled_impossible_check');
		}

		if (!in_array('https', stream_get_wrappers()))
		{
			if (!function_exists('curl_version') || !defined('CURL_VERSION_SSL'))
			{
				$warnings['https'] = \XF::phrase('php_no_ssl_support');
			}
			else
			{
				$curl = curl_version();
				if (!($curl['features'] & CURL_VERSION_SSL))
				{
					$warnings['https'] = \XF::phrase('php_no_ssl_support');
				}
			}
		}

		if (\XF\Util\Random::getLastUsedSource() == 'internal')
		{
			$warnings['random'] = \XF::phrase('php_weak_random_values');
		}

		$phpVersion = phpversion();
		if (version_compare($phpVersion, '5.6.0', '<'))
		{
			$warnings['phpVersion'] = \XF::phrase('php_version_x_outdated_upgrade', ['version' => $phpVersion]);
		}

		return $warnings;
	}

	protected function getDisabledFunctions()
	{
		try
		{
			$disabled = ini_get('disable_functions');
		}
		catch (\Exception $e)
		{
			return false;
		}

		return array_map(function($fn)
		{
			return trim(strtolower($fn));
		}, explode(',', $disabled));
	}

	public function deleteApplicationTables()
	{
		$db = $this->app->db();

		$removed = [];
		foreach ($db->fetchAllColumn('SHOW TABLES') AS $table)
		{
			if ($this->isApplicationTable($table))
			{
				$removed[] = $table;
				$db->query('DROP TABLE ' . $table);
			}
		}

		return $removed;
	}

	public function hasApplicationTables(\XF\Db\AbstractAdapter $db = null)
	{
		$db = $db ?: $this->app->db();

		foreach ($db->fetchAllColumn('SHOW TABLES') AS $table)
		{
			if ($this->isApplicationTable($table))
			{
				return true;
			}
		}

		return false;
	}

	public function isApplicationTable($table)
	{
		return (
			substr($table, 0, 3) == 'xf_'
			|| substr($table, 0, 11) == 'xengallery_'
		);
	}

	public function createApplicationTables($maxExecution = 0, $startOffset = 0, &$endOffset = false)
	{
		$mySql = new MySql();
		$tables = $mySql->getTables();
		$sm = $this->app->db()->getSchemaManager();

		$s = microtime(true);
		$i = -1;
		$endOffset = false;

		foreach ($tables AS $tableName => $definition)
		{
			$i++;
			if ($i < $startOffset)
			{
				continue;
			}

			$sm->createTable($tableName, $definition);

			if ($maxExecution && microtime(true) - $s > $maxExecution)
			{
				// start at the next one
				$endOffset = $i + 1;
				break;
			}
		}

		return array_keys($tables);
	}

	public function insertDefaultData()
	{
		$mySql = new MySql();
		$data = $mySql->getData();
		$db = $this->app->db();

		foreach ($data AS $dataQuery)
		{
			$db->query($dataQuery);
		}

		return count($data);
	}

	public function createDirectories()
	{
		$internalDataDir = \XF\Util\File::canonicalizePath($this->app->config('internalDataPath'));

		$dirs = [
			$internalDataDir . '/temp',
			$internalDataDir . '/code_cache',
		];
		foreach ($dirs AS $dir)
		{
			\XF\Util\File::createDirectory($dir, true);
		}
	}

	public function createInitialUser(array $baseData, $password)
	{
		$this->app->db()->beginTransaction();

		/** @var \XF\Repository\User $userRepo */
		$userRepo = $this->app->repository('XF:User');
		$user = $userRepo->setupBaseUser();
		$user->setOption('admin_edit', true);
		$user->bulkSet($baseData);
		$user->user_state = 'valid';
		$user->is_staff = true;

		/** @var \XF\Entity\UserAuth $auth */
		$auth = $user->getRelationOrDefault('Auth');
		$auth->setPassword($password);

		$user->save();

		/** @var \XF\Entity\Admin $admin */
		$admin = $user->getRelationOrDefault('Admin', false);
		$admin->is_super_admin = true;
		$admin->extra_user_group_ids = [\XF\Entity\User::GROUP_ADMIN];
		$admin->permission_cache = $this->app->db()->fetchAllColumn("
			SELECT admin_permission_id
			FROM xf_admin_permission
		");
		$admin->save();

		$moderator = $this->app->em()->create('XF:Moderator');
		$moderator->user_id = $user->user_id;
		$moderator->is_super_moderator = true;
		$moderator->extra_user_group_ids = [\XF\Entity\User::GROUP_MOD];
		$moderator->save();

		$permissions = $this->app->finder('XF:Permission')
			->where('Interface.is_moderator', 1)
			->where('permission_type', 'flag') // all that's supported
			->fetch();

		$permissionValues = [];
		foreach ($permissions AS $permission)
		{
			$permissionValues[$permission->permission_group_id][$permission->permission_id] = 'allow';
		}

		/** @var \XF\Service\UpdatePermissions $permissionUpdater */
		$permissionUpdater = $this->app->service('XF:UpdatePermissions');
		$permissionUpdater->setUser($user)->setGlobal();
		$permissionUpdater->updatePermissions($permissionValues);

		$this->app->db()->commit();

		return $user;
	}

	public function completeInstallation()
	{
		$this->writeInstallLock();
		$this->insertUpgradeLog(\XF::$versionId, null, 'install');
		$this->updateVersion();

		// we trigger some user changes here -- simplest to just clear this out
		$this->app->db()->emptyTable('xf_change_log');
	}

	public function writeInstallLock()
	{
		\XF\Util\File::writeInstallLock();
	}

	public function insertUpgradeLog($versionId, $lastStep = null, $type = 'upgrade')
	{
		$this->app->db()->insert('xf_upgrade_log', [
			'version_id' => $versionId,
			'last_step' => $lastStep,
			'completion_date' => $lastStep === null ? time() : 0,
			'log_type' => $type
		], false, 'last_step = VALUES(last_step), completion_date = VALUES(completion_date)');
	}

	public function updateVersion()
	{
		/** @var \XF\Repository\Option $optionRepo */
		$optionRepo = $this->app->repository('XF:Option');
		$optionRepo->updateOption('currentVersionId', \XF::$versionId);

		$this->app->db()->update(
			'xf_addon',
			['version_id' => \XF::$versionId, 'version_string' => \XF::$version],
			"addon_id = 'XF'"
		);

		$this->app->addOnDataManager()->rebuildActiveAddOnCache();
	}

	public function isInstalled()
	{
		return (
			\XF\Util\File::installLockExists()
			&& ($this->app->config('exists') || $this->app->config('legacyExists'))
		);
	}

	public function generateConfig(array $config)
	{
		$esc = "'\\";

		$lines = [];
		foreach ($config AS $key => $value)
		{
			if (is_array($value))
			{
				if (empty($value))
				{
					continue;
				}
				foreach ($value AS $subKey => $subValue)
				{
					$lines[] = '$config[\'' . addcslashes($key, $esc) . '\'][\'' . addcslashes($subKey, $esc) . '\'] = \'' . addcslashes($subValue, $esc) . '\';';
				}
				$lines[] = '';
			}
			else
			{
				$lines[] = '$config[\'' . addcslashes($key, $esc) . '\'] = ' . var_export($value, true) . ';';
			}
		}

		return "<?php\r\n\r\n" . implode("\r\n", $lines);
	}

	public function insertRebuildJob($jobName = null, array $extraJobs = [], $withCoreData = true, $forUpgradeFrom = null)
	{
		if (!$jobName)
		{
			$jobName = $this->getDefaultRebuildJobName();
		}

		$jobs = [];
		if ($withCoreData)
		{
			$jobs[] = ['XF:AddOnData', ['addon_id' => 'XF']];
		}

		// The 2.0 upgrade wipes out phrase and template maps which can lead to incomplete builds.
		// Force a full rebuild here to ensure everything is ok (though it will usually be duplicated work).
		$fromPre20 = ($forUpgradeFrom !== null && $forUpgradeFrom < 2000010);
		$skipCoreRebuild = $fromPre20 ? false : true;

		$jobs[] = ['XF:PhraseRebuild', ['skipCore' => $skipCoreRebuild]];
		$jobs[] = ['XF:TemplateRebuild', ['skipCore' => $skipCoreRebuild]];
		$jobs[] = 'XF:StylePropertyRebuild';
		$jobs[] = 'XF:PermissionRebuild';
		$jobs[] = 'XF:CoreCacheRebuild';

		foreach ($extraJobs AS $key => $value)
		{
			if (is_string($key) && is_array($value))
			{
				$jobs[] = [$key, $value];
			}
			else
			{
				$jobs[] = $value;
			}
		}

		$this->app->jobManager()->enqueueUnique($jobName, 'XF:Atomic', ['execute' => $jobs]);
	}

	public function getDefaultRebuildJobName()
	{
		return 'xfInstallUpgrade';
	}

	public function hasRebuildJobPending($jobName = null)
	{
		if (!$jobName)
		{
			$jobName = $this->getDefaultRebuildJobName();
		}

		$job = $this->app->jobManager()->getUniqueJob($jobName);
		return ($job ? true : false);
	}
}