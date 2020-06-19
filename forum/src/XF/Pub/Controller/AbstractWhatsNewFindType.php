<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\View;

abstract class AbstractWhatsNewFindType extends AbstractController
{
	abstract protected function getContentType();

	public function actionIndex(ParameterBag $params)
	{
		/** @var \XF\ControllerPlugin\FindNew $findNewPlugin */
		$findNewPlugin = $this->plugin('XF:FindNew');
		$contentType = $this->getContentType();

		$handler = $findNewPlugin->getFindNewHandler($contentType);
		if (!$handler)
		{
			return $this->noPermission();
		}

		$findNew = $findNewPlugin->getFindNewRecord($params->find_new_id, $contentType);
		if (!$findNew)
		{
			$filters = $findNewPlugin->getRequestedFilters($handler);
			$reply = $this->triggerNewFindNewAction($handler, $filters);

			if ($this->filter('save', 'bool') && $this->isPost())
			{
				$findNewPlugin->saveDefaultFilters($handler, $filters);
			}

			return $reply;
		}
		else
		{
			$remove = $this->filter('remove', 'str');
			if ($remove)
			{
				$filters = $findNew->filters;
				unset($filters[$remove]);

				return $this->triggerNewFindNewAction($handler, $filters);
			}
		}

		$page = $this->filterPage($params->page);
		$perPage = $handler->getResultsPerPage();

		if (!$findNew->result_count)
		{
			return $handler->getPageReply($this, $findNew, [], 1, $perPage);
		}

		$this->assertValidPage($page, $perPage, $findNew->result_count, $handler->getRoute(), $findNew);

		$pageIds = $findNew->getPageResultIds($page, $perPage);
		$results = $handler->getPageResults($pageIds);

		return $handler->getPageReply(
			$this, $findNew, $results->toArray(), $page, $perPage
		);
	}

	protected function triggerNewFindNewAction(\XF\FindNew\AbstractHandler $handler, array $filters)
	{
		/** @var \XF\ControllerPlugin\FindNew $findNewPlugin */
		$findNewPlugin = $this->plugin('XF:FindNew');

		$findNew = $findNewPlugin->runFindNewSearch($handler, $filters);
		if (!$findNew->result_count && !$findNew->filters)
		{
			// we can only bail out early without filters, because we need an idea to be able to modify them easily
			return $handler->getPageReply($this, $findNew, [], 1, $handler->getResultsPerPage());
		}

		if (!$findNew->exists())
		{
			$findNew->save();
		}

		return $this->redirect($this->buildLink($handler->getRoute(), $findNew));
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('viewing_latest_content');
	}
}