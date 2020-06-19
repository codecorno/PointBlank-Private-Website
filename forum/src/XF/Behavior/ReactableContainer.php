<?php

namespace XF\Behavior;

use XF\Mvc\Entity\Behavior;

class ReactableContainer extends Behavior
{
	protected function getDefaultConfig()
	{
		return [
			'childContentType' => null,
			'childIds' => null,
			'stateField' => null,
		];
	}

	protected function verifyConfig()
	{
		if (!$this->config['childContentType'])
		{
			throw new \LogicException("A childContentType value must be specified");
		}

		if (!is_array($this->config['childIds']) && !is_callable($this->config['childIds']))
		{
			throw new \LogicException("A childIds value must be callable (receiving the entity) or an array");
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

			if ($this->entity->isUpdate() && ($visibilityChange == 'enter' || $visibilityChange == 'leave'))
			{
				/** @var \XF\Repository\Reaction $reactionRepo */
				$reactionRepo = $this->repository('XF:Reaction');
				$reactionRepo->recalculateReactionIsCounted($this->config['childContentType'], $this->getChildIds());
			}
		}
	}

	public function postDelete()
	{
		/** @var \XF\Repository\Reaction $reactionRepo */
		$reactionRepo = $this->repository('XF:Reaction');
		$reactionRepo->fastDeleteReactions($this->config['childContentType'], $this->getChildIds());
	}

	protected function getChildIds()
	{
		$childIds = $this->config['childIds'];

		if (is_array($childIds))
		{
			return $childIds;
		}
		else
		{
			return $childIds($this->entity);
		}
	}
}