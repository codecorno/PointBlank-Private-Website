<?php

namespace XF\Admin\Controller;

class Search extends AbstractController
{
	public function actionSearch()
	{
		$query = $this->filter('q', 'string');

		/** @var \XF\AdminSearch\Searcher $searcher */
		$searcher = $this->app['adminSearcher'];
		$resultTypeSets = $searcher->search($query);

		$viewParams = [
			'query' => $query,
			'resultTypeSets' => $resultTypeSets
		];
		return $this->view('XF:Search\Search', 'quick_search_results', $viewParams);
	}
}