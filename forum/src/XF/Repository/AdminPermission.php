<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class AdminPermission extends Repository
{
	/**
	 * @return Finder
	 */
	public function findPermissionsForList()
	{
		return $this->finder('XF:AdminPermission')->order(['display_order']);
	}

	public function getPermissionTitlePairs()
	{
		return $this->findPermissionsForList()->fetch()->pluck(function($e, $k)
		{
			return [$k, $e->title];
		});
	}

	public function rebuildAdminPermissionCache()
	{
		$db = $this->em->getDb();
		$permissions = [];
		$permissionsSql = $db->query('
			SELECT admin_permission_entry.user_id, admin_permission_entry.admin_permission_id
			FROM xf_admin_permission_entry AS admin_permission_entry
			INNER JOIN xf_admin_permission AS admin_permission ON
				(admin_permission.admin_permission_id = admin_permission_entry.admin_permission_id)
		');
		while ($permission = $permissionsSql->fetch())
		{
			$permissions[$permission['user_id']][$permission['admin_permission_id']] = true;
		}

		/** @var \XF\Entity\Admin[] $admins */
		$admins = $this->em->findByIds('XF:Admin', array_keys($permissions));
		foreach ($admins AS $admin)
		{
			$admin->permission_cache = $permissions[$admin->user_id];
			$admin->saveIfChanged();
		}
	}
}