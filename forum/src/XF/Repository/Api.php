<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Api extends Repository
{
	/**
	 * @return Finder
	 */
	public function findApiKeysForList()
	{
		$finder = $this->finder('XF:ApiKey');

		return $finder
			->with(['User', 'Creator'])
			->setDefaultOrder('creation_date', 'desc');
	}

	public function getApiKeyHash($apiKey)
	{
		return sha1($apiKey, true);
	}

	/**
	 * @param string $key
	 * @param null|string|array $with
	 *
	 * @return null|\XF\Entity\ApiKey
	 */
	public function findApiKeyByKey($key, $with = null)
	{
		$hash = $this->getApiKeyHash($key);

		// look up based on the hash to do an efficient lookup but to make timing attacks much less viable
		$matchKey = $this->em->findOne('XF:ApiKey', ['api_key_hash' => $hash], $with);
		if ($matchKey && $matchKey->api_key === $key)
		{
			return $matchKey;
		}

		return null;
	}

	public function getFallbackApiKey()
	{
		$values = [
			'api_key_id' => 0,
			'api_key' => '',
			'api_key_hash' => '',
			'title' => 'Fallback',
			'is_super_user' => false,
			'user_id' => 0,
			'allow_all_scopes' => false,
			'scopes' => '[]',
			'active' => true,
			'creation_user_id' => 0,
			'creation_date' => \XF::$time,
			'last_use_date' => \XF::$time
		];
		$apiKey = $this->em->instantiateEntity('XF:ApiKey', $values);
		$apiKey->setReadOnly(true);
		$this->em->detachEntity($apiKey);

		return $apiKey;
	}

	/**
	 * @return Finder
	 */
	public function findApiScopesForList()
	{
		$finder = $this->finder('XF:ApiScope');

		return $finder->setDefaultOrder('api_scope_id');
	}

	public function rebuildApiScopeCache()
	{
		$db = $this->em->getDb();
		$scopes = [];
		$scopesSql = $db->query('
			SELECT ks.*
			FROM xf_api_key_scope AS ks
			INNER JOIN xf_api_scope AS s ON (ks.api_scope_id = s.api_scope_id)
		');
		while ($scope = $scopesSql->fetch())
		{
			$scopes[$scope['api_key_id']][$scope['api_scope_id']] = true;
		}

		/** @var \XF\Entity\ApiKey[] $keys */
		$keys = $this->em->findByIds('XF:ApiKey', array_keys($scopes));
		foreach ($keys AS $key)
		{
			$key->scopes = $scopes[$key->api_key_id];
			$key->setOption('update_scopes_from_cache', false);
			$key->saveIfChanged();
		}
	}

	public function pruneAttachmentKeys($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = \XF::$time - 86400;
		}

		$this->db()->delete('xf_api_attachment_key', 'create_date < ?', $cutOff);
	}
}