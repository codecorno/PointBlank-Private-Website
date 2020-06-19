<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class Permission extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'permissions';
	}
	
	public function export(Entity $permission)
	{
		if (!$this->isRelevant($permission))
		{
			return true;
		}

		$fileName = $this->getFileName($permission);

		$keys = [
			'permission_type',
			'interface_group_id',
			'display_order',
			'depend_permission_id'
		];
		$json = $this->pullEntityKeys($permission, $keys);

		return $this->developmentOutput->writeFile($this->getTypeDir(), $permission->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	protected function getEntityForImport($name, $addOnId, $json, array $options)
	{
		list($groupId, $permissionId) = explode('-', $name);

		/** @var \XF\Entity\Permission $permission */
		$permission = \XF::em()->find('XF:Permission', ['permission_group_id' => $groupId, 'permission_id' => $permissionId]);
		if (!$permission)
		{
			$permission = \XF::em()->create('XF:Permission');
		}

		$permission = $this->prepareEntityForImport($permission, $options);

		return $permission;
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$permission = $this->getEntityForImport($name, $addOnId, $json, $options);
		$permission->setOption('dependent_check', false);

		list($groupId, $permissionId) = explode('-', $name);

		$permission->bulkSetIgnore($json);
		$permission->permission_group_id = $groupId;
		$permission->permission_id = $permissionId;
		$permission->addon_id = $addOnId;
		$permission->save();
		// this will update the metadata itself

		return $permission;
	}

	protected function getFileName(Entity $permission, $new = true)
	{
		$groupId = $new ? $permission->getValue('permission_group_id') : $permission->getExistingValue('permission_group_id');
		$permissionId = $new ? $permission->getValue('permission_id') : $permission->getExistingValue('permission_id');
		return "{$groupId}-{$permissionId}.json";
	}
}