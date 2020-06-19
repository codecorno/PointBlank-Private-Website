<?php

namespace XF\Service\Thread;

use XF\Entity\Thread;
use XF\Entity\User;

class Mover extends \XF\Service\AbstractService
{
	/**
	 * @var Thread
	 */
	protected $thread;

	protected $alert = false;
	protected $alertReason = '';

	protected $notifyWatchers = false;

	protected $redirect = false;
	protected $redirectLength = 0;

	protected $prefixId = null;

	protected $extraSetup = [];

	public function __construct(\XF\App $app, Thread $thread)
	{
		parent::__construct($app);

		$this->thread = $thread;
	}

	public function getThread()
	{
		return $this->thread;
	}

	public function setSendAlert($alert, $reason = null)
	{
		$this->alert = (bool)$alert;
		if ($reason !== null)
		{
			$this->alertReason = $reason;
		}
	}

	public function setRedirect($redirect, $length = null)
	{
		$this->redirect = (bool)$redirect;
		if ($length !== null)
		{
			$this->redirectLength = intval($length);
		}
	}

	public function setPrefix($prefixId)
	{
		$this->prefixId = ($prefixId === null ? $prefixId : intval($prefixId));
	}

	public function setNotifyWatchers($value = true)
	{
		$this->notifyWatchers = (bool)$value;
	}

	public function addExtraSetup(callable $extra)
	{
		$this->extraSetup[] = $extra;
	}

	public function move(\XF\Entity\Forum $forum)
	{
		$actor = \XF::visitor();

		$thread = $this->thread;
		$oldForum = $thread->Forum;

		$moved = ($thread->node_id != $forum->node_id);

		foreach ($this->extraSetup AS $extra)
		{
			call_user_func($extra, $thread, $forum);
		}

		$thread->node_id = $forum->node_id;
		if ($this->prefixId !== null)
		{
			$thread->prefix_id = $this->prefixId;
		}

		if (!$thread->preSave())
		{
			throw new \XF\PrintableException($thread->getErrors());
		}

		$db = $this->db();
		$db->beginTransaction();

		$thread->save(true, false);

		if ($moved && $this->redirect && $oldForum)
		{
			/** @var \XF\Repository\ThreadRedirect $redirectRepo */
			$redirectRepo = $this->repository('XF:ThreadRedirect');
			$redirectRepo->createThreadRedirectionDouble($thread, $oldForum, $this->redirectLength);
		}

		$db->commit();

		if ($moved
			&& $thread->discussion_state == 'visible'
			&& $this->alert
			&& $thread->user_id != $actor->user_id
			&& $thread->discussion_type != 'redirect'
		)
		{
			/** @var \XF\Repository\Thread $threadRepo */
			$threadRepo = $this->repository('XF:Thread');
			$threadRepo->sendModeratorActionAlert($thread, 'move', $this->alertReason);
		}

		if ($moved
			&& $this->notifyWatchers
			&& $thread->FirstPost
			&& $thread->discussion_type != 'redirect'
		)
		{
			/** @var \XF\Service\Post\Notifier $notifier */
			$notifier = $this->service('XF:Post\Notifier', $thread->FirstPost, 'thread');
			if ($oldForum)
			{
				$notifier->skipUsersWatchingForum($oldForum);
			}
			$notifier->notifyAndEnqueue(3);
		}

		return $moved;
	}
}