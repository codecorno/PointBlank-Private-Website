<?php

namespace XF\SubContainer;

abstract class AbstractSubContainer implements \ArrayAccess
{
	/**
	 * @var \XF\Container
	 */
	protected $parent;

	/**
	 * @var \XF\App
	 */
	protected $app;

	/**
	 * @var \XF\Container
	 */
	protected $container;

	abstract public function initialize();

	public function __construct(\XF\Container $parent, \XF\App $app)
	{
		$this->parent = $parent;
		$this->app = $app;

		$this->container = new \XF\Container();
		$this->initialize();
	}

	/**
	 * Gets the callable class name for a dynamically extended class.
	 *
	 * @param string $class
	 * @param null|string $fakeBaseClass
	 * @return string
	 *
	 * @throws \Exception
	 */
	public function extendClass($class, $fakeBaseClass = null)
	{
		return $this->app->extendClass($class, $fakeBaseClass);
	}

	/**
	 * @param string $key
	 * @param \Closure $rebuildFunction
	 * @param \Closure|null $decoratorFunction
	 *
	 * @return \Closure
	 */
	public function fromRegistry($key, \Closure $rebuildFunction, \Closure $decoratorFunction = null)
	{
		return $this->app->fromRegistry($key, $rebuildFunction, $decoratorFunction);
	}

	public function get($key)
	{
		return $this->container->offsetGet($key);
	}

	/**
	 * @param mixed $key
	 *
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->container->offsetGet($key);
	}

	/**
	 * @param mixed $key
	 * @param mixed $value
	 */
	public function offsetSet($key, $value)
	{
		$this->container->offsetSet($key, $value);
	}

	/**
	 * @param mixed $key
	 *
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return $this->container->offsetExists($key);
	}

	/**
	 * @param mixed $key
	 */
	public function offsetUnset($key)
	{
		$this->container->offsetUnset($key);
	}

	/**
	 * @param string|null $key
	 *
	 * @return \XF\Container|mixed
	 */
	public function container($key = null)
	{
		return $key === null ? $this->container : $this->container[$key];
	}
}