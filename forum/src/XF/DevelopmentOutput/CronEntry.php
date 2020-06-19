<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class CronEntry extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'cron_entries';
	}

	public function export(Entity $entry)
	{
		if (!$this->isRelevant($entry))
		{
			return true;
		}

		$fileName = $this->getFileName($entry);

		$keys = [
			'cron_class',
			'cron_method',
			'run_rules',
			'active'
		];
		$json = $this->pullEntityKeys($entry, $keys);

		return $this->developmentOutput->writeFile($this->getTypeDir(), $entry->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$entry = $this->getEntityForImport($name, $addOnId, $json, $options);

		$entry->bulkSetIgnore($json);
		$entry->entry_id = $name;
		$entry->addon_id = $addOnId;
		$entry->save();
		// this will update the metadata itself

		return $entry;
	}
}