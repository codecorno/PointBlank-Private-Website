<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class AdminPermission extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'admin_permissions';
	}
	
	public function export(Entity $permission)
	{
		if (!$this->isRelevant($permission))
		{
			return true;
		}

		$fileName = $this->getFileName($permission);

		$keys = [
			'display_order',
		];
		$json = $this->pullEntityKeys($permission, $keys);

		return $this->developmentOutput->writeFile($this->getTypeDir(), $permission->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$permission = $this->getEntityForImport($name, $addOnId, $json, $options);

		$permission->bulkSetIgnore($json);
		$permission->admin_permission_id = $name;
		$permission->addon_id = $addOnId;
		$permission->save();
		// this will update the metadata itself

		return $permission;
	}
}