<?php

namespace XF\Api\Result;

use XF\Mvc\Entity\Entity;

interface EntityResultInterface extends ResultInterface
{
	public function skipColumn($column);
	public function includeColumn($column);
	public function includeGetter($getter);
	public function skipRelation($relation);
	public function includeRelation($relation, $verbosity = Entity::VERBOSITY_NORMAL, array $options = []);
	public function includeExtra($k, $v = null);
	public function addCallback(callable $c);
	public function __set($k, $v);
}