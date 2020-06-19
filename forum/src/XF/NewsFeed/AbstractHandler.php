<?php

namespace XF\NewsFeed;

use XF\Entity\NewsFeed;
use XF\Mvc\Entity\Entity;

class AbstractHandler
{
	protected $contentType;

	public function __construct($contentType)
	{
		$this->contentType = $contentType;
	}

	public function canViewContent(Entity $entity, &$error = null)
	{
		if (method_exists($entity, 'canView'))
		{
			return $entity->canView($error);
		}

		throw new \LogicException("Could not determine content viewability; please override");
	}

	public function canViewEntry(NewsFeed $newsFeed, Entity $content, &$error = null)
	{
		if ($newsFeed->action == 'reaction')
		{
			if (!isset($newsFeed->extra_data['reaction_id']))
			{
				throw new \LogicException("Reaction ID missing from news feed entry extra_data.");
			}

			$reactionId = $newsFeed->extra_data['reaction_id'];
			$reactionsCache = \XF::app()->container('reactions');

			if (!isset($reactionsCache[$reactionId]) || !$reactionsCache[$reactionId]['active'])
			{
				return false;
			}
		}
		return true;
	}

	public function contentIsVisible(Entity $entity, &$error = null)
	{
		if (method_exists($entity, 'isVisible'))
		{
			return $entity->isVisible($error);
		}

		if (\XF::$debugMode)
		{
			trigger_error("Could not determine content visibility; defaulted to true - please override", E_USER_WARNING);
		}

		return true;
	}

	public function isPublishable(Entity $entity, $action)
	{
		return true;
	}

	public function getTemplateName($action)
	{
		return 'public:news_feed_item_' . $this->contentType . '_' . $action;
	}

	public function getTemplateData($action, NewsFeed $newsFeed, Entity $content = null)
	{
		if (!$content)
		{
			$content = $newsFeed->Content;
		}

		return [
			'newsFeed' => $newsFeed,
			'user' => $newsFeed->User,
			'extra' => $newsFeed->extra_data,
			'content' => $content
		];
	}

	public function render(NewsFeed $newsFeed, Entity $content = null)
	{
		if (!$content)
		{
			$content = $newsFeed->Content;
			if (!$content)
			{
				return '';
			}
		}

		$action = $newsFeed->action;
		$template = $this->getTemplateName($action);
		$data = $this->getTemplateData($action, $newsFeed, $content);

		return \XF::app()->templater()->renderTemplate($template, $data);
	}

	public function getEntityWith()
	{
		return [];
	}

	public function getContent($id)
	{
		$content = \XF::app()->findByContentType($this->contentType, $id, $this->getEntityWith());
		return $this->addAttachmentsToContent($content);
	}

	public function getContentType()
	{
		return $this->contentType;
	}

	protected function addAttachmentsToContent($content)
	{
		return $content;
	}

	protected function addAttachments($content, $countKey = 'attach_count', $relationKey = 'Attachments')
	{
		/** @var \XF\Repository\Attachment $attachmentRepo */
		$attachmentRepo = \XF::repository('XF:Attachment');
		return $attachmentRepo->addAttachmentsToContent($content, $this->contentType, $countKey, $relationKey);
	}
}
