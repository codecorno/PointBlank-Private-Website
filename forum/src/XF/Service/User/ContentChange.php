<?php

namespace XF\Service\User;

use XF\Entity\User;

class ContentChange extends \XF\Service\AbstractService
{
	use \XF\MultiPartRunnerTrait;

	protected $originalUserId = 0;
	protected $originalUserName = '';

	protected $newUserId = null;
	protected $newUserName = null;

	protected $steps = [
		'stepReassignContent',
		'stepMergeThreadUserPost',
		'stepRebuildLikes',
		'stepRebuildReactions',
		'stepConversationRecipientCache',
		'stepApplyWarningGroupChanges',
		'stepReassignSearchIndex',
		'stepRebuildFinalCaches'
	];

	protected $updates = [
		'xf_admin_log' => ['user_id', 'emptyable' => false],
		'xf_attachment_data' => ['user_id'],
		'xf_ban_email' => ['create_user_id'],
		'xf_bookmark_item' => ['user_id'],
		'xf_bookmark_label' => ['user_id'],
		'xf_conversation_master' => [
			['user_id', 'username'],
			['last_message_user_id', 'last_message_username']
		],
		'xf_conversation_message' => ['user_id', 'username'],
		'xf_conversation_recipient' => ['user_id', 'emptyable' => false],
		'xf_conversation_user' => [
			['owner_user_id', 'emptyable' => false],
			['last_message_user_id', 'last_message_username']
		],
		'xf_deletion_log' => ['delete_user_id', 'delete_username'],
		'xf_edit_history' => ['edit_user_id'],
		'xf_error_log' => ['user_id'],
		'xf_feed' => ['user_id'],
		'xf_forum' => ['last_post_user_id', 'last_post_username'],
		'xf_forum_watch' => ['user_id', 'emptyable' => false],
		'xf_ip' => ['user_id', 'emptyable' => false],
		'xf_ip_match' => ['create_user_id'],
		'xf_reaction_content' => [
			['reaction_user_id'],
			['content_user_id']
		],
		'xf_moderator_log' => [
			['user_id'],
			['content_user_id', 'content_username']
		],
		'xf_news_feed' => ['user_id', 'username'],
		'xf_poll_vote' => ['user_id', 'emptyable' => false],
		'xf_post' => ['user_id', 'username'], // skip last edit user ID (performance reasons, minor benefit)
		'xf_profile_post' => [
			['profile_user_id', 'emptyable' => false],
			['user_id', 'username']
		],
		'xf_profile_post_comment' => ['user_id', 'username'],
		'xf_report' => [
			['content_user_id'],
			['assigned_user_id'],
			['last_modified_user_id', 'last_modified_username']
		],
		'xf_report_comment' => ['user_id', 'username'],
		'xf_spam_cleaner_log' => [
			['user_id', 'username'],
			['applying_user_id', 'applying_username']
		],
		'xf_spam_trigger_log' => ['user_id'],
		'xf_tag_content' => ['add_user_id'],
		'xf_thread' => [
			['user_id', 'username'],
			['last_post_user_id', 'last_post_username']
		],
		'xf_thread_reply_ban' => [
			['user_id', 'emptyable' => false],
			['ban_user_id']
		],
		'xf_thread_user_post' => ['user_id'], // this is combined with queries below to do a "merge"
		'xf_thread_watch' => ['user_id', 'emptyable' => false],
		'xf_user_alert' => ['user_id', 'username'],
		'xf_user_ban' => ['ban_user_id'],
		'xf_user_follow' => [
			['user_id', 'emptyable' => false],
			['follow_user_id', 'emptyable' => false]
		],
		'xf_user_ignored' => [
			['user_id', 'emptyable' => false],
			['ignored_user_id', 'emptyable' => false]
		],
		'xf_user_trophy' => ['user_id', 'emptyable' => false],
		'xf_user_upgrade_active' => ['user_id', 'emptyable' => false], // TODO: not merging change records, so can't really do this
		'xf_user_upgrade_expired' => ['user_id', 'emptyable' => false],
		'xf_warning' => [
			['user_id'],
			['warning_user_id']
		],
	];

	public function __construct(\XF\App $app, $originalUserId, $originalUserName = null)
	{
		parent::__construct($app);

		if ($originalUserId instanceof User)
		{
			if ($originalUserName === null)
			{
				$originalUserName = $originalUserId->username;
			}
			$originalUserId = $originalUserId->user_id;
		}

		if (!$originalUserName)
		{
			throw new \LogicException("Must provide an original username explicitly or a User entity");
		}

		$this->originalUserId = $originalUserId;
		$this->originalUserName = $originalUserName;

		$app->fire('user_content_change_init', [$this, &$this->updates]);
	}

	public function getOriginalUserId()
	{
		return $this->originalUserId;
	}

	public function getOriginalUserName()
	{
		return $this->originalUserName;
	}

	public function getNewUserId()
	{
		return $this->newUserId;
	}

	public function getNewUserName()
	{
		return $this->newUserName;
	}

	public function setupForDelete()
	{
		$this->newUserId = 0;
		$this->newUserName = $this->originalUserName; // ensure the values are correct

		return $this;
	}

	public function setupForNameChange($newUserName)
	{
		$this->newUserId = null;
		$this->newUserName = $newUserName;

		return $this;
	}

	public function setupForMerge(User $newUser)
	{
		$this->newUserId = $newUser->user_id;
		$this->newUserName = $newUser->username;

		return $this;
	}

	public function setupRaw($newUserId, $newUserName)
	{
		$this->newUserId = $newUserId;
		$this->newUserName = $newUserName;

		return $this;
	}

	protected function getSteps()
	{
		return $this->steps;
	}

	public function apply($maxRunTime = 0)
	{
		if ($this->newUserId === null && $this->newUserName === null)
		{
			// no work to do
			return \XF\ContinuationResult::completed();
		}

		$result = $this->runLoop($maxRunTime);

		return $result;
	}

	protected function stepReassignContent($lastOffset, $maxRunTime)
	{
		$db = $this->db();

		$originalUserId = $this->originalUserId;
		$newUserId = $this->newUserId;
		$newUserName = $this->newUserName;

		$lastOffset = $lastOffset === null ? -1 : $lastOffset;
		$thisOffset = -1;
		$start = microtime(true);

		foreach ($this->updates AS $table => $changes)
		{
			$thisOffset++;
			if ($thisOffset <= $lastOffset)
			{
				continue;
			}

			if (is_string($changes[0]))
			{
				// changes the simple ['user_id'] format to a consistent [['user_id']] format
				$changes = [$changes];
			}

			foreach ($changes AS $change)
			{
				$userIdColumn = $change[0];
				$userNameColumn = !empty($change[1]) ? $change[1] : null;

				$sqlUpdates = [];
				if ($newUserId !== null)
				{
					if (!$newUserId && isset($change['emptyable']) && !$change['emptyable'])
					{
						// trying to update user ID to 0 and this is set to be ignored
						continue;
					}

					$sqlUpdates[] = "`{$userIdColumn}` = " . $db->quote($newUserId);
				}
				if ($newUserName !== null && $userNameColumn)
				{
					$sqlUpdates[] = "`{$userNameColumn}` = " . $db->quote($newUserName);
				}
				if ($sqlUpdates)
				{
					$db->query("
						UPDATE IGNORE `{$table}` SET
							" . implode(', ', $sqlUpdates) . "
						WHERE `{$userIdColumn}` = ?
					", $originalUserId);
				}
			}

			$lastOffset = $thisOffset;
			if ($maxRunTime && microtime(true) - $start > $maxRunTime)
			{
				return $lastOffset; // continue at this position
			}
		}

		return null; // finished
	}

	protected function stepMergeThreadUserPost()
	{
		if ($this->newUserId === null)
		{
			return;
		}

		// merge the post counts here for accurate values
		$this->db()->beginTransaction();
		$this->db()->query("
			UPDATE xf_thread_user_post AS source, xf_thread_user_post AS target
			SET target.post_count = target.post_count + source.post_count
			WHERE source.user_id = ?
				AND source.thread_id = target.thread_id
				AND target.user_id = ?
		", [$this->originalUserId, $this->newUserId]);
		$this->db()->delete('xf_thread_user_post', 'user_id = ?', $this->originalUserId);
		$this->db()->commit();
	}

	protected function stepRebuildLikes($lastOffset, $maxRunTime)
	{
		$newLikeUserId = $this->newUserId !== null ? $this->newUserId : $this->originalUserId;
		$newLikeUserName = $this->newUserName !== null ? $this->newUserName : $this->originalUserName;

		$lastOffset = $lastOffset === null ? -1 : $lastOffset;
		$thisOffset = -1;
		$start = microtime(true);

		/** @var \XF\Repository\LikedContent $likeRepo */
		$likeRepo = $this->repository('XF:LikedContent');
		foreach ($likeRepo->getLikeHandlers() AS $contentType => $likeHandler)
		{
			$thisOffset++;
			if ($thisOffset <= $lastOffset)
			{
				continue;
			}

			$likeHandler->updateRecentCacheForUserChange(
				$this->originalUserId, $newLikeUserId,
				$this->originalUserName, $newLikeUserName
			);

			$lastOffset = $thisOffset;
			if ($maxRunTime && microtime(true) - $start > $maxRunTime)
			{
				return $lastOffset; // continue at this position
			}
		}

		return null;
	}

	protected function stepRebuildReactions($lastOffset, $maxRunTime)
	{
		$newReactionUserId = $this->newUserId !== null ? $this->newUserId : $this->originalUserId;
		$newReactionUserName = $this->newUserName !== null ? $this->newUserName : $this->originalUserName;

		$lastOffset = $lastOffset === null ? -1 : $lastOffset;
		$thisOffset = -1;
		$start = microtime(true);

		/** @var \XF\Repository\Reaction $reactionRepo */
		$reactionRepo = $this->repository('XF:Reaction');
		foreach ($reactionRepo->getReactionHandlers() AS $contentType => $reactionHandler)
		{
			$thisOffset++;
			if ($thisOffset <= $lastOffset)
			{
				continue;
			}

			$reactionHandler->updateRecentCacheForUserChange(
				$this->originalUserId, $newReactionUserId,
				$this->originalUserName, $newReactionUserName
			);

			$lastOffset = $thisOffset;
			if ($maxRunTime && microtime(true) - $start > $maxRunTime)
			{
				return $lastOffset; // continue at this position
			}
		}

		return null;
	}

	protected function stepConversationRecipientCache()
	{
		$newUserId = $this->newUserId !== null ? $this->newUserId : $this->originalUserId;
		$newUserName = $this->newUserName !== null ? $this->newUserName : $this->originalUserName;

		/** @var \XF\Repository\Conversation $convRepo */
		$convRepo = $this->repository('XF:Conversation');
		$convRepo->updateRecipientCacheForUserChange(
			$this->originalUserId, $newUserId,
			$this->originalUserName, $newUserName
		);
	}

	protected function stepApplyWarningGroupChanges()
	{
		$newUserId = $this->getNewUserId();
		if (!$newUserId)
		{
			// user has been deleted or it's just a name change, so no action needed
			return;
		}

		$userGroupChanges = $this->db()->fetchPairs("
			SELECT change_key, group_ids
			FROM xf_user_group_change
			WHERE user_id = ?
				AND change_key REGEXP '^warning_[0-9]+$'
		", $this->getOriginalUserId());

		foreach ($userGroupChanges AS $changeKey => $groupIds)
		{
			$userGroupChangeService = \XF::service('XF:User\UserGroupChange');
			$userGroupChangeService->addUserGroupChange($newUserId, $changeKey, $groupIds);
		}
	}

	protected function stepReassignSearchIndex()
	{
		if ($this->newUserId === null)
		{
			return;
		}

		$this->app->search()->reassignContent($this->originalUserId, $this->newUserId);
	}

	protected function stepRebuildFinalCaches()
	{
		if ($this->newUserId === null)
		{
			return;
		}

		$this->repository('XF:UserFollow')->rebuildFollowingCache($this->newUserId);
		$this->repository('XF:UserIgnored')->rebuildIgnoredCache($this->newUserId);
	}
}