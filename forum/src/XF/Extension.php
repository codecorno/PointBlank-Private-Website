<?php

namespace XF;

class Extension
{
	protected $listeners = [];

	protected $classExtensions = [];
	protected $extensionMap = [];

	public function __construct(array $listeners = [], array $classExtensions = [])
	{
		$this->listeners = $listeners;
		$this->classExtensions = $classExtensions;
	}

	public function fire($event, array $args = [], $hint = null)
	{
		$listeners = $this->listeners;

		if (empty($listeners[$event]))
		{
			return true;
		}

		if ($hint !== null)
		{
			if (!empty($listeners[$event]['_']))
			{
				foreach ($listeners[$event]['_'] AS $callback)
				{
					if (is_callable($callback))
					{
						$return = call_user_func_array($callback, $args);
						if ($return === false)
						{
							return false;
						}
					}
				}
			}

			if ($hint !== '_' && !empty($listeners[$event][$hint]))
			{
				foreach ($listeners[$event][$hint] AS $callback)
				{
					if (is_callable($callback))
					{
						$return = call_user_func_array($callback, $args);
						if ($return === false)
						{
							return false;
						}
					}
				}
			}
		}
		else
		{
			foreach ($listeners[$event] AS $callbacks)
			{
				foreach ($callbacks AS $callback)
				{
					if (is_callable($callback))
					{
						$return = call_user_func_array($callback, $args);
						if ($return === false)
						{
							return false;
						}
					}
				}
			}
		}

		return true;
	}

	public function getListeners($event)
	{
		return isset($this->listeners[$event]) ? $this->listeners[$event] : [];
	}

	public function addListener($event, $callback, $hint = '_')
	{
		$this->listeners[$event][$hint][] = $callback;
	}

	public function setListeners(array $listeners)
	{
		$this->listeners = $listeners;
	}

	public function removeListeners($event = null)
	{
		if ($event !== null)
		{
			unset($this->listeners[$event]);
		}
		else
		{
			$this->listeners = [];
		}
	}

	public function extendClass($class, $fakeBaseClass = null)
	{
		$class = ltrim($class, '\\');

		if (isset($this->extensionMap[$class]))
		{
			return $this->extensionMap[$class];
		}

		if (!$class)
		{
			return $class;
		}

		$extensions = !empty($this->classExtensions[$class]) ? $this->classExtensions[$class] : [];
		if (!$extensions)
		{
			$this->extensionMap[$class] = $class;
			return $class;
		}

		if (!class_exists($class))
		{
			if ($fakeBaseClass)
			{
				$fakeBaseClass = ltrim($fakeBaseClass, '\\');
				class_alias($fakeBaseClass, $class);
			}
			else
			{
				$this->extensionMap[$class] = $class;
				return $class;
			}
		}

		$finalClass = $class;

		try
		{
			foreach ($extensions AS $extendClass)
			{
				if (preg_match('/[;,$\/#"\'\.()]/', $extendClass))
				{
					continue;
				}

				// XFCP = XenForo Class Proxy, in case you're wondering

				$nsSplit = strrpos($extendClass, '\\');
				if ($nsSplit !== false && $ns = substr($extendClass, 0, $nsSplit))
				{
					$proxyClass = $ns . '\\XFCP_' . substr($extendClass, $nsSplit + 1);
				}
				else
				{
					$proxyClass = 'XFCP_' . $extendClass;
				}

				// TODO: there may be a situation where this fails. If we've changed the extensions after classes have
				// been loaded, it's possible these classes will already be loaded with a different config. Figure out
				// how to handle that if possible. Remains to be seen if it comes up (mostly relating to add-on imports).

				class_alias($finalClass, $proxyClass);
				$finalClass = $extendClass;

				if (!class_exists($extendClass))
				{
					throw new \Exception("Could not find class $extendClass when attempting to extend $class");
				}
			}
		}
		catch (\Exception $e)
		{
			$this->extensionMap[$class] = $class;
			throw $e;
		}

		$this->extensionMap[$class] = $finalClass;
		return $finalClass;
	}

	public function addClassExtension($class, $extension)
	{
		$class = ltrim($class, '\\');
		$extension = ltrim($extension, '\\');

		$this->classExtensions[$class][] = $extension;

		// TODO: if the class has already been loaded, we need to override the cache add our extension for the future
	}

	public function setClassExtensions(array $extensions)
	{
		$this->classExtensions = $extensions;
		$this->extensionMap = [];
	}

	public function removeClassExtensions($class = null)
	{
		if ($class !== null)
		{
			unset($this->classExtensions[$class]);
			unset($this->extensionMap[$class]);
		}
		else
		{
			$this->classExtensions = [];
			$this->extensionMap = [];
		}
	}

	/**
	 * Takes a class that may be dynamically extended and resolves it
	 * back to the root.
	 *
	 * @param string|object $class Class name or object
	 *
	 * @return string
	 */
	public function resolveExtendedClassToRoot($class)
	{
		if (is_object($class))
		{
			$class = get_class($class);
		}

		$finalClass = $class;
		while ($finalClass)
		{
			$nsEnd = strrpos($finalClass, '\\');
			if ($nsEnd)
			{
				$testClass = substr($finalClass, 0, $nsEnd) . '\\XFCP_' . substr($finalClass, $nsEnd + 1);
			}
			else
			{
				$testClass = "XFCP_$finalClass";
			}

			// This feels odd, the is_subclass_of returns true for the XFCP class though it's just the alias.
			// The parent class won't be the alias, but the actual parent name, so we just loop through until
			// we don't get anything aliased to an XFCP class.
			if (is_subclass_of($finalClass, $testClass))
			{
				$finalClass = get_parent_class($finalClass);
			}
			else
			{
				break;
			}
		}

		return $finalClass ? $finalClass : $class;
	}
}