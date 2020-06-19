<?php

namespace XF\DevelopmentOutput;

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
		if (!$this->isRelevant($group))
		{
			return true;
		}

		$fileName = $this->getFileName($group);

		$json = [
			'title' => $group->getValue('title'),
			'description' => $group->getValue('description'),
			'display_order' => $group->display_order
		];

		return $this->developmentOutput->writeFile(
			$this->getTypeDir(), $group->addon_id, $fileName, Json::jsonEncodePretty($json)
		);
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$group = $this->getEntityForImport($name, $addOnId, $json, $options);
		$group->setOption('update_phrase', false);

		$group->bulkSetIgnore($json);
		$group->group_name = $name;
		$group->style_id = 0;
		$group->addon_id = $addOnId;
		$group->save();
		// this will update the metadata itself

		return $group;
	}

	protected function getEntityForImport($name, $addOnId, $json, array $options)
	{
		/** @var \XF\Entity\StylePropertyGroup $group */
		$group = \XF::em()->getFinder('XF:StylePropertyGroup')->where([
			'group_name' => $name,
			'style_id' => 0
		])->fetchOne();
		if (!$group)
		{
			$group = \XF::em()->create('XF:StylePropertyGroup');
		}

		$group = $this->prepareEntityForImport($group, $options);

		return $group;
	}

	protected function getFileName(Entity $entity, $new = true)
	{
		$id = $new ? $entity->getValue('group_name') : $entity->getExistingValue('group_name');
		return "{$id}.json";
	}

	protected function isRelevant(Entity $entity, $new = true)
	{
		$styleId = $new ? $entity->getValue('style_id') : $entity->getExistingValue('style_id');
		return parent::isRelevant($entity, $new) && !$styleId;
	}
}