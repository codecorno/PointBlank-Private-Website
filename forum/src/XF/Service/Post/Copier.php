<?php

namespace XF\Service\Post;

use XF\Entity\Post;
use XF\Entity\Thread;

class Copier extends \XF\Service\AbstractService
{
	/**
	 * @var Thread
	 */
	protected $target;

	protected $existingTarget = false;

	protected $alert = false;
	protected $alertReason = '';

	protected $prefixId = null;

	protected $log = true;

	/**
	 * @var Thread[]
	 */
	protected $sourceThreads = [];

	/**
	 * @var Post[]
	 */
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

	public function setExistingTarget($existing)
	{
		$this->existingTarget = (bool)$existing;
	}

	public function setLog($log)
	{
		$this->log = (bool)$log;
	}

	public function setSendAlert($alert, $reason = null)
	{
		$this->alert = (bool)$alert;
		if ($reason !== null)
		{
			$this->alertReason = $reason;
		}
	}

	public function setPrefix($prefixId)
	{
		$this->prefixId = ($prefixId === null ? $prefixId : intval($prefixId));
	}

	public function copy($sourcePostsRaw)
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
		/** @var Thread[] $sourceThreads */
		$sourcePosts = [];
		$sourceThreads = [];

		foreach ($sourcePostsRaw AS $sourcePost)
		{
			$sourcePost->setOption('log_moderator', false);
			$sourcePosts[$sourcePost->post_id] = $sourcePost;

			/** @var Thread $sourceThread */
			$sourceThread = $sourcePost->Thread;
			if (!isset($sourceThreads[$sourceThread->thread_id]))
			{
				$sourceThread->setOption('log_moderator', false);
				$sourceThreads[$sourceThread->thread_id] = $sourceThread;
			}
		}

		$sourcePosts = \XF\Util\Arr::columnSort($sourcePosts, 'post_date');

		$this->sourceThreads = $sourceThreads;
		$this->sourcePosts = $sourcePosts;

		$target = $this->target;
		$target->setOption('log_moderator', false);

		if (!$target->thread_id)
		{
			$firstPost = reset($sourcePosts);

			$target->user_id = $firstPost->user_id;
			$target->username = $firstPost->username;
			$target->post_date = $firstPost->post_date;
		}

		$db->beginTransaction();

		$target->save();

		$this->copyDataToTarget();
		$this->updateTargetData();

		if ($this->alert)
		{
			$this->sendAlert();
		}

		$this->finalActions();

		$db->commit();

		return true;
	}

	protected function copyDataToTarget()
	{
		$resetValues = $this->getResetPostData();

		$position = 0;
		$firstPost = $this->existingTarget ? $this->target->FirstPost : null;

		// posts are sorted in date order
		foreach ($this->sourcePosts AS $sourcePost)
		{
			/** @var \XF\Entity\Post $newPost */
			$newPost = $this->em()->create('XF:Post');

			$values = $sourcePost->toArray(false);
			foreach ($resetValues AS $key)
			{
				unset($values[$key]);
			}

			$newPost->thread_id = $this->target->thread_id;
			$newPost->bulkSet($values);

			$newPost->position = $position;
			if (!$firstPost)
			{
				// first post is always visible, set $firstPost later
				$this->target->discussion_state = $newPost->message_state;
				$newPost->message_state = 'visible';
			}

			$newPost->save();

			if (!$firstPost)
			{
				$firstPost = $newPost;

				$this->target->fastUpdate('first_post_id', $newPost->post_id);
			}

			$embedMetadata = $sourcePost->embed_metadata;
			$newPost->embed_metadata = $this->updateEmbeds($sourcePost, $newPost, $embedMetadata ?: []);
			$newPost->saveIfChanged();

			if ($newPost->message_state == 'visible')
			{
				$position++;
			}
		}
	}

	protected function getResetPostData()
	{
		return  [
			'post_id',
			'thread_id',
			'position',
			'reaction_score',
			'reactions',
			'reaction_users',
			'warning_id',
			'warning_message',
			'last_edit_date',
			'last_edit_user_id',
			'edit_count'
		];
	}

	protected function updateEmbeds(Post $sourcePost, Post $newPost, array $embedMetadata)
	{
		$attachEmbed = isset($embedMetadata['attachments']) ? $embedMetadata['attachments'] : [];

		foreach ($sourcePost->Attachments AS $sourceAttachment)
		{
			/** @var \XF\Entity\Attachment $newAttachment */
			$newAttachment = $this->em()->create('XF:Attachment');

			$values = $sourceAttachment->toArray(false);
			unset($values['attachment_id'], $values['content_id'], $values['view_count']);

			$newAttachment->content_id = $newPost->post_id;
			$newAttachment->bulkSet($values);
			$newAttachment->save();

			$newPost->message = preg_replace(
				'#(\[attach(=[^]]+)?\])' . $sourceAttachment->attachment_id . '(\[/attach\])#i',
				'${1}' . $newAttachment->attachment_id . '${3}',
				$newPost->message
			);

			if (isset($attachEmbed[$sourceAttachment->attachment_id]))
			{
				unset($attachEmbed[$sourceAttachment->attachment_id]);
				$attachEmbed[$newAttachment->attachment_id] = $newAttachment->attachment_id;
			}
		}

		if ($attachEmbed)
		{
			$embedMetadata['attachments'] = $attachEmbed;
		}
		else
		{
			unset($embedMetadata['attachments']);
		}

		return $embedMetadata;
	}

	protected function updateTargetData()
	{
		$target = $this->target;

		if ($this->prefixId !== null)
		{
			$target->prefix_id = $this->prefixId;
		}
		$target->rebuildCounters();
		$target->save();

		$target->Forum->rebuildCounters();
		$target->Forum->save();

		/** @var \XF\Repository\Thread $threadRepo */
		$threadRepo = $this->repository('XF:Thread');
		$threadRepo->rebuildThreadPostPositions($target->thread_id);
		$threadRepo->rebuildThreadUserPostCounters($target->thread_id);
	}

	protected function sendAlert()
	{
		$target = $this->target;

		/** @var \XF\Repository\Post $postRepo */
		$postRepo = $this->repository('XF:Post');

		foreach ($this->sourcePosts AS $sourcePost)
		{
			if ($sourcePost->Thread->discussion_state == 'visible'
				&& $sourcePost->message_state == 'visible'
				&& $sourcePost->user_id != \XF::visitor()->user_id
			)
			{
				$alertExtras = [
					'targetTitle' => $target->title,
					'targetLink' => $this->app->router('public')->buildLink('nopath:posts', $sourcePost)
				];

				$postRepo->sendModeratorActionAlert($sourcePost, 'copy', $this->alertReason, $alertExtras);
			}
		}
	}

	protected function finalActions()
	{
		$target = $this->target;
		$postIds = array_keys($this->sourcePosts);

		if ($this->log)
		{
			$this->app->logger()->logModeratorAction('thread', $target, 'post_copy_target'  . ($this->existingTarget ? '_existing' : ''),
				['ids' => implode(', ', $postIds)]
			);

			foreach ($this->sourceThreads AS $sourceThread)
			{
				$this->app->logger()->logModeratorAction('thread', $sourceThread, 'post_copy_source', [
					'url' => $this->app->router('public')->buildLink('nopath:threads', $sourceThread),
					'title' => $target->title
				]);
			}
		}
	}
}