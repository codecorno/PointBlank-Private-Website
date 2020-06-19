<?php

namespace XF\Behavior;

use XF\Mvc\Entity\Behavior;

class DesignerOutputWritable extends Behavior
{
	protected function getDefaultOptions()
	{
		return [
			'write_designer_output' => true
		];
	}

	public function postSave()
	{
		if (!$this->isDesignerOutputWritable())
		{
			return;
		}

		$entity = $this->entity;
		$devOutput = \XF::app()->designerOutput();

		if ($devOutput->hasNameChange($entity))
		{
			$devOutput->delete($entity, false);
		}

		$devOutput->export($this->entity);
	}

	public function postDelete()
	{
		if (!$this->isDesignerOutputWritable())
		{
			return;
		}

		\XF::app()->designerOutput()->delete($this->entity);
	}

	public function isDesignerOutputWritable()
	{
		return (
			$this->options['write_designer_output']
			&& \XF::app()->designerOutput()->isEnabled()
		);
	}
}