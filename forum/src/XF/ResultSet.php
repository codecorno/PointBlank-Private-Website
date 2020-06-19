<?php

namespace XF;

class ResultSet
{
	/**
	 * @var ResultSetInterface
	 */
	protected $setInterface;

	protected $rawResults;

	protected $results;

	protected $resultsData = null;

	public function __construct(ResultSetInterface $setInterface, array $results = [])
	{
		$this->setInterface = $setInterface;
		$this->setRawResults($results);
	}

	public function getResultSetInterface()
	{
		return $this->setInterface;
	}

	public function setRawResults(array $results)
	{
		$this->rawResults = $results;
		$this->results = $this->normalizeResultIds($results);
		$this->resultsData = null;

		return $this;
	}

	public function getRawResults()
	{
		return $this->rawResults;
	}

	public function getResults()
	{
		return $this->results;
	}

	public function countResults()
	{
		return count($this->results);
	}

	public function getResultsData()
	{
		if ($this->resultsData === null)
		{
			$this->loadResultsData(false);
		}

		$output = [];
		foreach ($this->results AS $key => $null)
		{
			if (isset($this->resultsData[$key]))
			{
				$output[$key] = $this->resultsData[$key];
			}
		}

		return $output;
	}

	public function getResultsDataCallback(\Closure $closure)
	{
		if ($this->resultsData === null)
		{
			$this->loadResultsData(false);
		}

		$output = [];
		foreach ($this->results AS $key => $data)
		{
			if (isset($this->resultsData[$key]))
			{
				$type = $data[0];
				$id = $data[1];

				$value = $closure($this->resultsData[$key], $type, $id);
				if ($value)
				{
					$output[$key] = $value;
				}
			}
		}

		return $output;
	}

	public function getFirstResultData(&$type = null, &$id = null)
	{
		if ($this->resultsData === null)
		{
			$this->loadResultsData(false);
		}

		$first = reset($this->results);
		if ($first)
		{
			$key = key($this->results);
			if (isset($this->resultsData[$key]))
			{
				$type = $first[0];
				$id = $first[1];

				return $this->resultsData[$key];
			}
		}

		$type = null;
		$id = null;

		return null;
	}

	public function getLastResultData(&$type = null, &$id = null)
	{
		if ($this->resultsData === null)
		{
			$this->loadResultsData(false);
		}

		$first = end($this->results);
		if ($first)
		{
			$key = key($this->results);
			if (isset($this->resultsData[$key]))
			{
				$type = $first[0];
				$id = $first[1];

				return $this->resultsData[$key];
			}
		}

		$type = null;
		$id = null;

		return null;
	}

	protected function normalizeResultIds(array $results)
	{
		$output = [];
		foreach ($results AS $result)
		{
			if (is_array($result) && isset($result[0]))
			{
				$type = $result[0];
				$id = $result[1];
			}
			else if (is_array($result) && isset($result['content_type']))
			{
				$type = $result['content_type'];
				$id = $result['content_id'];
			}
			else
			{
				list($type, $id) = explode('-', $result);
			}

			$id = intval($id);
			$output["$type-$id"] = [$type, $id];
		}

		return $output;
	}

	public function sliceResults($start, $length, $limitToViewable = true)
	{
		if ($limitToViewable)
		{
			$this->limitToViewableResults();
		}

		$this->results = array_slice($this->results, $start, $length, true);

		return $this;
	}

	public function limitResults($maxResults, $limitToViewable = true)
	{
		return $this->sliceResults(0, $maxResults, $limitToViewable);
	}

	public function sliceResultsToPage($page, $perPage, $limitToViewable = true)
	{
		$page = max(1, intval($page));
		$perPage = max(1, intval($perPage));
		$offset = ($page - 1) * $perPage;

		$this->sliceResults($offset, $perPage, false);

		if ($limitToViewable)
		{
			$this->limitToViewableResults();
		}

		return $this;
	}

	public function limitToViewableResults()
	{
		$this->loadResultsData(true);

		return $this;
	}

	protected function loadResultsData($filterViewable)
	{
		$byType = [];
		foreach ($this->results AS $result)
		{
			$byType[$result[0]][$result[1]] = $result[1];
		}

		$typeResults = [];
		foreach ($byType AS $type => $ids)
		{
			$entities = $this->setInterface->getResultSetData($type, $ids, $filterViewable, $this->results);
			if ($entities instanceof \XF\Mvc\Entity\AbstractCollection)
			{
				$entities = $entities->toArray();
			}
			if ($entities)
			{
				$typeResults[$type] = $entities;
			}
		}

		$this->resultsData = [];

		foreach ($this->results AS $key => $output)
		{
			$type = $output[0];
			$id = $output[1];
			if (!isset($typeResults[$type][$id]))
			{
				unset($this->results[$key]);
			}
			else
			{
				$this->resultsData[$key] = $typeResults[$type][$id];
			}
		}
	}
}