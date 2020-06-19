<?php

namespace XF\Service\Thread;

use XF\Entity\Thread;
use XF\Entity\User;

class Merger extends \XF\Service\AbstractService
{
	/**
	 * @var Thread
	 */
	protected $target;

	protected $alert = false;
	protected $alertReason = '';

	protected $redirect = false;
	protected $redirectLength = 0;

	protected $log = true;

	protected $sourceThreads = [];
	protected $sourcePosts = [];

	public function __construct(\XF\App $app, Thread $target)
	{
		parent::__construct($app);

		$this->target = $target;
	}

	public function getTarget()
	{
		return $this->target;
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

	public function setLog($log)
	{
		$this->log = (bool)$log;
	}

	public function merge($sourceThreadsRaw)
	{
		if ($sourceThreadsRaw instanceof \XF\Mvc\Entity\AbstractCollection)
		{
			$sourceThreadsRaw = $sourceThreadsRaw->toArray();
		}
		else if ($sourceThreadsRaw instanceof Thread)
		{
			$sourceThreadsRaw = [$sourceThreadsRaw];
		}
		else if (!is_array($sourceThreadsRaw))
		{
			throw new \InvalidArgumentException('Threads must be provided as collection, array or entity');
		}

		if (!$sourceThreadsRaw)
		{
			return false;
		}

		$db = $this->db();

		/** @var Thread[] $sourceThreads */
		$sourceThreads = [];
		foreach ($sourceThreadsRaw AS $sourceThread)
		{
			$sourceThread->setOption('log_moderator', false);
			$sourceThreads[$sourceThread->thread_id] = $sourceThread;
		}

		$posts = $db->fetchAllKeyed("
			SELECT post_id, thread_id, user_id, message_state, reactions
			FROM xf_post
			WHERE thread_id IN (" . $db->quote(array_keys($sourceThreads)) . ")
		", 'post_id');

		$this->sourceThreads = $sourceThreads;
		$this->sourcePosts = $posts;

		$target = $this->target;
		$target->setOption('log_moderator', false);

		$db->beginTransaction();

		$this->moveDataToTarget();
		$this->updateTargetData();
		$this->updateUserCounters();

		if ($this->alert)
		{
			$this->sendAlert();
		}

		if ($this->redirect)
		{
			$this->convertSourcesToRedirects();
			$this->cleanUpSourceRedirects();
		}
		else
		{
			foreach ($sourceThreads AS $sourceThread)
			{
				$sourceThread->delete();
			}
		}

		$this->finalActions();

		$db->commit();

		return true;
	}

	protected function moveDataToTarget()
	{
		$db = $this->db();
		$target = $this->target;

		$sourcePosts = $this->sourcePosts;

		$sourceThreads = $this->sourceThreads;
		$sourceThreadIds = array_keys($sourceThreads);
		$sourceIdsQuoted = $db->quote($sourceThreadIds);

		$db->update('xf_post',
			['thread_id' => $target->thread_id],
			"thread_id IN ($sourceIdsQuoted)"
		);
		$db->update('xf_thread_watch',
			['thread_id' => $target->thread_id],
			"thread_id IN ($sourceIdsQuoted)",
			[], 'IGNORE'
		);
		$db->update('xf_thread_reply_ban',
			['thread_id' => $target->thread_id],
			"thread_id IN ($sourceIdsQuoted)",
			[], 'IGNORE'
		);
		$db->update('xf_tag_content',
			['content_id' => $target->thread_id],
			"content_type = 'thread' AND content_id IN ($sourceIdsQuoted)",
			[], 'IGNORE'
		);
	}

	protected function updateTargetData()
	{
		$db = $this->db();
		$target = $this->target;
		$sourceThreads = $this->sourceThreads;

		foreach ($sourceThreads AS $sourceThread)
		{
			$target->view_count += $sourceThread->view_count;

			if (!$target->discussion_type && $sourceThread->discussion_type == 'poll')
			{
				$pollMoved = $db->update('xf_poll',
					['content_id' => $target->thread_id],
					"content_type = 'thread' AND content_id = " . $db->quote($sourceThread->thread_id)
				);
				if ($pollMoved)
				{
					$target->discussion_type = 'poll';
					break;
				}
			}
		}

		$target->rebuildCounters();
		$target->save();

		/** @var \XF\Repository\Thread $threadRepo */
		$threadRepo = $this->repository('XF:Thread');
		$threadRepo->rebuildThreadPostPositions($target->thread_id);
		$threadRepo->rebuildThreadUserPostCounters($target->thread_id);

		/** @var \XF\Repository\Tag $tagRepo */
		$tagRepo = $this->repository('XF:Tag');
		$tagRepo->rebuildContentTagCache('thread', $target->thread_id);
	}

	protected function updateUserCounters()
	{
		$target = $this->target;

		$targetMessagesCount = (
			$target->Forum && $target->Forum->count_messages
			&& $target->discussion_state == 'visible'
		);
		$targetReactionsCount = ($target->discussion_state == 'visible');

		$sourcesMessagesCount = [];
		$sourcesReactionsCount = [];
		foreach ($this->sourceThreads AS $id => $sourceThread)
		{
			$sourcesMessagesCount[$id] = (
				$sourceThread->Forum && $sourceThread->Forum->count_messages
				&& $sourceThread->discussion_state == 'visible'
			);
			$sourcesReactionsCount[$id] = ($sourceThread->discussion_state == 'visible');
		}

		$reactionsEnable = [];
		$reactionsDisable = [];
		$userMessageCountAdjust = [];

		foreach ($this->sourcePosts AS $id => $post)
		{
			if ($post['message_state'] != 'visible')
			{
				continue; // everything will stay the same in the new thread
			}

			$sourceMessagesCount = $sourcesMessagesCount[$post['thread_id']];
			$sourceReactionsCount = $sourcesReactionsCount[$post['thread_id']];

			if ($post['reactions'])
			{
				if ($sourceReactionsCount && !$targetReactionsCount)
				{
					$reactionsDisable[] = $id;
				}
				else if (!$sourceReactionsCount && $targetReactionsCount)
				{
					$reactionsEnable[] = $id;
				}
			}

			$userId = $post['user_id'];
			if ($userId)
			{
				if ($sourceMessagesCount && !$targetMessagesCount)
				{
					if (!isset($userMessageCountAdjust[$userId]))
					{
						$userMessageCountAdjust[$userId] = 0;
					}
					$userMessageCountAdjust[$userId]--;
				}
				else if (!$sourceMessagesCount && $targetMessagesCount)
				{
					if (!isset($userMessageCountAdjust[$userId]))
					{
						$userMessageCountAdjust[$userId] = 0;
					}
					$userMessageCountAdjust[$userId]++;
				}
			}
		}

		if ($reactionsDisable)
		{
			/** @var \XF\Repository\Reaction $reactionRepo */
			$reactionRepo = $this->repository('XF:Reaction');
			$reactionRepo->fastUpdateReactionIsCounted('post', $reactionsDisable, false);
		}
		if ($reactionsEnable)
		{
			/** @var \XF\Repository\Reaction $reactionRepo */
			$reactionRepo = $this->repository('XF:Reaction');
			$reactionRepo->fastUpdateReactionIsCounted('post', $reactionsEnable, true);
		}
		foreach ($userMessageCountAdjust AS $userId => $adjust)
		{
			if ($adjust)
			{
				$this->db()->query("
					UPDATE xf_user
					SET message_count = GREATEST(0, message_count + ?)
					WHERE user_id = ?
				", [$adjust, $userId]);
			}
		}
	}

	protected function sendAlert()
	{
		$target = $this->target;
		$actor = \XF::visitor();

		/** @var \XF\Repository\Thread $threadRepo */
		$threadRepo = $this->repository('XF:Thread');

		$alertExtras = [
			'targetTitle' => $target->title,
			'targetLink' => $this->app->router('public')->buildLink('nopath:threads', $target)
		];

		foreach ($this->sourceThreads AS $sourceThread)
		{
			if ($sourceThread->discussion_state == 'visible'
				&& $sourceThread->user_id != $actor->user_id
				&& $sourceThread->discussion_type != 'redirect'
			)
			{
				$threadRepo->sendModeratorActionAlert($sourceThread, 'merge', $this->alertReason, $alertExtras);
			}
		}
	}

	protected function convertSourcesToRedirects()
	{
		$target = $this->target;

		/** @var \XF\Repository\ThreadRedirect $redirectRepo */
		$redirectRepo = $this->repository('XF:ThreadRedirect');

		foreach ($this->sourceThreads AS $sourceThread)
		{
			$sourceThread->discussion_type = 'redirect';
			$redirectRepo->createRedirectionRecordForThread($sourceThread, $target, $this->redirectLength, false);
			$sourceThread->save();
		}
	}

	protected function cleanUpSourceRedirects()
	{
		$db = $this->db();
		$sourceThreadIds = array_keys($this->sourceThreads);
		$sourceIdsQuoted = $db->quote($sourceThreadIds);

		$db->delete('xf_thread_watch', "thread_id IN ($sourceIdsQuoted)");
		$db->delete('xf_thread_reply_ban', "thread_id IN ($sourceIdsQuoted)");
		$db->delete('xf_thread_user_post', "thread_id IN ($sourceIdsQuoted)");
		$db->delete('xf_poll', "content_type = 'thread' AND content_id IN ($sourceIdsQuoted)");

		$this->app->search()->delete('thread', $sourceThreadIds);
	}

	protected function finalActions()
	{
		$target = $this->target;
		$sourceThreads = $this->sourceThreads;
		$sourceThreadIds = array_keys($sourceThreads);
		$postIds = array_keys($this->sourcePosts);

		if ($postIds)
		{
			$this->app->jobManager()->enqueue('XF:SearchIndex', [
				'content_type' => 'post',
				'content_ids' => $postIds
			]);
		}

		if ($this->log)
		{
			$this->app->logger()->logModeratorAction('thread', $target, 'merge_target',
				['ids' => implode(', ', $sourceThreadIds)]
			);
		}
	}
}