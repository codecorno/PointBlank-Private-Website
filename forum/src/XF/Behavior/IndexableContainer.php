<?php

namespace XF\Behavior;

use XF\Mvc\Entity\Behavior;

class IndexableContainer extends Behavior
{
	protected $onDeleteChildIds;

	protected function getDefaultConfig()
	{
		return [
			'childContentType' => null,
			'childIds' => null,
			'checkForUpdates' => null
		];
	}

	protected function verifyConfig()
	{
		if (!$this->config['childContentType'])
		{
			throw new \LogicException("A childContentType value must be specified");
		}

		if ($this->config['checkForUpdates'] === null && !is_callable([$this->entity, 'requiresChildSearchIndexUpdate']))
		{
			throw new \LogicException("If checkForUpdates is null/not specified, the entity must define requiresChildSearchIndexUpdate");
		}

		if (!is_array($this->config['childIds']) && !is_callable($this->config['childIds']))
		{
			throw new \LogicException("A childIds value must be callable (receiving the entity) or an array");
		}
	}

	public function postSave()
	{
		if ($this->requiresChildIndexUpdate())
		{
			$this->triggerReindex();
		}
	}

	public function triggerReindex()
	{
		$childIds = $this->getChildIds();
		if ($childIds)
		{
			$this->app()->jobManager()->enqueue('XF:SearchIndex', [
				'content_type' => $this->config['childContentType'],
				'content_ids' => $childIds
			]);
		}
	}

	protected function requiresChildIndexUpdate()
	{
		if ($this->entity->isInsert())
		{
			return false;
		}

		$checkForUpdates = $this->config['checkForUpdates'];

		if ($checkForUpdates === null)
		{
			// method is verified above
			return $this->entity->requiresChildSearchIndexUpdate();
		}
		else if (is_array($checkForUpdates) || is_string($checkForUpdates))
		{
			return $this->entity->isChanged($checkForUpdates);
		}
		else
		{
			return $checkForUpdates;
		}
	}

	public function preDelete()
	{
		$this->onDeleteChildIds = $this->getChildIds();
	}

	public function postDelete()
	{
		if ($this->onDeleteChildIds)
		{
			// note that this entity might not have a simple unique ID, so just use a generally unique identifier for it here
			\XF::runOnce(
				'searchIndexContainerDelete-' . $this->entity->getUniqueEntityId(),
				function()
				{
					$this->app()->search()->delete($this->config['childContentType'], $this->onDeleteChildIds);
				}
			);
		}
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