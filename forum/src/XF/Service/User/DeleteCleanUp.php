<?php

namespace XF\Service\User;

class DeleteCleanUp extends \XF\Service\AbstractService
{
	use \XF\MultiPartRunnerTrait;

	protected $userId;
	protected $userName;

	protected $steps = [
		'stepDeleteContent',
		'stepDeleteProfilePosts',
		'stepDeleteBookmarks',
		'stepChangeOwner',
		'stepMiscCleanUp'
	];
	
	protected $deletes = [
		'xf_admin' => 'user_id = ?',
		'xf_admin_permission_entry' => 'user_id = ?',
		'xf_approval_queue' => "content_type = 'user' AND content_id = ?",
		'xf_change_log' => "content_type = 'user' AND content_id = ?",
		'xf_conversation_user' => 'owner_user_id = ?', // leave recipient record for others
		'xf_draft' => 'user_id = ?',
		'xf_email_bounce_soft' => 'user_id = ?',
		'xf_find_new_default' => 'user_id = ?',
		'xf_flood_check' => 'user_id = ?',
		'xf_forum_read' => 'user_id = ?',
		'xf_forum_watch' => 'user_id = ?',
		'xf_ip' => "content_type = 'user' AND content_id = ?", // leave content IPs
		'xf_moderator' => 'user_id = ?',
		'xf_moderator_content' => 'user_id = ?',
		'xf_notice_dismissed' => 'user_id = ? ',
		'xf_permission_combination' => 'user_id = ?',
		'xf_permission_entry' => 'user_id = ?',
		'xf_permission_entry_content' => 'user_id = ?',
		'xf_poll_vote' => 'user_id = ?',
		'xf_tfa_attempt' => 'user_id = ?',
		'xf_thread_read' => 'user_id = ?',
		'xf_thread_reply_ban' => 'user_id = ?',
		'xf_thread_user_post' => 'user_id = ?',
		'xf_thread_watch' => 'user_id = ?',
		'xf_user_alert' => 'alerted_user_id = ?',
		'xf_user_alert_optout' => 'user_id = ?',
		'xf_user_ban' => 'user_id = ?',
		'xf_user_change_temp' => 'user_id = ?',
		'xf_user_confirmation' => 'user_id = ?',
		'xf_user_connected_account' => 'user_id = ?',
		'xf_user_field_value' => 'user_id = ?',
		'xf_user_follow' => [
			'user_id = ?',
			'follow_user_id = ?'
		],
		'xf_user_group_change' => 'user_id = ?',
		'xf_user_group_promotion_log' => 'user_id = ?',
		'xf_user_group_relation' => 'user_id = ?',
		'xf_user_ignored' => [
			'user_id = ?',
			'ignored_user_id = ?'
		],
		'xf_user_reject' => 'user_id = ?',
		'xf_user_remember' => 'user_id = ?',
		'xf_user_tfa' => 'user_id = ?',
		'xf_user_tfa_trusted' => 'user_id = ?',
		'xf_user_trophy' => 'user_id = ?',
		'xf_user_upgrade_active' => 'user_id = ?',
		'xf_user_upgrade_expired' => 'user_id = ?',
		'xf_warning' => 'user_id = ?',
		'xf_warning_action_trigger' => 'user_id = ?',
	];

	// let some areas be cleaned up by cron: find_new, search, session_activity, tag_result_cache

	public function __construct(\XF\App $app, $userId, $userName)
	{
		parent::__construct($app);

		$this->userId = $userId;
		$this->userName = $userName;

		$app->fire('user_delete_clean_init', [$this, &$this->deletes]);
	}

	protected function getSteps()
	{
		return $this->steps;
	}

	public function cleanUp($maxRunTime = 0)
	{
		$result = $this->runLoop($maxRunTime);

		return $result;
	}

	protected function stepDeleteContent($lastOffset, $maxRunTime)
	{
		$db = $this->db();

		// we shouldn't get an array here but has been seen
		if (is_array($lastOffset))
		{
			$lastOffset = null;
		}

		$lastOffset = $lastOffset === null ? -1 : $lastOffset;
		$thisOffset = -1;
		$start = microtime(true);

		foreach ($this->deletes AS $table => $actions)
		{
			$thisOffset++;
			if ($thisOffset <= $lastOffset)
			{
				continue;
			}

			if (!is_array($actions))
			{
				$actions = [$actions];
			}

			foreach ($actions AS $action)
			{
				$db->delete($table, $action, $this->userId);
			}

			$lastOffset = $thisOffset;
			if ($maxRunTime && microtime(true) - $start > $maxRunTime)
			{
				return $lastOffset; // continue at this position
			}
		}

		return null;
	}

	protected function stepDeleteProfilePosts($lastOffset, $maxRunTime)
	{
		$start = microtime(true);

		/** @var \XF\Entity\ProfilePost[] $profilePosts */
		$finder = $this->finder('XF:ProfilePost')
			->where('profile_user_id', $this->userId)
			->order('profile_post_id');

		// we shouldn't get an array here but has been seen
		if (is_array($lastOffset))
		{
			$lastOffset = null;
		}

		if ($lastOffset !== null)
		{
			$finder->where('profile_post_id', '>', $lastOffset);
		}

		$maxFetch = 1000;
		$profilePosts = $finder->fetch($maxFetch);
		$fetchedProfilePosts = count($profilePosts);

		if (!$fetchedProfilePosts)
		{
			return null; // done or nothing to do
		}

		foreach ($profilePosts AS $profilePost)
		{
			$lastOffset = $profilePost->profile_post_id;

			$profilePost->setOption('log_moderator', false);
			$profilePost->delete();

			if ($maxRunTime && microtime(true) - $start > $maxRunTime)
			{
				return $lastOffset; // continue at this position
			}
		}

		if ($fetchedProfilePosts == $maxFetch)
		{
			return $lastOffset; // more to do
		}
		else
		{
			return null;
		}
	}

	protected function stepDeleteBookmarks($lastOffset, $maxRunTime)
	{
		$start = microtime(true);

		/** @var \XF\Entity\BookmarkItem[] $bookmarks */
		$finder = $this->finder('XF:BookmarkItem')
			->where('user_id', $this->userId)
			->order('bookmark_id');

		// we shouldn't get an array here but has been seen
		if (is_array($lastOffset))
		{
			$lastOffset = null;
		}
		
		if ($lastOffset !== null)
		{
			$finder->where('bookmark_id', '>', $lastOffset);
		}

		$maxFetch = 1000;
		$bookmarks = $finder->fetch($maxFetch);
		$fetchedBookmarks = count($bookmarks);

		if (!$bookmarks)
		{
			return null; // done or nothing to do
		}

		foreach ($bookmarks AS $bookmark)
		{
			$lastOffset = $bookmark->bookmark_id;

			$bookmark->delete();

			if ($maxRunTime && microtime(true) - $start > $maxRunTime)
			{
				return $lastOffset; // continue at this position
			}
		}

		if ($fetchedBookmarks == $maxFetch)
		{
			return $lastOffset; // more to do
		}
		else
		{
			return null;
		}
	}

	protected function stepChangeOwner($lastOffset, $maxRunTime)
	{
		/** @var \XF\Service\User\ContentChange $contentChanger */
		$contentChanger = $this->service('XF:User\ContentChange', $this->userId, $this->userName);
		$contentChanger->setupForDelete();

		if (is_array($lastOffset))
		{
			list($changeStep, $changeLastOffset) = $lastOffset;
			$contentChanger->restoreState($changeStep, $changeLastOffset);
		}

		$result = $contentChanger->apply($maxRunTime);
		if ($result->isCompleted())
		{
			return null;
		}
		else
		{
			$continueData = $result->getContinueData();
			return [$continueData['currentStep'], $continueData['lastOffset']];
		}
	}

	protected function stepMiscCleanUp()
	{
		if ($this->userId)
		{
			// note: there's no reason for this to be 0, but if it were, this would delete entries it shouldn't
			$keys = $this->em()->getFinder('XF:ApiKey')->where('user_id', $this->userId)->fetch();
			/** @var \XF\Entity\ApiKey $key */
			foreach ($keys AS $key)
			{
				$key->delete();
			}
		}

		// it's worth keeping these records, but disassociate them
		$this->db()->update('xf_email_bounce_log', ['user_id' => 0], 'user_id = ?', $this->userId);

		/** @var \XF\Repository\ApprovalQueue $approvalRepo */
		$approvalRepo = $this->repository('XF:ApprovalQueue');
		$approvalRepo->rebuildUnapprovedCounts();
	}
}