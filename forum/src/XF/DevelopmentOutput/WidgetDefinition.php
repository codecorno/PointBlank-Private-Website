<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class WidgetDefinition extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'widget_definitions';
	}

	public function export(Entity $widgetDefinition)
	{
		if (!$this->isRelevant($widgetDefinition))
		{
			return true;
		}

		$fileName = $this->getFileName($widgetDefinition);

		$keys = [
			'definition_class'
		];
		$json = $this->pullEntityKeys($widgetDefinition, $keys);

		return $this->developmentOutput->writeFile($this->getTypeDir(), $widgetDefinition->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$widgetDefinition = $this->getEntityForImport($name, $addOnId, $json, $options);

		$widgetDefinition->bulkSetIgnore($json);
		$widgetDefinition->definition_id = $name;
		$widgetDefinition->addon_id = $addOnId;
		$widgetDefinition->save();
		// this will update the metadata itself

		return $widgetDefinition;
	}
}