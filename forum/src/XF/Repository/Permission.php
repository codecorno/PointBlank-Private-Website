<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Permission extends Repository
{
	public function getGlobalPermissionListData()
	{
		$permissions = $this->findPermissionsForList()->fetch();

		return [
			'interfaceGroups' => $this->findInterfaceGroupsForList()->fetch(),
			'permissionsGrouped' => $permissions->groupBy('interface_group_id')
		];
	}

	public function getContentPermissionListData($contentType)
	{
		$contentHandler = $this->getPermissionHandler($contentType);
		if (!$contentHandler)
		{
			throw new \InvalidArgumentException("No permission handler for $contentType");
		}

		$permissions = $this->findPermissionsForList()->fetch();
		$permissions = $permissions->filter(function($p) use($contentHandler)
		{
			return $contentHandler->isValidPermission($p);
		});

		return [
			'interfaceGroups' => $this->findInterfaceGroupsForList()->fetch(),
			'permissionsGrouped' => $permissions->groupBy('interface_group_id')
		];
	}

	/**
	 * @return Finder
	 */
	public function findPermissionsForList()
	{
		return $this->finder('XF:Permission')
			->whereAddOnActive()
			->order(['display_order', 'permission_id']);
	}

	public function getPermissionsGrouped()
	{
		$permissions = $this->finder('XF:Permission')
			->order(['permission_group_id', 'permission_id'])->fetch();
		return $permissions->groupBy('permission_group_id', 'permission_id');
	}

	/**
	 * @return Finder
	 */
	public function findInterfaceGroupsForList()
	{
		return $this->finder('XF:PermissionInterfaceGroup')
			->whereAddOnActive()
			->order(['display_order', 'interface_group_id']);
	}

	/**
	 * @return \XF\Permission\AbstractContentPermissions[]
	 */
	public function getPermissionHandlers()
	{
		return $this->app()->permissionBuilder()->getContentHandlers();
	}

	/**
	 * @param string $type
	 *
	 * @return \XF\Permission\AbstractContentPermissions|null
	 */
	public function getPermissionHandler($type)
	{
		return $this->app()->permissionBuilder()->getContentHandler($type);
	}
}