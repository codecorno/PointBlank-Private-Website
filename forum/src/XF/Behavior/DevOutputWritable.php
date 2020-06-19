<?php

namespace XF\Behavior;

use XF\Mvc\Entity\Behavior;

class DevOutputWritable extends Behavior
{
	protected function getDefaultOptions()
	{
		return [
			'write_dev_output' => true
		];
	}

	public function postSave()
	{
		if (!$this->isDevOutputWritable())
		{
			return;
		}

		$entity = $this->entity;

		if (!$entity->getNewValues())
		{
			return;
		}

		$devOutput = \XF::app()->developmentOutput();

		if ($devOutput->hasNameChange($entity))
		{
			$devOutput->delete($entity, false);
		}

		$devOutput->export($entity);
	}

	public function postDelete()
	{
		if (!$this->isDevOutputWritable())
		{
			return;
		}

		\XF::app()->developmentOutput()->delete($this->entity);
	}

	public function isDevOutputWritable()
	{
		return (
			$this->options['write_dev_output']
			&& \XF::app()->developmentOutput()->isEnabled()
		);
	}
}