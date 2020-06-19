<?php

namespace XF\Criteria;

use XF\Util\Arr;

class User extends AbstractCriteria
{
	protected function isSpecialMatched($rule, array $data, \XF\Entity\User $user)
	{
		// custom user fields
		if (preg_match('/^user_field_(.+)$/', $rule, $matches))
		{
			/** @var \XF\CustomField\Set|null $cFS */
			$cFS = $user->user_id ? $user->Profile->custom_fields : null;

			$fieldId = $matches[1];

			if (!$cFS || !isset($cFS->{$fieldId}))
			{
				return false;
			}

			$value = $cFS->{$fieldId};

			// text fields - check that data exists within the text value
			if (isset($data['text']))
			{
				if (stripos($value, $data['text']) === false)
				{
					return false;
				}
			}
			// choice fields - check that data is in the choice array
			else if (isset($data['choices']))
			{
				// multi-choice
				if (is_array($value))
				{
					if (!array_intersect($value, $data['choices']))
					{
						return false;
					}
				}
				// single choice
				else
				{
					if (!in_array($value, $data['choices']))
					{
						return false;
					}
				}
			}

			return true;
		}

		return null;
	}

	protected function isUnknownMatched($rule, array $data, \XF\Entity\User $user)
	{
		$eventReturnValue = false;
		$this->app->fire('criteria_user', [$rule, $data, $user, &$eventReturnValue]);

		return $eventReturnValue;
	}

	protected function _matchUsername(array $data, \XF\Entity\User $user)
	{
		$names = Arr::stringToArray(utf8_strtolower($data['names']), '/\s*,\s*/');
		return in_array(utf8_strtolower($user->username), $names);
	}

	protected function _matchUsernameSearch(array $data, \XF\Entity\User $user)
	{
		if ($this->findNeedle($data['needles'], $user->username) === false)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	protected function _matchEmailSearch(array $data, \XF\Entity\User $user)
	{
		if ($this->findNeedle($data['needles'], $user->email) === false)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	protected function _matchRegisteredDays(array $data, \XF\Entity\User $user)
	{
		if (!$user->register_date)
		{
			return false;
		}
		$daysRegistered = floor((time() - $user->register_date) / 86400);
		if ($daysRegistered < $data['days'])
		{
			return false;
		}

		return true;
	}

	protected function _matchMessagesPosted(array $data, \XF\Entity\User $user)
	{
		return ($user->message_count && $user->message_count >= $data['messages']);
	}

	protected function _matchMessagesMaximum(array $data, \XF\Entity\User $user)
	{
		return ($user->message_count <= $data['messages']);
	}

	// note: for backwards compatibility
	protected function _matchLikeCount(array $data, \XF\Entity\User $user)
	{
		$data['reactions'] = $data['likes'];
		unset($data['likes']);
		return $this->_matchReactionScore($data, $user);
	}

	protected function _matchReactionScore(array $data, \XF\Entity\User $user)
	{
		return ($user->reaction_score && $user->reaction_score >= $data['reactions']);
	}

	// note: for backwards compatibility
	protected function _matchLikeRatio(array $data, \XF\Entity\User $user)
	{
		return $this->_matchReactionRatio($data, $user);
	}

	protected function _matchReactionRatio(array $data, \XF\Entity\User $user)
	{
		if (!$user->message_count || !$user->reaction_score)
		{
			return false;
		}

		$ratio = $user->reaction_score / $user->message_count;
		return ($ratio >= $data['ratio']);
	}

	protected function _matchTrophyPoints(array $data, \XF\Entity\User $user)
	{
		return ($user->trophy_points && $user->trophy_points >= $data['points']);
	}

	protected function _matchInactiveDays(array $data, \XF\Entity\User $user)
	{
		if (!$user->last_activity_ || !$user->user_id)
		{
			return false;
		}

		$daysInactive = floor((time() - $user->last_activity_) / 86400);
		if ($daysInactive < $data['days'])
		{
			return false;
		}

		return true;
	}

	protected function _matchIsLoggedIn(array $data, \XF\Entity\User $user)
	{
		return ($user->user_id > 0);
	}

	protected function _matchIsGuest(array $data, \XF\Entity\User $user)
	{
		return ($user->user_id == 0);
	}

	protected function _matchIsModerator(array $data, \XF\Entity\User $user)
	{
		return (bool)$user->is_moderator;
	}

	protected function _matchIsAdmin(array $data, \XF\Entity\User $user)
	{
		return (bool)$user->is_admin;
	}

	protected function _matchIsBanned(array $data, \XF\Entity\User $user)
	{
		return (bool)$user->is_banned;
	}

	protected function _matchWithTfa(array $data, \XF\Entity\User $user)
	{
		return (bool)$user->Option->use_tfa;
	}

	protected function _matchWithoutTfa(array $data, \XF\Entity\User $user)
	{
		return $user->Option->use_tfa ? false : true;
	}

	protected function _matchHasAvatar(array $data, \XF\Entity\User $user)
	{
		return $user->user_id && ($user->avatar_date || $user->gravatar);
	}

	protected function _matchNoAvatar(array $data, \XF\Entity\User $user)
	{
		if (!$user->user_id || $user->avatar_date || $user->gravatar)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	protected function _matchHasHighDpiAvatar(array $data, \XF\Entity\User $user)
	{
		return $user->user_id && ($user->avatar_highdpi || $user->gravatar);
	}

	protected function _matchNoHighdpiAvatar(array $data, \XF\Entity\User $user)
	{
		// don't match if not logged in, has no avatar, or already has a gravatar, or a high-dpi avatar
		if (!$user->user_id || !$user->avatar_date || $user->gravatar || $user->avatar_highdpi)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	protected function _matchConnectedAccounts(array $data, \XF\Entity\User $user)
	{
		if (empty($data['provider_ids']))
		{
			return false;
		}
		foreach ($data['provider_ids'] AS $providerId)
		{
			if (isset($user->Profile->connected_accounts[$providerId]))
			{
				return true;
			}
		}
		return false;
	}

	protected function _matchUserGroups(array $data, \XF\Entity\User $user)
	{
		if (empty($data['user_group_ids']))
		{
			return false;
		}
		return $user->isMemberOf($data['user_group_ids']);
	}

	protected function _matchNotUserGroups(array $data, \XF\Entity\User $user)
	{
		return $this->_matchUserGroups($data, $user) ? false : true;
	}

	protected function _matchUserState(array $data, \XF\Entity\User $user)
	{
		return ($user->user_state && $user->user_state == $data['state']);
	}

	protected function _matchBirthday(array $data, \XF\Entity\User $user)
	{
		if (!$user->user_id || !$user->Profile || !$user->Profile->dob_day || !$user->Profile->dob_month)
		{
			return false;
		}

		try
		{
			$tz = new \DateTimeZone($user->timezone);
		}
		catch (\Exception $e)
		{
			$tz = \XF::language()->getTimeZone();
		}

		$dt = new \DateTime('now', $tz);
		return ("{$user->Profile->dob_day}.{$user->Profile->dob_month}" === $dt->format('j.n'));
	}

	protected function _matchLanguage(array $data, \XF\Entity\User $user)
	{
		$languageId = ($user->language_id ?: $this->app->options()->defaultLanguageId);
		return ($languageId == $data['language_id']);
	}
}