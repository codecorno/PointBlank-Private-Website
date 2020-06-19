<?php

namespace XF\Mvc\Entity;

abstract class Repository
{
	/**
	 * @var Manager
	 */
	protected $em;

	protected $identifier;

	public function __construct(Manager $em, $identifier)
	{
		$this->em = $em;
		$this->identifier = $identifier;
	}

	/**
	 * @return \XF\Db\AbstractAdapter
	 */
	public function db()
	{
		return $this->em->getDb();
	}

	/**
	 * @param string $identifier
	 *
	 * @return Finder
	 */
	public function finder($identifier)
	{
		return $this->em->getFinder($identifier);
	}

	/**
	 * @param string $identifier
	 *
	 * @return Repository
	 */
	public function repository($identifier)
	{
		return $this->em->getRepository($identifier);
	}

	/**
	 * @return \XF\App
	 */
	public function app()
	{
		return \XF::app();
	}

	/**
	 * @return \ArrayObject
	 */
	public function options()
	{
		return $this->app()->options();
	}

	public function __sleep()
	{
		throw new \LogicException('Instances of ' . __CLASS__ . ' cannot be serialized or unserialized');
	}

	public function __wakeup()
	{
		throw new \LogicException('Instances of ' . __CLASS__ . ' cannot be serialized or unserialized');
	}
}