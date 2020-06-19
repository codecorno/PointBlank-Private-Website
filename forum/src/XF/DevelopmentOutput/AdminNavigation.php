<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class AdminNavigation extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'admin_navigation';
	}

	public function export(Entity $navigation)
	{
		if (!$this->isRelevant($navigation))
		{
			return true;
		}

		$fileName = $this->getFileName($navigation);

		$keys = [
			'parent_navigation_id',
			'display_order',
			'link',
			'icon',
			'admin_permission_id',
			'debug_only',
			'development_only',
			'hide_no_children'
		];
		$json = $this->pullEntityKeys($navigation, $keys);

		return $this->developmentOutput->writeFile($this->getTypeDir(), $navigation->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$navigation = $this->getEntityForImport($name, $addOnId, $json, $options);
		$navigation->setOption('verify_parent', false);
		$navigation->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);

		$navigation->bulkSetIgnore($json);
		$navigation->navigation_id = $name;
		$navigation->addon_id = $addOnId;
		$navigation->save();
		// this will update the metadata itself

		return $navigation;
	}
}