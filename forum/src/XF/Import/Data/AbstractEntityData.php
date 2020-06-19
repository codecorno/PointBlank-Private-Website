<?php

namespace XF\Import\Data;

use XF\Mvc\Entity\Entity;

abstract class AbstractEntityData extends AbstractData
{
	/**
	 * @var Entity
	 */
	protected $entity;

	protected $primaryKey;

	abstract protected function getEntityShortName();

	protected function init()
	{
		$this->entity = $this->em()->create($this->getEntityShortName());

		$primaryKey = $this->entity->structure()->primaryKey;
		if (is_array($primaryKey) && count($primaryKey) == 1)
		{
			$primaryKey = reset($primaryKey);
		}
		else if (is_array($primaryKey))
		{
			throw new \LogicException("Compound primary keys are not supported by the entity data importer. A custom version must be implemented.");
		}
		$this->primaryKey = $primaryKey;
	}

	public function set($field, $value, array $options = [])
	{
		if (!isset($options['forceConstraint']))
		{
			$options['forceConstraint'] = true;
		}

		return $this->entity->set($field, $value, $options);
	}

	public function get($field)
	{
		return $this->entity->get($field);
	}

	protected function preSave($oldId)
	{
		$primaryKey = $this->primaryKey;

		if (!$this->entity->isChanged($primaryKey))
		{
			if ($this->retainIds() && $oldId !== false)
			{
				$this->entity->set($primaryKey, $oldId, ['forceSet' => true]);
			}
		}

		$this->entity->preSave();
	}

	protected function write($oldId)
	{
		$this->entity->save();

		return $this->entity->get($this->primaryKey);
	}

	protected function importedIdFound($oldId, $newId)
	{
		$this->entity->set($this->primaryKey, $newId);
		// Note that if save is called, this will still insert. Normally, we wouldn't call save on this in this instance though.
	}

	public function save($oldId)
	{
		$return = parent::save($oldId);

		// don't cache to avoid memory issues
		$this->em()->detachEntity($this->entity);

		return $return;
	}
}