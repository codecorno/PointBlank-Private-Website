<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class Route extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'routes';
	}
	
	public function export(Entity $route)
	{
		if (!$this->isRelevant($route))
		{
			return true;
		}

		$fileName = $this->getFileName($route);

		$json = [
			'route_type' => $route->route_type,
			'route_prefix' => $route->route_prefix,
			'sub_name' => $route->sub_name,
			'format' => $route->format,
			'build_class' => $route->build_class,
			'build_method' => $route->build_method,
			'controller' => $route->controller,
			'context' => $route->context,
			'action_prefix' => $route->action_prefix,
		];

		return $this->developmentOutput->writeFile($this->getTypeDir(), $route->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	protected function getEntityForImport($name, $addOnId, $json, array $options)
	{
		$route = \XF::em()->getFinder('XF:Route')->where([
			'route_type' => $json->route_type,
			'route_prefix' => $json->route_prefix,
			'sub_name' => $json->sub_name
		])->fetchOne();
		if (!$route)
		{
			$route = \XF::em()->create('XF:Route');
		}

		$route = $this->prepareEntityForImport($route, $options);

		return $route;
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents);

		$route = $this->getEntityForImport($name, $addOnId, $json, $options);
		$route->setOption('check_duplicate', false);

		$route->route_type = $json->route_type;
		$route->route_prefix = $json->route_prefix;
		$route->sub_name = $json->sub_name;
		$route->format = $json->format;
		$route->build_class = $json->build_class;
		$route->build_method = $json->build_method;
		$route->controller = $json->controller;
		$route->context = $json->context;
		$route->action_prefix = $json->action_prefix;
		$route->addon_id = $addOnId;
		$route->save();
		// this will update the metadata itself

		return $route;
	}
	
	protected function getFileName(Entity $route, $new = true)
	{
		$routeType = $new ? $route->getValue('route_type') : $route->getExistingValue('route_type');
		$routePrefix = $new ? $route->getValue('route_prefix') : $route->getExistingValue('route_prefix');
		$subName = $new ? $route->getValue('sub_name') : $route->getExistingValue('sub_name');

		$subNameFile = preg_replace('#[^a-z0-9_-]#i', '-', $subName);
		$subNameFile = preg_replace('#-{2,}#', '-', $subNameFile);

		return "{$routeType}_{$routePrefix}_{$subNameFile}.json";
	}
}