<?php

namespace XF;

class PermissionCache
{
	protected $db;

	protected $globalPerms = [];
	protected $contentPerms = [];

	protected $globalCacheRun = [];

	public function __construct(Db\AbstractAdapter $db)
	{
		$this->db = $db;
	}

	public function getPermissionSet($permissionCombinationId)
	{
		return new PermissionSet($this, $permissionCombinationId);
	}

	public function getGlobalPerms($permissionCombinationId)
	{
		// value will be null if we failed to retrieve the permissions
		if (array_key_exists($permissionCombinationId, $this->globalPerms))
		{
			return $this->globalPerms[$permissionCombinationId];
		}

		$cache = $this->db->fetchOne("
			SELECT cache_value
			FROM xf_permission_combination
			WHERE permission_combination_id = ?
		", $permissionCombinationId);

		if ($cache)
		{
			$cache = $this->decodePermissionCache($cache);
		}
		if (!$cache)
		{
			$cache = null;
		}

		$this->globalPerms[$permissionCombinationId] = $cache;

		return $cache;
	}

	public function setGlobalPerms($permissionCombinationId, array $perms)
	{
		$this->globalPerms[$permissionCombinationId] = $perms;
	}

	public function getContentPerms($permissionCombinationId, $contentType, $contentId)
	{
		if (
			isset($this->contentPerms[$permissionCombinationId][$contentType])
			&& array_key_exists($contentId, $this->contentPerms[$permissionCombinationId][$contentType])
		)
		{
			return $this->contentPerms[$permissionCombinationId][$contentType][$contentId];
		}

		$cache = $this->db->fetchOne("
			SELECT cache_value
			FROM xf_permission_cache_content
			WHERE permission_combination_id = ?
				AND content_type = ?
				AND content_id = ?
		", [$permissionCombinationId, $contentType, $contentId]);

		if ($cache)
		{
			$cache = $this->decodePermissionCache($cache);
		}
		if (!$cache)
		{
			$cache = null;
		}

		$this->contentPerms[$permissionCombinationId][$contentType][$contentId] = $cache;

		return $cache;
	}

	public function cacheAllContentPerms($permissionCombinationId, $contentType)
	{
		if (isset($this->globalCacheRun[$contentType][$permissionCombinationId]))
		{
			return;
		}
		$this->globalCacheRun[$contentType][$permissionCombinationId] = true;

		$permsResult = $this->db->fetchPairs("
			SELECT content_id, cache_value
			FROM xf_permission_cache_content
			WHERE permission_combination_id = ?
				AND content_type = ?
		", [$permissionCombinationId, $contentType]);

		foreach ($permsResult AS $contentId => $cache)
		{
			$cache = $this->decodePermissionCache($cache);

			$this->contentPerms[$permissionCombinationId][$contentType][$contentId] = $cache;
		}
	}

	public function cacheContentPermsByIds($permissionCombinationId, $contentType, array $contentIds)
	{
		if (!$contentIds)
		{
			return;
		}

		$permsResult = $this->db->fetchPairs("
			SELECT content_id, cache_value
			FROM xf_permission_cache_content
			WHERE permission_combination_id = ?
				AND content_type = ?
				AND content_id IN (" . $this->db->quote($contentIds) . ")
		", [$permissionCombinationId, $contentType]);

		foreach ($permsResult AS $contentId => $cache)
		{
			$cache = $this->decodePermissionCache($cache);

			$this->contentPerms[$permissionCombinationId][$contentType][$contentId] = $cache;
		}
	}

	public function cacheMultipleContentPermsForContent(array $permissionCombinationIds, $contentType, $contentId)
	{
		if (!$permissionCombinationIds)
		{
			return;
		}

		$permsResult = $this->db->fetchPairs("
			SELECT permission_combination_id, cache_value
			FROM xf_permission_cache_content
			WHERE permission_combination_id IN (" . $this->db->quote($permissionCombinationIds) . ")
				AND content_type = ?
				AND content_id = ?
		", [$contentType, $contentId]);

		foreach ($permsResult AS $permissionCombinationId => $cache)
		{
			$cache = $this->decodePermissionCache($cache);

			$this->contentPerms[$permissionCombinationId][$contentType][$contentId] = $cache;
		}
	}

	protected function decodePermissionCache($cache)
	{
		$cacheDecoded = @json_decode($cache, true);
		if ($cacheDecoded === null && \XF::options()->currentVersionId < 2010010)
		{
			$cacheDecoded = @unserialize($cache);
		}

		return $cacheDecoded ?: null;
	}

	public function setContentPerms($permissionCombinationId, $contentType, $contentId, array $perms)
	{
		$this->contentPerms[$permissionCombinationId][$contentType][$contentId] = $perms;
	}

	public function bulkSetContentPerms($permissionCombinationId, $contentType, array $permsPerContent)
	{
		foreach ($permsPerContent AS $contentId => $perms)
		{
			$this->contentPerms[$permissionCombinationId][$contentType][$contentId] = $perms;
		}
	}
}