<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null search_id
 * @property array search_results
 * @property int result_count
 * @property string search_type
 * @property string search_query
 * @property array search_constraints
 * @property string search_order
 * @property bool search_grouping
 * @property array user_results
 * @property array warnings
 * @property int user_id
 * @property int search_date
 * @property string query_hash
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class Search extends Entity
{
	public function setupFromQuery(\XF\Search\Query\Query $query, array $constraints = [])
	{
		$this->search_type = $query->getHandlerType() ?: '';
		$this->search_query = $query->getKeywords();
		$this->search_constraints = $constraints;
		$this->search_order = $query->getOrderName();
		$this->search_grouping = $query->getGroupByType() ? true : false;
		$this->warnings = $query->getWarnings();
		$this->query_hash = $query->getUniqueQueryHash();
	}

	protected function verifySearchResults(&$results)
	{
		if (!is_array($results))
		{
			$results = [];
		}

		$this->result_count = count($results);
		return true;
	}

	protected function verifySearchConstraints(&$constraints)
	{
		if (!is_array($constraints))
		{
			$constraints = [];
			return true;
		}

		ksort($constraints);

		foreach ($constraints AS $key => $value)
		{
			if ($value === [] || $value === '' || $value === null)
			{
				unset($constraints[$key]);
			}
		}

		return true;
	}

	protected function verifyWarnings(&$warnings)
	{
		if (!is_array($warnings))
		{
			$warnings = [];
			return true;
		}

		foreach ($warnings AS &$warning)
		{
			$warning = strval($warning);
		}

		return true;
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_search';
		$structure->shortName = 'XF:Search';
		$structure->primaryKey = 'search_id';
		$structure->columns = [
			'search_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'search_results' => ['type' => self::JSON_ARRAY, 'required' => true],
			'result_count' => ['type' => self::UINT, 'default' => 0],
			'search_type' => ['type' => self::STR, 'maxLength' => 25, 'default' => ''],
			'search_query' => ['type' => self::STR, 'maxLength' => 200, 'default' => ''],
			'search_constraints' => ['type' => self::JSON_ARRAY, 'default' => []],
			'search_order' => ['type' => self::STR, 'maxLength' => 50, 'default' => ''],
			'search_grouping' => ['type' => self::BOOL, 'default' => false],
			'user_results' => ['type' => self::JSON_ARRAY, 'default' => []],
			'warnings' => ['type' => self::JSON_ARRAY, 'default' => []],
			'user_id' => ['type' => self::UINT, 'required' => true],
			'search_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'query_hash' => ['type' => self::STR, 'maxLength' => 32, 'default' => '']
		];
		$structure->getters = [
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