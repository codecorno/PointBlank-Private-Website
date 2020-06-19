<?php

namespace XF\Db\Schema;

use XF\Db\AbstractAdapter;

abstract class AbstractDefinition
{
	protected $existingDefinition;

	/**
	 * @var AbstractAdapter
	 */
	protected $db;

	/**
	 * @var AbstractDdl
	 */
	protected $ddl;

	protected $name;

	protected $drop;

	protected $forceChanges = false;

	abstract public function getDefinition($change = false);
	abstract public function setDefinition();
	abstract public function resetDefinition();
	abstract public function getComparisonValue();

	public function __construct(
		AbstractAdapter $db, AbstractDdl $ddl, $name, array $existingDefinition = null, $force = false
	)
	{
		$this->db = $db;
		$this->ddl = $ddl;
		$this->name = $name;
		$this->existingDefinition = $existingDefinition;
		$this->forceChanges = $force;

		$this->init();
	}

	protected function init()
	{
	}

	public function getName()
	{
		return $this->name;
	}

	public function forceChanges($force = true)
	{
		$this->forceChanges = $force;

		return $this;
	}

	public function drop()
	{
		if (!$this->isAlter())
		{
			throw new \InvalidArgumentException(
				"It is not possible to drop the column named {$this->name} during table create."
			);
		}

		$this->drop = true;
	}

	public function isAlter()
	{
		return ($this->ddl instanceof Alter);
	}

	protected function compare(AbstractDefinition $existing)
	{
		return ($this->getComparisonValue() == $existing->getComparisonValue());
	}

	public function toArray()
	{
		$array = get_object_vars($this);
		unset($array['db'], $array['ddl'], $array['existingDefinition']);
		return $array;
	}
}