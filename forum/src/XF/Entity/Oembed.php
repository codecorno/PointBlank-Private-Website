<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null oembed_id
 * @property string media_site_id
 * @property string media_id
 * @property string media_hash
 * @property string|null title
 * @property int fetch_date
 * @property int first_request_date
 * @property int last_request_date
 * @property int views
 * @property bool pruned
 * @property int is_processing
 * @property int failed_date
 * @property int fail_count
 *
 * GETTERS
 * @property string url
 *
 * RELATIONS
 * @property \XF\Entity\BbCodeMediaSite BbCodeMediaSite
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\OembedReferrer[] Referrers
 */
class Oembed extends Entity
{
	const FAILURE = 1;

	protected $failure = null;

	public function getAbstractedJsonPath()
	{
		return sprintf('internal-data://oembed_cache/%d/%d-%s.json',
			floor($this->oembed_id / 1000),
			$this->oembed_id,
			$this->media_hash
		);
	}

	public function isValid()
	{
		if ($this->pruned)
		{
			return false;
		}

		return $this->app()->fs()->has($this->getAbstractedJsonPath());
	}

	public function setAsFailure()
	{
		$this->failure = self::FAILURE;
		$this->setReadOnly(true);
	}

	public function isFailure()
	{
		return $this->failure ? true : false;
	}

	public function isRefreshRequired()
	{
		$filePath = $this->getAbstractedJsonPath();
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

		$ttl = $this->app()->options()->oEmbedCacheTTL;
		if ($ttl && $this->fetch_date < \XF::$time - $ttl * 86400)
		{
			return true;
		}

		if (!$fs->has($filePath))
		{
			return true;
		}

		$refresh = $this->app()->options()->oEmbedCacheRefresh;
		if ($refresh && !$this->fail_count && $this->fetch_date < \XF::$time - $refresh * 86400)
		{
			return true;
		}

		return false;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return str_replace('{$id}', $this->media_id, $this->BbCodeMediaSite->oembed_url_scheme);
	}

	public function isFailureRefreshRequired()
	{
		if (!$this->failed_date || !$this->fail_count)
		{
			return false;
		}

		switch ($this->fail_count)
		{
			case 1: $delay = 30; break; // 30 seconds
			case 2: $delay = 2.5 * 60; break; // 2.5 minutes
			case 3: $delay = 10 * 60; break; // 10 minutes
			case 4: $delay = 1800; break; // 30 mins
			case 5: $delay = 2 * 3600; break; // 2 hours

			default:
				$delay = ($this->fail_count - 5) * 86400; // 1, 2, 3... days
		}

		return \XF::$time >= ($this->failed_date + $delay);
	}

	public function prune()
	{
		$this->pruned = true;
		$this->save();

		\XF\Util\File::deleteFromAbstractedPath($this->getAbstractedJsonPath());

		return true;
	}

	protected function _preSave()
	{
		if ($this->isChanged('media_site_id') || $this->isChanged('media_id'))
		{
			$this->media_hash = md5($this->media_site_id . $this->media_id);
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_oembed';
		$structure->shortName = 'XF:Oembed';
		$structure->primaryKey = 'oembed_id';
		$structure->columns = [
			'oembed_id' => ['type' => self::UINT, 'nullable' => true, 'autoIncrement' => true],
			'media_site_id' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'media_id' => ['type' => self::STR, 'maxLength' => 250, 'required' => true],
			'media_hash' => ['type' => self::STR, 'maxLength' => 32, 'required' => true],
			'title' => ['type' => self::STR, 'nullable' => true , 'default' => null],
			'fetch_date' => ['type' => self::UINT, 'default' => 0],
			'first_request_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'last_request_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'views' => ['type' => self::UINT, 'default' => 0],
			'pruned' => ['type' => self::BOOL, 'default' => false],
			'is_processing' => ['type' => self::UINT, 'default' => 0],
			'failed_date' => ['type' => self::UINT, 'default' => 0],
			'fail_count' => ['type' => self::UINT, 'default' => 0],
		];
		$structure->getters = ['url' => true];
		$structure->relations = [
			'BbCodeMediaSite' => [
				'entity' => 'XF:BbCodeMediaSite',
				'type' => self::TO_ONE,
				'conditions' => 'media_site_id',
				'primary' => true
			],
			'Referrers' => [
				'entity' => 'XF:OembedReferrer',
				'type' => self::TO_MANY,
				'conditions' => 'oembed_id',
				'order' => ['last_date', 'DESC']
			]
		];

		return $structure;
	}
}