<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class CodeEvent extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'code_events';
	}
	
	public function export(Entity $event)
	{
		if (!$this->isRelevant($event))
		{
			return true;
		}

		$fileName = $this->getFileName($event);

		$keys = [
			'description',
		];
		$json = $this->pullEntityKeys($event, $keys);

		return $this->developmentOutput->writeFile($this->getTypeDir(), $event->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$event = $this->getEntityForImport($name, $addOnId, $json, $options);

		$event->bulkSetIgnore($json);
		$event->event_id = $name;
		$event->addon_id = $addOnId;
		$event->save();
		// this will update the metadata itself

		return $event;
	}
}