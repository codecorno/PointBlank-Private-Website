<?php

namespace XF\Search\Query;

use XF\Http\Request;
use XF\Search\Data\AbstractData;

class Query
{
	/** @var \XF\Search\Search */
	protected $search;

	/**
	 * @var \XF\Search\Data\AbstractData|null
	 */
	protected $handler = null;

	protected $handlerType = null;
	protected $types = null;

	protected $keywords = '';
	protected $parsedKeywords = null;
	protected $titleOnly = false;

	protected $allowHidden = false;

	/**
	 * @var int[]
	 */
	protected $userIds = [];

	protected $maxDate = 0;
	protected $minDate = 0;

	/**
	 * @var MetadataConstraint[]
	 */
	protected $metadataConstraints = [];

	/**
	 * @var array
	 */
	protected $permissionConstraints = [];

	/**
	 * @var SqlConstraint[]
	 */
	protected $sqlConstraints = [];

	protected $groupByType = null;

	/**
	 * @var string|SqlOrder
	 */
	protected $order = 'date';
	protected $orderName = 'date';

	protected $errors = [];
	protected $warnings = [];

	public function __construct(\XF\Search\Search $search)
	{
		$this->search = $search;

		$this->orderedBy($search->isRelevanceSupported() ? 'relevance' : 'date');
	}

	public function forTypeHandler(AbstractData $handler, Request $request, array &$urlConstraints = [])
	{
		$this->handler = $handler;
		$this->handlerType = $handler->getContentType();
		$this->types = $handler->getSearchableContentTypes();

		$handler->applyTypeConstraintsFromInput($this, $request, $urlConstraints);

		return $this;
	}

	public function getHandlerType()
	{
		return $this->handlerType;
	}

	public function getHandler()
	{
		return $this->handler;
	}

	public function inType($type)
	{
		return $this->inTypes(is_array($type) ? $type : [$type]);
	}

	public function inTypes(array $types)
	{
		$this->types = $types;

		return $this;
	}

	public function getTypes()
	{
		return $this->types;
	}

	public function withKeywords($keywords, $titleOnly = false)
	{
		$this->keywords = trim($keywords);
		$this->parsedKeywords = $this->search->getParsedKeywords($this->keywords, $error, $warning);
		$this->titleOnly = (bool)$titleOnly;

		if ($error)
		{
			$this->error('keywords', $error);
		}
		if ($warning)
		{
			$this->warning('keywords', $warning);
		}

		return $this;
	}

	public function getKeywords()
	{
		return $this->keywords;
	}

	public function getParsedKeywords()
	{
		return $this->parsedKeywords;
	}

	public function inTitleOnly($titleOnly = true)
	{
		$this->titleOnly = (bool)$titleOnly;

		return $this;
	}

	public function getTitleOnly()
	{
		return $this->titleOnly;
	}

	public function allowHidden($allow = true)
	{
		$this->allowHidden = (bool)$allow;

		return $this;
	}

	public function getAllowHidden()
	{
		return $this->allowHidden;
	}

	public function byUserId($userId)
	{
		return $this->byUserIds([$userId]);
	}

	public function byUserIds(array $userIds)
	{
		$idsFiltered = [];
		foreach ($userIds AS $id)
		{
			$id = intval($id);
			if ($id > 0)
			{
				$idsFiltered[] = $id;
			}
		}

		if (!$idsFiltered)
		{
			throw new \InvalidArgumentException("No valid users to limit search to");
		}

		$this->userIds = $idsFiltered;

		return $this;
	}

	public function getUserIds()
	{
		return $this->userIds;
	}

	public function withinDateRange($min, $max)
	{
		$min = intval($min);
		$max = intval($max);

		if ($max > $min)
		{
			throw new \InvalidArgumentException("Max date must be greater than min");
		}

		$this->minDate = $min;
		$this->maxDate = $max;

		return $this;
	}

	public function newerThan($min)
	{
		$this->minDate = max(0, intval($min));

		return $this;
	}

	public function olderThan($max)
	{
		$this->maxDate = max(0, intval($max));

		return $this;
	}

	public function getMinDate()
	{
		return $this->minDate;
	}

	public function getMaxDate()
	{
		return $this->maxDate;
	}

	public function withTags($tags, $match = 'all')
	{
		if (!is_array($tags))
		{
			$tags = [$tags];
		}
		$tags = array_map('intval', $tags);
		$tags = array_unique($tags);
		if ($tags)
		{
			$this->withMetadata('tag', $tags, $match);
		}

		return $this;
	}

	public function withMetadata($name, $value = null, $match = 'any')
	{
		if ($name instanceof MetadataConstraint)
		{
			$this->metadataConstraints[] = $name;
		}
		else
		{
			$this->metadataConstraints[] = new MetadataConstraint($name, $value, $match);
		}

		return $this;
	}

	/**
	 * @return MetadataConstraint[]
	 */
	public function getMetadataConstraints()
	{
		return $this->metadataConstraints;
	}

	/**
	 * @param array $contentTypes
	 * @param MetadataConstraint[] $constraints
	 *
	 * @return $this
	 */
	public function withPermissionConstraints(array $contentTypes, array $constraints)
	{
		if ($contentTypes && $constraints)
		{
			$this->permissionConstraints[implode('-', $contentTypes)] = [
				'types' => $contentTypes,
				'constraints' => $constraints
			];
		}

		return $this;
	}

	public function getPermissionConstraints()
	{
		return $this->permissionConstraints;
	}

	public function withSql(SqlConstraint $constraint)
	{
		$this->sqlConstraints[] = $constraint;

		return $this;
	}

	/**
	 * @return SqlConstraint[]
	 */
	public function getSqlConstraints()
	{
		return $this->sqlConstraints;
	}

	public function hasQueryConstraints()
	{
		return ($this->sqlConstraints || $this->order instanceof SqlOrder);
	}

	public function withGroupedResults()
	{
		if ($this->handler)
		{
			$type = $this->handler->getGroupByType();
			if ($type)
			{
				$this->groupByType = $type;
			}
		}
	}

	public function getGroupByType()
	{
		return $this->groupByType;
	}

	public function orderedBy($order)
	{
		if (is_string($order))
		{
			$this->orderName = $order;
		}

		if (is_string($order) && $this->handler)
		{
			$newOrder = $this->handler->getTypeOrder($order);
			if ($newOrder)
			{
				$order = $newOrder;
			}
		}

		$this->order = $order;

		return $this;
	}

	public function getOrder()
	{
		return $this->order;
	}

	public function getOrderName()
	{
		return $this->orderName;
	}

	public function error($key, $message)
	{
		$this->errors[$key] = $message;

		return $this;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function warning($key, $message)
	{
		$this->warnings[$key] = $message;

		return $this;
	}

	public function getWarnings()
	{
		return $this->warnings;
	}

	public function getUniqueQueryHash()
	{
		return md5(serialize($this->getUniqueQueryComponents()));
	}

	public function getUniqueQueryComponents()
	{
		return [
			'handlerType' => $this->handlerType,
			'types' => $this->types,
			'keywords' => $this->keywords,
			'titleOnly' => $this->titleOnly,
			'userIds' => $this->userIds,
			'maxDate' => $this->maxDate,
			'minDate' => $this->minDate,
			'metadataConstraints' => $this->metadataConstraints,
			'sqlConstraints' => $this->sqlConstraints,
			'groupByType' => $this->groupByType,
			'order' => $this->order
		];
	}
}