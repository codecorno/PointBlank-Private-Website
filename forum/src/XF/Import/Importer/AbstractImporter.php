<?php

namespace XF\Import\Importer;

abstract class AbstractImporter
{
	/**
	 * @var \XF\App
	 */
	protected $app;

	protected $baseConfig = [];

	protected $sourceInitialized = false;

	/**
	 * @var \XF\Import\Helper
	 */
	protected $helper;

	/**
	 * @var \XF\Import\Session
	 */
	protected $session;

	/**
	 * @var \XF\Import\DataManager|null
	 */
	protected $dataManager;

	abstract protected function getBaseConfigDefault();
	abstract public function renderBaseConfigOptions(array $vars);
	abstract public function validateBaseConfig(array &$baseConfig, array &$errors);

	abstract protected function getStepConfigDefault();
	abstract public function renderStepConfigOptions(array $vars);
	abstract public function validateStepConfig(array $steps, array &$stepConfig, array &$errors);

	abstract public function canRetainIds();
	abstract public function resetDataForRetainIds();
	abstract public function getSteps();

	public function getSuggestedLogTableName()
	{
		$class = (new \ReflectionClass($this))->getShortName();
		$logPrefix = strtolower("import_log_{$class}");

		$i = 1;
		$sm = $this->db()->getSchemaManager();

		do
		{
			$tableName = "{$logPrefix}_{$i}";

			if (!$sm->tableExists($tableName))
			{
				break;
			}

			$i++;
		}
		while (true);

		return $tableName;
	}

	/**
	 * Allows additional import steps to be injected at specific positions within the existing list of steps.
	 *
	 * @param array $existingSteps
	 * @param       $newStep
	 * @param       $newStepKey
	 * @param       $afterExistingKey
	 *
	 * @return array
	 */
	public function extendSteps(array $existingSteps, $newStep, $newStepKey, $afterExistingKey)
	{
		$newPosition = array_search($afterExistingKey, array_keys($existingSteps));
		if ($newPosition === false)
		{
			throw new \LogicException('Value ' . $afterExistingKey . ' not found within provided steps.');
		}
		$newPosition += 1;

		$steps = array_slice($existingSteps, 0, $newPosition, true);
		$steps[$newStepKey] = $newStep;

		$steps += array_slice($existingSteps, $newPosition, count($existingSteps), true);

		return $steps;
	}

	abstract protected function doInitializeSource();

	abstract public function getFinalizeJobs(array $stepsRun);

	public static function getListInfo()
	{
		throw new \LogicException("getListInfo must be overridden in " . get_called_class());
	}

	public function __construct(\XF\App $app)
	{
		$this->app = $app;
	}

	public function isBeta()
	{
		$info = static::getListInfo();
		return !empty($info['beta']);
	}

	public function setBaseConfig(array $config)
	{
		$this->baseConfig = array_replace_recursive($this->getBaseConfigDefault(), $config);
		$this->sourceInitialized = false;
	}

	public function getBaseConfig()
	{
		return $this->baseConfig;
	}

	public function initialize(\XF\Import\Session $session, \XF\Import\DataManager $dataManager, array $baseConfig)
	{
		$this->session = $session;
		$this->dataManager = $dataManager;
		$this->setBaseConfig($baseConfig);

		$this->initializeSource();
	}

	public function getFinalNotes(\XF\Import\Session $session, $context)
	{
		return [];
	}

	public function getSession()
	{
		return $this->session;
	}

	public function getDataManager()
	{
		return $this->dataManager;
	}

	public function initializeSource()
	{
		if (!$this->baseConfig)
		{
			throw new \LogicException("Cannot initialize without a base config");
		}

		if (!$this->sourceInitialized)
		{
			$this->doInitializeSource();
			$this->sourceInitialized = true;
		}
	}

	public function prepareBaseConfigFromInput(array $config, \XF\Http\Request $request)
	{
		return $config;
	}

	public function prepareStepConfigFromInput(array $config, \XF\Http\Request $request)
	{
		return $config;
	}

	public function getSourceTitle()
	{
		$info = static::getListInfo();
		$title = $info['source'];

		if ($this->isBeta())
		{
			$title .= ' ' . \XF::phrase('(beta)');
		}

		return $title;
	}

	public function getStepSpecificConfig($step, array $fullStepConfig)
	{
		$defaultStepConfig = $this->getStepConfigDefault();
		$default = isset($defaultStepConfig[$step]) ? $defaultStepConfig[$step] : [];
		$forStep = isset($fullStepConfig[$step]) ? $fullStepConfig[$step] : [];

		return array_replace_recursive($default, $forStep);
	}

	/**
	 * @return \XF\Import\Helper
	 */
	protected function getHelper()
	{
		if (!$this->helper)
		{
			$class = \XF::extendClass('XF\Import\Helper');
			$this->helper = new $class($this);
		}

		return $this->helper;
	}

	/**
	 * @param string $helper
	 *
	 * @return \XF\Import\DataHelper\AbstractHelper
	 */
	public function getDataHelper($helper)
	{
		return $this->dataManager->helper($helper);
	}

	public function mapKeys(array $originalData, array $map, $skipUnset = false)
	{
		$output = [];

		foreach ($map AS $originalKey => $newKey)
		{
			if (is_int($originalKey))
			{
				$originalKey = $newKey;
			}

			if (!array_key_exists($originalKey, $originalData))
			{
				if ($skipUnset)
				{
					continue;
				}

				throw new \InvalidArgumentException("Could not find '$originalKey' in data");
			}

			$output[$newKey] = $originalData[$originalKey];
		}

		return $output;
	}

	public function mapXfKeys(array $originalData, array $map, $skipUnset = false)
	{
		$output = [];

		foreach ($map AS $newKey => $originalKey)
		{
			if (is_int($newKey))
			{
				$newKey = $originalKey;
			}

			if (!array_key_exists($originalKey, $originalData))
			{
				if ($skipUnset)
				{
					continue;
				}

				throw new \InvalidArgumentException("Could not find '$originalKey' in data");
			}

			$output[$newKey] = $originalData[$originalKey];
		}

		return $output;
	}

	/**
	 * @param string $type
	 *
	 * @return \XF\Import\Data\AbstractData
	 */
	public function newHandler($type)
	{
		return $this->dataManager->newHandler($type);
	}

	public function importData($type, $oldId, array $data)
	{
		return $this->dataManager->importData($type, $oldId, $data);
	}

	public function lookup($type, $oldIds)
	{
		return $this->dataManager->lookup($type, $oldIds);
	}

	public function lookupId($type, $id, $default = false)
	{
		return $this->dataManager->lookupId($type, $id, $default);
	}

	public function typeMap($type)
	{
		return $this->dataManager->typeMap($type);
	}

	public function log($type, $oldId, $newId)
	{
		$this->dataManager->log($type, $oldId, $newId);
	}

	public function logHandler($handlerType, $oldId, $newId)
	{
		$this->dataManager->logHandler($handlerType, $oldId, $newId);
	}

	public function mapIdsFromArray(array $oldIds, array $map)
	{
		$new = [];
		foreach ($oldIds AS $oldId)
		{
			if (!empty($map[$oldId]))
			{
				$new[] = $map[$oldId];
			}
		}

		return $new;
	}

	/**
	 * Returns an array containing only the values of the specified key(s) in the source array
	 *
	 * @param array $array
	 * @param string|array $key
	 *
	 * @return array
	 */
	public function pluck(array $array, $key)
	{
		if (!is_array($key))
		{
			$key = [$key];
		}

		$output = [];

		foreach ($key AS $keyName)
		{
			$output += array_column($array, $keyName);
		}

		return $output;
	}

	/**
	 * Renames array key $oldName as $newName for each item in $array
	 *
	 * @param array $array
	 * @param       $oldName
	 * @param       $newName
	 *
	 * @return array
	 */
	protected function arrayKeyRename(array $array, $oldName, $newName)
	{
		foreach ($array AS $key => $value)
		{
			$array[$key][$newName] = $value[$oldName];
			unset($array[$key][$oldName]);
		}

		return $array;
	}

	public function isInvalidUtf8($value)
	{
		return $this->dataManager->isInvalidUtf8($value);
	}

	public function convertToUtf8($value, $fromCharset = null, $convertHtml = null)
	{
		return $this->dataManager->convertToUtf8($value, $fromCharset, $convertHtml);
	}

	public function convertToId($string, $maxLength = 25)
	{
		return $this->dataManager->convertToId($string, $maxLength);
	}

	public function convertToUniqueId($string, array &$existing, $maxLength = 25)
	{
		if ($this->isInvalidUtf8($string))
		{
			$string = $this->convertToUtf8($string);
		}

		if ($maxLength)
		{
			$string = $this->convertToId($string, $maxLength);
		}
		if (!strlen($string))
		{
			$string = \XF::$time;
		}

		$idBase = $string;

		$i = 1;

		while (isset($existing[$string]))
		{
			$string = $idBase;

			$i++;

			if ($maxLength)
			{
				$string = $this->convertToId($idBase, $maxLength - 1 - strlen($i));
			}

			$string .= '_' . $i;
		}

		$existing[$string] = true;

		return $string;
	}

	public function convertToDateCriteria($unixTime, $userTimeZone = 0, $timeZone = 'Europe/London')
	{
		return [
			'ymd' => date('Y-m-d', $unixTime),
			'hh' => date('H', $unixTime),
			'mm' => date('i', $unixTime),
			'user_tz' => $userTimeZone,
			'timezone' => $timeZone
		];
	}

	public function decodeValue($value, $type)
	{
		if ($value === null)
		{
			return null;
		}

		switch ($type)
		{
			case 'serialized':
				return @unserialize($value);

			case 'serialized-array':
				$result = @unserialize($value);
				if (!is_array($result))
				{
					$result = [];
				}
				return $result;

			case 'json':
				return @json_decode($value, true);

			case 'json-array':
				$result = @json_decode($value, true);
				if (!is_array($result))
				{
					$result = [];
				}
				return $result;

			case 'serialized-json-array':
				$result = \XF\Util\Json::decodeJsonOrSerialized($value);
				if (!is_array($result))
				{
					$result = [];
				}
				return $result;

			case 'list-lines':
				return $value === '' ? [] : preg_split('/\r?\n/', $value);

			case 'list-comma':
				return $value === '' ? [] : explode(',', $value);

			case 'bool':
				return $value ? true : false;

			default:
				throw new \InvalidArgumentException("Unknown decode type '$type'");
		}
	}

	public function rewriteEmbeddedAttachments(\XF\Mvc\Entity\Entity $container, \XF\Entity\Attachment $attachment, $oldId, array $extras, $messageCol = 'message')
	{
		if (isset($container->$messageCol))
		{
			$message = $container->$messageCol;

			$message = preg_replace_callback(
				"#(\[ATTACH[^\]]*\]){$oldId}(\[/ATTACH\])#siU",
				function ($match) use ($attachment, $container)
				{
					$id = $attachment->attachment_id;

					if (isset($container->embed_metadata))
					{
						$metadata = $container->embed_metadata;
						$metadata['attachments'][$id] = $id;

						$container->embed_metadata = $metadata;
					}

					/*
					 * Note: We use '$id._xfImport' as the attachment id in the XenForo replacement
					 * to avoid it being replaced again if we come across an attachment whose source id
					 * is the same as this one's imported id.
					 */

					return $match[1] . $id . '._xfImport' . $match[2];
				},
				$message
			);

			$container->$messageCol = $message;
		}
	}

	/**
	 * @deprecated handled automatically in the database adapter if a tablePrefix is passed in.
	 *
	 * @param $prefix
	 * @param $SQL
	 *
	 * @return string|string[]|null
	 */
	protected function prepareImportSql($prefix, $SQL)
	{
		if ($prefix == '')
		{
			return $SQL;
		}

		return preg_replace('/((?:\s|^)(?:UPDATE|FROM|JOIN|STRAIGHT_JOIN)\s)([a-z0-9_-]+(?:\s|$))/siU', '$1' . $prefix . '$2$3', $SQL);
	}

	/**
	 * Attempts to convert a time zone offset into a location string
	 *
	 * @param float Offset (in hours) from UTC
	 * @param boolean Apply daylight savings
	 *
	 * @return string Location string, such as Europe/London
	 */
	public function getTimezoneFromOffset($offset, $useDst)
	{
		switch ($offset)
		{
			case -12: return 'Pacific/Midway'; // not right, but closest
			case -11: return 'Pacific/Midway';
			case -10: return 'Pacific/Honolulu';
			case -9.5: return 'Pacific/Marquesas';
			case -9: return 'America/Anchorage';
			case -8: return 'America/Los_Angeles';
			case -7: return ($useDst ? 'America/Denver' : 'America/Phoenix');
			case -6: return ($useDst ? 'America/Chicago' : 'America/Belize');
			case -5: return ($useDst ? 'America/New_York' : 'America/Bogota');
			case -4.5: return 'America/Caracas';
			case -4: return ($useDst ? 'America/Halifax' : 'America/La_Paz');
			case -3.5: return 'America/St_Johns';
			case -3: return ($useDst ? 'America/Argentina/Buenos_Aires' : 'America/Argentina/Mendoza');
			case -2: return 'America/Noronha';
			case -1: return ($useDst ? 'Atlantic/Azores' : 'Atlantic/Cape_Verde');
			case 0: return ($useDst ? 'Europe/London' : 'Atlantic/Reykjavik');
			case 1: return ($useDst ? 'Europe/Amsterdam' : 'Africa/Algiers');
			case 2: return ($useDst ? 'Europe/Athens' : 'Africa/Johannesburg');
			case 3: return 'Africa/Nairobi';
			case 3.5: return 'Asia/Tehran';
			case 4: return ($useDst ? 'Asia/Yerevan' : 'Europe/Moscow');
			case 4.5: return 'Asia/Kabul';
			case 5: return ($useDst ? 'Indian/Mauritius' : 'Asia/Tashkent');
			case 5.5: return 'Asia/Kolkata';
			case 5.75: return 'Asia/Kathmandu';
			case 6: return ($useDst ? 'Asia/Novosibirsk' : 'Asia/Almaty');
			case 6.5: return 'Asia/Yangon';
			case 7: return ($useDst ? 'Asia/Krasnoyarsk' : 'Asia/Bangkok');
			case 8: return ($useDst ? 'Asia/Irkutsk' : 'Asia/Hong_Kong');
			case 9: return ($useDst ? 'Asia/Yakutsk' : 'Asia/Tokyo');
			case 9.5: return ($useDst ? 'Australia/Adelaide' : 'Australia/Darwin');
			case 10: return ($useDst ? 'Australia/Hobart' : 'Australia/Brisbane');
			case 11: return ($useDst ? 'Asia/Magadan' : 'Pacific/Noumea');
			case 11.5: return 'Pacific/Norfolk';
			case 12: return ($useDst ? 'Pacific/Auckland' : 'Pacific/Fiji');
			case 12.75: return 'Pacific/Chatham';
			case 13: return 'Pacific/Tongatapu';
			case 14: return 'Pacific/Kiritimati';

			default: return 'Europe/London';
		}
	}

	protected function setPermission(array &$permissionsArray, $permissionGroup, $permissionName, $permissionValue = 'allow')
	{
		$permissionsArray[$permissionGroup][$permissionName] = $permissionValue;
	}

	public function __get($name)
	{
		switch ($name)
		{
			case 'prefix':
				return isset($this->baseConfig['db']['prefix']) ? $this->baseConfig['db']['prefix'] : '';

			default:
				throw new \LogicException("Undefined index $name");
		}
	}

	public function db()
	{
		return $this->app->db();
	}

	public function em()
	{
		return $this->app->em();
	}
}
