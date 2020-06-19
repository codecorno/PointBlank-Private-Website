<?php

namespace XF\Search;

use XF\Mvc\Entity\Entity;
use XF\Search\Source\AbstractSource;

class Search implements \XF\ResultSetInterface
{
	/**
	 * @var AbstractSource
	 */
	protected $source;

	protected $types;
	protected $handlers = [];

	public function __construct(AbstractSource $source, array $types)
	{
		$this->source = $source;
		$this->types = $types;
	}

	public function index($contentType, $entity, $deleteIfNeeded = true)
	{
		$handler = $this->handler($contentType);

		if (!$entity instanceof Entity)
		{
			$entity = $handler->getContent(intval($entity));
			if (!$entity)
			{
				return false;
			}
		}

		$record = $handler->getIndexData($entity);
		if ($record)
		{
			$this->source->index($record);
			return true;
		}
		else
		{
			if ($deleteIfNeeded)
			{
				$this->delete($contentType, $entity);
			}
			return false;
		}
	}

	public function indexEntities($contentType, $entities)
	{
		$this->enableBulkIndexing();

		foreach ($entities AS $entity)
		{
			$this->index($contentType, $entity);
		}

		$this->disableBulkIndexing();
	}

	public function indexByIds($contentType, array $contentIds)
	{
		if (!$contentIds)
		{
			return;
		}

		$entities = $this->handler($contentType)->getContent($contentIds);
		$this->indexEntities($contentType, $entities);
	}

	public function indexRange($contentType, $lastId, $amount)
	{
		$handler = $this->handler($contentType);
		$entities = $handler->getContentInRange($lastId, $amount);
		if (!$entities->count())
		{
			return false;
		}

		$this->indexEntities($contentType, $entities);

		$keys = $entities->keys();
		return $keys ? max($keys) : false;
	}

	public function enableBulkIndexing()
	{
		$this->source->enableBulkIndexing();
	}

	public function disableBulkIndexing()
	{
		$this->source->disableBulkIndexing();
	}

	public function delete($contentType, $del)
	{
		if ($del instanceof Entity)
		{
			$del = $del->getIdentifierValues();
			if (!$del || count($del) != 1)
			{
				throw new \InvalidArgumentException("Entity does not have an ID or does not have a simple key");
			}
			$del = intval(reset($del));
		}

		if (!is_int($del) && !is_array($del))
		{
			throw new \InvalidArgumentException("IDs to delete must be an array or an integer");
		}
		if (!$del)
		{
			return;
		}

		$this->source->delete($contentType, $del);
	}

	public function truncate($type = null)
	{
		return $this->source->truncate($type);
	}

	public function reassignContent($oldUserId, $newUserId)
	{
		$this->source->reassignContent($oldUserId, $newUserId);
	}

	/**
	 * @return \XF\Search\Query\Query
	 */
	public function getQuery()
	{
		$extendClass = \XF::extendClass('XF\Search\Query\Query');
		return new $extendClass($this);
	}

	public function getParsedKeywords($keywords, &$error = null, &$warning = null)
	{
		return $this->source->parseKeywords($keywords, $error, $warning);
	}

	public function isRelevanceSupported()
	{
		return $this->source->isRelevanceSupported();
	}

	public function search(Query\Query $query, $maxResults = null, $applyVisitorPermissions = true)
	{
		$maxResults = intval($maxResults);
		if ($maxResults <= 0)
		{
			$maxResults = max(\XF::options()->maximumSearchResults, 20);
		}

		$this->applyPermissionConstraints($query);

		$results = $this->source->search($query, $maxResults);

		$resultSet = $this->getResultSet($results)->limitResults($maxResults, $applyVisitorPermissions);
		return $resultSet->getResults();
	}

	protected function applyPermissionConstraints(Query\Query $query)
	{
		$this->applyGlobalPermissionConstraints($query);

		$handler = $query->getHandler();
		if ($handler)
		{
			// we're already restricted to the correct content types, so we can skip the permission constraint approach
			foreach ($handler->getTypePermissionConstraints($query, true) AS $constraint)
			{
				$query->withMetadata($constraint);
			}
		}
		else
		{
			foreach ($this->getValidHandlers() AS $handler)
			{
				$query->withPermissionConstraints(
					$handler->getSearchableContentTypes(),
					$handler->getTypePermissionConstraints($query, false)
				);
			}
		}
	}

	protected function applyGlobalPermissionConstraints(Query\Query $query)
	{
		if (\XF::visitor()->is_moderator)
		{
			$query->allowHidden();
		}
	}

	public function getResultSet(array $results)
	{
		return new \XF\ResultSet($this, $results);
	}

	public function getResultSetData($type, array $ids, $filterViewable = true, array $results = null)
	{
		if (!$this->isValidContentType($type))
		{
			return [];
		}

		$handler = $this->handler($type);
		$entities = $handler->getContent($ids, true);

		if ($filterViewable)
		{
			$entities = $entities->filter(function($entity) use ($handler)
			{
				return $handler->canViewContent($entity);
			});
		}

		if (is_array($results))
		{
			$entities = $entities->filter(function($entity) use ($handler, $results)
			{
				return $handler->canIncludeInResults($entity, $results);
			});
		}

		return $entities;
	}

	/**
	 * @param \XF\ResultSet $resultSet
	 * @param array $options
	 *
	 * @return RenderWrapper[]
	 */
	public function wrapResultsForRender(\XF\ResultSet $resultSet, array $options = [])
	{
		return $resultSet->getResultsDataCallback(function($result, $type, $id) use ($options)
		{
			return new RenderWrapper($this->handler($type), $result, $options);
		});
	}

	public function isValidContentType($type)
	{
		return isset($this->types[$type]) && class_exists($this->types[$type]);
	}

	public function getAvailableTypes()
	{
		return array_keys($this->types);
	}

	/**
	 * @param string $type
	 *
	 * @return \XF\Search\Data\AbstractData
	 */
	public function handler($type)
	{
		if (isset($this->handlers[$type]))
		{
			return $this->handlers[$type];
		}

		if (!isset($this->types[$type]))
		{
			throw new \InvalidArgumentException("Unknown search handler type '$type'");
		}

		$class = $this->types[$type];
		if (class_exists($class))
		{
			$class = \XF::extendClass($class);
		}

		$this->handlers[$type] = new $class($type, $this);
		return $this->handlers[$type];
	}

	/**
	 * @return \XF\Search\Data\AbstractData[]
	 */
	public function getValidHandlers()
	{
		$handlers = [];
		foreach ($this->getAvailableTypes() AS $type)
		{
			if ($this->isValidContentType($type))
			{
				$handlers[$type] = $this->handler($type);
			}
		}

		return $handlers;
	}

	public function getSearchTypeTabs()
	{
		$tabs = [];
		foreach ($this->getValidHandlers() AS $type => $handler)
		{
			$tab = $handler->getSearchFormTab();
			if ($tab)
			{
				if (!isset($tab['order']))
				{
					$tab['order'] = 10;
				}
				$tabs[$type] = $tab;
			}
		}

		$tabs = \XF\Util\Arr::columnSort($tabs, 'order');

		return $tabs;
	}
}