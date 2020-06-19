<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null user_id
 * @property string username
 * @property string email
 * @property int style_id
 * @property int language_id
 * @property string timezone
 * @property bool visible
 * @property bool activity_visible
 * @property int user_group_id
 * @property array secondary_group_ids
 * @property int display_style_group_id
 * @property int permission_combination_id_
 * @property int message_count
 * @property int alerts_unread
 * @property int conversations_unread
 * @property int register_date
 * @property int last_activity_
 * @property int trophy_points
 * @property int avatar_date
 * @property int avatar_width
 * @property int avatar_height
 * @property bool avatar_highdpi
 * @property string gravatar
 * @property string user_state
 * @property bool is_moderator
 * @property bool is_admin
 * @property bool is_staff
 * @property bool is_banned
 * @property int reaction_score
 * @property string custom_title
 * @property int warning_points
 * @property string secret_key
 * @property int privacy_policy_accepted
 * @property int terms_accepted
 *
 * GETTERS
 * @property \XF\PermissionSet PermissionSet
 * @property int permission_combination_id
 * @property bool is_super_admin
 * @property int last_activity
 * @property string email_confirm_key
 * @property int warning_count
 *
 * RELATIONS
 * @property \XF\Entity\Admin Admin
 * @property \XF\Entity\UserAuth Auth
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\UserConnectedAccount[] ConnectedAccounts
 * @property \XF\Entity\UserOption Option
 * @property \XF\Entity\PermissionCombination PermissionCombination
 * @property \XF\Entity\UserProfile Profile
 * @property \XF\Entity\UserPrivacy Privacy
 * @property \XF\Entity\UserBan Ban
 * @property \XF\Entity\UserReject Reject
 * @property \XF\Entity\SessionActivity Activity
 * @property \XF\Entity\ApprovalQueue ApprovalQueue
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\UserFollow[] Following
 */
class User extends Entity
{
	const GROUP_GUEST = 1;
	const GROUP_REG = 2;
	const GROUP_ADMIN = 3;
	const GROUP_MOD = 4;

	/************************* HELPERS & GETTERS ***************************/

	public function getAbstractedCustomAvatarPath($size)
	{
		$userId = $this->user_id;

		return sprintf('data://avatars/%s/%d/%d.jpg',
			$size,
			floor($userId / 1000),
			$userId
		);
	}

	public function getAvatarType()
	{
		if ($this->gravatar)
		{
			return 'gravatar';
		}
		else if ($this->avatar_date)
		{
			return 'custom';
		}
		else
		{
			return 'default';
		}
	}

	public function getAvatarUrl($sizeCode, $forceType = null, $canonical = false)
	{
		$app = $this->app();

		$sizeMap = $app->container('avatarSizeMap');
		if (!isset($sizeMap[$sizeCode]))
		{
			// Always fallback to 's' in the event of an unknown size (e.g. 'xs', 'xxs' etc.)
			$sizeCode = 's';
		}

		if ($this->gravatar && $forceType != 'custom')
		{
			return $this->getGravatarUrl($sizeCode);
		}
		else if ($this->avatar_date)
		{
			$group = floor($this->user_id / 1000);
			return $app->applyExternalDataUrl(
				"avatars/{$sizeCode}/{$group}/{$this->user_id}.jpg?{$this->avatar_date}",
				$canonical
			);
		}
		else
		{
			return null;
		}
	}

	public function getAvatarUrl2x($sizeCode, $forceType = null, $canonical = false)
	{
		$sizeMap = $this->app()->container('avatarSizeMap');

		switch ($sizeCode)
		{
			case 'xs':
			case 's':
				if ($this->avatarSupportsSize($sizeMap['m']))
				{
					return $this->getAvatarUrl('m', $forceType, $canonical);
				}
				break;

			case 'm':
				if ($this->avatarSupportsSize($sizeMap['l']))
				{
					return $this->getAvatarUrl('l', $forceType, $canonical);
				}
				break;

			case 'l':
				if ($this->avatar_highdpi || $this->gravatar)
				{
					return $this->getAvatarUrl('h', $forceType, $canonical);
				}
				break;
		}

		return '';
	}

	protected function avatarSupportsSize($size)
	{
		if ($this->gravatar)
		{
			// We can always support 2x gravatars
			return true;
		}
		return ($this->avatar_date && $this->avatar_width >= $size && $this->avatar_height >= $size);
	}

	public function getGravatarUrl($sizeCode, $email = null)
	{
		$sizeMap = $this->app()->container('avatarSizeMap');
		if (!isset($sizeMap[$sizeCode]))
		{
			$sizeCode = 's';
		}

		if ($email === null)
		{
			$email = $this->gravatar ?: $this->email;
		}

		$md5 = md5(strtolower(trim($email)));
		$size = $sizeMap[$sizeCode];

		return "https://secure.gravatar.com/avatar/{$md5}?s={$size}";
	}

	public function isMemberOf($userGroupId)
	{
		if ($userGroupId instanceof UserGroup)
		{
			$userGroupId = $userGroupId->user_group_id;
		}

		if (!$userGroupId)
		{
			return false;
		}

		if (is_array($userGroupId))
		{
			if (
				in_array($this->user_group_id, $userGroupId)
				|| array_intersect($userGroupId, $this->secondary_group_ids)
			)
			{
				return true;
			}
		}
		else
		{
			if ($this->user_group_id == $userGroupId || in_array($userGroupId, $this->secondary_group_ids))
			{
				return true;
			}
		}

		return false;
	}

	public function isOnline()
	{
		if (!$this->canViewOnlineStatus())
		{
			return false;
		}

		$onlineCutOff = time() - $this->app()->options()->onlineStatusTimeout * 60;
		return ($this->user_id == \XF::visitor()->user_id || ($this->Activity && $this->Activity->view_date > $onlineCutOff));
	}

	/**
	 * Determines if links posted by this user should be considered trusted. For example, this will mean that the
	 * links are "follow" (rather than "nofollow").
	 *
	 * @return bool
	 */
	public function isLinkTrusted()
	{
		return $this->is_staff;
	}

	public function authenticate($password)
	{
		if (!$this->Auth)
		{
			return false;
		}
		return $this->Auth->authenticate($password);
	}

	public function getIp($type)
	{
		/** @var \XF\Repository\Ip $ipRepo */
		$ipRepo = $this->repository('XF:Ip');

		return $ipRepo->getLoggedIp('user', $this->user_id, $type);
	}

	public function getSharedIpUsers($logDays)
	{
		/** @var \XF\Repository\Ip $ipRepo */
		$ipRepo = $this->repository('XF:Ip');

		return $ipRepo->getSharedIpUsers($this->user_id, $logDays);
	}

	public function getSpamDetails()
	{
		/** @var \XF\Repository\Spam $spamRepo */
		$spamRepo = $this->repository('XF:Spam');

		$spamTriggerLogsFinder = $spamRepo->findSpamTriggerLogs()->forContent('user', $this->user_id);

		return $spamTriggerLogsFinder->fetch()->pluckNamed('details');
	}

	public function hasAdminPermission($permissionId)
	{
		if (!$this->is_admin || !$this->Admin)
		{
			return false;
		}

		/** @var \XF\Entity\Admin $admin */
		$admin = $this->Admin;
		return $admin->hasAdminPermission($permissionId);
	}

	/**
	 * @return int
	 */
	public function getLastActivity()
	{
		return ($this->Activity && $this->Activity->view_date ? $this->Activity->view_date : $this->last_activity_);
	}

	/**
	 * @return bool
	 */
	public function getIsSuperAdmin()
	{
		if ($this->is_admin && $this->Admin)
		{
			return $this->Admin->is_super_admin;
		}

		return false;
	}

	/**
	 * @return \XF\PermissionSet
	 */
	public function getPermissionSet()
	{
		return \XF::permissionCache()->getPermissionSet($this->permission_combination_id);
	}

	/**
	 * @return int
	 */
	public function getPermissionCombinationId()
	{
		return $this->user_state == 'valid'
			? $this->getValue('permission_combination_id')
			: \XF\Repository\User::$guestPermissionCombinationId;
	}

	/**
	 * @return string
	 */
	public function getEmailConfirmKey()
	{
		return hash_hmac('md5', $this->user_id . $this->email, $this->secret_key);
	}

	/**
	 * @return int
	 */
	public function getWarningCount()
	{
		/** @var \XF\Repository\Warning $warningRepo */
		$warningRepo = $this->repository('XF:Warning');
		return $warningRepo->findUserWarningsForList($this->user_id)->total();
	}

	public function canEdit()
	{
		if (!$this->exists())
		{
			return false;
		}

		$visitor = \XF::visitor();

		if (!$visitor->is_super_admin && $this->is_super_admin)
		{
			return false;
		}

		if ($visitor->is_admin && $visitor->hasAdminPermission('user'))
		{
			return true;
		}

		if ($this->is_admin && $this->is_moderator && $this->is_staff)
		{
			// moderators can't edit admins/mods/staff
			return false;
		}

		return $visitor->hasPermission('general', 'editBasicProfile');
	}

	public function canBan(&$error = null)
	{
		$visitor = \XF::visitor();

		if (!$this->user_id || !$visitor->is_moderator || $this->user_id == $visitor->user_id)
		{
			return false;
		}

		if ($this->is_admin || $this->is_moderator)
		{
			$error = \XF::phraseDeferred('this_user_is_an_admin_or_moderator_choose_another');
			return false;
		}

		return $visitor->hasPermission('general', 'banUser');
	}

	public function canCleanSpam()
	{
		return $this->hasPermission('general', 'cleanSpam');
	}

	public function isPossibleSpammer(&$error = null)
	{
		// guest
		if (!$this->user_id)
		{
			return false;
		}

		// self
		if ($this->user_id == \XF::visitor()->user_id)
		{
			$error = \XF::phraseDeferred('sorry_dave');
			return false;
		}

		// staff
		if ($this->is_admin || $this->is_moderator)
		{
			$error = \XF::phraseDeferred('spam_cleaner_no_admins_or_mods');
			return false;
		}

		$criteria = $this->app()->options()->spamUserCriteria;

		if ($criteria['message_count'] && $this->message_count > $criteria['message_count'])
		{
			$error = \XF::phraseDeferred('spam_cleaner_too_many_messages', ['message_count' => $criteria['message_count']]);
			return false;
		}

		if ($criteria['register_date'] && $this->register_date < (time() - $criteria['register_date'] * 86400))
		{
			$error = \XF::phraseDeferred('spam_cleaner_registered_too_long', ['register_days' => $criteria['register_date']]);
			return false;
		}

		if ($criteria['reaction_score'] && $this->reaction_score > $criteria['reaction_score'])
		{
			$error = \XF::phraseDeferred('spam_cleaner_reaction_score_too_high', ['reaction_score' => $criteria['reaction_score']]);
			return false;
		}

		return true;
	}

	public function isSpamCheckRequired()
	{
		return (
			!$this->is_admin
			&& !$this->is_moderator
			&& $this->app()->options()->maxContentSpamMessages
			&& !$this->hasPermission('general', 'bypassSpamCheck')
			&& $this->message_count < $this->app()->options()->maxContentSpamMessages
		);
	}

	public function canViewOnlineStatus()
	{
		$visitor = \XF::visitor();

		if ($this->user_state == 'disabled')
		{
			return false;
		}
		if (!$this->last_activity)
		{
			return false;
		}
		if ($this->visible || $this->user_id == $visitor->user_id)
		{
			return true;
		}

		return $visitor->canBypassUserPrivacy();
	}

	public function canViewCurrentActivity()
	{
		$visitor = \XF::visitor();

		if (!$this->last_activity)
		{
			return false;
		}
		if (($this->visible && $this->activity_visible) || $this->user_id == $visitor->user_id)
		{
			return true;
		}

		return $visitor->canBypassUserPrivacy();
	}

	public function canViewBookmarks()
	{
		return $this->user_id && $this->hasPermission('bookmark', 'view');
	}

	public function isWarnable()
	{
		return !$this->is_admin && !$this->is_moderator;
	}

	public function canWarn(&$error = null)
	{
		$visitor = \XF::visitor();

		if (!$visitor->user_id || $this->user_id == $visitor->user_id)
		{
			return false;
		}

		return $this->isWarnable() && $visitor->hasPermission('general', 'warn');
	}

	public function canViewWarnings()
	{
		return ($this->user_id && $this->hasPermission('general', 'viewWarning'));
	}

	public function canApproveRejectUser()
	{
		return $this->is_moderator && $this->hasPermission('general', 'approveRejectUser');
	}

	public function canEditProfile()
	{
		return $this->exists() && $this->hasPermission('general', 'editProfile');
	}

	public function canEditSignature()
	{
		return ($this->exists()
			&& $this->hasPermission('general', 'editSignature')
			&& $this->hasPermission('signature', 'maxPrintable') != 0
			&& $this->hasPermission('signature', 'maxLines') != 0);
	}

	public function canBypassUserPrivacy()
	{
		return $this->exists() && $this->hasPermission('general', 'bypassUserPrivacy');
	}

	public function isPrivacyCheckMet($privacyKey, User $user)
	{
		if (!$this->Privacy)
		{
			return true;
		}

		/** @var UserPrivacy $privacy */
		$privacy = $this->Privacy;
		return $privacy->isPrivacyCheckMet($privacyKey, $user);
	}

	public function canSearch(&$error = null)
	{
		return $this->hasPermission('general', 'search') && $this->app()->options()->enableSearch;
	}

	public function canChangeLanguage(&$error = null)
	{
		return (bool)(count($this->app()->container('language.cache')) > 1);
	}

	public function canChangeStyle(&$error = null)
	{
		$styles = array_filter($this->app()->container('style.cache'), function($style)
		{
			return ($this->is_admin || $style['user_selectable']);
		});
		return (bool)(count($styles) > 1);
	}

	public function canViewMemberList()
	{
		return $this->hasPermission('general', 'viewMemberList');
	}

	public function canReport(&$error = null)
	{
		if (!$this->user_id || !$this->hasPermission('general', 'report'))
		{
			$error = \XF::phraseDeferred('you_may_not_report_this_content');
			return false;
		}

		return true;
	}

	public function canBeReported(&$error = null)
	{
		if ($this->is_staff)
		{
			return false;
		}

		return \XF::visitor()->canReport($error);
	}

	public function canViewFullProfile(&$error = null)
	{
		$visitor = \XF::visitor();
		if ($visitor->user_id == $this->user_id)
		{
			return true;
		}

		if (!$visitor->hasPermission('general', 'viewProfile'))
		{
			return false;
		}

		if (!$this->isPrivacyCheckMet('allow_view_profile', $visitor))
		{
			$error = \XF::phraseDeferred('member_limits_viewing_profile');
			return false;
		}

		if (
			($this->user_state == 'moderated' || $this->user_state == 'email_confirm' || $this->user_state == 'rejected')
			&& !$visitor->canBypassUserPrivacy()
		)
		{
			$error = \XF::phraseDeferred('this_users_profile_is_not_available');
			return false;
		}

		if ($this->is_banned)
		{
			/** @var UserBan|null $ban */
			$ban = $this->Ban;
			if ($ban && !$ban->end_date && !$visitor->canBypassUserPrivacy())
			{
				$error = \XF::phraseDeferred('this_users_profile_is_not_available');
				return false;
			}
		}

		return true;
	}

	public function canViewBasicProfile(&$error = null)
	{
		return true;
	}

	public function canViewProfilePosts(&$error = null)
	{
		return $this->hasPermission('general', 'viewProfile') && $this->hasPermission('profilePost', 'view');
	}

	public function canViewPostsOnProfile(&$error = null)
	{
		return $this->canViewFullProfile() && \XF::visitor()->hasPermission('profilePost', 'view');
	}

	public function canViewDeletedPostsOnProfile()
	{
		return \XF::visitor()->hasPermission('profilePost', 'viewDeleted');
	}

	public function canViewModeratedPostsOnProfile()
	{
		return \XF::visitor()->hasPermission('profilePost', 'viewModerated');
	}

	public function canPostOnProfile()
	{
		$visitor = \XF::visitor();

		return ($visitor->user_id
			&& $visitor->hasPermission('profilePost', 'view')
			&& $visitor->hasPermission('profilePost', 'post')
			&& ($this->user_id == $visitor->user_id || $this->isPrivacyCheckMet('allow_post_profile', $visitor))
			&& $this->user_state != 'disabled'
		);
	}

	public function canViewLatestActivity()
	{
		$visitor = \XF::visitor();

		if (!$this->app()->options()->enableNewsFeed)
		{
			return false;
		}

		if ($visitor->canBypassUserPrivacy())
		{
			return true;
		}

		return (
			$this->isPrivacyCheckMet('allow_receive_news_feed', $visitor)
			&& $this->user_state != 'disabled'
		);
	}

	public function canViewIdentities()
	{
		$visitor = \XF::visitor();

		if (!$visitor->hasPermission('general', 'viewProfile'))
		{
			return false;
		}

		if ($visitor->canBypassUserPrivacy())
		{
			return true;
		}

		return (
			$this->isPrivacyCheckMet('allow_view_profile', $visitor)
			&& $this->isPrivacyCheckMet('allow_view_identities', $visitor)
		);
	}

	public function canViewIps()
	{
		return $this->exists() && $this->hasPermission('general', 'viewIps');
	}

	public function canFollowUser(User $user)
	{
		if (!$user->user_id || !$this->user_id)
		{
			return false;
		}

		if ($user->user_id == $this->user_id)
		{
			return false;
		}

		if ($this->user_state != 'valid')
		{
			return false;
		}

		if ($this->isFollowing($user))
		{
			return true;
		}

		if (!in_array($user->user_state, ['valid', 'email_confirm', 'email_confirm_edit']))
		{
			return false;
		}

		return true;
	}

	public function isFollowing(User $user)
	{
		return $this->Profile && $this->Profile->isFollowing($user);
	}

	public function canIgnoreUser(User $user, &$error = '')
	{
		if (!$user->user_id || !$this->user_id)
		{
			return false;
		}

		if ($user->is_staff)
		{
			$error = \XF::phraseDeferred('staff_members_may_not_be_ignored');
			return false;
		}

		if ($user->user_id == $this->user_id)
		{
			$error = \XF::phraseDeferred('you_may_not_ignore_yourself');
			return false;
		}

		if ($this->user_state != 'valid')
		{
			return false;
		}

		if (!in_array($user->user_state, ['valid', 'email_confirm', 'email_confirm_edit', 'email_bounce']))
		{
			return false;
		}

		return true;
	}

	public function isIgnoring($userId)
	{
		if (!$this->user_id)
		{
			return false;
		}

		if ($userId instanceof User)
		{
			$userId = $userId->user_id;
		}

		if (!$userId || !$this->Profile)
		{
			return false;
		}

		$ignored = $this->Profile->ignored;
		return $ignored && isset($ignored[$userId]);
	}

	public function canStartConversation()
	{
		if (!$this->exists())
		{
			return false;
		}

		$maxRecipients = $this->hasPermission('conversation', 'maxRecipients');
		return (
			$this->hasPermission('conversation', 'start')
			&& ($maxRecipients == -1 || $maxRecipients > 0)
		);
	}

	public function canStartConversationWith(\XF\Entity\User $user)
	{
		if (!$this->canBypassUserPrivacy() && !$user->canReceiveConversation())
		{
			return false;
		}

		if (!$user->user_id || $user->user_id == $this->user_id)
		{
			return false;
		}

		return (
			$this->canStartConversation()
			&& $user->Privacy->isPrivacyCheckMet('allow_send_personal_conversation', $this)
			&& $user->user_state != 'disabled'
			&& !$user->is_banned
		);
	}

	public function canReceiveConversation()
	{
		return $this->hasPermission('conversation', 'receive');
	}

	/**
	 * Checks whether the user can upload and manage attachments globally for the specified permission group.
	 *
	 * @param string $group
	 *
	 * @return bool
	 */
	public function canUploadAndManageAttachments($group = 'forum')
	{
		return ($this->user_id && $this->hasPermission($group, 'uploadAttachment'));
	}

	public function canUploadAvatar()
	{
		return ($this->user_id && $this->hasPermission('avatar', 'allowed'));
	}

	public function canUseContactForm()
	{
		$options = $this->app()->options();

		return (
			!$this->is_banned
			&& $options->contactUrl['type']
			&& $this->hasPermission('general', 'useContactForm')
		);
	}

	public function canUsePushNotifications()
	{
		if (!\XF::isPushUsable())
		{
			return false;
		}

		return (
			$this->user_id
			&& $this->hasPermission('general', 'usePush')
		);
	}

	public function canCreateThread(&$error = null)
	{
		return $this->hasPermission('forum', 'postThread');
	}

	public function isShownCaptcha()
	{
		return !$this->user_id;
	}

	public function isAwaitingEmailConfirmation()
	{
		return in_array($this->user_state, ['email_confirm', 'email_confirm_edit']);
	}

	public function getAllowedUserMentions(array $mentions)
	{
		$maxMentions = $this->hasPermission('general', 'maxMentionedUsers');
		if ($maxMentions == 0)
		{
			return [];
		}
		if ($maxMentions < 0) // unlimited
		{
			return $mentions;
		}

		return array_slice($mentions, 0, $maxMentions, true);
	}

	public function hasPermission($group, $permission)
	{
		return $this->PermissionSet->hasGlobalPermission($group, $permission);
	}

	public function hasContentPermission($contentType, $contentId, $permission)
	{
		return $this->PermissionSet->hasContentPermission($contentType, $contentId, $permission);
	}

	public function hasNodePermission($contentId, $permission)
	{
		return $this->PermissionSet->hasContentPermission('node', $contentId, $permission);
	}

	public function cacheNodePermissions(array $nodeIds = null)
	{
		if (is_array($nodeIds))
		{
			\XF::permissionCache()->cacheContentPermsByIds($this->permission_combination_id, 'node', $nodeIds);
		}
		else
		{
			\XF::permissionCache()->cacheAllContentPerms($this->permission_combination_id, 'node');
		}
	}

	public function rebuildUserGroupRelations($newTransaction = true)
	{
		if (!$this->user_id)
		{
			throw new \LogicException("User must be saved first");
		}

		$db = $this->db();
		$userId = $this->user_id;

		$inserts = [];
		$inserts[] = [
			'user_id' => $userId,
			'user_group_id' => $this->user_group_id,
			'is_primary' => 1
		];
		foreach ($this->secondary_group_ids AS $groupId)
		{
			$inserts[] = [
				'user_id' => $userId,
				'user_group_id' => $groupId,
				'is_primary' => 0
			];
		}

		if($newTransaction)
		{
			$db->beginTransaction();
		}

		$db->delete('xf_user_group_relation', 'user_id = ?' , $this->user_id);
		$db->insertBulk('xf_user_group_relation', $inserts, false, 'is_primary = VALUES(is_primary)');

		if ($newTransaction)
		{
			$db->commit();
		}
	}

	public function rebuildDisplayStyleGroup()
	{
		if (!$this->user_id)
		{
			throw new \LogicException("User must be saved first");
		}

		$groupRepo = $this->getUserGroupRepo();
		$id = $groupRepo->getDisplayGroupIdForUser($this);
		if ($id != $this->display_style_group_id)
		{
			$this->fastUpdate('display_style_group_id', $id);
		}
	}

	public function rebuildPermissionCombination()
	{
		if (!$this->user_id)
		{
			throw new \LogicException("User must be saved first");
		}

		/** @var \XF\Repository\PermissionCombination $combinationRepo */
		$combinationRepo = $this->repository('XF:PermissionCombination');
		$combinationRepo->updatePermissionCombinationForUser($this);
	}

	public function removeUserFromGroup($groupId)
	{
		if ($groupId instanceof UserGroup)
		{
			$groupId = $groupId->user_group_id;
		}

		if ($this->user_group_id == $groupId)
		{
			$this->user_group_id = self::GROUP_REG;
			return true;
		}

		$ids = $this->secondary_group_ids;
		$position = array_search($groupId, $ids);
		if ($position !== false)
		{
			unset($ids[$position]);
			$this->secondary_group_ids = $ids;
			return true;
		}

		return false;
	}

	//************************* VERIFIERS ***************************

	protected function verifyUsername(&$username)
	{
		if ($username === $this->getExistingValue('username'))
		{
			return true; // unchanged, always pass
		}

		/** @var \XF\Validator\Username $validator */
		$validator = $this->app()->validator('Username');
		$username = $validator->coerceValue($username);
		if ($this->user_id)
		{
			$validator->setOption('self_user_id', $this->user_id);
		}
		if ($this->getOption('admin_edit'))
		{
			$validator->setOption('admin_edit', true);
		}
		if (!$validator->isValid($username, $errorKey))
		{
			$this->error($validator->getPrintableErrorValue($errorKey), 'username');
			return false;
		}

		return true;
	}

	protected function verifyEmail(&$email)
	{
		if ($this->isUpdate() && $email === $this->getExistingValue('email'))
		{
			return true;
		}

		if ($this->getOption('admin_edit') && $email === '')
		{
			return true;
		}

		/** @var \XF\Repository\Banning $banningRepo */
		$banningRepo = $this->repository('XF:Banning');

		$bannedEmails = $this->app()->container('bannedEmails');

		$emailValidator = $this->app()->validator('Email');
		if (!$this->getOption('admin_edit'))
		{
			$emailValidator->setOption('banned', $bannedEmails);
		}

		$emailValidator->setOption('check_typos', true);

		if (!$emailValidator->isValid($email, $errorKey))
		{
			if ($errorKey == 'banned')
			{
				$this->error(\XF::phrase('email_address_you_entered_has_been_banned_by_administrator'), 'email');

				// try to find triggering banned email entry. try exact match first...
				$emailBan = $this->_em->findOne('XF:BanEmail', ['banned_email' => $email]);
				if (!$emailBan)
				{
					// ...otherwise find the first entry that triggered
					$bannedEmail = $banningRepo->getBannedEntryFromEmail($email, $bannedEmails);
					if ($bannedEmail)
					{
						$emailBan = $this->_em->findOne('XF:BanEmail', ['banned_email' => $bannedEmail]);
					}
				}
				if ($emailBan)
				{
					$emailBan->fastUpdate('last_triggered_date', time());
				}
			}
			else if ($errorKey == 'typo')
			{
				$this->error(\XF::phrase('email_address_you_entered_appears_have_typo'));
			}
			else
			{
				$this->error(\XF::phrase('please_enter_valid_email'), 'email');
			}

			return false;
		}

		$existingUser = $this->finder('XF:User')->where('email', $email)->fetchOne();
		if ($existingUser && $existingUser['user_id'] != $this->user_id)
		{
			$this->error(\XF::phrase('email_addresses_must_be_unique'), 'email');
			return false;
		}

		return true;
	}

	protected function verifyStyleId(&$styleId)
	{
		if ($styleId && !$this->_em->find('XF:Style', $styleId))
		{
			$styleId = 0;
		}

		return true;
	}

	protected function verifyLanguageId(&$languageId)
	{
		if ($languageId && !$this->_em->find('XF:Language', $languageId))
		{
			$languageId = 0;
		}

		return true;
	}

	protected function verifyTimezone(&$timezone)
	{
		if (!$timezone)
		{
			$timezone = $this->app()->options()->guestTimeZone;
		}

		$tzs = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL_WITH_BC);
		if (!in_array($timezone, $tzs))
		{
			$this->error(\XF::phrase('please_select_valid_time_zone'), 'timezone');
			return false;
		}

		return true;
	}

	protected function verifyCustomTitle(&$title)
	{
		if ($title === $this->getExistingValue('custom_title'))
		{
			return true; // can always keep the existing value
		}
		if ($this->getOption('admin_edit'))
		{
			return true;
		}

		if ($title !== $this->app()->stringFormatter()->censorText($title))
		{
			$this->error(\XF::phrase('please_enter_custom_title_that_does_not_contain_any_censored_words'), 'custom_title');
			return false;
		}

		if (!$this->is_moderator && !$this->is_admin)
		{
			$disallowed = $this->getOption('custom_title_disallowed');
			if ($disallowed)
			{
				foreach ($disallowed AS $value)
				{
					$value = trim($value);
					if ($value === '')
					{
						continue;
					}
					if (stripos($title, $value) !== false)
					{
						$this->error(\XF::phrase('please_enter_another_custom_title_disallowed_words'), 'custom_title');
						return false;
					}
				}
			}
		}

		return true;
	}

	//************************* LIFE CYCLE ***************************

	protected function _preSave()
	{
		if ($this->isChanged('user_group_id') || $this->isChanged('secondary_group_ids'))
		{
			$groupRepo = $this->getUserGroupRepo();
			$this->display_style_group_id = $groupRepo->getDisplayGroupIdForUser($this);
		}

		if ($this->isChanged(['user_group_id', 'secondary_group_ids']))
		{
			// Do not allow a primary user group also be a secondary user group.
			$this->secondary_group_ids = array_diff(
				$this->secondary_group_ids,
				[$this->user_group_id]
			);
		}

		if (!$this->secret_key)
		{
			$this->secret_key = \XF::generateRandomString(32);
		}

		if ($this->isInsert() && !$this->isChanged('email') && empty($this->_errors['email']))
		{
			$this->email = '';
		}

		if ($this->isChanged('email') && $this->email && empty($this->_errors['email']))
		{
			// Redo the duplicate email check. This tries to reduce a race condition that can be extended
			// due to third-party spam checks.
			$matchUserId = $this->db()->fetchOne("
				SELECT user_id
				FROM xf_user
				WHERE email = ?
			", $this->email);
			if ($matchUserId && (!$this->user_id || $matchUserId != $this->user_id))
			{
				$this->error(\XF::phrase('email_addresses_must_be_unique'), 'username');
			}
		}

		if ($this->isChanged('username') && empty($this->_errors['username']))
		{
			// Redo the duplicate name check. This tries to reduce a race condition that can be extended
			// due to third-party spam checks.
			$matchUserId = $this->db()->fetchOne("
				SELECT user_id
				FROM xf_user
				WHERE username = ?
			", $this->username);
			if ($matchUserId && (!$this->user_id || $matchUserId != $this->user_id))
			{
				$this->error(\XF::phrase('usernames_must_be_unique'), 'username');
			}
		}
	}

	protected function _postSave()
	{
		if ($this->isChanged('user_group_id') || $this->isChanged('secondary_group_ids'))
		{
			$this->rebuildUserGroupRelations(false);
			$this->rebuildPermissionCombination();
		}

		if ($this->isUpdate() && $this->isChanged('username') && $this->getExistingValue('username') != null && $this->getOption('enqueue_rename_cleanup'))
		{
			$this->app()->jobManager()->enqueue('XF:UserRenameCleanUp', [
				'originalUserId' => $this->user_id,
				'originalUserName' => $this->getExistingValue('username'),
				'newUserName' => $this->username
			]);
		}

		$approvalChange = $this->isStateChanged('user_state', 'moderated');
		if ($approvalChange == 'enter')
		{
			$approvalQueue = $this->getRelationOrDefault('ApprovalQueue', false);

			$approvalQueue->content_type = 'user';
			$approvalQueue->content_id = $this->user_id;
			$approvalQueue->content_date = $this->register_date;

			$approvalQueue->save();
		}
		else if ($approvalChange == 'leave' && $this->ApprovalQueue)
		{
			$this->ApprovalQueue->delete();
		}

		$rejectionChange = $this->isStateChanged('user_state', 'rejected');
		if ($rejectionChange == 'enter' && !$this->Reject)
		{
			/** @var UserReject $reject */
			$reject = $this->getRelationOrDefault('Reject', false);
			$reject->setFromVisitor();
			$reject->save();
		}
		else if ($rejectionChange == 'leave' && $this->Reject)
		{
			$this->Reject->delete();
		}

		if ($this->isChanged('is_staff'))
		{
			if ($this->isUpdate())
			{
				$this->repository('XF:UserIgnored')->rebuildIgnoredCacheByIgnoredUser($this->user_id);
			}

			$this->repository('XF:MemberStat')->emptyCache('staff_members');
		}
	}

	protected function _postDelete()
	{
		$db = $this->db();
		$userId = $this->user_id;

		// Quickly delete the 1:1 entries that form the core record. We'll clean up the rest of the data in a
		// separate process. Not using cascadeDelete here as we may block deletion of this stuff in the other
		// entities as it could cause problems
		$db->delete('xf_user_authenticate', 'user_id = ?', $userId);
		$db->delete('xf_user_option', 'user_id = ?', $userId);
		$db->delete('xf_user_profile', 'user_id = ?', $userId);
		$db->delete('xf_user_privacy', 'user_id = ?', $userId);

		/** @var \XF\Service\User\Avatar $avatar */
		$avatar = $this->app()->service('XF:User\Avatar', $this);
		$avatar->deleteAvatarForUserDelete();

		if ($this->getOption('enqueue_delete_cleanup'))
		{
			$this->app()->jobManager()->enqueue('XF:UserDeleteCleanUp', [
				'userId' => $this->user_id,
				'username' => $this->username
			]);
		}
	}

	public function rejectUser($reason = '', User $byUser = null)
	{
		if ($this->user_state == 'rejected')
		{
			return false;
		}

		$this->user_state = 'rejected';

		/** @var \XF\Entity\UserReject $reject */
		$reject = $this->getRelationOrDefault('Reject');

		if ($byUser)
		{
			$reject->setFromUser($byUser);
		}

		$reject->reject_reason = $reason;

		$this->save();

		return true;
	}

	/**
	 * @param \XF\Api\Result\EntityResult $result
	 * @param int $verbosity
	 * @param array $options
	 *
	 * @api-desc Information about the user. Different information will be included based on permissions and verbosity.
	 *
	 * @api-out <perm|verbose> str $about
	 * @api-out <perm> bool $activity_visible
	 * @api-out <cond> int $age The user's current age. Only included if available.
	 * @api-out <perm|verbose> array $alert_optout
	 * @api-out <perm|verbose> string $allow_post_profile
	 * @api-out <perm|verbose> string $allow_receive_news_feed
	 * @api-out <perm|verbose> string $allow_send_personal_conversation
	 * @api-out <perm|verbose> string $allow_view_identities
	 * @api-out <perm|verbose> string $allow_view_profile
	 * @api-out object $avatar_urls Maps from size types to URL.
	 * @api-out bool $can_ban
	 * @api-out bool $can_converse
	 * @api-out bool $can_edit
	 * @api-out bool $can_follow
	 * @api-out bool $can_ignore
	 * @api-out bool $can_post_profile
	 * @api-out bool $can_view_profile
	 * @api-out bool $can_view_profile_posts
	 * @api-out bool $can_warn
	 * @api-out <perm|verbose> bool $content_show_signature
	 * @api-out <perm|verbose> string $creation_watch_state
	 * @api-out <perm> object $custom_fields Map of custom field keys and values.
	 * @api-out <perm> string $custom_title Will have a value if a custom title has been specifically set; prefer user_title instead.
	 * @api-out <perm> object $dob Date of birth with year, month and day keys.
	 * @api-out <perm|verbose> string $email
	 * @api-out <perm|verbose> bool $email_on_conversation
	 * @api-out <perm|verbose> string $gravatar
	 * @api-out <perm|verbose> bool $interaction_watch_state
	 * @api-out <perm> bool $is_admin
	 * @api-out <perm> bool $is_banned
	 * @api-out <perm> bool $is_discouraged
	 * @api-out <cond> bool $is_followed True if the visitor is following this user. Only included if visitor is not a guest.
	 * @api-out <cond> bool $is_ignored True if the visitor is ignoring this user. Only included if visitor is not a guest.
	 * @api-out <perm> bool $is_moderator
	 * @api-out <perm> bool $is_super_admin
	 * @api-out <perm> int $last_activity Unix timestamp of user's last activity, if available.
	 * @api-out str $location
	 * @api-out <perm|verbose> bool $push_on_conversation
	 * @api-out <perm|verbose> array $push_optout
	 * @api-out <perm|verbose> bool $receive_admin_email
	 * @api-out <perm> array $secondary_group_ids
	 * @api-out <perm|verbose> bool $show_dob_date
	 * @api-out <perm|verbose> bool $show_dob_year
	 * @api-out str $signature
	 * @api-out <perm|verbose> string $timezone
	 * @api-out <perm|verbose> array $use_tfa
	 * @api-out <perm> int $user_group_id
	 * @api-out <perm> str $user_state
	 * @api-out str $user_title
	 * @api-out <perm> bool $visible
	 * @api-out <perm> int $warning_points Current warning points.
	 * @api-out <perm> str $website
	 */
	protected function setupApiResultData(
		\XF\Api\Result\EntityResult $result, $verbosity = self::VERBOSITY_NORMAL, array $options = []
	)
	{
		if (!$this->user_id)
		{
			// possible to be called on a guest user, in which case return a stub
			$result->skipColumn(['message_count', 'register_date', 'trophy_points', 'is_staff', 'reaction_score']);
			return;
		}

		$visitor = \XF::visitor();

		$isSelf = ($visitor->user_id == $this->user_id);
		$isBypassingPermissions = \XF::isApiBypassingPermissions();
		$hasAdminPerms = $visitor->hasAdminPermission('user');

		if ($verbosity < self::VERBOSITY_NORMAL)
		{
			// this indicates that we just want a stub result
			$includeExtendedProfile = false;
			$includePrivateProfile = false;
			$includeInternalProfile = false;
		}
		else if ($isBypassingPermissions || !empty($options['full_profile']))
		{
			// always return everything
			$includeExtendedProfile = true;
			$includePrivateProfile = true;
			$includeInternalProfile = true;
		}
		else
		{
			$includeExtendedProfile = $isSelf || $this->canViewFullProfile();
			$includePrivateProfile = $isSelf || $hasAdminPerms;
			$includeInternalProfile = $hasAdminPerms;
		}

		$profile = $this->Profile;
		$option = $this->Option;
		$privacy = $this->Privacy;

		$birthday = $profile->getBirthday($isBypassingPermissions);

		// basic profile info

		$result->user_title = $this->custom_title ?: $this->app()->templater()->getDefaultUserTitleForUser($this);
		$result->signature = $profile->signature;
		$result->location = $profile->location;

		$avatarUrls = [];
		foreach (array_keys($this->app()->container('avatarSizeMap')) AS $avatarSize)
		{
			$avatarUrls[$avatarSize] = $this->getAvatarUrl($avatarSize, null, true);
		}
		$result->avatar_urls = $avatarUrls;

		if (!empty($birthday['age']))
		{
			$result->age = $birthday['age'];
		}

		if ($isBypassingPermissions || $this->canViewOnlineStatus())
		{
			$result->last_activity = $this->last_activity;
		}

		if ($visitor->user_id)
		{
			$result->includeExtra([
				'is_ignored' => $visitor->isIgnoring($this),
				'is_followed' => $visitor->isFollowing($this)
			]);
		}

		if ($isBypassingPermissions || $visitor->canViewWarnings())
		{
			$result->includeColumn('warning_points');
		}

		if ($isBypassingPermissions || $visitor->canBypassUserPrivacy())
		{
			$result->includeColumn('is_banned');
		}

		// extended profile

		if ($includeExtendedProfile)
		{
			$result->website = $profile->website;

			if ($birthday)
			{
				$result->dob = [
					'year' => $birthday['age'] ? $profile->dob_year : null,
					'month' => $profile->dob_month,
					'day' => $profile->dob_day
				];
			}

			if ($includePrivateProfile)
			{
				// return all values
				$fieldValues = $profile->custom_fields->getFieldValues();
			}
			else
			{
				// only return what's public

				$fieldValues = [];
				/** @var \XF\CustomField\Definition[] $fields */
				$fields = $profile->custom_fields->getDefinitionSet()->filterGroup('personal')->filter('profile');
				foreach ($fields AS $fieldId => $field)
				{
					$fieldValues[$fieldId] = $profile->custom_fields->getFieldValue($fieldId);
				}

				if ($this->canViewIdentities())
				{
					$fields = $profile->custom_fields->getDefinitionSet()->filterGroup('contact')->filter('profile');
					foreach ($fields AS $fieldId => $field)
					{
						$fieldValues[$fieldId] = $profile->custom_fields->getFieldValue($fieldId);
					}
				}
			}

			$result->custom_fields = (object)$fieldValues;

			if ($verbosity > self::VERBOSITY_NORMAL)
			{
				$result->about = $profile->about;
			}
		}

		// private profile -- things only shown to the user and to admins

		if ($includePrivateProfile)
		{
			$result->includeColumn([
				'is_admin',
				'is_moderator',
				'visible',
				'activity_visible',
				'custom_title'
			]);
			$result->includeGetter('is_super_admin');

			if ($verbosity > self::VERBOSITY_NORMAL)
			{
				$result->includeColumn(['email', 'timezone', 'gravatar']);

				$result->includeExtra([
					'show_dob_year' => $option->show_dob_year,
					'show_dob_date' => $option->show_dob_date,
					'content_show_signature' => $option->content_show_signature,
					'receive_admin_email' => $option->receive_admin_email,
					'email_on_conversation' => $option->email_on_conversation,
					'push_on_conversation' => $option->push_on_conversation,
					'creation_watch_state' => $option->creation_watch_state,
					'interaction_watch_state' => $option->interaction_watch_state,
					'alert_optout' => $option->alert_optout,
					'push_optout' => $option->push_optout,
					'usa_tfa' => $option->use_tfa
				]);
				$result->includeExtra([
					'allow_view_profile' => $privacy->allow_view_profile,
					'allow_post_profile' => $privacy->allow_post_profile,
					'allow_send_personal_conversation' => $privacy->allow_send_personal_conversation,
					'allow_view_identities' => $privacy->allow_view_identities,
					'allow_receive_news_feed' => $privacy->allow_receive_news_feed,
				]);
			}
		}

		// internal profile -- things only shown to the admin

		if ($includeInternalProfile)
		{
			$result->includeColumn(['user_group_id', 'secondary_group_ids', 'user_state']);

			$result->is_discouraged = $option->is_discouraged;
		}

		// general permission checks

		$result->can_edit = $this->canEdit();
		$result->can_ban = $this->canBan();
		$result->can_warn = $this->canWarn();
		$result->can_view_profile = $this->canViewFullProfile();
		$result->can_view_profile_posts = $this->canViewPostsOnProfile();
		$result->can_post_profile = $this->canPostOnProfile();
		$result->can_follow = $visitor->canFollowUser($this);
		$result->can_ignore = $visitor->canIgnoreUser($this);
		$result->can_converse = $visitor->canStartConversationWith($this);
	}

	protected function _setupDefaults()
	{
		$options = \XF::options();

		$defaults = $options->registrationDefaults;
		$this->visible = $defaults['visible'] ? true : false;
		$this->activity_visible = $defaults['activity_visible'] ? true : false;

		$this->user_group_id = self::GROUP_REG;
		$this->timezone = $options->guestTimeZone;
		$this->language_id = \XF::language()->getId();
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user';
		$structure->shortName = 'XF:User';
		$structure->contentType = 'user';
		$structure->primaryKey = 'user_id';
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true, 'changeLog' => false],
			'username' => ['type' => self::STR, 'maxLength' => 50,
				'required' => 'please_enter_valid_name', 'api' => true
			],
			'email' => ['type' => self::STR, 'maxLength' => 120],
			'style_id' => ['type' => self::UINT, 'default' => 0, 'changeLog' => false],
			'language_id' => ['type' => self::UINT, 'default' => 0, 'changeLog' => false],
			'timezone' => ['type' => self::STR, 'maxLength' => 50, 'default' => 'Europe/London'],
			'visible' => ['type' => self::BOOL, 'default' => true],
			'activity_visible' => ['type' => self::BOOL, 'default' => true],
			'user_group_id' => ['type' => self::UINT, 'required' => true],
			'secondary_group_ids' => ['type' => self::LIST_COMMA, 'default' => [],
				'list' => ['type' => 'posint', 'unique' => true, 'sort' => SORT_NUMERIC]
			],
			'display_style_group_id' => ['type' => self::UINT, 'default' => 0, 'changeLog' => false],
			'permission_combination_id' => ['type' => self::UINT, 'default' => 0, 'changeLog' => false],
			'message_count' => ['type' => self::UINT, 'forced' => true, 'default' => 0, 'changeLog' => false, 'api' => true],
			'alerts_unread' => ['type' => self::UINT, 'forced' => true, 'default' => 0, 'changeLog' => false],
			'conversations_unread' => ['type' => self::UINT, 'forced' => true, 'default' => 0, 'changeLog' => false],
			'register_date' => ['type' => self::UINT, 'default' => \XF::$time, 'api' => true],
			'last_activity' => ['type' => self::UINT, 'default' => \XF::$time, 'changeLog' => false],
			'trophy_points' => ['type' => self::UINT, 'forced' => true, 'default' => 0, 'changeLog' => false, 'api' => true],
			'avatar_date' => ['type' => self::UINT, 'default' => 0],
			'avatar_width' => ['type' => self::UINT, 'max' => 65535, 'default' => 0, 'changeLog' => false],
			'avatar_height' => ['type' => self::UINT, 'max' => 65535, 'default' => 0, 'changeLog' => false],
			'avatar_highdpi' => ['type' => self::BOOL, 'default' => false, 'changeLog' => false],
			'gravatar' => ['type' => self::STR, 'maxLength' => 120, 'default' => '',
				'match' => 'email_empty'
			],
			'user_state' => ['type' => self::STR, 'default' => 'valid',
				'allowedValues' => [
					'valid', 'email_confirm', 'email_confirm_edit', 'moderated', 'email_bounce', 'rejected', 'disabled'
				]
			],
			'is_moderator' => ['type' => self::BOOL, 'default' => false],
			'is_admin' => ['type' => self::BOOL, 'default' => false],
			'is_staff' => ['type' => self::BOOL, 'default' => false, 'api' => true],
			'is_banned' => ['type' => self::BOOL, 'default' => false],
			'reaction_score' => ['type' => self::INT, 'default' => 0, 'changeLog' => false, 'api' => true],
			'custom_title' => ['type' => self::STR, 'maxLength' => 50, 'default' => '',
				'censor' => true
			],
			'warning_points' => ['type' => self::UINT, 'forced' => true, 'default' => 0, 'changeLog' => false],
			'secret_key' => ['type' => self::BINARY, 'maxLength' => 32, 'required' => true, 'changeLog' => false],
			'privacy_policy_accepted' => ['type' => self::UINT, 'default' => 0],
			'terms_accepted' => ['type' => self::UINT, 'default' => 0]
		];
		$structure->behaviors = [
			'XF:ChangeLoggable' => [] // will pick up content type automatically
		];
		$structure->getters = [
			'PermissionSet' => [
				'getter' => true,
				'cache' => true
			],
			'permission_combination_id' => false,
			'is_super_admin' => true,
			'last_activity' => true,
			'email_confirm_key' => true,
			'warning_count' => true
		];
		$structure->relations = [
			'Admin' => [
				'entity' => 'XF:Admin',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'Auth' => [
				'entity' => 'XF:UserAuth',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'ConnectedAccounts' => [
				'entity' => 'XF:UserConnectedAccount',
				'type' => self::TO_MANY,
				'conditions' => 'user_id',
				'key' => 'provider'
			],
			'Option' => [
				'entity' => 'XF:UserOption',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'PermissionCombination' => [
				'entity' => 'XF:PermissionCombination',
				'type' => self::TO_ONE,
				'conditions' => 'permission_combination_id',
				'proxy' => true,
				'primary' => true
			],
			'Profile' => [
				'entity' => 'XF:UserProfile',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'Privacy' => [
				'entity' => 'XF:UserPrivacy',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'Ban' => [
				'entity' => 'XF:UserBan',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'Reject' => [
				'entity' => 'XF:UserReject',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'Activity' => [
				'entity' => 'XF:SessionActivity',
				'type' => self::TO_ONE,
				'conditions' => [
					['user_id', '=', '$user_id'],
					['unique_key', '=', '$user_id', '']
				],
				'primary' => true
			],
			'ApprovalQueue' => [
				'entity' => 'XF:ApprovalQueue',
				'type' => self::TO_ONE,
				'conditions' => [
					['content_type', '=', 'user'],
					['content_id', '=', '$user_id']
				],
				'primary' => true
			],
			'Following' => [
				'entity' => 'XF:UserFollow',
				'type' => self::TO_MANY,
				'conditions' => 'user_id',
				'key' => 'follow_user_id'
			]
		];

		$structure->columnAliases = [
			'like_count' => 'reaction_score'
		];

		$options = \XF::options();

		$structure->options = [
			'custom_title_disallowed' => !empty($options->disallowedCustomTitles)
				? preg_split('/\r?\n/', $options->disallowedCustomTitles)
				: [],
			'admin_edit' => false,
			'enqueue_rename_cleanup' => true,
			'enqueue_delete_cleanup' => true
		];

		$structure->withAliases = [
			'api' => ['Profile', 'Privacy', 'Option', 'Activity', 'Admin']
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\User
	 */
	protected function getUserRepo()
	{
		return $this->repository('XF:User');
	}

	/**
	 * @return \XF\Repository\UserGroup
	 */
	protected function getUserGroupRepo()
	{
		return $this->repository('XF:UserGroup');
	}
}