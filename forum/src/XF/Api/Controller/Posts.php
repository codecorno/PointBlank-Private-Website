<?php

namespace XF\Api\Controller;

use XF\Mvc\Entity\Entity;
use XF\Mvc\ParameterBag;

/**
 * @api-group Posts
 */
class Posts extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertApiScopeByRequestMethod('thread');
	}

	/**
	 * @api-desc Adds a new reply to a thread.
	 *
	 * @api-in int $thread_id <req> ID of the thread to reply to.
	 * @api-in str $message <req>
	 * @api-in str $attachment_key API attachment key to upload files. Attachment key context type must be post with context[thread_id] set to this thread ID.
	 */
	public function actionPost(ParameterBag $params)
	{
		$this->assertRequiredApiInput(['thread_id', 'message']);

		$threadId = $this->filter('thread_id', 'uint');

		/** @var \XF\Entity\Thread $thread */
		$thread = $this->assertViewableApiRecord('XF:Thread', $threadId);

		if (\XF::isApiCheckingPermissions() && !$thread->canReply($error))
		{
			return $this->noPermission($error);
		}

		$replier = $this->setupThreadReply($thread);

		if (\XF::isApiCheckingPermissions())
		{
			$replier->checkForSpam();
		}

		if (!$replier->validate($errors))
		{
			return $this->error($errors);
		}

		/** @var \XF\Entity\Post $post */
		$post = $replier->save();
		$this->finalizeThreadReply($replier);

		return $this->apiSuccess([
			'post' => $post->toApiResult(Entity::VERBOSITY_VERBOSE)
		]);
	}

	/**
	 * @param \XF\Entity\Thread $thread
	 *
	 * @return \XF\Service\Thread\Replier
	 */
	protected function setupThreadReply(\XF\Entity\Thread $thread)
	{
		/** @var \XF\Service\Thread\Replier $replier */
		$replier = $this->service('XF:Thread\Replier', $thread);

		$message = $this->filter('message', 'str');
		$replier->setMessage($message);

		if (\XF::isApiBypassingPermissions() || $thread->Forum->canUploadAndManageAttachments())
		{
			$attachmentKey = $this->filter('attachment_key', 'str');
			$hash = $this->getAttachmentTempHashFromKey($attachmentKey, 'post', ['thread_id' => $thread->thread_id]);
			$replier->setAttachmentHash($hash);
		}

		return $replier;
	}

	protected function finalizeThreadReply(\XF\Service\Thread\Replier $replier)
	{
		$replier->sendNotifications();

		$thread = $replier->getThread();
		$post = $replier->getPost();
		$visitor = \XF::visitor();

		if ($thread->canWatch())
		{
			/** @var \XF\Repository\ThreadWatch $threadWatchRepo */
			$threadWatchRepo = $this->repository('XF:ThreadWatch');

			$watch = $this->filter('watch_thread', '?bool');
			if ($watch)
			{
				$state = $this->filter('watch_thread_email', 'bool') ? 'watch_email' : 'watch_no_email';
				$threadWatchRepo->setWatchState($thread, $visitor, $state);
			}
			else if ($watch === null)
			{
				// use user preferences
				$threadWatchRepo->autoWatchThread($thread, $visitor, false);
			}
		}

		if ($visitor->user_id)
		{
			$readDate = $thread->getVisitorReadDate();
			if ($readDate && $readDate >= $thread->getPreviousValue('last_post_date'))
			{
				$this->getThreadRepo()->markThreadReadByVisitor($thread, $post->post_date);
			}
		}
	}

	/**
	 * @return \XF\Repository\Thread
	 */
	protected function getThreadRepo()
	{
		return $this->repository('XF:Thread');
	}

	/**
	 * @return \XF\Repository\Post
	 */
	protected function getPostRepo()
	{
		return $this->repository('XF:Post');
	}
}