<?php

namespace XF\Api\Controller;

use XF\Mvc\Entity\Entity;
use XF\Mvc\ParameterBag;

/**
 * @api-group Threads
 */
class Thread extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertApiScopeByRequestMethod('thread');
	}

	/**
	 * @api-desc Gets information about the specified thread.
	 *
	 * @api-in bool $with_posts If specified, the response will include a page of posts.
	 * @api-in int $page The page of posts to include
	 *
	 * @api-out Thread $thread
	 * @api-see self::getPostsInThreadPaginated()
	 */
	public function actionGet(ParameterBag $params)
	{
		$thread = $this->assertViewableThread($params->thread_id);

		if ($this->filter('with_posts', 'bool'))
		{
			$postData = $this->getPostsInThreadPaginated($thread, $this->filterPage());
		}
		else
		{
			$postData = [];
		}

		$result = [
			'thread' => $thread->toApiResult(Entity::VERBOSITY_VERBOSE)
		];
		$result += $postData;

		return $this->apiResult($result);
	}

	/**
	 * @api-desc Gets a page of posts in the specified conversation.
	 *
	 * @api-in int $page
	 *
	 * @api-see self::getPostsInThreadPaginated
	 */
	public function actionGetPosts(ParameterBag $params)
	{
		$thread = $this->assertViewableThread($params->thread_id);

		$postData = $this->getPostsInThreadPaginated($thread, $this->filterPage());

		return $this->apiResult($postData);
	}

	/**
	 * @api-out Post[] $posts List of posts on the requested page
	 * @api-out pagination $pagination Pagination details
	 *
	 * @param \XF\Entity\Thread $thread
	 * @param int $page
	 * @param null|int $perPage
	 *
	 * @return array
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function getPostsInThreadPaginated(\XF\Entity\Thread $thread, $page = 1, $perPage = null)
	{
		$perPage = intval($perPage);
		if ($perPage <= 0)
		{
			$perPage = $this->options()->messagesPerPage;
		}
		$total = $thread->reply_count + 1;

		$this->assertValidApiPage($page, $perPage, $total);

		$finder = $this->setupPostFinder($thread);

		$posts = $finder->onPage($page, $perPage)->fetch();

		/** @var \XF\Repository\Attachment $attachmentRepo */
		$attachmentRepo = $this->repository('XF:Attachment');
		$attachmentRepo->addAttachmentsToContent($posts, 'post');

		$postResults = $posts->toApiResults();

		return [
			'posts' => $postResults,
			'pagination' => $this->getPaginationData($postResults, $page, $perPage, $total)
		];
	}

	/**
	 * @param \XF\Entity\Thread $thread
	 * @return \XF\Finder\Post
	 */
	protected function setupPostFinder(\XF\Entity\Thread $thread)
	{
		/** @var \XF\Finder\Post $finder */
		$finder = $this->finder('XF:Post');
		$finder
			->inThread($thread)
			->orderByDate()
			->with('api');

		return $finder;
	}

	/**
	 * @param \XF\Entity\Thread $thread
	 *
	 * @return \XF\Service\Thread\Editor
	 */
	protected function setupThreadEdit(\XF\Entity\Thread $thread)
	{
		/** @var \XF\Service\Thread\Editor $editor */
		$editor = $this->service('XF:Thread\Editor', $thread);

		$input = $this->filter([
			'prefix_id' => '?uint',
			'title' => '?str',
			'discussion_open' => '?bool',
			'sticky' => '?bool',
			'custom_fields' => 'array',
			'add_tags' => 'array-str',
			'remove_tags' => 'array-str',
		]);

		$isBypassingPermissions = \XF::isApiBypassingPermissions();
		$isCheckingPermissions = \XF::isApiCheckingPermissions();

		if (isset($input['prefix_id']))
		{
			$prefixId = $input['prefix_id'];
			if ($prefixId != $thread->prefix_id
				&& $isCheckingPermissions
				&& !$thread->Forum->isPrefixUsable($input['prefix_id'])
			)
			{
				$prefixId = 0; // not usable, just blank it out
			}
			$editor->setPrefix($prefixId);
		}

		if (isset($input['title']))
		{
			$editor->setTitle($input['title']);
		}

		if (isset($input['discussion_open']) && ($isBypassingPermissions || $thread->canLockUnlock()))
		{
			$editor->setDiscussionOpen($input['discussion_open']);
		}
		if (isset($input['sticky']) && ($isBypassingPermissions || $thread->canStickUnstick()))
		{
			$editor->setSticky($input['sticky']);
		}

		if ($input['custom_fields'])
		{
			$editor->setCustomFields($input['custom_fields'], true);
		}

		if ($isBypassingPermissions || $thread->canEditTags())
		{
			if ($input['add_tags'])
			{
				$editor->addTags($input['add_tags']);
			}
			if ($input['remove_tags'])
			{
				$editor->removeTags($input['remove_tags']);
			}
		}

		return $editor;
	}

	/**
	 * @api-desc Updates the specified thread
	 *
	 * @api-in int $prefix_id
	 * @api-in str $title
	 * @api-in bool $discussion_open
	 * @api-in bool $sticky
	 * @api-in string $custom_fields[<name>]
	 * @api-in array $add_tags
	 * @api-in array $remove_tags
	 *
	 * @api-out true $success
	 * @api-out Thread $thread
	 */
	public function actionPost(ParameterBag $params)
	{
		$thread = $this->assertViewableThread($params->thread_id);

		if (\XF::isApiCheckingPermissions() && !$thread->canEdit($error))
		{
			return $this->noPermission($error);
		}

		$editor = $this->setupThreadEdit($thread);
		if (!$editor->validate($errors))
		{
			return $this->error($errors);
		}

		$editor->save();

		return $this->apiSuccess([
			'thread' => $thread->toApiResult(Entity::VERBOSITY_VERBOSE)
		]);
	}

	/**
	 * @api-desc Deletes the specified thread. Default to soft deletion.
	 *
	 * @api-in bool $hard_delete
	 * @api-in str $reason
	 * @api-in bool $starter_alert
	 * @api-in str $starter_alert_reason
	 *
	 * @api-out true $success
	 */
	public function actionDelete(ParameterBag $params)
	{
		$thread = $this->assertViewableThread($params->thread_id);

		if (\XF::isApiCheckingPermissions() && !$thread->canDelete('soft', $error))
		{
			return $this->noPermission($error);
		}

		$type = 'soft';
		$reason = $this->filter('reason', 'str');

		if ($this->filter('hard_delete', 'bool'))
		{
			$this->assertApiScope('thread:delete_hard');

			if (\XF::isApiCheckingPermissions() && !$thread->canDelete('hard', $error))
			{
				return $this->noPermission($error);
			}

			$type = 'hard';
		}

		/** @var \XF\Service\Thread\Deleter $deleter */
		$deleter = $this->service('XF:Thread\Deleter', $thread);

		if ($this->filter('starter_alert', 'bool'))
		{
			$deleter->setSendAlert(true, $this->filter('starter_alert_reason', 'str'));
		}

		$deleter->delete($type, $reason);

		return $this->apiSuccess();
	}

	/**
	 * @param int $id
	 * @param string|array $with
	 *
	 * @return \XF\Entity\Thread
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertViewableThread($id, $with = 'api')
	{
		return $this->assertViewableApiRecord('XF:Thread', $id, $with);
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