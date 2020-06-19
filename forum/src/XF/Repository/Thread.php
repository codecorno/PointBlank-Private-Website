<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Thread extends Repository
{
	public function findThreadsForForumView(\XF\Entity\Forum $forum, array $limits = [])
	{
		/** @var \XF\Finder\Thread $finder */
		$finder = $this->finder('XF:Thread');
		$finder
			->inForum($forum, $limits)
			->with('full');

		return $finder;
	}

	public function findThreadsForRssFeed(\XF\Entity\Forum $forum = null)
	{
		/** @var \XF\Finder\Thread $finder */
		$finder = $this->finder('XF:Thread');

		$finder->where('discussion_state', 'visible')
			->setDefaultOrder('last_post_date', 'DESC')
			->where('discussion_type', '!=', 'redirect')
			->with(['Forum', 'User', 'FirstPost']);

		if ($forum)
		{
			$finder->where('node_id', $forum->node_id);
		}
		else
		{
			$finder->where('Forum.find_new', 1)
				->where('last_post_date', '>', $this->getReadMarkingCutOff());
		}

		return $finder;
	}

	/**
	 * @param bool|false $unreadOnly
	 *
	 * @return \XF\Finder\Thread
	 */
	public function findThreadsForWatchedList($unreadOnly = false)
	{
		$visitor = \XF::visitor();
		$userId = $visitor->user_id;

		/** @var \XF\Finder\Thread $finder */
		$finder = $this->finder('XF:Thread');
		$finder
			->with('fullForum')
			->with('Watch|' . $userId, true)
			->where('discussion_state', 'visible')
			->setDefaultOrder('last_post_date', 'DESC');

		if ($unreadOnly)
		{
			$finder->unreadOnly($userId);
		}

		return $finder;
	}

	public function findThreadsStartedByUser($userId)
	{
		return $this->finder('XF:Thread')
			->with('fullForum')
			->with(['Forum', 'User'])
			->where('user_id', $userId)
			->where('discussion_type', '<>', 'redirect')
			->setDefaultOrder('last_post_date', 'DESC');
	}

	public function findThreadsWithPostsByUser($userId)
	{
		return $this->finder('XF:Thread')
			->with('fullForum')
			->with(['Forum', 'User'])
			->exists('UserPosts|' . $userId)
			->where('discussion_type', '<>', 'redirect')
			->setDefaultOrder('last_post_date', 'DESC');
	}

	public function findThreadsWithNoReplies()
	{
		return $this->finder('XF:Thread')
			->with('fullForum')
			->with(['Forum', 'User'])
			->where('reply_count', 0)
			->where('discussion_type', '<>', 'redirect')
			->where('last_post_date', '>', $this->getReadMarkingCutOff()) // for performance reasons
			->order('last_post_date', 'DESC')
			->indexHint('FORCE', 'last_post_date');
	}

	/**
	 * @return Finder|\XF\Finder\Thread
	 */
	public function findLatestThreads()
	{
		return $this->finder('XF:Thread')
			->with(['Forum', 'User'])
			->where('discussion_state', 'visible')
			->where('discussion_type', '<>', 'redirect')
			->order('post_date', 'DESC');
	}

	/**
	 * @return \XF\Finder\Thread
	 */
	public function findThreadsWithLatestPosts()
	{
		return $this->finder('XF:Thread')
			->with(['Forum', 'User'])
			->where('Forum.find_new', true)
			->where('discussion_state', 'visible')
			->where('discussion_type', '<>', 'redirect')
			->where('last_post_date', '>', $this->getReadMarkingCutOff())
			->order('last_post_date', 'DESC')
			->indexHint('FORCE', 'last_post_date');
	}

	/**
	 * @return \XF\Finder\Thread
	 */
	public function findThreadsWithUnreadPosts($userId = null)
	{
		$threadFinder = $this->findThreadsWithLatestPosts();

		$userId = $userId ?: \XF::visitor()->user_id;

		if (!$userId)
		{
			return $threadFinder;
		}

		return $threadFinder->unreadOnly($userId);
	}

	/**
	 * @param \XF\Entity\Forum|null $forum If provided, applies forum-specific limits
	 *
	 * @return \XF\Finder\Thread
	 */
	public function findThreadsForApi(\XF\Entity\Forum $forum = null)
	{
		/** @var \XF\Finder\Thread $threadFinder */
		$threadFinder = $this->finder('XF:Thread')
			->with('api')
			->where('discussion_type', '!=', 'redirect');

		if ($forum)
		{
			$limits = [];
			if (\XF::isApiBypassingPermissions())
			{
				$limits['visibility'] = false;
			}

			$threadFinder->inForum($forum, $limits);
		}
		else
		{
			$threadFinder->where('Forum.find_new', 1)
				->setDefaultOrder('last_post_date', 'DESC');

			if (\XF::isApiCheckingPermissions())
			{
				$forums = $this->repository('XF:Forum')->getViewableForums();
				$threadFinder->where('node_id', $forums->keys())
					->where('discussion_state', 'visible');
			}
		}

		return $threadFinder;
	}

	public function logThreadView(\XF\Entity\Thread $thread)
	{
		$this->db()->query("
			INSERT INTO xf_thread_view
				(thread_id, total)
			VALUES
				(? , 1)
			ON DUPLICATE KEY UPDATE
				total = total + 1
		", $thread->thread_id);
	}

	public function batchUpdateThreadViews()
	{
		$db = $this->db();
		$db->query("
			UPDATE xf_thread AS t
			INNER JOIN xf_thread_view AS tv ON (t.thread_id = tv.thread_id)
			SET t.view_count = t.view_count + tv.total
		");
		$db->emptyTable('xf_thread_view');
	}

	public function markThreadReadByUser(\XF\Entity\Thread $thread, \XF\Entity\User $user, $newRead = null)
	{
		if (!$user->user_id)
		{
			return false;
		}

		if ($newRead === null)
		{
			$newRead = max(\XF::$time, $thread->last_post_date);
		}

		$cutOff = $this->getReadMarkingCutOff();
		if ($newRead <= $cutOff)
		{
			return false;
		}

		$readDate = $thread->getUserReadDate($user);
		if ($newRead <= $readDate)
		{
			return false;
		}

		$this->db()->insert('xf_thread_read', [
			'thread_id' => $thread->thread_id,
			'user_id' => $user->user_id,
			'thread_read_date' => $newRead
		], false, 'thread_read_date = VALUES(thread_read_date)');

		if ($newRead < $thread->last_post_date)
		{
			// thread no fully viewed
			return false;
		}

		if ($thread->Forum && !$this->countUnreadThreadsInForumForUser($thread->Forum, $user))
		{
			/** @var \XF\Repository\Forum $forumRepo */
			$forumRepo = $this->repository('XF:Forum');
			$forumRepo->markForumReadByUser($thread->Forum, $user->user_id);
		}

		return true;
	}

	public function markThreadReadByVisitor(\XF\Entity\Thread $thread, $newRead = null)
	{
		$visitor = \XF::visitor();
		return $this->markThreadReadByUser($thread, $visitor, $newRead);
	}

	public function pruneThreadReadLogs($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = $this->getReadMarkingCutOff();
		}

		$this->db()->delete('xf_thread_read', 'thread_read_date < ?', $cutOff);
	}

	public function countUnreadThreadsInForumForUser(\XF\Entity\Forum $forum, \XF\Entity\User $user)
	{
		$userId = $user->user_id;
		if (!$userId)
		{
			return 0;
		}

		$read = $forum->Read[$userId];
		$cutOff = $this->getReadMarkingCutOff();

		$readDate = $read ? max($read->forum_read_date, $cutOff) : $cutOff;

		$finder = $this->finder('XF:Thread');
		$finder
			->where('node_id', $forum->node_id)
			->where('last_post_date', '>', $readDate)
			->where('discussion_state', 'visible')
			->where('discussion_type', '<>', 'redirect')
			->whereOr(
				["Read|{$userId}.thread_id", null],
				[$finder->expression('%s > %s', 'last_post_date', "Read|{$userId}.thread_read_date")]
			)
			->skipIgnored();

		return $finder->total();
	}

	public function countUnreadThreadsInForum(\XF\Entity\Forum $forum)
	{
		$visitor = \XF::visitor();
		return $this->countUnreadThreadsInForumForUser($forum, $visitor);
	}

	public function getReadMarkingCutOff()
	{
		return \XF::$time - $this->options()->readMarkingDataLifetime * 86400;
	}

	public function rebuildThreadUserPostCounters($threadId)
	{
		$db = $this->db();

		$db->beginTransaction();
		$db->delete('xf_thread_user_post', 'thread_id = ?', $threadId);
		$db->query("
			INSERT INTO xf_thread_user_post (thread_id, user_id, post_count)
			SELECT thread_id, user_id, COUNT(*)
			FROM xf_post
			WHERE thread_id = ?
				AND message_state = 'visible'
				AND user_id > 0
			GROUP BY user_id
		", $threadId);
		$db->commit();
	}

	public function rebuildThreadPostPositions($threadId)
	{
		$db = $this->db();
		$db->query('SET @position := -1');
		$db->query("
			UPDATE xf_post
			SET position = (@position := IF(message_state = 'visible', @position + 1, GREATEST(@position, 0)))
			WHERE thread_id = ?
			ORDER BY post_date
		", $threadId);
	}

	public function sendModeratorActionAlert(\XF\Entity\Thread $thread, $action, $reason = '', array $extra = [])
	{
		if (!$thread->user_id || !$thread->User)
		{
			return false;
		}

		$extra = array_merge([
			'title' => $thread->title,
			'prefix_id' => $thread->prefix_id,
			'link' => $this->app()->router('public')->buildLink('nopath:threads', $thread),
			'reason' => $reason
		], $extra);

		/** @var \XF\Repository\UserAlert $alertRepo */
		$alertRepo = $this->repository('XF:UserAlert');
		$alertRepo->alert(
			$thread->User,
			0, '',
			'user', $thread->user_id,
			"thread_{$action}", $extra
		);

		return true;
	}

	/**
	 * @param $url
	 * @param null $type
	 * @param null $error
	 *
	 * @return null|\XF\Entity\Thread
	 */
	public function getThreadFromUrl($url, $type = null, &$error = null)
	{
		$routePath = $this->app()->request()->getRoutePathFromUrl($url);
		$routeMatch = $this->app()->router($type)->routeToController($routePath);
		$params = $routeMatch->getParameterBag();

		if (!$params->thread_id)
		{
			$error = \XF::phrase('no_thread_id_could_be_found_from_that_url');
			return null;
		}

		$thread = $this->app()->find('XF:Thread', $params->thread_id);
		if (!$thread)
		{
			$error = \XF::phrase('no_thread_could_be_found_with_id_x', ['thread_id' => $params->thread_id]);
			return null;
		}

		if ($thread->discussion_type == 'redirect')
		{
			$error = \XF::phrase('please_provide_url_of_non_redirect_thread');
			return null;
		}

		return $thread;
	}
}