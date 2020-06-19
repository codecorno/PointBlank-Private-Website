<?php

namespace XF\EditHistory;

use XF\Mvc\Entity\Entity;

abstract class AbstractHandler
{
	protected $contentType;

	public function __construct($contentType)
	{
		$this->contentType = $contentType;
	}

	abstract public function canViewHistory(Entity $content);
	abstract public function canRevertContent(Entity $content);

	abstract public function getContentTitle(Entity $content);
	abstract public function getContentText(Entity $content);
	abstract public function getContentLink(Entity $content);
	abstract public function getBreadcrumbs(Entity $content);

	abstract public function revertToVersion(Entity $content, \XF\Entity\EditHistory $history, \XF\Entity\EditHistory $previous = null);

	abstract public function getHtmlFormattedContent($text, Entity $content = null);

	public function getEditCount(Entity $content)
	{
		return $content->edit_count;
	}

	public function getSectionContext()
	{
		return '';
	}

	public function getEntityWith()
	{
		return [];
	}

	public function getContent($id)
	{
		return \XF::app()->findByContentType($this->contentType, $id, $this->getEntityWith());
	}
}