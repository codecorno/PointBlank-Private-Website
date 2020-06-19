<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property string allow_view_profile
 * @property string allow_post_profile
 * @property string allow_send_personal_conversation
 * @property string allow_view_identities
 * @property string allow_receive_news_feed
 *
 * RELATIONS
 * @property \XF\Entity\User User
 */
class UserPrivacy extends Entity
{
	public function isPrivacyCheckMet($privacyKey, \XF\Entity\User $user)
	{
		if ($this->user_id == $user->user_id || $user->canBypassUserPrivacy())
		{
			return true;
		}

		switch ($this->{$privacyKey})
		{
			case 'everyone': return true;
			case 'none':     return false;
			case 'members':  return ($user->user_id > 0);
			case 'followed': return $this->User->isFollowing($user);
			default:         return true;
		}
	}

	protected function verifyPrivacyChoice(&$choice)
	{
		if (!in_array(strtolower($choice), ['everyone', 'members', 'followed', 'none']))
		{
			$choice = 'none';
		}

		return true;
	}

	protected function _setupDefaults()
	{
		$options = \XF::options();

		$defaults = $options->registrationDefaults;
		$this->allow_view_profile = $defaults['allow_view_profile'];
		$this->allow_post_profile = $defaults['allow_post_profile'];
		$this->allow_send_personal_conversation = $defaults['allow_send_personal_conversation'];
		$this->allow_view_identities = $defaults['allow_view_identities'];
		$this->allow_receive_news_feed = $defaults['allow_receive_news_feed'];
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_privacy';
		$structure->shortName = 'XF:UserPrivacy';
		$structure->primaryKey = 'user_id';
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true],
			'allow_view_profile' => ['type' => self::STR, 'default' => 'everyone',
				'allowedValues' => ['everyone', 'members', 'followed', 'none'],
				'verify' => 'verifyPrivacyChoice'
			],
			'allow_post_profile' => ['type' => self::STR, 'default' => 'members',
				'allowedValues' => ['everyone', 'members', 'followed', 'none'],
				'verify' => 'verifyPrivacyChoice'
			],
			'allow_send_personal_conversation' => ['type' => self::STR, 'default' => 'members',
				'allowedValues' => ['everyone', 'members', 'followed', 'none'],
				'verify' => 'verifyPrivacyChoice'
			],
			'allow_view_identities' => ['type' => self::STR, 'default' => 'everyone',
				'allowedValues' => ['everyone', 'members', 'followed', 'none'],
				'verify' => 'verifyPrivacyChoice'
			],
			'allow_receive_news_feed' => ['type' => self::STR, 'default' => 'everyone',
				'allowedValues' => ['everyone', 'members', 'followed', 'none'],
				'verify' => 'verifyPrivacyChoice'
			]
		];
		$structure->behaviors = [
			'XF:ChangeLoggable' => ['contentType' => 'user']
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			]
		];

		return $structure;
	}
}