<?php

namespace XF\DevelopmentOutput;

use XF\DevelopmentOutput;
use XF\Mvc\Entity\Entity;

abstract class AbstractHandler
{
	/**
	 * @var DevelopmentOutput
	 */
	protected $developmentOutput;
	protected $shortName;

	public function __construct(DevelopmentOutput $developmentOutput, $shortName)
	{
		$this->developmentOutput = $developmentOutput;
		$this->shortName = $shortName;
	}

	abstract protected function getTypeDir();
	
	abstract public function export(Entity $entity);
	abstract public function import($name, $addOnId, $contents, array $metadata, array $options = []);

	/**
	 * @param $name
	 * @param $addOnId
	 * @param $json
	 * @param array $options
	 * @return null|Entity
	 */
	protected function getEntityForImport($name, $addOnId, $json, array $options)
	{
		$entity = \XF::em()->find($this->shortName, $name);
		if (!$entity)
		{
			$entity = \XF::em()->create($this->shortName);
		}

		$entity = $this->prepareEntityForImport($entity, $options);

		return $entity;
	}

	protected function prepareEntityForImport(Entity $entity, array $options)
	{
		if (!empty($options['import']))
		{
			$entity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
		}
		return $entity;
	}

	public function hasNameChange(Entity $entity)
	{
		if ($entity->isUpdate())
		{
			if ($entity->isChanged('addon_id'))
			{
				return true;
			}
			if ($this->getFileName($entity, true) !== $this->getFileName($entity, false))
			{
				return true;
			}
		}

		return false;
	}

	public function delete(Entity $entity, $new = true)
	{
		if (!$this->isRelevant($entity, $new))
		{
			return false;
		}
		
		$fileName = $this->getFileName($entity, $new);
		$addOnId = $new ? $entity->getValue('addon_id') : $entity->getExistingValue('addon_id');
		return $this->developmentOutput->deleteFile($this->getTypeDir(), $addOnId, $fileName);
	}

	protected function isRelevant(Entity $entity, $new = true)
	{
		$addOnId = $new ? $entity->getValue('addon_id') : $entity->getExistingValue('addon_id');
		$entityClass = \XF::stringToClass($this->shortName, '%s\Entity\%s');

		if (!$addOnId || $this->developmentOutput->isAddOnSkipped($addOnId))
		{
			return false;
		}

		return ($entity instanceof $entityClass);
	}
	
	protected function getFileName(Entity $entity, $new = true)
	{
		$id = $new ? $entity->getEntityId() : $entity->getExistingEntityId();
		return "{$id}.json";
	}

	protected function pullEntityKeys(Entity $entity, array $keys)
	{
		$json = [];
		foreach ($keys AS $key)
		{
			$json[$key] = $entity->isValidColumn($key) ? $entity->getValue($key) : $entity->get($key);
		}

		return $json;
	}

	protected function setEntityKeys(Entity $entity, array $keys, array $values)
	{
		$json = [];
		foreach ($keys AS $key)
		{
			if (array_key_exists($key, $values))
			{
				$entity->$key = $values[$key];
			}
		}

		return $json;
	}
}