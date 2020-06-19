<?php

namespace XF\Bookmark;

use XF\Entity\BookmarkItem;
use XF\Entity\BookmarkTrait;
use XF\Mvc\Entity\Entity;

abstract class AbstractHandler
{
	protected $contentType;

	public function __construct($contentType)
	{
		$this->contentType = $contentType;
	}

	abstract public function getContentTitle(Entity $content);
	abstract public function getContentRoute(Entity $content);

	public function getContentUser(Entity $content)
	{
		if (isset($content->User))
		{
			return $content->User;
		}

		return null;
	}

	public function getContentLink(Entity $content)
	{
		return \XF::app()->router('public')->buildLink('canonical:' . $this->getContentRoute($content), $content);
	}

	public function getEditLink(Entity $content)
	{
		return \XF::app()->router('public')->buildLink($this->getContentRoute($content) . '/bookmark', $content);
	}

	public function getDeleteLink(Entity $content)
	{
		return \XF::app()->router('public')->buildLink($this->getContentRoute($content) . '/bookmark', $content, ['delete' => 1]);
	}

	public function canViewContent(Entity $content, &$error = null)
	{
		if (method_exists($content, 'canView'))
		{
			return $content->canView($error);
		}

		throw new \LogicException("Could not determine content viewability; please override");
	}

	protected function getDefaultTemplateData(BookmarkItem $bookmark, Entity $content = null)
	{
		if (!$content)
		{
			$content = $bookmark->Content;
		}

		return [
			'bookmark' => $bookmark,
			'user' => $bookmark->User,
			'content' => $content
		];
	}

	/**
	 * @return null|string
	 */
	public function getCustomIconTemplateName()
	{
		return null;
	}

	public function getCustomIconTemplateData(BookmarkItem $bookmark, Entity $content = null)
	{
		return $this->getDefaultTemplateData($bookmark, $content);
	}

	public function renderCustomIcon(BookmarkItem $bookmark, Entity $content = null)
	{
		if (!$this->getCustomIconTemplateName())
		{
			return '';
		}

		if (!$content)
		{
			$content = $bookmark->Content;
			if (!$content)
			{
				return '';
			}
		}

		$template = $this->getCustomIconTemplateName();
		$data = $this->getCustomIconTemplateData($bookmark, $content);

		return \XF::app()->templater()->renderTemplate($template, $data);
	}

	/**
	 * @return string
	 */
	public function getItemTemplateName()
	{
		return 'public:bookmark_item_' . $this->contentType;
	}

	/**
	 * @param BookmarkItem $bookmark
	 * @param Entity|BookmarkTrait|null $content
	 *
	 * @return array
	 */
	public function getItemTemplateData(BookmarkItem $bookmark, Entity $content = null)
	{
		return $this->getDefaultTemplateData($bookmark, $content);
	}

	/**
	 * @param BookmarkItem $bookmark
	 * @param Entity|BookmarkTrait|null $content
	 *
	 * @return string
	 */
	public function renderMessageFallback(BookmarkItem $bookmark, Entity $content = null)
	{
		if (!$content)
		{
			$content = $bookmark->Content;
			if (!$content)
			{
				return '';
			}
		}

		$template = $this->getItemTemplateName();
		$data = $this->getItemTemplateData($bookmark, $content);

		return \XF::app()->templater()->renderTemplate($template, $data);
	}

	public function getEntityWith()
	{
		return [];
	}

	/**
	 * @param $id
	 * @return BookmarkTrait|Entity|null
	 */
	public function getContent($id)
	{
		return \XF::app()->findByContentType($this->contentType, $id, $this->getEntityWith());
	}

	public function getContentType()
	{
		return $this->contentType;
	}
}