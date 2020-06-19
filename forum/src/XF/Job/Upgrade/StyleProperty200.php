<?php

namespace XF\Job\Upgrade;

use XF\Job\AbstractJob;

class StyleProperty200 extends AbstractJob
{
	protected $defaultData = [
		'properties' => []
	];

	public function run($maxRunTime)
	{
		if (!$this->data['properties'] || !is_array($this->data['properties']))
		{
			return $this->complete();
		}

		/** @var \XF\Entity\StyleProperty[] $properties */
		$properties = $this->app->finder('XF:StyleProperty')
			->where('style_id', 0)
			->keyedBy('property_name')
			->fetch();

		/** @var \XF\Entity\Style[] $styles */
		$styles = $this->app->finder('XF:Style')->fetch();

		$this->app->db()->beginTransaction();

		foreach ($this->data['properties'] AS $styleId => $propertyMap)
		{
			$styleId = intval($styleId);
			if ($styleId < 1 || !is_array($propertyMap) || !isset($styles[$styleId]))
			{
				continue;
			}

			$style = $styles[$styleId];

			foreach ($propertyMap AS $propertyName => $value)
			{
				if (!isset($properties[$propertyName]))
				{
					continue;
				}

				$property = $properties[$propertyName]->getPropertyCopyInStyle($style);
				if ($property->updatePropertyValue($value))
				{
					$property->save(false, true);
				}
			}
		}

		$this->app->db()->commit();

		return $this->complete();
	}

	public function getStatusMessage()
	{
		return 'Converting legacy style properties...';
	}

	public function canCancel()
	{
		return false;
	}

	public function canTriggerByChoice()
	{
		return false;
	}
}