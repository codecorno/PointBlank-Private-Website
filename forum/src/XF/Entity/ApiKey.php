<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null api_key_id
 * @property string api_key
 * @property string api_key_hash
 * @property string title
 * @property bool is_super_user
 * @property int user_id
 * @property bool allow_all_scopes
 * @property array scopes
 * @property bool active
 * @property int creation_user_id
 * @property int creation_date
 * @property int last_use_date
 *
 * GETTERS
 * @property mixed key_type
 * @property mixed api_key_snippet
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\User Creator
 */
class ApiKey extends Entity
{
	public function isFallbackKey()
	{
		if ($this->isUpdate())
		{
			return !$this->api_key || !$this->api_key_hash;
		}
		else
		{
			return false;
		}
	}

	public function hasScope($scope)
	{
		if ($this->allow_all_scopes)
		{
			return true;
		}

		return !empty($this->scopes[$scope]);
	}

	public function getKeyType()
	{
		if ($this->is_super_user)
		{
			return 'super';
		}
		else if ($this->user_id)
		{
			return 'user';
		}
		else
		{
			return 'guest';
		}
	}

	public function getApiKeySnippet()
	{
		return substr($this->api_key, 0, 8) . '...';
	}

	public function hasNotifiableChanges()
	{
		return (
			$this->isInsert()
			|| $this->isChanged(['api_key', 'allow_all_scopes', 'scopes', 'is_super_user', 'user_id'])
		);
	}

	public function generateKeyValue()
	{
		return \XF::generateRandomString(32);
	}

	protected function verifyScopes(&$scopes)
	{
		$keyedCache = [];

		foreach ($scopes AS $key => $value)
		{
			if ($value === true || $value === 1 || $value === "1")
			{
				$keyedCache[$key] = true;
			}
			else if (is_int($key) && is_string($value))
			{
				$keyedCache[$value] = true;
			}
		}

		$scopes = $keyedCache;

		return true;
	}

	protected function _preSave()
	{
		if ($this->isInsert())
		{
			$this->api_key = $this->generateKeyValue();

			if (!$this->creation_user_id)
			{
				$this->creation_user_id = \XF::visitor()->user_id;
			}
		}
		else if (!$this->getOption('allow_unsafe_edit'))
		{
			if ($this->isChanged('api_key'))
			{
				$this->error('An API key cannot be changed once it is created.');
			}

			if ($this->isChanged(['is_super_user', 'user_id']))
			{
				$this->error('An API key type cannot be changed once it is created.');
			}
		}

		if ($this->isChanged('api_key'))
		{
			$this->api_key_hash = $this->repository('XF:Api')->getApiKeyHash($this->api_key);
		}

		if ($this->isChanged('allow_all_scopes') && $this->allow_all_scopes)
		{
			$this->scopes = [];
		}
	}

	protected function _postSave()
	{
		if ($this->isChanged('scopes') && $this->getOption('update_scopes_from_cache'))
		{
			$apiKeyId = $this->api_key_id;
			$inserts = [];
			foreach ($this->scopes AS $scope => $null)
			{
				$inserts[] = [
					'api_key_id' => $apiKeyId,
					'api_scope_id' => $scope
				];
			}

			if ($this->isUpdate())
			{
				$this->db()->delete('xf_api_key_scope', 'api_key_id = ?', $apiKeyId);
			}
			if ($inserts)
			{
				$this->db()->insertBulk('xf_api_key_scope', $inserts);
			}
		}
	}

	protected function _postDelete()
	{
		$this->db()->delete('xf_api_key_scope', 'api_key_id = ?', $this->api_key_id);
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_api_key';
		$structure->shortName = 'XF:ApiKey';
		$structure->primaryKey = 'api_key_id';
		$structure->columns = [
			'api_key_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'api_key' => ['type' => self::STR, 'required' => true, 'maxlength' => 32],
			'api_key_hash' => ['type' => self::BINARY, 'required' => true, 'maxlength' => 20],
			'title' => ['type' => self::STR, 'maxLength' => 50, 'required' => true],
			'is_super_user' => ['type' => self::BOOL, 'default' => false],
			'user_id' => ['type' => self::UINT, 'default' => 0],
			'allow_all_scopes' => ['type' => self::BOOL, 'default' => false],
			'scopes' => ['type' => self::JSON_ARRAY, 'default' => []],
			'active' => ['type' => self::BOOL, 'default' => true],
			'creation_user_id' => ['type' => self::UINT, 'default' => 0],
			'creation_date' => ['type' => self::UINT, 'default' => \XF::$time],
			'last_use_date' => ['type' => self::UINT, 'default' => 0],
		];
		$structure->behaviors = [];
		$structure->getters = [
			'key_type' => true,
			'api_key_snippet' => true
		];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'Creator' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => [['user_id', '=', '$creation_user_id']],
				'primary' => true
			]
		];
		$structure->options = [
			'allow_unsafe_edit' => false,
			'update_scopes_from_cache' => true
		];

		return $structure;
	}
}