<?php

namespace XF\Attachment;

use XF\Entity\Attachment;
use XF\Mvc\Entity\Entity;

class Post extends AbstractHandler
{
	public function getContainerWith()
	{
		$visitor = \XF::visitor();

		return ['Thread', 'Thread.Forum', 'Thread.Forum.Node.Permissions|' . $visitor->permission_combination_id];
	}

	public function canView(Attachment $attachment, Entity $container, &$error = null)
	{
		/** @var \XF\Entity\Post $container */
		if (!$container->canView())
		{
			return false;
		}

		/** @var \XF\Entity\Thread $thread */
		$thread = $container->Thread;
		return $thread->canViewAttachments($error);
	}

	public function canManageAttachments(array $context, &$error = null)
	{
		$forum = $this->getForumFromContext($context);
		return ($forum && $forum->canUploadAndManageAttachments());
	}

	public function onAttachmentDelete(Attachment $attachment, Entity $container = null)
	{
		if (!$container)
		{
			return;
		}

		/** @var \XF\Entity\Post $container */
		$container->attach_count--;
		$container->save();
	}

	public function getConstraints(array $context)
	{
		/** @var \XF\Repository\Attachment $attachRepo */
		$attachRepo = \XF::repository('XF:Attachment');

		$constraints = $attachRepo->getDefaultAttachmentConstraints();

		$forum = $this->getForumFromContext($context);
		if ($forum && $forum->canUploadVideos())
		{
			$constraints = $attachRepo->applyVideoAttachmentConstraints($constraints);
		}

		return $constraints;
	}

	public function getContainerIdFromContext(array $context)
	{
		return isset($context['post_id']) ? intval($context['post_id']) : null;
	}

	public function getContainerLink(Entity $container, array $extraParams = [])
	{
		return \XF::app()->router('public')->buildLink('posts', $container, $extraParams);
	}

	public function getContext(Entity $entity = null, array $extraContext = [])
	{
		if ($entity instanceof \XF\Entity\Post)
		{
			$extraContext['post_id'] = $entity->post_id;
		}
		else if ($entity instanceof \XF\Entity\Thread)
		{
			$extraContext['thread_id'] = $entity->thread_id;
		}
		else if ($entity instanceof \XF\Entity\Forum)
		{
			$extraContext['node_id'] = $entity->node_id;
		}
		else
		{
			throw new \InvalidArgumentException("Entity must be post, thread or forum");
		}

		return $extraContext;
	}

	protected function getForumFromContext(array $context)
	{
		$em = \XF::em();
		$forum = null;

		if (!empty($context['post_id']))
		{
			/** @var \XF\Entity\Post $post */
			$post = $em->find('XF:Post', intval($context['post_id']), ['Thread', 'Thread.Forum']);
			if (!$post || !$post->canView() || !$post->canEdit())
			{
				return null;
			}

			$forum = $post->Thread->Forum;
		}
		else if (!empty($context['thread_id']))
		{
			/** @var \XF\Entity\Thread $thread */
			$thread = $em->find('XF:Thread', intval($context['thread_id']), ['Forum']);
			if (!$thread || !$thread->canView())
			{
				return null;
			}

			$forum = $thread->Forum;
		}
		else if (!empty($context['node_id']))
		{
			/** @var \XF\Entity\Forum $forum */
			$forum = $em->find('XF:Forum', intval($context['node_id']));
			if (!$forum || !$forum->canView())
			{
				return null;
			}
		}
		else
		{
			return null;
		}

		return $forum;
	}
}