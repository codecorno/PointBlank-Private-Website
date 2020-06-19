<?php

namespace XF\Behavior;

use XF\Mvc\Entity\Behavior;

class Indexable extends Behavior
{
	protected function getDefaultConfig()
	{
		return [
			'checkForUpdates' => null
		];
	}

	protected function verifyConfig()
	{
		if (!$this->contentType())
		{
			throw new \LogicException("Structure must provide a contentType value");
		}

		if ($this->config['checkForUpdates'] === null && !is_callable([$this->entity, 'requiresSearchIndexUpdate']))
		{
			throw new \LogicException("If checkForUpdates is null/not specified, the entity must define requiresSearchIndexUpdate");
		}
	}

	public function postSave()
	{
		if ($this->requiresIndexUpdate())
		{
			$this->triggerReindex();
		}
	}

	public function triggerReindex()
	{
		// if inserting this content, it won't exist, so don't need to trigger a delete
		$deleteIfNeeded = $this->entity->isInsert() ? false : true;

		\XF::runOnce(
			'searchIndex-' . $this->contentType() . $this->entity->getEntityId(),
			function() use($deleteIfNeeded)
			{
				$this->app()->search()->index($this->contentType(), $this->entity, $deleteIfNeeded);
			}
		);
	}

	protected function requiresIndexUpdate()
	{
		if ($this->entity->isInsert())
		{
			return true;
		}

		$checkForUpdates = $this->config['checkForUpdates'];

		if ($checkForUpdates === null)
		{
			// method is verified above
			return $this->entity->requiresSearchIndexUpdate();
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

	public function postDelete()
	{
		\XF::runOnce(
			'searchIndex-' . $this->contentType() . $this->entity->getEntityId(),
			function()
			{
				$this->app()->search()->delete($this->contentType(), $this->entity);
			}
		);
	}
}