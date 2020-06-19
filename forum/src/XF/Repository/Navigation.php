<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Navigation extends Repository
{
	/**
	 * @return Finder
	 */
	public function findNavigationForList()
	{
		return $this->finder('XF:Navigation')->order(['parent_navigation_id', 'display_order']);
	}

	public function createNavigationTree($entries = null, $rootId = '')
	{
		if ($entries === null)
		{
			$entries = $this->findNavigationForList()->fetch();
		}

		return new \XF\Tree($entries, 'parent_navigation_id', $rootId);
	}


	/**
	 * @return \XF\Entity\Navigation[]
	 */
	public function getTopLevelEntries()
	{
		return $this->finder('XF:Navigation')->where('parent_navigation_id', '')->order('display_order')->fetch();
	}

	/**
	 * @param string $type
	 * @param bool $throw
	 *
	 * @return \XF\Navigation\AbstractType|null
	 */
	public function getTypeHandler($type, $throw = false)
	{
		$handlerClass = $this->db()->fetchOne("
			SELECT handler_class
			FROM xf_navigation_type
			WHERE navigation_type_id = ?
		", $type);
		if (!$handlerClass)
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("No navigation type handler for '$type'");
			}
			return null;
		}

		$handlerClass = \XF::stringToClass($handlerClass, '%s\Navigation\%s');

		if (!class_exists($handlerClass))
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("Navigation type handler for '$type' does not exist: $handlerClass");
			}
			return null;
		}

		$handlerClass = \XF::extendClass($handlerClass);
		return new $handlerClass($type);
	}

	/**
	 * @return \XF\Navigation\AbstractType[]
	 */
	public function getTypeHandlers()
	{
		$pairs = $this->db()->fetchPairs("
			SELECT navigation_type_id, handler_class
			FROM xf_navigation_type
			ORDER BY display_order
		");
		$handlers = [];
		foreach ($pairs AS $type => $class)
		{
			$className = \XF::stringToClass($class, '%s\Navigation\%s');
			if (class_exists($className))
			{
				$className = \XF::extendClass($className);
				$handlers[$type] = new $className($type);
			}
		}

		return $handlers;
	}

	public function rebuildNavigationCache()
	{
		$entries = $this->finder('XF:Navigation')
			->whereAddOnActive()
			->order(['parent_navigation_id', 'display_order'])
			->fetch();

		$tree = $this->createNavigationTree($entries);

		/** @var \XF\Navigation\Compiler $navigationCompiler */
		$navigationCompiler = $this->app()['navigation.compiler'];
		$code = $navigationCompiler->compileTree($tree);

		$cacheFile = 'code-cache://' . $this->app()['navigation.file'];
		$contents = "<?php\n\n" . $code;
		\XF\Util\File::writeToAbstractedPath($cacheFile, $contents);
	}
}