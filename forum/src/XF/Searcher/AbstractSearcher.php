<?php

namespace XF\Searcher;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Manager;
use XF\Util\Arr;

abstract class AbstractSearcher
{
	protected $em;
	protected $structure;

	protected $rawCriteria = [];
	protected $filteredCriteria = [];

	protected $allowedRelations = [];
	protected $formats = [];
	protected $blacklist = [];
	protected $whitelist = [];

	// This should contain any keys that use array-values
	protected $arrayValueKeys = [];

	protected $orderOptions = [];

	protected $order = [];

	public function __construct(Manager $em, array $criteria = null)
	{
		$this->em = $em;
		$this->structure = $em->getEntityStructure($this->getEntityType());
		$this->orderOptions = $this->getDefaultOrderOptions();

		$this->init();

		if ($criteria)
		{
			$this->setCriteria($criteria);
		}
	}

	abstract protected function getEntityType();

	/**
	 * Return the default allowed order options, in [column] => printable name form
	 *
	 * @return array
	 */
	abstract protected function getDefaultOrderOptions();

	protected function init()
	{
	}

	public function setCriteria(array $criteria)
	{
		$this->rawCriteria = $criteria;
		$this->filteredCriteria = $this->filterCriteria($criteria);
	}

	public function getRawCriteria()
	{
		return $this->rawCriteria;
	}

	public function getFilteredCriteria()
	{
		return $this->filteredCriteria;
	}

	public function addBlacklist($entry)
	{
		if (is_array($entry))
		{
			$this->blacklist = Arr::mapMerge($this->blacklist, $entry);
		}
		else
		{
			$this->blacklist[$entry] = true;
		}

		return $this;
	}

	public function setBlacklist(array $blacklist)
	{
		$this->blacklist = $blacklist;
		return $this;
	}

	public function addWhitelist($entry)
	{
		if (is_array($entry))
		{
			$this->whitelist = Arr::mapMerge($this->whitelist, $entry);
		}
		else
		{
			$this->whitelist[$entry] = true;
		}

		return $this;
	}

	public function setWhitelist(array $whitelist)
	{
		$this->whitelist = $whitelist;
		return $this;
	}

	public function getWhitelist()
	{
		return $this->whitelist;
	}

	public function addOrderOptions($key, $text)
	{
		$this->orderOptions[$key] = $text;
		return $this;
	}

	public function getOrderOptions()
	{
		return $this->orderOptions;
	}

	public function getRecommendedOrderDirection($key)
	{
		if (!isset($this->orderOptions[$key]))
		{
			return 'desc';
		}

		$structure = $this->structure;
		if (!isset($structure->columns[$key]))
		{
			return 'desc';
		}

		if ($structure->columns[$key]['type'] == Entity::STR)
		{
			return 'asc';
		}
		else
		{
			return 'desc';
		}
	}

	public function setOrder($order, $direction = 'asc', $secondaryOrder = null, $secondaryDirection = 'asc')
	{
		$newOrder = [];

		if ($order && isset($this->orderOptions[$order]))
		{
			$newOrder[] = [$order, strtolower($direction) == 'asc' ? 'asc' : 'desc'];
		}

		if ($secondaryOrder && isset($this->orderOptions[$secondaryOrder]))
		{
			$newOrder[] = [$secondaryOrder, strtolower($secondaryDirection) == 'asc' ? 'asc' : 'desc'];
		}

		if ($newOrder)
		{
			$this->order = $newOrder;
			return true;
		}
		else
		{
			return false;
		}
	}

	public function getOrder()
	{
		return $this->order;
	}

	protected function filterCriteria(array $criteria, $relation = null)
	{
		if ($relation === null)
		{
			$structure = $this->structure;
			$allowedRelations = array_fill_keys($this->allowedRelations, true);
			$formats = $this->formats;
			$blacklist = $this->blacklist;
			$whitelist = $this->whitelist;
		}
		else
		{
			$structure = $this->em->getEntityStructure($this->structure->relations[$relation]['entity']);
			$allowedRelations = [];
			$formats = isset($this->formats[$relation]) ? $this->formats[$relation] : [];
			$blacklist = isset($this->blacklist[$relation]) ? $this->blacklist[$relation] : [];
			$whitelist = isset($this->whitelist[$relation]) ? $this->whitelist[$relation] : [];
		}

		foreach ($criteria AS $key => &$value)
		{
			if (isset($blacklist[$key]) || ($whitelist && !isset($whitelist[$key])))
			{
				unset($criteria[$key]);
			}
			else if (isset($structure->relations[$key]))
			{
				if (isset($allowedRelations[$key]) && is_array($value))
				{
					$value = $this->filterCriteria($value, $key);
				}
				else
				{
					$value = false;
				}
				if (!$value || !is_array($value))
				{
					unset($criteria[$key]);
				}
			}
			else
			{
				$column = isset($structure->columns[$key]) ? $structure->columns[$key] : null;
				$format = isset($formats[$key]) ? $formats[$key] : null;
				if (!$this->validateCriteriaValue($key, $value, $column, $format, $relation))
				{
					unset($criteria[$key]);
				}
			}
		}

		return $criteria;
	}

	protected function validateCriteriaValue($key, &$value, $column, $format, $relation)
	{
		$validated = $this->validateSpecialCriteriaValue($key, $value, $column, $format, $relation);
		if ($validated === true || $validated === false)
		{
			return $validated;
		}
		// null falls through

		if (is_array($value) && !in_array($key, $this->arrayValueKeys))
		{
			$uniqueValues = array_unique($value, SORT_REGULAR);
			if (count($uniqueValues) == 1 && is_int(key($uniqueValues)))
			{
				$value = reset($uniqueValues);
			}
		}

		if ($value === '' || (is_array($value) && !count($value)))
		{
			return false;
		}

		if (is_array($value) && $column)
		{
			if (isset($column['allowedValues']))
			{
				$allowedValues = $column['allowedValues'];
				$matched = 0;
				foreach ($allowedValues AS $allowed)
				{
					if (in_array($allowed, $value))
					{
						$matched++;
					}
				}
				if ($matched == count($allowedValues))
				{
					// all possible values selected
					return false;
				}
			}

			if ($column['type'] == Entity::BOOL && count($value) == 2)
			{
				// both values selected
				return false;
			}

			if (isset($value['start']) || isset($value['end']))
			{
				if (isset($column['min']))
				{
					$min = $column['min'];
				}
				else if ($column['type'] == Entity::UINT)
				{
					$min = 0;
				}
				else
				{
					$min = null;
				}
				if (isset($column['max']))
				{
					$max = $column['max'];
				}
				else
				{
					$max = null;
				}

				if (isset($value['start']))
				{
					if ($value['start'] === ''
						|| ($min !== null && is_numeric($value['start']) && $value['start'] <= $min)
						|| ($format == 'date' && !$this->validateDate($value['start']))
					)
					{
						unset($value['start']);
					}
				}
				if (isset($value['end']))
				{
					if ($value['end'] === ''
						|| ($min !== null && is_numeric($value['end']) && $value['end'] < $min)
						|| ($max !== null && is_numeric($value['end']) && $value['end'] >= $max)
						|| ($format == 'date' && !$this->validateDate($value['end']))
					)
					{
						unset($value['end']);
					}
				}

				if (!$value)
				{
					return false;
				}
			}
		}

		$validated = $this->validateSpecialCriteriaValueAfter($key, $value, $column, $format, $relation);
		if ($validated === true || $validated === false)
		{
			return $validated;
		}

		return true;
	}

	protected function validateSpecialCriteriaValue($key, &$value, $column, $format, $relation)
	{
		return null;
	}

	protected function validateSpecialCriteriaValueAfter($key, &$value, $column, $format, $relation)
	{
		return null;
	}

	protected function validateDate($value)
	{
		return (is_int($value) || preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $value));
	}

	public function getFinder()
	{
		$finder = $this->em->getFinder($this->getEntityType());
		$this->applyCriteria($finder, $this->filteredCriteria);

		if ($this->order)
		{
			$finder->setDefaultOrder($this->order);
		}

		return $finder;
	}

	protected function applyCriteria(Finder $finder, array $criteria, $relation = null)
	{
		if ($relation === null)
		{
			$structure = $this->structure;
			$formats = $this->formats;
		}
		else
		{
			$structure = $this->em->getEntityStructure($this->structure->relations[$relation]['entity']);
			$formats = isset($this->formats[$relation]) ? $this->formats[$relation] : [];
		}

		foreach ($criteria AS $key => &$value)
		{
			if (isset($structure->relations[$key]))
			{
				$this->applyCriteria($finder, $value, $key);
			}
			else
			{
				$column = isset($structure->columns[$key]) ? $structure->columns[$key] : null;
				$format = isset($formats[$key]) ? $formats[$key] : null;
				$this->applyCriteriaValue($finder, $key, $value, $column, $format, $relation);
			}
		}
	}

	protected function applyCriteriaValue(Finder $finder, $key, $value, $column, $format, $relation)
	{
		if ($this->applySpecialCriteriaValue($finder, $key, $value, $column, $format, $relation))
		{
			return;
		}

		if (!$column)
		{
			return;
		}

		$columnName = ($relation ? "$relation." : '') . $key;

		if ($format == 'like')
		{
			$value = $finder->escapeLike($value, '%?%');
			$finder->where($columnName, 'LIKE', $value);
			return;
		}

		if ($format == 'date')
		{
			if (is_array($value))
			{
				if (isset($value['start']))
				{
					$value['start'] = $this->convertDateToInteger($value['start']);
				}
				if (isset($value['end']))
				{
					$value['end'] = $this->convertDateToInteger($value['end'], true);
				}
			}
			else
			{
				$value = $this->convertDateToInteger($value);
			}
		}

		$value = $this->castValueToColumnType($value, $column['type']);

		if (is_array($value))
		{
			$hasMin = isset($value['start']);
			$hasMax = isset($value['end']);
			if ($hasMin && $hasMax)
			{
				$finder->where($columnName, 'BETWEEN', [$value['start'], $value['end']]);
				return;
			}
			else if ($hasMin)
			{
				$finder->where($columnName, '>=', $value['start']);
				return;
			}
			else if ($hasMax)
			{
				$finder->where($columnName, '<=', $value['end']);
				return;
			}
		}

		$finder->where($columnName, '=', $value);
	}

	protected function castValueToColumnType($value, $type)
	{
		if (is_array($value))
		{
			foreach ($value AS &$v)
			{
				$v = $this->castValueToColumnType($v, $type);
			}

			return $value;
		}

		switch ($type)
		{
			case Entity::BOOL:
				return ($value ? 1 : 0);

			case Entity::INT:
			case Entity::UINT:
			case Entity::FLOAT:
				return $value + 0;

			default:
				return strval($value);
		}
	}

	protected function convertDateToInteger($date, $dayEnd = false)
	{
		if (is_int($date))
		{
			return $date;
		}

		$d = new \DateTime($date, \XF::language()->getTimeZone());
		if ($dayEnd)
		{
			$d->setTime(23, 59, 59);
		}

		return intval($d->format('U'));
	}

	protected function applySpecialCriteriaValue(Finder $finder, $key, $value, $column, $format, $relation)
	{
		return false;
	}

	public function getFormData()
	{
		return [];
	}

	public function getFormCriteria()
	{
		if ($this->rawCriteria)
		{
			return $this->rawCriteria;
		}
		else
		{
			return $this->getFormDefaults();
		}
	}

	public function getFormDefaults()
	{
		return [];
	}
}