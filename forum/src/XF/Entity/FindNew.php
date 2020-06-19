<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null find_new_id
 * @property string content_type
 * @property array filters
 * @property string filter_hash
 * @property int user_id
 * @property array results
 * @property int cache_date
 *
 * GETTERS
 * @property int result_count
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class FindNew extends Entity
{
	/**
	 * @return int
	 */
	public function getResultCount()
	{
		$results = $this->results;
		return $results ? count($results) : 0;
	}

	public function getPageResultIds($page, $perPage)
	{
		$page = max(1, intval($page));
		$perPage = max(1, intval($perPage));

		$results = $this->results;
		if ($results)
		{
			return array_slice($results, ($page - 1) * $perPage, $perPage);
		}
		else
		{
			return [];
		}
	}

	public function getFilterHash()
	{
		return md5(json_encode($this->filters));
	}

	protected function verifyFilters(&$value)
	{
		if (is_array($value))
		{
			ksort($value);
		}

		return true;
	}

	protected function _preSave()
	{
		$this->filter_hash = $this->getFilterHash();
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_find_new';
		$structure->shortName = 'XF:FindNew';
		$structure->primaryKey = 'find_new_id';
		$structure->columns = [
			'find_new_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'filters' => ['type' => self::JSON_ARRAY, 'default' => []],
			'filter_hash' => ['type' => self::STR, 'maxLength' => 32, 'default' => ''],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'results' => ['type' => self::LIST_COMMA, 'default' => []],
			'cache_date' => ['type' => self::UINT, 'default' => \XF::$time],
		];
		$structure->getters = [
			'result_count' => true
		];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			]
		];

		return $structure;
	}
}