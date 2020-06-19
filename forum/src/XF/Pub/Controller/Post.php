<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class Post extends AbstractController
{
	public function actionIndex(ParameterBag $params)
	{
		$post = $this->assertViewablePost($params->post_id);

		return $this->redirectPermanently($this->plugin('XF:Thread')->getPostLink($post));
	}

	public function actionShow(ParameterBag $params)
	{
		$post = $this->assertViewablePost($params->post_id);

		$viewParams = [
			'post' => $post,
			'thread' => $post->Thread,
			'forum' => $post->Thread->Forum,
			'canInlineMod' => $post->canUseInlineModeration()
		];
		return $this->view('XF:Post\Show', 'post', $viewParams);
	}

	/**
	 * @param \XF\Entity\Post $post
	 *
	 * @return \XF\Service\Post\Editor
	 */
	protected function setupPostEdit(\XF\Entity\Post $post)
	{
		$message = $this->plugin('XF:Editor')->fromInput('message');

		/** @var \XF\Service\Post\Editor $editor */
		$editor = $this->service('XF:Post\Editor', $post);
		if ($post->canEditSilently())
		{
			$silentEdit = $this->filter('silent', 'bool');
			if ($silentEdit)
			{
				$editor->logEdit(false);
				if ($this->filter('clear_edit', 'bool'))
				{
					$post->last_edit_date = 0;
				}
			}
		}
		$editor->setMessage($message);

		$forum = $post->Thread->Forum;
		if ($forum->canUploadAndManageAttachments())
		{
			$editor->setAttachmentHash($this->filter('attachment_hash', 'str'));
		}

		if ($this->filter('author_alert', 'bool') && $post->canSendModeratorActionAlert())
		{
			$editor->setSendAlert(true, $this->filter('author_alert_reason', 'str'));
		}

		return $editor;
	}

	/**
	 * @param \XF\Entity\Thread $thread
	 * @param array $threadChanges Returns a list of whether certain important thread fields are changed
	 *
	 * @return \XF\Service\Thread\Editor
	 */
	protected function setupFirstPostThreadEdit(\XF\Entity\Thread $thread, &$threadChanges)
	{
		/** @var \XF\Service\Thread\Editor $threadEditor */
		$threadEditor = $this->service('XF:Thread\Editor', $thread);

		$prefixId = $this->filter('prefix_id', 'uint');
		if ($prefixId != $thread->prefix_id && !$thread->Forum->isPrefixUsable($prefixId))
		{
			$prefixId = 0; // not usable, just blank it out
		}
		$threadEditor->setPrefix($prefixId);

		$threadEditor->setTitle($this->filter('title', 'str'));

		$customFields = $this->filter('custom_fields', 'array');
		$threadEditor->setCustomFields($customFields);

		$threadChanges = [
			'title' => $thread->isChanged(['title', 'prefix_id']),
			'customFields' => $thread->isChanged('custom_fields')
		];

		return $threadEditor;
	}

	protected function finalizePostEdit(\XF\Service\Post\Editor $editor, \XF\Service\Thread\Editor $threadEditor = null)
	{

	}

	public function actionEdit(ParameterBag $params)
	{
		$post = $this->assertViewablePost($params->post_id, ['Thread.Prefix']);
		if (!$post->canEdit($error))
		{
			return $this->noPermission($error);
		}

		$thread = $post->Thread;

		if ($this->isPost())
		{
			$editor = $this->setupPostEdit($post);
			$editor->checkForSpam();

			if ($post->isFirstPost() && $thread->canEdit())
			{
				$threadEditor = $this->setupFirstPostThreadEdit($thread, $threadChanges);
				$editor->setThreadEditor($threadEditor);
			}
			else
			{
				$threadEditor = null;
				$threadChanges = [];
			}

			if (!$editor->validate($errors))
			{
				return $this->error($errors);
			}

			$editor->save();

			$this->finalizePostEdit($editor, $threadEditor);

			if ($this->filter('_xfWithData', 'bool') && $this->filter('_xfInlineEdit', 'bool'))
			{
				/** @var \XF\Repository\Attachment $attachmentRepo */
				$attachmentRepo = $this->repository('XF:Attachment');
				$attachmentRepo->addAttachmentsToContent([$post->post_id => $post], 'post');

				$viewParams = [
					'post' => $post,
					'thread' => $thread
				];
				$reply = $this->view('XF:Post\EditNewPost', 'post_edit_new_post', $viewParams);
				$reply->setJsonParams([
					'message' => \XF::phrase('your_changes_have_been_saved'),
					'threadChanges' => $threadChanges
				]);
				return $reply;
			}
			else
			{
				return $this->redirect($this->buildLink('posts', $post));
			}
		}
		else
		{
			/** @var \XF\Entity\Forum $forum */
			$forum = $post->Thread->Forum;
			if ($forum->canUploadAndManageAttachments())
			{
				/** @var \XF\Repository\Attachment $attachmentRepo */
				$attachmentRepo = $this->repository('XF:Attachment');
				$attachmentData = $attachmentRepo->getEditorData('post', $post);
			}
			else
			{
				$attachmentData = null;
			}

			$prefix = $thread->Prefix;
			$prefixes = $forum->getUsablePrefixes($prefix);

			$viewParams = [
				'post' => $post,
				'thread' => $thread,
				'forum' => $forum,
				'prefixes' => $prefixes,
				'attachmentData' => $attachmentData,
				'quickEdit' => $this->filter('_xfWithData', 'bool')
			];
			return $this->view('XF:Post\Edit', 'post_edit', $viewParams);
		}
	}

	public function actionPreview(ParameterBag $params)
	{
		$this->assertPostOnly();

		$post = $this->assertViewablePost($params->post_id);
		if (!$post->canEdit($error))
		{
			return $this->noPermission($error);
		}

		$thread = $post->Thread;

		$editor = $this->setupPostEdit($post);

		if (!$editor->validate($errors))
		{
			return $this->error($errors);
		}

		$attachments = [];
		$tempHash = $this->filter('attachment_hash', 'str');

		if ($thread->Forum->canUploadAndManageAttachments())
		{
			/** @var \XF\Repository\Attachment $attachmentRepo */
			$attachmentRepo = $this->repository('XF:Attachment');
			$attachmentData = $attachmentRepo->getEditorData('post', $post, $tempHash);
			$attachments = $attachmentData['attachments'];
		}

		return $this->plugin('XF:BbCodePreview')->actionPreview(
			$post->message, 'post', $post->User, $attachments, $thread->canViewAttachments()
		);
	}

	public function actionDelete(ParameterBag $params)
	{
		$post = $this->assertViewablePost($params->post_id);
		if (!$post->canDelete('soft', $error))
		{
			return $this->noPermission($error);
		}

		if ($this->isPost())
		{
			$type = $this->filter('hard_delete', 'bool') ? 'hard' : 'soft';
			$reason = $this->filter('reason', 'str');

			if (!$post->canDelete($type, $error))
			{
				return $this->noPermission($error);
			}

			/** @var \XF\Entity\Thread $thread */
			$thread = $post->Thread;

			/** @var \XF\Service\Post\Deleter $deleter */
			$deleter = $this->service('XF:Post\Deleter', $post);

			if ($this->filter('author_alert', 'bool') && $post->canSendModeratorActionAlert())
			{
				$deleter->setSendAlert(true, $this->filter('author_alert_reason', 'str'));
			}

			$deleter->delete($type, $reason);

			$this->plugin('XF:InlineMod')->clearIdFromCookie('post', $post->post_id);

			if ($deleter->wasThreadDeleted())
			{
				$this->plugin('XF:InlineMod')->clearIdFromCookie('thread', $post->thread_id);

				return $this->redirect(
					$thread && $thread->Forum
						? $this->buildLink('forums', $thread->Forum)
						: $this->buildLink('index')
				);
			}
			else
			{
				return $this->redirect(
					$this->getDynamicRedirect($this->buildLink('threads', $thread), false)
				);
			}
		}
		else
		{
			$viewParams = [
				'post' => $post,
				'thread' => $post->Thread,
				'forum' => $post->Thread->Forum
			];
			return $this->view('XF:Post\Delete', 'post_delete', $viewParams);
		}
	}

	public function actionUndelete(ParameterBag $params)
	{
		$post = $this->assertViewablePost($params->post_id);

		/** @var \XF\ControllerPlugin\Undelete $plugin */
		$plugin = $this->plugin('XF:Undelete');
		return $plugin->actionUndelete(
			$post,
			$this->buildLink('posts/undelete', $post),
			$this->buildLink('posts', $post),
			\XF::phrase('post_in_thread_x', [
				'title' => $post->Thread->title,
			]),
			'message_state'
		);
	}

	public function actionIp(ParameterBag $params)
	{
		$post = $this->assertViewablePost($params->post_id);
		$breadcrumbs = $post->Thread->getBreadcrumbs();

		/** @var \XF\ControllerPlugin\Ip $ipPlugin */
		$ipPlugin = $this->plugin('XF:Ip');
		return $ipPlugin->actionIp($post, $breadcrumbs);
	}

	public function actionReport(ParameterBag $params)
	{
		$post = $this->assertViewablePost($params->post_id);
		if (!$post->canReport($error))
		{
			return $this->noPermission($error);
		}

		/** @var \XF\ControllerPlugin\Report $reportPlugin */
		$reportPlugin = $this->plugin('XF:Report');
		return $reportPlugin->actionReport(
			'post', $post,
			$this->buildLink('posts/report', $post),
			$this->buildLink('posts', $post)
		);
	}

	public function actionQuote(ParameterBag $params)
	{
		$post = $this->assertViewablePost($params->post_id);
		if (!$post->Thread->canReply($error))
		{
			return $this->noPermission($error);
		}

		return $this->plugin('XF:Quote')->actionQuote($post, 'post');
	}

	public function actionHistory(ParameterBag $params)
	{
		return $this->rerouteController('XF:EditHistory', 'index', [
			'content_type' => 'post',
			'content_id' => $params->post_id
		]);
	}

	public function actionBookmark(ParameterBag $params)
	{
		$post = $this->assertViewablePost($params->post_id);

		/** @var \XF\ControllerPlugin\Bookmark $bookmarkPlugin */
		$bookmarkPlugin = $this->plugin('XF:Bookmark');

		return $bookmarkPlugin->actionBookmark(
			$post, $this->buildLink('posts/bookmark', $post)
		);
	}

	public function actionReact(ParameterBag $params)
	{
		$post = $this->assertViewablePost($params->post_id);

		/** @var \XF\ControllerPlugin\Reaction $reactionPlugin */
		$reactionPlugin = $this->plugin('XF:Reaction');
		return $reactionPlugin->actionReactSimple($post, 'posts');
	}

	public function actionReactions(ParameterBag $params)
	{
		$post = $this->assertViewablePost($params->post_id);

		$breadcrumbs = $post->Thread->getBreadcrumbs();
		$title = \XF::phrase('members_who_reacted_to_message_x', ['position' => ($post->position + 1)]);

		/** @var \XF\ControllerPlugin\Reaction $reactionPlugin */
		$reactionPlugin = $this->plugin('XF:Reaction');
		return $reactionPlugin->actionReactions(
			$post,
			'posts/reactions',
			$title, $breadcrumbs
		);
	}

	public function actionWarn(ParameterBag $params)
	{
		$post = $this->assertViewablePost($params->post_id);

		if (!$post->canWarn($error))
		{
			return $this->noPermission($error);
		}

		$breadcrumbs = $post->Thread->getBreadcrumbs();

		/** @var \XF\ControllerPlugin\Warn $warnPlugin */
		$warnPlugin = $this->plugin('XF:Warn');
		return $warnPlugin->actionWarn(
			'post', $post,
			$this->buildLink('posts/warn', $post),
			$breadcrumbs
		);
	}

	public function actionShare(ParameterBag $params)
	{
		$post = $this->assertViewablePost($params->post_id);
		$thread = $post->Thread;

		/** @var \XF\ControllerPlugin\Share $sharePlugin */
		$sharePlugin = $this->plugin('XF:Share');
		return $sharePlugin->actionTooltip(
			$post->isFirstPost()
				? $this->buildLink('canonical:threads', $thread)
				: $this->buildLink('canonical:threads/post', $thread, ['post_id' => $post->post_id]),
			$post->isFirstPost()
				? \XF::phrase('thread_x', ['title' => $thread->title])
				: \XF::phrase('post_in_thread_x', ['title' => $thread->title]),
			$post->isFirstPost()
				? \XF::phrase('share_this_thread')
				: \XF::phrase('share_this_post')
		);
	}

	/**
	 * @param $postId
	 * @param array $extraWith
	 *
	 * @return \XF\Entity\Post
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertViewablePost($postId, array $extraWith = [])
	{
		$visitor = \XF::visitor();
		$extraWith[] = 'Thread';
		$extraWith[] = 'Thread.Forum';
		$extraWith[] = 'Thread.Forum.Node';
		$extraWith[] = 'Thread.Forum.Node.Permissions|' . $visitor->permission_combination_id;

		/** @var \XF\Entity\Post $post */
		$post = $this->em()->find('XF:Post', $postId, $extraWith);
		if (!$post)
		{
			throw $this->exception($this->notFound(\XF::phrase('requested_post_not_found')));
		}
		if (!$post->canView($error))
		{
			throw $this->exception($this->noPermission($error));
		}

		$this->plugin('XF:Node')->applyNodeContext($post->Thread->Forum->Node);

		return $post;
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

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('viewing_thread');  // no need to be more specific - this is a fairly infrequent event
	}
}