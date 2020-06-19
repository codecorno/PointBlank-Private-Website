<?php

namespace XF;

class PermissionSet
{
	protected $permCache;
	protected $permissionCombinationId;

	protected $globalPerms = [];

	public function __construct(PermissionCache $permCache, $permissionCombinationId)
	{
		$this->permCache = $permCache;
		$this->permissionCombinationId = $permissionCombinationId;
	}

	public function getPermissionCache()
	{
		return $this->permCache;
	}

	public function getPermissionCombinationId()
	{
		return $this->permissionCombinationId;
	}

	public function getGlobalPerms()
	{
		return $this->permCache->getGlobalPerms($this->permissionCombinationId);
	}

	public function hasGlobalPermission($group, $permission)
	{
		$permissions = $this->permCache->getGlobalPerms($this->permissionCombinationId);
		if (!$permissions || !isset($permissions[$group][$permission]))
		{
			return false;
		}

		return $permissions[$group][$permission];
	}

	public function getContentPerms($contentType, $contentId)
	{
		return $this->permCache->getContentPerms($this->permissionCombinationId, $contentType, $contentId);
	}

	public function hasContentPermission($contentType, $contentId, $permission)
	{
		$permissions = $this->permCache->getContentPerms($this->permissionCombinationId, $contentType, $contentId);
		if (!$permissions || !isset($permissions[$permission]))
		{
			return false;
		}

		return $permissions[$permission];
	}

	public function cacheAllContentPerms($contentType)
	{
		$this->permCache->cacheAllContentPerms($this->permissionCombinationId, $contentType);
	}
}