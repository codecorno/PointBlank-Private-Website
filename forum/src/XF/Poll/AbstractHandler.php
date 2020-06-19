<?php

namespace XF\Poll;

use XF\Entity\Poll;
use XF\Mvc\Entity\Entity;

abstract class AbstractHandler
{
	protected $contentType;

	public function __construct($contentType)
	{
		$this->contentType = $contentType;
	}

	abstract public function canCreate(Entity $content, &$error = null);
	abstract public function canEdit(Entity $content, Poll $poll, &$error = null);
	abstract public function canAlwaysEditDetails(Entity $content, Poll $poll, &$error = null);
	abstract public function canDelete(Entity $content, Poll $poll, &$error = null);
	abstract public function canVote(Entity $content, Poll $poll, &$error = null);

	abstract public function getPollLink($action, Entity $content, array $extraParams = []);

	abstract public function finalizeCreation(Entity $content, Poll $poll);
	abstract public function finalizeDeletion(Entity $content, Poll $poll);

	public function canViewContent(Entity $content, &$error = null)
	{
		return $content->canView($error);
	}

	public function getEntityWith()
	{
		return [];
	}

	public function getContent($id)
	{
		return \XF::app()->findByContentType($this->contentType, $id, $this->getEntityWith());
	}

	public function getContentType()
	{
		return $this->contentType;
	}
}