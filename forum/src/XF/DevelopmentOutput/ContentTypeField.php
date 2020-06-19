<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class ContentTypeField extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'content_type_fields';
	}

	public function export(Entity $field)
	{
		if (!$this->isRelevant($field))
		{
			return true;
		}

		$fileName = $this->getFileName($field);

		$json = [
			'content_type' => $field->content_type,
			'field_name' => $field->field_name,
			'field_value' => $field->field_value
		];

		return $this->developmentOutput->writeFile($this->getTypeDir(), $field->addon_id, $fileName, Json::jsonEncodePretty($json));
	}
	
	protected function getEntityForImport($name, $addOnId, $json, array $options)
	{
		$field = \XF::em()->getFinder('XF:ContentTypeField')->where([
			'content_type' => $json->content_type,
			'field_name' => $json->field_name
		])->fetchOne();
		if (!$field)
		{
			$field = \XF::em()->create('XF:ContentTypeField');
		}

		$field = $this->prepareEntityForImport($field, $options);

		return $field;
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents);
		
		$field = $this->getEntityForImport($name, $addOnId, $json, $options);

		$field->content_type = $json->content_type;
		$field->field_name = $json->field_name;
		$field->field_value = $json->field_value;
		$field->addon_id = $addOnId;
		$field->save();
		// this will update the metadata itself

		return $field;
	}

	protected function getFileName(Entity $field, $new = true)
	{
		$contentType = $new ? $field->getValue('content_type') : $field->getExistingValue('content_type');
		$fieldName = $new ? $field->getValue('field_name') : $field->getExistingValue('field_name');
		return "{$contentType}-{$fieldName}.json";
	}
}