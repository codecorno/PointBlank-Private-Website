<?php

namespace XF\DesignerOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class StylePropertyGroup extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'style_property_groups';
	}

	public function export(Entity $group)
	{
		$fileName = $this->getFileName($group);

		$json = [
			'title' => $group->getValue('title'),
			'description' => $group->getValue('description'),
			'display_order' => $group->display_order
		];

		return $this->designerOutput->writeFile(
			$this->getTypeDir(), $group->Style, $fileName, Json::jsonEncodePretty($json)
		);
	}

	protected function getEntityForImport($name, $styleId, $json, array $options)
	{
		/** @var \XF\Entity\StylePropertyGroup $group */
		$group = \XF::em()->getFinder('XF:StylePropertyGroup')->where([
			'group_name' => $name,
			'style_id' => $styleId
		])->fetchOne();
		if (!$group)
		{
			$group = \XF::em()->create('XF:StylePropertyGroup');
		}

		$group = $this->prepareEntityForImport($group, $options);

		return $group;
	}

	public function import($name, $styleId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$group = $this->getEntityForImport($name, $styleId, $json, $options);
		$group->setOption('update_phrase', false);

		$group->bulkSet($json);
		$group->group_name = $name;
		$group->style_id = $styleId;
		$group->save();
		// this will update the metadata itself

		return $group;
	}

	protected function getFileName(Entity $entity, $new = true)
	{
		$id = $new ? $entity->getValue('group_name') : $entity->getExistingValue('group_name');
		return "{$id}.json";
	}
}