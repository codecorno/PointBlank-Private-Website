<?php

namespace XF\Search\Data;

use XF\Mvc\Entity\Entity;
use XF\Search\MetadataStructure;

abstract class AbstractData
{
	protected $contentType;

	/**
	 * @var \XF\Search\Search
	 */
	protected $searcher;

	public function __construct($contentType, \XF\Search\Search $searcher)
	{
		$this->contentType = $contentType;
		$this->searcher = $searcher;
	}

	abstract public function getIndexData(Entity $entity);
	abstract public function setupMetadataStructure(MetadataStructure $structure);
	abstract public function getResultDate(Entity $entity);
	abstract public function getTemplateData(Entity $entity, array $options = []);

	public function getMetadataStructure()
	{
		$structure = new MetadataStructure();
		$this->setupMetadataStructure($structure);

		return $structure->getFields();
	}

	public function getEntityWith($forView = false)
	{
		return [];
	}

	public function getTemplateName()
	{
		return 'public:search_result_' . $this->contentType;
	}

	public function renderResult(Entity $entity, array $options = [])
	{
		$template = $this->getTemplateName();
		$data = $this->getTemplateData($entity, $options);

		return \XF::app()->templater()->renderTemplate($template, $data);
	}

	public function getSearchableContentTypes()
	{
		return [$this->contentType];
	}

	public function getSearchFormTab()
	{
		return null;
	}

	public function getSectionContext()
	{
		return null;
	}

	public function getTypeFormTemplate()
	{
		return 'public:search_form_' . $this->contentType;
	}

	public function getSearchFormData()
	{
		return [];
	}

	public function applyTypeConstraintsFromInput(\XF\Search\Query\Query $query, \XF\Http\Request $request, array &$urlConstraints)
	{
	}

	public function getGroupByType()
	{
		return null;
	}

	public function getTypeOrder($order)
	{
		return null;
	}

	/**
	 * This allows you to specify constraints to avoid including search results that will ultimately be filtered
	 * out due to permissions.In most cases, the query should not generally be modified. It is passed in to allow inspection.
	 *
	 * Note that your returned constraints may not be applied only to results of the relevant types. If possible, you
	 * should only return "none" constraints using metadata keys that are unique to the involved content types.
	 *
	 * $isOnlyType will be true when the search is specific to this type. This allows different constraints to be applied
	 * when searching within the type. For example, this could implicitly disable searching of a content type unless targeted.
	 *
	 * @param \XF\Search\Query\Query $query $query
	 * @param bool $isOnlyType Will be true if the search is specifically limited to this type.
	 *
	 * @return \XF\Search\Query\MetadataConstraint[] Only an array of metadata constraints may be returned.
	 */
	public function getTypePermissionConstraints(\XF\Search\Query\Query $query, $isOnlyType)
	{
		return [];
	}

	public function canUseInlineModeration(Entity $entity, &$error = null)
	{
		return false;
	}

	public function canViewContent(Entity $entity, &$error = null)
	{
		if (method_exists($entity, 'canView'))
		{
			return $entity->canView($error);
		}

		throw new \LogicException("Could not determine content viewability; please override");
	}

	public function canIncludeInResults(Entity $entity, array $resultIds)
	{
		return true;
	}

	public function getContent($id, $forView = false)
	{
		return \XF::app()->findByContentType($this->contentType, $id, $this->getEntityWith($forView));
	}

	public function getContentInRange($lastId, $amount, $forView = false)
	{
		$entityId = \XF::app()->getContentTypeFieldValue($this->contentType, 'entity');
		if (!$entityId)
		{
			throw new \LogicException("Content type {$this->contentType} must define an 'entity' value");
		}

		$em = \XF::em();
		$key = $em->getEntityStructure($entityId)->primaryKey;
		if (is_array($key))
		{
			if (count($key) > 1)
			{
				throw new \LogicException("Entity $entityId must only have a single primary key");
			}
			$key = reset($key);
		}

		$finder = $em->getFinder($entityId)->where($key, '>', $lastId)
			->order($key)
			->with($this->getEntityWith($forView));

		return $finder->fetch($amount);
	}

	public function getContentType()
	{
		return $this->contentType;
	}
}