<?php

namespace XF\Import;

class DataManager
{
	/**
	 * @var \XF\App
	 */
	protected $app;

	/**
	 * @var Log
	 */
	protected $log;

	protected $retainIds = false;

	protected $fullUnicode = false;

	protected $sourceCharset = null;
	protected $sourceConvertHtml = false;

	protected $dataClassMap = [];

	/**
	 * @var \XF\Import\DataHelper\AbstractHelper[]
	 */
	protected $helpers = [];

	public function __construct(\XF\App $app, Log $log, $retainIds, $fullUnicode = false)
	{
		$this->app = $app;
		$this->log = $log;
		$this->retainIds = $retainIds;
		$this->fullUnicode = $fullUnicode;
	}

	public function getLog()
	{
		return $this->log;
	}

	public function getRetainIds()
	{
		return $this->retainIds;
	}

	public function getFullUnicode()
	{
		return $this->fullUnicode;
	}

	public function setSourceCharset($charset, $convertHtml = false)
	{
		$this->sourceCharset = $charset;
		$this->sourceConvertHtml = $convertHtml;
	}

	public function getSourceCharset()
	{
		return $this->sourceCharset;
	}

	/**
	 * @param string $type
	 * @param bool $log
	 *
	 * @return \XF\Import\Data\AbstractData
	 */
	public function newHandler($type, $log = true)
	{
		if (!isset($this->dataClassMap[$type]))
		{
			$class = \XF::stringToClass($type, '%s\Import\Data\%s');
			$class = $this->app->extendClass($class);
			$this->dataClassMap[$type] = $class;

			if (!class_exists($class))
			{
				throw new \InvalidArgumentException("Could not find $type handler ($class)");
			}
		}
		else
		{
			$class = $this->dataClassMap[$type];
		}

		return new $class($this, $log);
	}

	public function importData($type, $oldId, array $data, $log = true)
	{
		$handler = $this->newHandler($type, $log);
		$handler->bulkSet($data);
		return $handler->save($oldId);
	}

	public function lookup($type, $oldIds)
	{
		return $this->log->lookup($type, $oldIds);
	}

	public function lookupId($type, $id, $default = false)
	{
		return $this->log->lookupId($type, $id, $default);
	}

	public function typeMap($type)
	{
		return $this->log->typeMap($type);
	}

	public function log($type, $oldId, $newId)
	{
		$this->log->log($type, $oldId, $newId);
	}

	public function logHandler($handlerType, $oldId, $newId)
	{
		$handler = $this->newHandler($handlerType);
		$this->log->log($handler->getImportType(), $oldId, $newId);
	}

	public function isInvalidUtf8($string)
	{
		if (is_string($string) && !preg_match('/./su', $string))
		{
			return true;
		}

		return false;
	}

	public function convertToUtf8($string, $fromCharset = null, $convertHtml = null)
	{
		if ($fromCharset === null)
		{
			$fromCharset = $this->sourceCharset;
		}
		if ($convertHtml === null)
		{
			$convertHtml = $this->sourceConvertHtml;
		}

		$string = strval($string);

		if (preg_match('/[\x80-\xff]/', $string))
		{
			if ($fromCharset)
			{
				$newString = false;
				if (function_exists('mb_convert_encoding'))
				{
					$newString = @mb_convert_encoding($string, 'utf-8', $fromCharset);

					if (PHP_VERSION_ID < 70204 && $this->isInvalidUtf8($newString))
					{
						// 7.2.4 has a fix for so bad replacements in the mbstring tables. Some edge cases
						// can cause failures, so we need to check this and fallback to iconv if needed.
						$newString = false;
					}
				}
				if (!$newString && function_exists('iconv'))
				{
					$newString = @iconv($fromCharset, 'UTF-8//IGNORE', $string);
					if ($newString && strtolower($fromCharset) == 'utf-8')
					{
						$newString = utf8_bad_replace($newString);
					}
				}
				$string = ($newString ? $newString : preg_replace('/[\x80-\xff]/', '', $string));
			}
			else
			{
				$string = utf8_bad_replace($string);
			}
		}

		$string = utf8_unhtml($string, $convertHtml);

		return $this->stripExtendedUtf8IfNeeded($string);
	}

	public function stripExtendedUtf8IfNeeded($string)
	{
		if (!$this->fullUnicode)
		{
			$string = preg_replace('/[\xF0-\xF7].../', '', $string);
			$string = preg_replace('/[\xF8-\xFB]..../', '', $string);
		}

		return $string;
	}

	public function convertToUtf8IfNeeded($string, $fromCharset = null, $convertHtml = null)
	{
		if ($this->isInvalidUtf8($string))
		{
			return $this->convertToUtf8($string, $fromCharset, $convertHtml);
		}
		else
		{
			return $this->stripExtendedUtf8IfNeeded($string);
		}
	}

	/**
	 * Converts a title string to an ID - 'My Example Thing' -> my_example_thing
	 *
	 * @param     $string
	 * @param int $maxLength
	 * @param string $glue
	 *
	 * @return mixed
	 */
	public function convertToId($string, $maxLength = 25)
	{
		$formatter = new \XF\Str\Formatter();

		return str_replace('-', '_',
			$this->app()->router()->prepareStringForUrl(
				$formatter->wholeWordTrim($string, $maxLength, 0, ''), true));
	}

	/**
	 * @param string $class
	 *
	 * @return DataHelper\AbstractHelper
	 */
	public function helper($class)
	{
		if (!isset($this->helpers[$class]))
		{
			$class = \XF::stringToClass($class, '%s\Import\DataHelper\%s');
			$class = \XF::extendClass($class);
			$this->helpers[$class] = new $class($this);
		}

		return $this->helpers[$class];
	}

	public function db()
	{
		return $this->app->db();
	}

	public function em()
	{
		return $this->app->em();
	}

	public function app()
	{
		return $this->app;
	}
}