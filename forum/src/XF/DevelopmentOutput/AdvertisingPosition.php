<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class AdvertisingPosition extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'advertising_positions';
	}

	public function export(Entity $advertisingPosition)
	{
		if (!$this->isRelevant($advertisingPosition))
		{
			return true;
		}

		$fileName = $this->getFileName($advertisingPosition);

		$keys = [
			'arguments',
			'active'
		];
		$json = $this->pullEntityKeys($advertisingPosition, $keys);

		return $this->developmentOutput->writeFile($this->getTypeDir(), $advertisingPosition->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$advertisingPosition = $this->getEntityForImport($name, $addOnId, $json, $options);

		$advertisingPosition->bulkSetIgnore($json);
		$advertisingPosition->position_id = $name;
		$advertisingPosition->addon_id = $addOnId;
		$advertisingPosition->save();
		// this will update the metadata itself

		return $advertisingPosition;
	}
}