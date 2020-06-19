<?php

namespace XF\Service\Post;

use XF\Entity\Post;
use XF\Entity\User;

class Merger extends \XF\Service\AbstractService
{
	/**
	 * @var Post
	 */
	protected $target;

	protected $originalTargetMessage;

	/**
	 * @var \XF\Service\Post\Preparer
	 */
	protected $postPreparer;

	protected $alert = false;
	protected $alertReason = '';

	protected $log = true;

	/**
	 * @var \XF\Entity\Thread[]
	 */
	protected $sourceThreads = [];

	/**
	 * @var \XF\Entity\Post[]
	 */
	protected $sourcePosts = [];

	public function __construct(\XF\App $app, Post $target)
	{
		parent::__construct($app);

		$this->target = $target;
		$this->originalTargetMessage = $target->message;
		$this->postPreparer = $this->service('XF:Post\Preparer', $this->target);
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

	public function setLog($log)
	{
		$this->log = (bool)$log;
	}

	public function setMessage($message, $format = true)
	{
		return $this->postPreparer->setMessage($message, $format);
	}

	public function merge($sourcePostsRaw)
	{
		if ($sourcePostsRaw instanceof \XF\Mvc\Entity\AbstractCollection)
		{
			$sourcePostsRaw = $sourcePostsRaw->toArray();
		}
		else if ($sourcePostsRaw instanceof Post)
		{
			$sourcePostsRaw = [$sourcePostsRaw];
		}
		else if (!is_array($sourcePostsRaw))
		{
			throw new \InvalidArgumentException('Posts must be provided as collection, array or entity');
		}

		if (!$sourcePostsRaw)
		{
			return false;
		}

		$db = $this->db();

		/** @var Post[] $sourcePosts */
		$sourcePosts = [];

		/** @var \XF\Entity\Thread[] $sourceThreads */
		$sourceThreads = [];

		foreach ($sourcePostsRaw AS $sourcePost)
		{
			$sourcePost->setOption('log_moderator', false);
			$sourcePosts[$sourcePost->post_id] = $sourcePost;

			/** @var \XF\Entity\Thread $sourceThread */
			$sourceThread = $sourcePost->Thread;
			if (!isset($sourceThreads[$sourceThread->thread_id]))
			{
				$sourceThread->setOption('log_moderator', false);
				$sourceThreads[$sourceThread->thread_id] = $sourceThread;
			}
		}

		$this->sourceThreads = $sourceThreads;
		$this->sourcePosts = $sourcePosts;

		$target = $this->target;
		$target->setOption('log_moderator', false);

		$db->beginTransaction();

		$this->moveDataToTarget();
		$this->updateTargetData();
		$this->updateSourceData();
		$this->updateUserCounters();

		if ($this->alert)
		{
			$this->sendAlert();
		}

		$this->finalActions();

		$target->save();

		$db->commit();

		return true;
	}

	protected function moveDataToTarget()
	{
		$db = $this->db();
		$target = $this->target;

		$sourcePosts = $this->sourcePosts;
		$sourcePostIds = array_keys($sourcePosts);
		$sourceIdsQuoted = $db->quote($sourcePostIds);

		$rows = $db->update('xf_attachment',
			['content_id' => $target->post_id],
			"content_id IN ($sourceIdsQuoted) AND content_type = 'post'"
		);

		$target->attach_count += $rows;

		$db->update(
			'xf_bookmark_item',
			[
				'content_type' => 'post',
				'content_id' => $target->post_id
			],
			"content_id IN ($sourceIdsQuoted) AND content_type = 'post'",
			[], 'IGNORE'
		);

		foreach ($sourcePosts AS $sourcePost)
		{
			$sourcePost->delete();
		}
	}

	protected function updateTargetData()
	{
		/** @var \XF\Entity\Thread $targetThread */
		$targetThread = $this->target->Thread;

		$targetThread->rebuildCounters();
		$targetThread->save();

		/** @var \XF\Repository\Thread $threadRepo */
		$threadRepo = $this->repository('XF:Thread');
		$threadRepo->rebuildThreadPostPositions($targetThread->thread_id);
		$threadRepo->rebuildThreadUserPostCounters($targetThread->thread_id);
	}

	protected function updateSourceData()
	{
		/** @var \XF\Repository\Thread $threadRepo */
		$threadRepo = $this->repository('XF:Thread');

		foreach ($this->sourceThreads AS $sourceThread)
		{
			$sourceThread->rebuildCounters();

			$sourceThread->save(); // has to be saved for the delete to work (if needed).

			if (array_key_exists($sourceThread->first_post_id, $this->sourcePosts) && $sourceThread->reply_count == 0)
			{
				$sourceThread->delete(); // first post has been moved out, no other replies, thread now empty
			}
			else
			{
				$threadRepo->rebuildThreadPostPositions($sourceThread->thread_id);
				$threadRepo->rebuildThreadUserPostCounters($sourceThread->thread_id);
			}

			$sourceThread->Forum->rebuildCounters();
			$sourceThread->Forum->save();
		}
	}

	protected function updateUserCounters()
	{
		$target = $this->target;
		$targetThread = $target->Thread;

		$targetMessagesCount = (
			$targetThread->Forum && $targetThread->Forum->count_messages
			&& $targetThread->discussion_state == 'visible'
		);
		$targetReactionsCount = ($targetThread->discussion_state == 'visible');

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

			$sourceMessagesCount = $sourcesMessagesCount[$post->thread_id];
			$sourceReactionsCount = $sourcesReactionsCount[$post->thread_id];

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

			$userId = $post->user_id;
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
		/** @var \XF\Repository\Post $postRepo */
		$postRepo = $this->repository('XF:Post');

		$alerted = [];
		foreach ($this->sourcePosts AS $sourcePost)
		{
			if (isset($alerted[$sourcePost->user_id]))
			{
				continue;
			}

			if ($sourcePost->message_state == 'visible' && $sourcePost->user_id != \XF::visitor()->user_id)
			{
				$postRepo->sendModeratorActionAlert($sourcePost, 'merge', $this->alertReason);
				$alerted[$sourcePost->user_id] = true;
			}
		}
	}

	protected function finalActions()
	{
		$target = $this->target;
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
			$this->app->logger()->logModeratorAction('post', $target, 'merge_target',
				['ids' => implode(', ', $postIds)]
			);
		}

		$preEditMergeMessage = $this->originalTargetMessage;
		foreach ($this->sourcePosts AS $s)
		{
			$preEditMergeMessage .= "\n\n" . $s->message;
		}
		$preEditMergeMessage = trim($preEditMergeMessage);

		$options = $this->app->options();
		if ($options->editLogDisplay['enabled'] && $this->log && $target->message != $preEditMergeMessage)
		{
			$target->last_edit_date = \XF::$time;
			$target->last_edit_user_id = \XF::visitor()->user_id;
		}

		if ($options->editHistory['enabled'])
		{
			$visitor = \XF::visitor();
			$ip = $this->app->request()->getIp();

			/** @var \XF\Repository\EditHistory $editHistoryRepo */
			$editHistoryRepo = $this->app->repository('XF:EditHistory');

			// Log an edit history record for the target post's original message then log a further record
			// for the pre-merge result of all the source and target messages. These two entries should ensure
			// there is no context loss as a result of merging a series of posts.
			$editHistoryRepo->insertEditHistory('post', $target, $visitor, $this->originalTargetMessage, $ip);
			$target->edit_count++;

			if ($target->message != $preEditMergeMessage)
			{
				$editHistoryRepo->insertEditHistory('post', $target, $visitor, $preEditMergeMessage, $ip);
				$target->edit_count++;
			}
		}
	}
}