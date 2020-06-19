<?php

namespace XF\Api\Docs;

use XF\Api\Docs\Renderer\RendererInterface;

class Compiler
{
	/**
	 * @var AnnotationParser
	 */
	protected $annotationParser;

	/**
	 * @var ClassParser
	 */
	protected $classParser;

	/**
	 * @var Annotation\RouteBlock[][]
	 */
	protected $routesByAddOn = [];

	/**
	 * @var Annotation\TypeBlock[]
	 */
	protected $types = [];

	public static $methodSortOrder = [
		'GET' => 0,
		'POST' => 1,
		'PUT' => 2,
		'PATCH' => 3,
		'DELETE' => 4
	];

	public function __construct(AnnotationParser $annotationParser, ClassParser $classParser)
	{
		$annotationParser->setClassParser($classParser);

		$this->annotationParser = $annotationParser;
		$this->classParser = $classParser;
	}

	public function compileForAddOn($addOnId)
	{
		$ds = \XF::$DS;

		// add-on IDs use a forward slash (not a backslash), so standardize
		$addOnId = str_replace('\\', '/', $addOnId);

		if ($addOnId == 'XF')
		{
			$baseDir = \XF::getSourceDirectory() . $ds . 'XF';
		}
		else
		{
			$baseDir = \XF::getAddOnDirectory() . $ds . str_replace('/', $ds, $addOnId);
		}

		$entityDir = "{$baseDir}{$ds}Entity";
		if (file_exists($entityDir))
		{
			$classPrefix = str_replace('/', '\\', $addOnId);

			foreach (new \DirectoryIterator($entityDir) AS /** @var \DirectoryIterator $file */ $file)
			{
				if ($file->getExtension() == 'php')
				{
					$entityName = $classPrefix . ':' . substr($file->getBasename(), 0, -4);
					$typeBlock = $this->classParser->parseEntityClass($entityName);
					if ($typeBlock)
					{
						$this->types[$typeBlock->type] = $typeBlock;
					}
				}
			}
		}

		$this->routesByAddOn[$addOnId] = [];

		$apiRoutes = \XF::db()->fetchAll("
			SELECT route_prefix, format, controller
			FROM xf_route
			WHERE route_type = 'api'
				AND addon_id = ?
		", $addOnId);
		foreach ($apiRoutes AS $route)
		{
			$controllerRoutes = $this->classParser->parseControllerClass(
				$route['controller'],
				$this->getRouteUrl($route['route_prefix'], $route['format'])
			);
			$this->routesByAddOn[$addOnId] = array_merge($this->routesByAddOn[$addOnId], $controllerRoutes);
		}
	}

	protected function getRouteUrl($prefix, $format)
	{
		$extra = $format;

		$extra = preg_replace(
			'#:(\+)?int(?:_p)?<([a-zA-Z0-9_]+)(?:,[a-zA-Z0-9_]+)?>/?#',
			'{$2}/',
			$extra
		);

		$extra = preg_replace(
			'#:(\+)?str(?:_p)?<([a-zA-Z0-9_]+)>/?#',
			'{$2}/',
			$extra
		);

		$extra = preg_replace(
			'#:page<([a-zA-Z0-9_]+)>/?#',
			'page-{page}',
			$extra
		);
		$extra = preg_replace(
			'#:page/?#',
			'page-{page}',
			$extra
		);

		$extra = preg_replace(
			'#:(\+)?any<([a-zA-Z0-9_]+)>/?#',
			'{$2}/',
			$extra
		);

		// simplify names to "id" if they end in _id
		$extra = preg_replace(
			'#\{[a-zA-Z0-9_]+_id\}#',
			'{id}',
			$extra
		);

		return $prefix . '/' . $extra;
	}

	public function getRoutesFlattened()
	{
		$routes = [];
		foreach ($this->routesByAddOn AS $addOnRoutes)
		{
			foreach ($addOnRoutes AS $k => $v)
			{
				$routes[$k] = $v;
			}
		}

		return $this->sortRoutes($routes);
	}

	public function getRoutesByGroup()
	{
		$routeGroupings = [];

		foreach ($this->routesByAddOn AS $addOnRoutes)
		{
			foreach ($addOnRoutes AS $k => $route)
			{
				$group = $route->group ?: 'ungrouped';
				$routeGroupings[$group][$k] = $route;
			}
		}

		return $this->sortRoutesGrouped($routeGroupings);
	}

	public function getRoutesByAddOn()
	{
		return $this->routesByAddOn;
	}

	public function getRoutesForAddOn($addOnId)
	{
		return isset($this->routesByAddOn[$addOnId]) ? $this->routesByAddOn[$addOnId] : [];
	}

	public function addOnHasRoutes($addOnId)
	{
		return isset($this->routesByAddOn[$addOnId]);
	}

	public function getTypes()
	{
		return $this->types;
	}

	/**
	 * @param Annotation\RouteBlock[] $routes
	 *
	 * @return Annotation\RouteBlock[]
	 */
	public function sortRoutes(array $routes)
	{
		uasort($routes, function(Annotation\RouteBlock $r1, Annotation\RouteBlock $r2)
		{
			if ($r1->route !== $r2->route)
			{
				return ($r1->route < $r2->route ? -1 : 1);
			}

			$r1Order = isset(self::$methodSortOrder[$r1->method]) ? self::$methodSortOrder[$r1->method] : 100;
			$r2Order = isset(self::$methodSortOrder[$r2->method]) ? self::$methodSortOrder[$r2->method] : 100;

			if ($r1Order === $r2Order)
			{
				return 0;
			}
			return ($r1Order < $r2Order ? -1 : 1);
		});

		return $routes;
	}

	/**
	 * @param Annotation\RouteBlock[][] $routesGrouped
	 *
	 * @return Annotation\RouteBlock[][]
	 */
	public function sortRoutesGrouped(array $routesGrouped)
	{
		foreach ($routesGrouped AS &$routes)
		{
			$routes = $this->sortRoutes($routes);
		}

		return $routesGrouped;
	}

	public function render(RendererInterface $renderer)
	{
		return $renderer->render($this->getRoutesByGroup(), $this->types);
	}
}