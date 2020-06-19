<?php

namespace XF\DesignerOutput;

use XF\DesignerOutput;
use XF\Mvc\Entity\Entity;

abstract class AbstractHandler
{
	/**
	 * @var DesignerOutput
	 */
	protected $designerOutput;
	protected $shortName;

	public function __construct(DesignerOutput $designerOutput, $shortName)
	{
		$this->designerOutput = $designerOutput;
		$this->shortName = $shortName;
	}

	abstract protected function getTypeDir();
	
	abstract public function export(Entity $entity);
	abstract protected function getEntityForImport($name, $styleId, $json, array $options);
	abstract public function import($name, $styleId, $contents, array $metadata, array $options = []);

	abstract protected function getFileName(Entity $entity, $new = true);

	public function delete(Entity $entity, $new = true)
	{
		if (!$this->isRelevant($entity))
		{
			return false;
		}

		$fileName = $this->getFileName($entity, $new);
		return $this->designerOutput->deleteFile($this->getTypeDir(), $entity->Style, $fileName);
	}

	protected function isRelevant(Entity $entity)
	{
		$entityClass = \XF::stringToClass($this->shortName, '%s\Entity\%s');
		return ($entity instanceof $entityClass);
	}

	protected function prepareEntityForImport(Entity $entity, array $options)
	{
		if (!empty($options['import']))
		{
			$entity->getBehavior('XF:DesignerOutputWritable')->setOption('write_designer_output', false);
		}
		return $entity;
	}

	public function hasNameChange(Entity $entity)
	{
		if ($entity->isUpdate())
		{
			if ($this->getFileName($entity, true) !== $this->getFileName($entity, false))
			{
				return true;
			}
		}

		return false;
	}
}