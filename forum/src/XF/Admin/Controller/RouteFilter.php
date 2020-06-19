<?php

namespace XF\Admin\Controller;

use XF\Http\Request;
use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class RouteFilter extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('option');
	}

	public function actionIndex()
	{
		$viewParams = [
			'routeFilters' => $this->getRouteFilterRepo()
				->findRouteFiltersForList()
				->fetch()
		];
		return $this->view('XF:RouteFilter\Listing', 'route_filter_list', $viewParams);
	}

	protected function routeFilterAddEdit(\XF\Entity\RouteFilter $routeFilter)
	{
		/** @var \XF\Mvc\Router $publicRouter */
		$publicRouter = $this->app->container('router.public');

		$fullIndex = $publicRouter->buildLink('full:index');
		$fullThreadLink = $publicRouter->buildLink('full:threads', ['thread_id' => 1, 'title' => 'example']);
		$routeValue = str_replace([$fullIndex, '?'], '', $fullThreadLink);

		$viewParams = [
			'routeFilter' => $routeFilter,
			'fullThreadLink' => $fullThreadLink,
			'routeValue' => $routeValue
		];
		return $this->view('XF:RouteFilter\Edit', 'route_filter_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$routeFilter = $this->assertRouteFilterExists($params['route_filter_id']);
		return $this->routeFilterAddEdit($routeFilter);
	}

	public function actionAdd()
	{
		$routeFilter = $this->em()->create('XF:RouteFilter');
		return $this->routeFilterAddEdit($routeFilter);
	}

	protected function routeFilterSaveProcess(\XF\Entity\RouteFilter $routeFilter)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'find_route' => 'str',
			'replace_route' => 'str',
			'url_to_route_only' => 'str',
			'enabled' => 'bool'
		]);
		$form->basicEntitySave($routeFilter, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['route_filter_id'])
		{
			$routeFilter = $this->assertRouteFilterExists($params['route_filter_id']);
		}
		else
		{
			$routeFilter = $this->em()->create('XF:RouteFilter');
		}

		$this->routeFilterSaveProcess($routeFilter)->run();

		return $this->redirect($this->buildLink('route-filters'));
	}

	public function actionDelete(ParameterBag $params)
	{
		$routeFilter = $this->assertRouteFilterExists($params['route_filter_id']);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$routeFilter,
			$this->buildLink('route-filters/delete', $routeFilter),
			$this->buildLink('route-filters/edit', $routeFilter),
			$this->buildLink('route-filters'),
			$routeFilter->find_route
		);
	}

	public function actionToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		return $plugin->actionToggle('XF:RouteFilter', 'enabled');
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\RouteFilter
	 */
	protected function assertRouteFilterExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:RouteFilter', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\RouteFilter
	 */
	protected function getRouteFilterRepo()
	{
		return $this->repository('XF:RouteFilter');
	}
}