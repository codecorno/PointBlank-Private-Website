<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class OptionGroup extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'option_groups';
	}
	
	public function export(Entity $group)
	{
		if (!$this->isRelevant($group))
		{
			return true;
		}

		$fileName = $this->getFileName($group);

		$keys = [
			'icon',
			'display_order',
			'debug_only'
		];
		$json = $this->pullEntityKeys($group, $keys);

		return $this->developmentOutput->writeFile($this->getTypeDir(), $group->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$group = $this->getEntityForImport($name, $addOnId, $json, $options);

		$group->bulkSetIgnore($json);
		$group->group_id = $name;
		$group->addon_id = $addOnId;
		$group->save();
		// this will update the metadata itself

		return $group;
	}
}