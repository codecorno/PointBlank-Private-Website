<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class TemplateModification extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'template_modifications';
	}

	public function export(Entity $modification)
	{
		if (!$this->isRelevant($modification))
		{
			return true;
		}

		$fileName = $this->getFileName($modification);

		$json = [
			'template' => $modification->template,
			'description' => $modification->description,
			'execution_order' => $modification->execution_order,
			'enabled' => $modification->enabled,
			'action' => $modification->action,
			'find' => $modification->find,
			'replace' => $modification->replace
		];

		return $this->developmentOutput->writeFile($this->getTypeDir(), $modification->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	protected function getEntityForImport($name, $addOnId, $json, array $options)
	{
		list($type, $modKey) = explode('/', $name, 2);

		$modification = \XF::em()->getFinder('XF:TemplateModification')->where('modification_key', $modKey)->fetchOne();
		if (!$modification)
		{
			$modification = \XF::em()->create('XF:TemplateModification');
		}

		$modification = $this->prepareEntityForImport($modification, $options);

		return $modification;
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		list($type, $modKey) = explode('/', $name, 2);

		$json = json_decode($contents);

		$modification = $this->getEntityForImport($name, $addOnId, $json, $options);

		$modification->modification_key = $modKey;
		$modification->type = $type;
		$modification->template = $json->template;
		$modification->description = $json->description;
		$modification->execution_order = $json->execution_order;
		$modification->enabled = $json->enabled;
		$modification->action = $json->action;
		$modification->find = $json->find;
		$modification->replace = $json->replace;
		$modification->addon_id = $addOnId;
		$modification->save();
		// this will update the metadata itself

		return $modification;
	}

	protected function getFileName(Entity $modification, $new = true)
	{
		$modKey = $new ? $modification->getValue('modification_key') : $modification->getExistingValue('modification_key');
		return "{$modification->type}/{$modKey}.json";
	}
}