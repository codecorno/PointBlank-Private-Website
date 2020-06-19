<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null result_id
 * @property string url
 * @property string url_hash
 * @property string|null title
 * @property string|null description
 * @property string|null image_url
 * @property string|null favicon_url
 * @property int last_request_date
 * @property bool pending
 * @property int error_count
 *
 * GETTERS
 * @property bool is_recrawl
 * @property mixed host
 */
class UnfurlResult extends Entity
{
	/**
	 * @return bool
	 */
	public function getIsRecrawl()
	{
		return ($this->pending && $this->requiresRecrawl());
	}

	public function getHost()
	{
		return parse_url($this->url, PHP_URL_HOST);
	}

	public function requiresRecrawl()
	{
		if ($this->error_count >= 3)
		{
			return false;
		}

		if ($this->last_request_date && $this->last_request_date <= \XF::$time - 30 * 86400)
		{
			return true;
		}

		return false;
	}

	public function isBasicLink()
	{
		return (!$this->pending
			&& !$this->description
			&& !$this->image_url
			&& !$this->favicon_url
		);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_unfurl_result';
		$structure->shortName = 'XF:UnfurlResult';
		$structure->primaryKey = 'result_id';
		$structure->columns = [
			'result_id' => ['type' => self::UINT, 'nullable' => true, 'autoIncrement' => true],
			'url' => ['type' => self::STR, 'required' => true],
			'url_hash' => ['type' => self::STR, 'maxLength' => 32, 'required' => true],
			'title' => ['type' => self::STR, 'nullable' => true],
			'description' => ['type' => self::STR, 'nullable' => true],
			'image_url' => ['type' => self::STR, 'nullable' => true],
			'favicon_url' => ['type' => self::STR, 'nullable' => true],
			'last_request_date' => ['type' => self::UINT, 'default' => 0],
			'pending' => ['type' => self::BOOL, 'default' => false],
			'error_count' => ['type' => self::UINT, 'default' => 0]
		];
		$structure->getters = [
			'is_recrawl' => true,
			'host' => true
		];
		$structure->relations = [];

		return $structure;
	}
}