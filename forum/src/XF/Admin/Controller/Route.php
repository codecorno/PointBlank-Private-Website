<?php

namespace XF\Admin\Controller;

use XF\Mvc\ParameterBag;

class Route extends AbstractController
{
	/**
	 * @param $action
	 * @param ParameterBag $params
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertDevelopmentMode();
	}

	public function actionIndex()
	{
		$routeRepo = $this->getRouteRepo();
		$routes = $routeRepo->findRoutesForList()->fetch();

		$routeTypes = $routeRepo->getRouteTypes();

		$selectedTab = $this->filter('route_type', 'str');
		if (empty($selectedTab))
		{
			reset($routeTypes);
			$selectedTab = key($routeTypes);
		}

		$viewParams = [
			'routeTypes' => $routeTypes,
			'routesGrouped' => $routes->groupBy('route_type'),
			'selectedTab' => $selectedTab,
			'totalRoutes' => count($routes)
		];
		return $this->view('XF:Route\Listing', 'route_list', $viewParams);
	}

	protected function routeAddEdit(\XF\Entity\Route $route)
	{
		$viewParams = [
			'route' => $route,
			'routeTypes' => $this->getRouteRepo()->getRouteTypes()
		];
		return $this->view('XF:Route\Edit', 'route_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$route = $this->assertRouteExists($params['route_id']);
		return $this->routeAddEdit($route);
	}

	public function actionAdd()
	{
		$route = $this->em()->create('XF:Route');
		$route->route_type = $this->filter('type', 'str');

		return $this->routeAddEdit($route);
	}

	protected function routeSaveProcess(\XF\Entity\Route $route)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'route_type' => 'str',
			'route_prefix' => 'str',
			'sub_name' => 'str',
			'format' => 'str',
			'build_class' => 'str',
			'build_method' => 'str',
			'controller' => 'str',
			'context' => 'str',
			'action_prefix' => 'str',
			'addon_id' => 'str'
		]);
		// TODO: routing callbacks

		$form->basicEntitySave($route, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['route_id'])
		{
			$route = $this->assertRouteExists($params['route_id']);
		}
		else
		{
			$route = $this->em()->create('XF:Route');
		}

		$this->routeSaveProcess($route)->run();

		return $this->redirect($this->buildLink('routes', null, ['route_type' => $route->route_type]) . $this->buildLinkHash($route->route_id));
	}

	public function actionDelete(ParameterBag $params)
	{
		$route = $this->assertRouteExists($params->route_id);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$route,
			$this->buildLink('routes/delete', $route),
			$this->buildLink('routes/edit', $route),
			$this->buildLink('routes'),
			$route->unique_name
		);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Route
	 */
	protected function assertRouteExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:Route', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Route
	 */
	protected function getRouteRepo()
	{
		return $this->repository('XF:Route');
	}
}