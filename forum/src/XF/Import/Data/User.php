<?php

namespace XF\Import\Data;

class User extends AbstractData
{
	/**
	 * @var EntityEmulator[]
	 */
	protected $tables = [];

	protected $regIp;

	protected $avatarPath;

	/**
	 * @var Admin|null
	 */
	protected $admin;

	/**
	 * @var UserBan|null
	 */
	protected $ban;

	protected $permissions = [];

	protected $rejection = [
		'date' => null,
		'user_id' => 0,
		'reason' => ''
	];

	public function getImportType()
	{
		return 'user';
	}

	protected function init()
	{
		$em = $this->em();
		$vf = $em->getValueFormatter();

		$this->tables = [
			'user' => new EntityEmulator($this, $em->getEntityStructure('XF:User'), $vf),
			'option' => new EntityEmulator($this, $em->getEntityStructure('XF:UserOption'), $vf),
			'privacy' => new EntityEmulator($this, $em->getEntityStructure('XF:UserPrivacy'), $vf),
			'profile' => new EntityEmulator($this, $em->getEntityStructure('XF:UserProfile'), $vf),
			'auth' => new EntityEmulator($this, $em->getEntityStructure('XF:UserAuth'), $vf),
		];
	}

	public function setPasswordData($scheme, array $data)
	{
		$auth = $this->tables['auth'];
		$auth->scheme_class = $scheme;
		$auth->data = $data;
	}

	public function setRegistrationIp($ip)
	{
		$this->regIp = $ip;
	}

	public function setRejectionDetails(array $details)
	{
		foreach ($details AS $k => $v)
		{
			if (substr($k, 0, 7) == 'reject_')
			{
				$k = substr($k, 7);
			}
			if (isset($this->rejection[$k]))
			{
				$this->rejection[$k] = $v;
			}
		}
	}

	public function setAdmin(array $data)
	{
		if (!$this->admin)
		{
			$this->admin = $this->dataManager->newHandler('XF:Admin', false);
		}

		$this->admin->bulkSet($data);
	}

	public function setBan(array $data)
	{
		if (!$this->ban)
		{
			$this->ban = $this->dataManager->newHandler('XF:UserBan', false);
		}

		$this->ban->bulkSet($data);
	}

	public function setPermissions(array $permissions)
	{
		$this->permissions = $permissions;
	}

	public function setCustomFields(array $customFields)
	{
		foreach ($customFields AS $k => &$fieldValue)
		{
			if (is_string($fieldValue))
			{
				$fieldValue = $this->convertToUtf8($fieldValue);
			}
			else if (is_null($fieldValue))
			{
				unset($customFields[$k]);
			}
		}

		$this->tables['profile']->custom_fields = $customFields;
	}

	public function setAvatarPath($path)
	{
		$this->avatarPath = $path;
	}

	public function set($field, $value, array $options = [])
	{
		$set = false;
		foreach ($this->tables AS $table)
		{
			if ($table->exists($field))
			{
				$table->set($field, $value, $options);
				$set = true;
			}
		}

		if (!$set)
		{
			throw new \InvalidArgumentException("Unknown column '$field'");
		}
	}

	public function bulkSetDirect($part, array $fields, array $options = [])
	{
		foreach ($fields AS $field => $value)
		{
			$this->setDirect($part, $field, $value, $options);
		}
	}

	public function setDirect($part, $field, $value, array $options = [])
	{
		if (!isset($this->tables[$part]))
		{
			throw new \InvalidArgumentException("Invalid table '$part'");
		}

		$this->tables[$part]->set($field, $value, $options);
	}

	public function get($field)
	{
		foreach ($this->tables AS $table)
		{
			if ($table->exists($field))
			{
				return $table->get($field);
			}
		}

		throw new \InvalidArgumentException("Unknown column '$field'");
	}

	protected function write($oldId)
	{
		$db = $this->db();
		$user = $this->tables['user'];

		if ($oldId == 1 && $this->retainIds())
		{
			// We need to keep admin access for user 1 if retaining IDs as otherwise, we might not be able to continue.
			// For safety, keep the email and password that was registered with the install to prevent someone from
			// being able to gain access to this.

			$user->remove(['last_activity', 'is_admin', 'email']);
			$this->admin = null;
			$this->tables['profile']->remove('password_date');
			$this->tables['auth']->remove(['scheme_class', 'data']);

			foreach ($this->tables AS $table)
			{
				$table->update($oldId, $db);
			}

			$newId = $oldId;
		}
		else
		{
			$newId = $user->insert($oldId, $db);

			foreach ($this->tables AS $type => $table)
			{
				if ($type == 'user')
				{
					continue;
				}

				$table->set('user_id', $newId);
				$table->insert(false, $db);
			}
		}

		return $newId;
	}

	protected function importedIdFound($oldId, $newId)
	{
		foreach ($this->tables AS $table)
		{
			$table->set('user_id', $newId);
		}
	}

	protected function preSave($oldId)
	{
		$user = $this->tables['user'];

		if (strpos($user->username, ',') !== false)
		{
			throw new \LogicException("Username cannot contain a comma");
		}

		if ($oldId == 1 && $this->retainIds())
		{
			// we're just doing an update so don't do any other work we'd be doing for an insert
			return;
		}

		if (!$user->secret_key)
		{
			$user->secret_key = \XF::generateRandomString(32);
		}

		if (!$user->language_id)
		{
			// unless a language ID has been set, set the default (as XF doesn't use 0).
			$user->language_id = \XF::options()->defaultLanguageId;
		}

		// TODO: check DoB
	}

	protected function postSave($oldId, $newId)
	{
		$isMainUser = ($oldId == 1 && $this->retainIds());

		$user = $this->tables['user'];
		$groupInserts = [
			[
				'user_id' => $newId,
				'user_group_id' => $user->user_group_id,
				'is_primary' => 1
			]
		];
		foreach ($user->secondary_group_ids AS $groupId)
		{
			$groupInserts[] = [
				'user_id' => $newId,
				'user_group_id' => $groupId,
				'is_primary' => 0
			];
		}
		$this->db()->insertBulk('xf_user_group_relation', $groupInserts, false, 'is_primary = VALUES(is_primary)');

		$profile = $this->tables['profile'];
		if ($profile->custom_fields)
		{
			$this->insertCustomFieldValues('xf_user_field_value', 'user_id', $newId, $profile->custom_fields);
		}

		$option = $this->tables['option'];
		if ($option->alert_optout)
		{
			$optOutInserts = [];
			foreach ($option->alert_optout AS $optOut)
			{
				$optOutInserts[] = [
					'user_id' => $newId,
					'alert' => $optOut
				];
			}
			$this->db()->insertBulk('xf_user_alert_optout', $optOutInserts, false, false, 'IGNORE');
		}

		if ($this->user_state == 'moderated')
		{
			$this->db()->insert('xf_approval_queue', [
				'content_type' => 'user',
				'content_id' => $newId,
				'content_date' => $this->register_date
			], true);
		}
		else if ($this->user_state == 'rejected')
		{
			$this->db()->insert('xf_user_reject', [
				'user_id' => $newId,
				'reject_date' => $this->rejection['date'] ?: $this->register_date,
				'reject_user_id' => $this->rejection['user_id'] ?: 0,
				'reject_reason' => $this->convertToUtf8($this->rejection['reason']) ?: ''
			], true);
		}

		if ($this->regIp)
		{
			$this->importRawIp($newId, 'user', $newId, 'register', $this->regIp, $this->register_date);
		}

		if ($this->permissions)
		{
			/** @var \XF\Import\DataHelper\Permission $permissionHelper */
			$permissionHelper = $this->dataManager->helper('XF:Permission');
			$permissionHelper->insertUserPermissions($newId, $this->permissions);
		}

		if ($this->admin && !$isMainUser)
		{
			$this->admin->user_id = $newId;
			$this->admin->save($oldId);
		}
		if ($this->ban)
		{
			$this->ban->user_id = $newId;
			$this->ban->save($oldId);
		}

		/** @var \XF\Entity\User $user */
		$user = $this->em()->find('XF:User', $newId);
		$user->rebuildPermissionCombination();

		if ($this->avatarPath)
		{
			/** @var \XF\Import\DataHelper\Avatar $avatarHelper */
			$avatarHelper = $this->dataManager->helper('XF:Avatar');
			$avatarHelper->setAvatarFromFile($this->avatarPath, $user);
		}

		$this->em()->detachEntity($user);
	}

	public function mergeFromInto($oldUserId, $targetUserId)
	{
		$mappedId = $this->dataManager->lookup($this->getImportType(), $oldUserId);
		if ($mappedId !== false)
		{
			return $mappedId;
		}

		$db = $this->db();

		$db->beginTransaction();

		foreach ($this->getMergeUpdateSql() AS $table => $updates)
		{
			$db->query("
				UPDATE `{$table}` SET
					" . implode(",\n", $updates) . "
				WHERE user_id = ?
			", $targetUserId);
		}

		$this->dataManager->log($this->getImportType(), $oldUserId, $targetUserId);

		$db->commit();

		return $targetUserId;
	}

	protected function getMergeUpdateSql()
	{
		$updates = [];
		$db = $this->db();

		if ($this->message_count)
		{
			$updates['xf_user']['message_count'] = 'message_count = message_count + ' . $db->quote($this->message_count);
		}
		if ($this->register_date)
		{
			$updates['xf_user']['register_date'] = 'register_date = LEAST(register_date, ' . $db->quote($this->register_date) . ')';
		}

		return $updates;
	}

}