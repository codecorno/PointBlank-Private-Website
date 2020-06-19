<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Route extends Repository
{
	/**
	 * @return Finder
	 */
	public function findRoutesForList()
	{
		return $this->finder('XF:Route')->order(['route_type', 'route_prefix', 'sub_name']);
	}

	public function getRouteTypes()
	{
		return [
			'public' => \XF::phrase('public'),
			'admin' => \XF::phrase('admin'),
			'api' => \XF::phrase('api')
		];
	}

	public function getRouteCacheData($routeType)
	{
		$routes = $this->finder('XF:Route')
			->where('route_type', $routeType)
			->with('AddOn')
			->whereAddOnActive()
			->order(['route_prefix', 'sub_name'])
			->fetch();

		$output = [];
		foreach ($routes AS $route)
		{
			$data = [
				'format' => $route->format,
				'build_callback' => $route->build_class ? [$route->build_class, $route->build_method] : null,
				'controller' => $route->controller,
				'context' => $route->context,
				'action_prefix' => $route->action_prefix
			];

			if ($route->AddOn && $route->AddOn->is_processing)
			{
				$this->disableProcessingAddOnRoute($route, $data);
			}

			$output[$route->route_prefix][$route->sub_name] = $data;
		}

		foreach ($output AS $prefix => &$sub)
		{
			uksort($sub, function($a, $b)
			{
				return strlen($a) < strlen($b);
			});
		}

		return $output;
	}

	protected function disableProcessingAddOnRoute(\XF\Entity\Route $route, array &$cacheData)
	{
		$cacheData['controller'] = 'XF:Error';
		$cacheData['force_action'] = 'addOnUpgrade';
	}

	public function rebuildRouteCache($routeType)
	{
		$cache = $this->getRouteCacheData($routeType);
		\XF::registry()->set('routes' . ucfirst($routeType), $cache);
		return $cache;
	}

	public function rebuildRouteCaches()
	{
		foreach (array_keys($this->getRouteTypes()) AS $type)
		{
			$this->rebuildRouteCache($type);
		}
	}
}