<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property bool show_dob_year
 * @property bool show_dob_date
 * @property bool content_show_signature
 * @property bool receive_admin_email
 * @property bool email_on_conversation
 * @property bool push_on_conversation
 * @property bool is_discouraged
 * @property string creation_watch_state
 * @property string interaction_watch_state
 * @property array alert_optout
 * @property array push_optout
 * @property bool use_tfa
 *
 * RELATIONS
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\UserAlertOptOut[] AlertOptOut
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\UserPushOptOut[] PushOptOut
 * @property \XF\Entity\User User
 */
class UserOption extends Entity
{
	public function doesReceiveAlert($contentType, $action)
	{
		return in_array("{$contentType}_{$action}", $this->alert_optout) ? false : true;
	}

	public function doesReceivePush($contentType, $action)
	{
		if (!\XF::isPushUsable())
		{
			return false;
		}

		return ($this->doesReceiveAlert($contentType, $action)
			&& in_array("{$contentType}_{$action}", $this->push_optout) ? false : true
		);
	}

	protected function _postSave()
	{
		if ($this->isChanged('alert_optout'))
		{
			$inserts = [];
			foreach ($this->alert_optout AS $optOut)
			{
				$inserts[] = [
					'user_id' => $this->user_id,
					'alert' => $optOut
				];
			}
			$this->db()->delete('xf_user_alert_optout', 'user_id = ?', $this->user_id);
			if ($inserts)
			{
				$this->db()->insertBulk('xf_user_alert_optout', $inserts, true);
			}
		}
		if ($this->isChanged('push_optout'))
		{
			$inserts = [];
			foreach ($this->push_optout AS $optOut)
			{
				$inserts[] = [
					'user_id' => $this->user_id,
					'push' => $optOut
				];
			}
			$this->db()->delete('xf_user_push_optout', 'user_id = ?', $this->user_id);
			if ($inserts)
			{
				$this->db()->insertBulk('xf_user_push_optout', $inserts, true);
			}
		}
	}

	protected function _setupDefaults()
	{
		$options = \XF::options();

		$defaults = $options->registrationDefaults;
		$this->content_show_signature = $defaults['content_show_signature'] ? true : false;
		$this->show_dob_year = $defaults['show_dob_year'] ? true : false;
		$this->show_dob_date = $defaults['show_dob_date'] ? true : false;
		$this->receive_admin_email = $defaults['receive_admin_email'] ? true : false;
		$this->email_on_conversation = $defaults['email_on_conversation'] ? true : false;
		$this->push_on_conversation = $options->enablePush ? true : false;
		$this->creation_watch_state = $defaults['creation_watch_state'];
		$this->interaction_watch_state = $defaults['interaction_watch_state'];
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_user_option';
		$structure->shortName = 'XF:UserOption';
		$structure->primaryKey = 'user_id';
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true, 'changeLog' => false],
			'show_dob_year' => ['type' => self::BOOL, 'default' => true],
			'show_dob_date' => ['type' => self::BOOL, 'default' => true],
			'content_show_signature' => ['type' => self::BOOL, 'default' => true],
			'receive_admin_email' => ['type' => self::BOOL, 'default' => true],
			'email_on_conversation' => ['type' => self::BOOL, 'default' => true],
			'push_on_conversation' => ['type' => self::BOOL, 'default' => true],
			'is_discouraged' => ['type' => self::BOOL, 'default' => false],
			'creation_watch_state' => ['type' => self::STR, 'default' => '',
				'allowedValues' => ['', 'watch_no_email', 'watch_email']
			],
			'interaction_watch_state' => ['type' => self::STR, 'default' => '',
				'allowedValues' => ['', 'watch_no_email', 'watch_email']
			],
			'alert_optout' => ['type' => self::LIST_COMMA, 'default' => [],
				'list' => ['type' => 'str', 'unique' => true, 'sort' => true],
				'changeLog' => false
			],
			'push_optout' => ['type' => self::LIST_COMMA, 'default' => [],
				'list' => ['type' => 'str', 'unique' => true, 'sort' => true],
				'changeLog' => false
			],
			'use_tfa' => ['type' => self::BOOL, 'default' => false]
		];
		$structure->behaviors = [
			'XF:ChangeLoggable' => ['contentType' => 'user']
		];
		$structure->getters = [];
		$structure->relations = [
			'AlertOptOut' => [
				'entity' => 'XF:UserAlertOptOut',
				'type' => self::TO_MANY,
				'conditions' => 'user_id'
			],
			'PushOptOut' => [
				'entity' => 'XF:UserPushOptOut',
				'type' => self::TO_MANY,
				'conditions' => 'user_id'
			],
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