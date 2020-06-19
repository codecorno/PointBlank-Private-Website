<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class PermissionInterfaceGroup extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'permission_interface_groups';
	}

	public function export(Entity $interfaceGroup)
	{
		if (!$this->isRelevant($interfaceGroup))
		{
			return true;
		}

		$fileName = $this->getFileName($interfaceGroup);

		$keys = [
			'display_order',
			'is_moderator'
		];
		$json = $this->pullEntityKeys($interfaceGroup, $keys);

		return $this->developmentOutput->writeFile($this->getTypeDir(), $interfaceGroup->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$interfaceGroup = $this->getEntityForImport($name, $addOnId, $json, $options);

		$interfaceGroup->bulkSetIgnore($json);
		$interfaceGroup->interface_group_id = $name;
		$interfaceGroup->addon_id = $addOnId;
		$interfaceGroup->save();
		// this will update the metadata itself

		return $interfaceGroup;
	}
}