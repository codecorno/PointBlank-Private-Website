<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class Option extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'options';
	}
	
	public function export(Entity $option)
	{
		if (!$this->isRelevant($option))
		{
			return true;
		}

		// if the only change is the option value, we don't need to write this
		$newValues = $option->getNewValues();
		if (count($newValues) == 1 && $option->isChanged('option_value'))
		{
			return true;
		}

		$fileName = $this->getFileName($option);

		$keys = [
			'edit_format',
			'edit_format_params',
			'data_type',
			'sub_options',
			'validation_class',
			'validation_method'
		];
		$json = $this->pullEntityKeys($option, $keys);
		$json['default_value'] = $option->getValue('default_value');

		$relations = [];
		foreach ($option->Relations AS $relation)
		{
			$relations[$relation->group_id] = $relation->display_order;
		}
		$json['relations'] = $relations;

		return $this->developmentOutput->writeFile($this->getTypeDir(), $option->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$option = $this->getEntityForImport($name, $addOnId, $json, $options);
		$option->setOption('verify_validation_callback', false);

		$relations = $json['relations'];
		unset($json['relations']);

		$option->bulkSetIgnore($json);
		$option->option_id = $name;
		$option->addon_id = $addOnId;
		$option->save();
		// this will update the metadata itself

		if (!empty($relations))
		{
			$option->updateRelations($relations);
		}

		return $option;
	}
}