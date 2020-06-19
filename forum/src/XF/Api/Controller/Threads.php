<?php

namespace XF\Api\Controller;

use XF\Mvc\Entity\Entity;
use XF\Mvc\ParameterBag;

/**
 * @api-group Threads
 */
class Threads extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertApiScopeByRequestMethod('thread');
	}

	/**
	 * @api-desc Gets a list of threads
	 *
	 * @api-in int $page
	 *
	 * @api-out Thread[] $threads
	 * @api-out pagination $pagination
	 */
	public function actionGet()
	{
		$page = $this->filterPage();
		$perPage = $this->options()->discussionsPerPage;

		$threadFinder = $this->setupThreadFinder()->limitByPage($page, $perPage);
		$total = $threadFinder->total();

		$this->assertValidApiPage($page, $perPage, $total);

		$threads = $threadFinder->fetch();

		if (\XF::isApiCheckingPermissions())
		{
			// only filtered to the forums we could view -- could still be other conditions
			$threads = $threads->filterViewable();
		}

		return $this->apiResult([
			'threads' => $threads->toApiResults(),
			'pagination' => $this->getPaginationData($threads, $page, $perPage, $total)
		]);
	}

	/**
	 * @param array $filters List of filters that have been applied from input
	 * @param array|null $sort If array, sort that has been applied from input
	 *
	 * @return \XF\Finder\Thread
	 */
	protected function setupThreadFinder(&$filters = [], &$sort = null)
	{
		$threadRepo = $this->repository('XF:Thread');
		$threadFinder = $threadRepo->findThreadsForApi();

		/** @var \XF\Api\ControllerPlugin\Thread $threadPlugin */
		$threadPlugin = $this->plugin('XF:Api:Thread');

		$filters = $threadPlugin->applyThreadListFilters($threadFinder);

		$sort = $threadPlugin->applyThreadListSort($threadFinder);

		if (!isset($filters['last_days']))
		{
			if (!$sort || ($sort[0] == 'last_post_date' && $sort[1] == 'desc'))
			{
				$threadFinder->where('last_post_date', '>', $threadRepo->getReadMarkingCutOff());
			}
		}

		return $threadFinder;
	}

	/**
	 * @return \XF\Api\Mvc\Reply\ApiResult|\XF\Mvc\Reply\Error
	 * @throws \XF\Mvc\Reply\Exception
	 *
	 * @api-desc Creates a thread.
	 *
	 * @api-in <req> int $node_id ID of the forum to create the thread in.
	 * @api-in <req> str $title Title of the thread.
	 * @api-in <req> str $message Body of the first post in the thread.
	 * @api-in int $prefix_id ID of the prefix to apply to the thread. If not valid in the selected forum, will be ignored.
	 * @api-in str[] $tags Array of tag names to apply to the thread.
	 * @api-in string $custom_fields[<name>] Value to apply to the custom field with the specified name.
	 * @api-in bool $discussion_open
	 * @api-in bool $sticky
	 * @api-in str $attachment_key API attachment key to upload files. Attachment key context type must be post with context[node_id] set to the ID of the forum this is being posted in.
	 *
	 * @api-out true $success
	 * @api-out Thread $thread
	 *
	 * @api-error no_permission No permission error.
	 */
	public function actionPost()
	{
		$this->assertRequiredApiInput(['node_id', 'title', 'message']);

		$nodeId = $this->filter('node_id', 'uint');

		/** @var \XF\Entity\Forum $forum */
		$forum = $this->assertViewableApiRecord('XF:Forum', $nodeId);

		if (\XF::isApiCheckingPermissions() && !$forum->canCreateThread($error))
		{
			return $this->noPermission($error);
		}

		$creator = $this->setupThreadCreate($forum);

		if (\XF::isApiCheckingPermissions())
		{
			$creator->checkForSpam();
		}

		if (!$creator->validate($errors))
		{
			return $this->error($errors);
		}

		/** @var \XF\Entity\Thread $thread */
		$thread = $creator->save();
		$this->finalizeThreadCreate($creator);

		return $this->apiSuccess([
			'thread' => $thread->toApiResult(Entity::VERBOSITY_VERBOSE)
		]);
	}

	protected function setupThreadCreate(\XF\Entity\Forum $forum)
	{
		$input = $this->filter([
			'title' => 'str',
			'message' => 'str',
			'prefix_id' => 'uint',
			'custom_fields' => 'array',
			'tags' => 'array-str',
			'discussion_open' => '?bool',
			'sticky' => '?bool',
			'attachment_key' => 'str'
		]);

		$isBypassingPermissions = \XF::isApiBypassingPermissions();

		/** @var \XF\Service\Thread\Creator $creator */
		$creator = $this->service('XF:Thread\Creator', $forum);

		$creator->setContent($input['title'], $input['message']);
		$creator->setCustomFields($input['custom_fields']);

		if ($input['prefix_id'] && ($isBypassingPermissions || $forum->isPrefixUsable($input['prefix_id'])))
		{
			$creator->setPrefix($input['prefix_id']);
		}

		if ($isBypassingPermissions || $forum->canEditTags())
		{
			$creator->setTags($input['tags']);
		}

		if ($isBypassingPermissions || $forum->canUploadAndManageAttachments())
		{
			$hash = $this->getAttachmentTempHashFromKey($input['attachment_key'], 'post', ['node_id' => $forum->node_id]);
			$creator->setAttachmentHash($hash);
		}

		// TODO: polls

		$thread = $creator->getThread();

		if (isset($input['discussion_open']) && ($isBypassingPermissions || $thread->canLockUnlock()))
		{
			$creator->setDiscussionOpen($input['discussion_open']);
		}
		if (isset($input['sticky']) && ($isBypassingPermissions || $thread->canStickUnstick()))
		{
			$creator->setSticky($input['sticky']);
		}

		return $creator;
	}

	protected function finalizeThreadCreate(\XF\Service\Thread\Creator $creator)
	{
		$creator->sendNotifications();

		$thread = $creator->getThread();
		$visitor = \XF::visitor();

		if ($visitor->user_id)
		{
			$this->getThreadRepo()->markThreadReadByVisitor($thread, $thread->post_date);
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