<?php

namespace XF\ChangeLog;

use XF\Mvc\Entity\Entity;
use XF\Util\Arr;

class User extends AbstractHandler
{
	protected $groupMap;

	/**
	 * @var \XF\Entity\UserField[]
	 */
	protected $customFields;

	protected function getLabelMap()
	{
		return [
			'username'                => 'user_name',
			'email'                   => 'email_address',
			'timezone'                => 'time_zone',
			'visible'                 => 'show_online_status',
			'activity_visible'        => 'show_current_activity',
			'user_group_id'           => 'user_group',
			'secondary_group_ids'     => 'secondary_user_groups',
			'register_date'           => 'joined',
			'is_moderator'            => 'moderator',
			'is_admin'                => 'administrator',
			'is_staff'                => 'staff_member',
			'is_banned'               => 'banned',
			'use_tfa'                 => 'two_step_verification_enabled',
			'privacy_policy_accepted' => 'accepted_privacy_policy',
			'terms_accepted'          => 'accepted_terms_rules',

			'show_dob_year'				=> 'show_year_of_birth',
			'show_dob_date'				=> 'show_day_and_month_of_birth',
			'content_show_signature'	=> 'show_signatures_with_messages',
			'receive_admin_email'		=> 'receive_site_mailings',
			'email_on_conversation'		=> 'email_conversation_notifications',
			'push_on_conversation'		=> 'push_conversation_notifications',
			'is_discouraged'			=> 'discouraged',
			'creation_watch_state'		=> 'watch_content_on_creation',
			'interaction_watch_state'	=> 'watch_content_on_interaction',

			'allow_view_profile'               => 'view_your_details_on_your_profile_page',
			'allow_post_profile'               => 'post_messages_on_your_profile_page',
			'allow_send_personal_conversation' => 'start_conversations_with_you',
			'allow_view_identities'            => 'view_your_identities',
			'allow_receive_news_feed'          => 'receive_your_news_feed',

			'data'         => 'password', // for historical reasons
		];
	}

	protected function getFormatterMap()
	{
		return [
			'user_group_id'           => 'formatUserGroup',
			'secondary_group_ids'     => 'formatUserGroupList',
			'timezone'                => 'formatTimeZone',
			'visible'                 => 'formatYesNo',
			'activity_visible'        => 'formatYesNo',
			'avatar_date'             => 'formatDateTime',
			'register_date'           => 'formatDateTime',
			'user_state'              => 'formatUserState',
			'is_moderator'            => 'formatYesNo',
			'is_admin'                => 'formatYesNo',
			'is_staff'                => 'formatYesNo',
			'is_banned'               => 'formatYesNo',
			'use_tfa'                 => 'formatYesNo',
			'privacy_policy_accepted' => 'formatDateTime',
			'terms_accepted'          => 'formatDateTime',

			'show_dob_year'				=> 'formatYesNo',
			'show_dob_date'				=> 'formatYesNo',
			'content_show_signature'	=> 'formatYesNo',
			'receive_admin_email'		=> 'formatYesNo',
			'email_on_conversation'		=> 'formatYesNo',
			'push_on_conversation'		=> 'formatYesNo',
			'is_discouraged'			=> 'formatYesNo',
			'creation_watch_state'		=> 'formatWatchState',
			'interaction_watch_state'	=> 'formatWatchState',

			'allow_view_profile'               => 'formatPrivacyValue',
			'allow_post_profile'               => 'formatPrivacyValue',
			'allow_send_personal_conversation' => 'formatPrivacyValue',
			'allow_view_identities'            => 'formatPrivacyValue',
			'allow_receive_news_feed'          => 'formatPrivacyValue',
		];
	}

	protected function getProtectedFields()
	{
		return [
			'username' => true,
			'privacy_policy_accepted' => true,
			'terms_accepted' => true,

			'receive_admin_email' => true,
		];
	}

	protected function getPrefixHandlers()
	{
		return [
			'custom_fields' => ['labelCustomField', 'formatCustomField']
		];
	}

	public function getDefaultEditUserId(Entity $entity)
	{
		$userId = \XF::visitor()->user_id;
		if (!$userId && isset($entity->user_id))
		{
			// guest edits should be treated as self edits; these are mostly system level edits
			$userId = $entity->user_id;
		}

		return $userId;
	}

	protected function formatUserGroup($userGroupId)
	{
		if (!is_array($this->groupMap))
		{
			/** @var \XF\Repository\UserGroup $groupRepo */
			$groupRepo = \XF::repository('XF:UserGroup');
			$this->groupMap = $groupRepo->getUserGroupTitlePairs();
		}

		return isset($this->groupMap[$userGroupId]) ? $this->groupMap[$userGroupId] : $userGroupId;
	}

	protected function formatUserGroupList($userGroupIds)
	{
		$values = [];
		$ids = Arr::stringToArray($userGroupIds, '/,/');
		foreach ($ids AS $id)
		{
			$values[] = $this->formatUserGroup($id);
		}

		return implode(', ', $values);
	}

	protected function formatTimeZone($value)
	{
		/** @var \XF\Data\TimeZone $tzData */
		$tzData = \XF::app()->data('XF:TimeZone');
		$tzs = $tzData->getTimeZoneData();
		return isset($tzs[$value]) ? \XF::phrase($tzs[$value]['phrase']) : $value;
	}

	protected function formatUserState($value)
	{
		switch ($value)
		{
			case 'valid': return \XF::phrase('valid');
			case 'email_confirm': return \XF::phrase('awaiting_email_confirmation');
			case 'email_confirm_edit': return \XF::phrase('awaiting_email_confirmation_from_edit');
			case 'email_bounce': return \XF::phrase('email_invalid_bounced');
			case 'moderated': return \XF::phrase('awaiting_approval');
			case 'rejected': return \XF::phrase('rejected');
			case 'disabled': return \XF::phrase('disabled');
			default: return $value;
		}
	}

	protected function formatWatchState($value)
	{
		switch ($value)
		{
			case 'watch_no_email': return \XF::phrase('yes');
			case 'watch_email': return \XF::phrase('yes_with_email');
			case '': return \XF::phrase('no');
			default: return $value;
		}
	}

	protected function formatPrivacyValue($value)
	{
		switch ($value)
		{
			case 'everyone': return \XF::phrase('all_visitors');
			case 'members': return \XF::phrase('members_only');
			case 'followed': return \XF::phrase('followed_members_only');
			case 'none': return \XF::phrase('nobody');
			default: return $value;
		}
	}

	protected function labelCustomField($field)
	{
		return $this->labelCustomFieldGeneric('XF:UserField', $field);
	}

	protected function formatCustomField($field, $value)
	{
		return $this->formatCustomFieldGeneric('XF:UserField', $field, $value);
	}
}