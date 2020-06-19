<?php

namespace XF\Bookmark;

use XF\Mvc\Entity\Entity;

class Post extends AbstractHandler
{
	/**
	 * @param Entity|\XF\Entity\Post $content
	 *
	 * @return \XF\Phrase
	 */
	public function getContentTitle(Entity $content)
	{
		if ($content->isFirstPost())
		{
			return \XF::phrase('thread_x', [
				'title' => $content->Thread->title
			]);
		}
		else
		{
			return \XF::phrase('post_in_thread_x', [
				'title' => $content->Thread->title
			]);
		}
	}

	/**
	 * @param Entity|\XF\Entity\Post $content
	 *
	 * @return string
	 */
	public function getContentRoute(Entity $content)
	{
		return 'posts';
	}

	/**
	 * @param Entity|\XF\Entity\Post $content
	 *
	 * @return null|\XF\Entity\User
	 */
	public function getContentUser(Entity $content)
	{
		if ($content->isFirstPost())
		{
			return $content->Thread->User;
		}
		else
		{
			return $content->User;
		}
	}

	/**
	 * @param Entity|\XF\Entity\Post $content
	 *
	 * @return string
	 */
	public function getContentLink(Entity $content)
	{
		if ($content->isFirstPost())
		{
			return \XF::app()->router('public')->buildLink('canonical:threads', $content->Thread);
		}
		else
		{
			return \XF::app()->router('public')->buildLink('canonical:posts', $content);
		}
	}

	public function getEntityWith()
	{
		$visitor = \XF::visitor();

		return ['Thread', 'Thread.Forum', 'Thread.Forum.Node.Permissions|' . $visitor->permission_combination_id];
	}
}