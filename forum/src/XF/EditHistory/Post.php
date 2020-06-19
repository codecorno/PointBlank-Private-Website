<?php

namespace XF\EditHistory;

use XF\Mvc\Entity\Entity;


class Post extends AbstractHandler
{
	/**
	 * @param \XF\Entity\Post $content
	 */
	public function canViewHistory(Entity $content)
	{
		return ($content->canView() && $content->canViewHistory());
	}

	/**
	 * @param \XF\Entity\Post $content
	 */
	public function canRevertContent(Entity $content)
	{
		return $content->canEdit();
	}

	/**
	 * @param \XF\Entity\Post $content
	 */
	public function getContentTitle(Entity $content)
	{
		return \XF::phrase('post_in_thread_x', ['title' => $content->Thread->title]);
	}

	/**
	 * @param \XF\Entity\Post $content
	 */
	public function getContentText(Entity $content)
	{
		return $content->message;
	}

	public function getContentLink(Entity $content)
	{
		return \XF::app()->router('public')->buildLink('posts', $content);
	}

	/**
	 * @param \XF\Entity\Post $content
	 */
	public function getBreadcrumbs(Entity $content)
	{
		/** @var \XF\Mvc\Router $router */
		$router = \XF::app()->container('router');

		$breadcrumbs = $content->Thread->Forum->getBreadcrumbs();
		$breadcrumbs[] = [
			'value' => $content->Thread->title,
			'href' => $router->buildLink('threads', $content->Thread)
		];
		return $breadcrumbs;
	}

	/**
	 * @param \XF\Entity\Post $content
	 */
	public function revertToVersion(Entity $content, \XF\Entity\EditHistory $history, \XF\Entity\EditHistory $previous = null)
	{
		/** @var \XF\Service\Post\Editor $editor */
		$editor = \XF::app()->service('XF:Post\Editor', $content);

		$editor->logEdit(false);
		$editor->setMessage($history->old_text);

		if (!$previous || $previous->edit_user_id != $content->user_id)
		{
			$content->last_edit_date = 0;
		}
		else if ($previous && $previous->edit_user_id == $content->user_id)
		{
			$content->last_edit_date = $previous->edit_date;
			$content->last_edit_user_id = $previous->edit_user_id;
		}

		return $editor->save();
	}

	public function getHtmlFormattedContent($text, Entity $content = null)
	{
		return \XF::app()->templater()->func('bb_code', [$text, 'post', $content]);
	}

	public function getSectionContext()
	{
		return 'forums';
	}

	public function getEntityWith()
	{
		$visitor = \XF::visitor();
		return ['Thread', 'Thread.Forum', 'Thread.Forum.Node.Permissions|' . $visitor->permission_combination_id];
	}
}