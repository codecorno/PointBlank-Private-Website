<?php

namespace XF\Sitemap;

/**
 * @property int $fileSet
 * @property int $fileCount
 * @property int $fileSize
 * @property int $fileEntryCount
 * @property int $totalEntryCount
 * @property array $pendingTypes
 * @property string $currentType
 * @property int $lastTypeId
 * @property bool $coreWritten
 */
class BuildState
{
	protected $possibleTypes = [];

	/**
	 * @var int
	 */
	protected $fileSet;

	/**
	 * @var int
	 */
	protected $fileCount = 1;

	/**
	 * @var int
	 */
	protected $fileSize = 0;

	/**
	 * @var int
	 */
	protected $fileEntryCount = 0;

	/**
	 * @var int
	 */
	protected $totalEntryCount = 0;

	/**
	 * @var array|null
	 */
	protected $pendingTypes;

	/**
	 * @var string
	 */
	protected $currentType = '';

	/**
	 * @var int
	 */
	protected $lastTypeId = 0;

	/**
	 * @var bool
	 */
	protected $coreWritten = false;

	public function __construct(array $possibleTypes)
	{
		$this->possibleTypes = $possibleTypes;
	}

	public function getFileSet()
	{
		if (!$this->fileSet)
		{
			$this->fileSet = \XF::$time;
		}

		return $this->fileSet;
	}

	public function getPendingTypes()
	{
		if ($this->pendingTypes === null)
		{
			$this->pendingTypes = array_keys($this->possibleTypes);
		}

		return $this->pendingTypes;
	}

	public function getNextType()
	{
		$pendingTypes = $this->getPendingTypes();
		if (!$pendingTypes)
		{
			return '';
		}

		return array_shift($this->pendingTypes);
	}

	public function getActiveType()
	{
		while (!$this->currentType || !isset($this->possibleTypes[$this->currentType]))
		{
			$this->currentType = $this->getNextType();
			$this->lastTypeId = 0;

			if (!$this->currentType)
			{
				// nothing left
				break;
			}
		}

		return $this->currentType;
	}

	public function resetCurrentType()
	{
		$this->currentType = '';
		$this->lastTypeId = 0;
	}

	public function entryAdded()
	{
		$this->fileEntryCount++;
		$this->totalEntryCount++;
	}

	public function incrementFile()
	{
		$this->fileCount++;
		$this->fileSize = 0;
		$this->fileEntryCount = 0;
	}

	public function __get($key)
	{
		$getter = 'get' . $key;
		if (method_exists($this, $getter))
		{
			return $this->{$getter}();
		}
		else if (property_exists($this, $key))
		{
			return $this->{$key};
		}
		else
		{
			throw new \InvalidArgumentException("Unknown build state field '$key'");
		}
	}

	public function __set($key, $value)
	{
		if (property_exists($this, $key))
		{
			$this->{$key} = $value;
		}
		else
		{
			throw new \InvalidArgumentException("Unknown build state field '$key'");
		}
	}
}