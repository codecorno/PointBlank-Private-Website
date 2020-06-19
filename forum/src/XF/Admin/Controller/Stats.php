<?php

namespace XF\Admin\Controller;

use XF\Mvc\ParameterBag;

class Stats extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('viewStatistics');
	}

	public function actionIndex()
	{
		$grouping = $this->filter('grouping', 'str');
		if (!$grouping || !isset($this->app['stats.groupings'][$grouping]))
		{
			$grouping = 'daily';
		}

		/** @var \XF\Stats\Grouper\AbstractGrouper $grouper */
		$grouper = $this->app->create('stats.grouper', $grouping);

		$displayTypes = $this->filter('display_types', 'array-str');
		if (!$displayTypes)
		{
			$displayTypes = ['post', 'post_reaction'];
		}

		// by default, we only have colors for 15 lines
		$displayTypes = array_slice($displayTypes, 0, 15);

		if (!$start = $this->filter('start', 'datetime'))
		{
			$start = $grouper->getDefaultStartDate();
		}

		if (!$end = $this->filter('end', 'datetime'))
		{
			$end = \XF::$time;
		}

		$statsRepo = $this->getStatsRepo();

		/** @var \XF\Service\Stats\Grapher $grapher */
		$grapher = $this->service('XF:Stats\Grapher', $start, $end, $displayTypes);
		$data = $grapher->getGroupedData($grouper);

		$viewParams = [
			'grouping' => $grouping,
			'displayTypes' => $displayTypes,
			'displayTypesPhrased' => $statsRepo->getStatsTypePhrases($displayTypes),
			'data' => $data,

			'start' => $start,
			'end' => $end,
			'endDisplay' => ($end >= \XF::$time ? 0 : $end),

			'statsTypeOptions' => $statsRepo->getStatsTypeOptions(),
			'datePresets' => \XF::language()->getDatePresets(),

			'contentTypePhrases' => $this->app->getContentTypePhrases(true)
		];
		return $this->view('XF:Stats\Stats', 'stats', $viewParams);
	}

	/**
	 * @return \XF\Repository\Stats
	 */
	protected function getStatsRepo()
	{
		return $this->repository('XF:Stats');
	}
}