<?php

namespace XF\Import\Data;

class UserGroup extends AbstractEmulatedData
{
	protected $permissions = [];

	public function getImportType()
	{
		return 'user_group';
	}

	public function getEntityShortName()
	{
		return 'XF:UserGroup';
	}

	public function setPermissions(array $permissions)
	{
		$this->permissions = $permissions;
	}

	protected function postSave($oldId, $newId)
	{
		if ($this->permissions)
		{
			/** @var \XF\Import\DataHelper\Permission $permissionHelper */
			$permissionHelper = $this->dataManager->helper('XF:Permission');
			$permissionHelper->insertUserGroupPermissions($newId, $this->permissions);
		}

		/** @var \XF\Repository\UserGroup $repo */
		$repo = $this->repository('XF:UserGroup');

		\XF::runOnce('rebuildUserGroupImport', function() use ($repo)
		{
			$repo->rebuildDisplayStyleCache();
			$repo->rebuildUserBannerCache();
		});
	}
}