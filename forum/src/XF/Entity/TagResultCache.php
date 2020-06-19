<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null result_cache_id
 * @property int tag_id
 * @property int user_id
 * @property int cache_date
 * @property int expiry_date
 * @property array results
 *
 * RELATIONS
 * @property \XF\Entity\Tag Tag
 * @property \XF\Entity\User User
 */
class TagResultCache extends Entity
{
	public function getPageResults($page, $perPage)
	{
		$page = max(1, intval($page));
		$perPage = max(1, intval($perPage));

		$offset = ($page - 1) * $perPage;

		return array_slice($this->results, $offset, $perPage);
	}

	public function requiresRefetch()
	{
		return !is_array($this->results) || $this->expiry_date < \XF::$time;
	}

	protected function verifyResults(&$results)
	{
		if (!is_array($results))
		{
			$results = [];
		}

		$this->expiry_date = \XF::$time + 3600;

		return true;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_tag_result_cache';
		$structure->shortName = 'XF:TagResultCache';
		$structure->primaryKey = 'result_cache_id';
		$structure->columns = [
			'result_cache_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'tag_id' => ['type' => self::UINT, 'required' => true],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'cache_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'expiry_date' => ['type' => self::UINT, 'default' => 0],
			'results' => ['type' => self::JSON_ARRAY, 'required' => true]
		];
		$structure->getters = [
		];
		$structure->relations = [
			'Tag' => [
				'entity' => 'XF:Tag',
				'type' => self::TO_ONE,
				'conditions' => 'tag_id',
				'primary' => true
			],
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Tag
	 */
	protected function getTagRepo()
	{
		return $this->repository('XF:Tag');
	}
}