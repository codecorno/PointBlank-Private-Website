<?php

namespace XF\Behavior;

use XF\Mvc\Entity\Behavior;

class Taggable extends Behavior
{
	protected function getDefaultConfig()
	{
		return [
			'stateField' => null
		];
	}

	protected function verifyConfig()
	{
		if (!$this->contentType())
		{
			throw new \LogicException("Structure must provide a contentType value");
		}

		if ($this->config['stateField'] === null)
		{
			throw new \LogicException("stateField config must be overridden; if no field is present, use an empty string");
		}
	}

	public function postSave()
	{
		if ($this->config['stateField'])
		{
			$visibilityChange = $this->entity->isStateChanged($this->config['stateField'], 'visible');

			if ($this->entity->isUpdate())
			{
				/** @var \XF\Repository\Tag $tagRepo */
				$tagRepo = $this->repository('XF:Tag');

				if ($visibilityChange == 'enter')
				{
					$tagRepo->updateContentVisibility($this->contentType(), $this->id(), true);
				}
				else if ($visibilityChange == 'leave')
				{
					$tagRepo->updateContentVisibility($this->contentType(), $this->id(), false);
				}
			}
		}
	}

	public function postDelete()
	{
		/** @var \XF\Repository\Tag $tagRepo */
		$tagRepo = $this->repository('XF:Tag');
		$tagRepo->removeContentTags($this->contentType(), $this->id());
	}
}