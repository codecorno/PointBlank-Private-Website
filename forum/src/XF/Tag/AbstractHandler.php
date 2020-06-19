<?php

namespace XF\Tag;

use XF\Mvc\Entity\Entity;

abstract class AbstractHandler
{
	protected $contentType;

	public function __construct($contentType)
	{
		$this->contentType = $contentType;
	}

	abstract public function getPermissionsFromContext(Entity $entity);
	abstract public function getContentDate(Entity $entity);
	abstract public function getContentVisibility(Entity $entity);
	abstract public function getTemplateData(Entity $entity, array $options = []);

	public function updateContentTagCache(Entity $content, array $cache)
	{
		if (!isset($content->tags))
		{
			throw new \LogicException("No 'tags' cache found; please override");
		}

		$content->tags = $cache;
		$content->save();
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

	public function canViewContent(Entity $entity, &$error = null)
	{
		if (method_exists($entity, 'canView'))
		{
			return $entity->canView($error);
		}

		throw new \LogicException("Could not determine content viewability; please override");
	}

	public function getContent($id, $forView = false)
	{
		return \XF::app()->findByContentType($this->contentType, $id, $this->getEntityWith($forView));
	}

	public function canUseInlineModeration(Entity $entity, &$error = null)
	{
		return false;
	}

	public function getContentType()
	{
		return $this->contentType;
	}
}