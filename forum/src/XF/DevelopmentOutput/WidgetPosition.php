<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class WidgetPosition extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'widget_positions';
	}

	public function export(Entity $widgetPosition)
	{
		if (!$this->isRelevant($widgetPosition))
		{
			return true;
		}

		$fileName = $this->getFileName($widgetPosition);

		$keys = [
			'active'
		];
		$json = $this->pullEntityKeys($widgetPosition, $keys);

		return $this->developmentOutput->writeFile($this->getTypeDir(), $widgetPosition->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$widgetPosition = $this->getEntityForImport($name, $addOnId, $json, $options);

		$widgetPosition->bulkSetIgnore($json);
		$widgetPosition->position_id = $name;
		$widgetPosition->addon_id = $addOnId;
		$widgetPosition->save();
		// this will update the metadata itself

		return $widgetPosition;
	}
}