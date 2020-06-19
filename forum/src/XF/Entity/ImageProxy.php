<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null image_id
 * @property string url
 * @property string url_hash
 * @property int file_size
 * @property string file_name
 * @property string mime_type
 * @property int fetch_date
 * @property int first_request_date
 * @property int last_request_date
 * @property int views
 * @property bool pruned
 * @property int is_processing
 * @property int failed_date
 * @property int fail_count
 *
 * RELATIONS
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\ImageProxyReferrer[] Referrers
 */
class ImageProxy extends Entity
{
	protected $placeholderPath;

	public function setAsPlaceholder($filePath, $mimeType, $fileName = null)
	{
		if ($this->placeholderPath)
		{
			throw new \InvalidArgumentException("Once an image is marked as a placeholder, it cannot be changed");
		}

		if (!file_exists($filePath) || !is_readable($filePath))
		{
			throw new \InvalidArgumentException("Placeholder path '$filePath' doesn't exist or isn't readable");
		}

		$this->placeholderPath = $filePath;
		$this->file_name = $fileName ?: basename($filePath);
		$this->mime_type = $mimeType;
		$this->file_size = filesize($filePath);

		$this->setReadOnly(true);
	}

	public function isPlaceholder()
	{
		return $this->placeholderPath ? true : false;
	}

	public function getPlaceholderPath()
	{
		return $this->placeholderPath;
	}

	public function getAbstractedImagePath()
	{
		return sprintf('internal-data://image_cache/%d/%d-%s.data',
			floor($this->image_id / 1000),
			$this->image_id,
			$this->url_hash
		);
	}

	public function isValid()
	{
		if ($this->pruned)
		{
			return false;
		}

		return $this->app()->fs()->has($this->getAbstractedImagePath());
	}

	public function isRefreshRequired()
	{
		if ($this->placeholderPath)
		{
			return false;
		}

		$filePath = $this->getAbstractedImagePath();
		$fs = $this->app()->fs();

		if ($this->is_processing && \XF::$time - $this->is_processing < 5)
		{
			if ($fs->has($filePath))
			{
				return false;
			}

			$maxSleep = 5 - (\XF::$time - $this->is_processing);
			for ($i = 0; $i < $maxSleep; $i++)
			{
				if ($fs->has($filePath))
				{
					return false;
				}
			}
		}

		if ($this->failed_date && $this->fail_count)
		{
			return $this->isFailureRefreshRequired();
		}

		if ($this->pruned)
		{
			return true;
		}

		$ttl = $this->app()->options()->imageCacheTTL;
		if ($ttl && $this->fetch_date < \XF::$time - $ttl * 86400)
		{
			return true;
		}

		if (!$fs->has($filePath))
		{
			return true;
		}

		$refresh = $this->app()->options()->imageCacheRefresh;
		if ($refresh && !$this->fail_count && $this->fetch_date < \XF::$time - $refresh * 86400)
		{
			return true;
		}

		return false;
	}

	public function getNextPlannedRefreshDate()
	{
		if ($this->placeholderPath)
		{
			return \XF::$time;
		}

		if ($this->is_processing)
		{
			// check again in 5 seconds
			return \XF::$time + 5;
		}

		$dates = [];

		if ($this->fetch_date)
		{
			$ttl = $this->app()->options()->imageCacheTTL;
			if ($ttl)
			{
				$dates[] = $this->fetch_date + ($ttl * 86400);
			}

			$refresh = $this->app()->options()->imageCacheRefresh;
			if ($refresh && !$this->fail_count)
			{
				$dates[] = $this->fetch_date + ($refresh * 86400);
			}
		}

		$failureRefresh = $this->getNextFailureRefreshDate();
		if ($failureRefresh)
		{
			$dates[] = $failureRefresh;
		}

		if (!$dates)
		{
			// no refresh planned
			return null;
		}

		return max(\XF::$time, min($dates));
	}

	public function isFailureRefreshRequired()
	{
		$refreshDate = $this->getNextFailureRefreshDate();
		if (!$refreshDate)
		{
			return false;
		}

		return (\XF::$time >= $refreshDate);
	}

	public function getNextFailureRefreshDate()
	{
		if (!$this->failed_date || !$this->fail_count)
		{
			return null;
		}

		switch ($this->fail_count)
		{
			case 1: $delay = 60; break; // 1 minute
			case 2: $delay = 5 * 60; break; // 5 minutes
			case 3: $delay = 30 * 60; break; // 30 minutes
			case 4: $delay = 3600; break; // 1 hour
			case 5: $delay = 6 * 3600; break; // 6 hours

			default:
				$delay = ($this->fail_count - 5) * 86400; // 1, 2, 3... days
		}

		return ($this->failed_date + $delay);
	}

	public function getETagValue()
	{
		if ($this->isPlaceholder() || $this->fail_count || $this->pruned)
		{
			return null;
		}

		return sha1($this->url . $this->fetch_date);
	}

	public function prune()
	{
		if ($this->placeholderPath)
		{
			return false;
		}

		$this->pruned = true;
		$this->save();

		\XF\Util\File::deleteFromAbstractedPath($this->getAbstractedImagePath());

		return true;
	}

	protected function verifyFileName(&$fileName)
	{
		if (!preg_match('/./su', $fileName))
		{
			$fileName = preg_replace('/[\x80-\xFF]/', '?', $fileName);
		}

		$fileName = \XF::cleanString($fileName);

		// ensure the filename fits -- if it's too long, take off from the beginning to keep extension
		$length = utf8_strlen($fileName);
		if ($length > 250)
		{
			$fileName = utf8_substr($fileName, $length - 250);
		}

		return true;
	}

	protected function verifyUrl(&$url)
	{
		$url = $this->getProxyRepo()->cleanUrlForFetch($url);

		if (!preg_match('#^https?://#i', $url))
		{
			$this->error('Developer: invalid URL', 'url');
			return false;
		}

		return true;
	}

	protected function _preSave()
	{
		if ($this->placeholderPath)
		{
			throw new \LogicException("Cannot save placeholder image");
		}

		if ($this->isChanged('url'))
		{
			$this->url_hash = md5($this->url);
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_image_proxy';
		$structure->shortName = 'XF:ImageProxy';
		$structure->primaryKey = 'image_id';
		$structure->columns = [
			'image_id' => ['type' => self::UINT, 'nullable' => true, 'autoIncrement' => true],
			'url' => ['type' => self::STR, 'required' => true],
			'url_hash' => ['type' => self::STR, 'maxLength' => 32, 'required' => true],
			'file_size' => ['type' => self::UINT, 'default' => 0],
			'file_name' => ['type' => self::STR, 'maxLength' => 250, 'default' => ''],
			'mime_type' => ['type' => self::STR, 'maxLength' => 100, 'default' => ''],
			'fetch_date' => ['type' => self::UINT, 'default' => 0],
			'first_request_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'last_request_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'views' => ['type' => self::UINT, 'default' => 0],
			'pruned' => ['type' => self::BOOL, 'default' => false],
			'is_processing' => ['type' => self::UINT, 'default' => 0],
			'failed_date' => ['type' => self::UINT, 'default' => 0],
			'fail_count' => ['type' => self::UINT, 'default' => 0],
		];
		$structure->getters = [];
		$structure->relations = [
			'Referrers' => [
				'entity' => 'XF:ImageProxyReferrer',
				'type' => self::TO_MANY,
				'conditions' => 'image_id',
				'order' => ['last_date', 'DESC']
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\ImageProxy
	 */
	protected function getProxyRepo()
	{
		return $this->repository('XF:ImageProxy');
	}
}