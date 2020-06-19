<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Util\Arr;

class Search extends AbstractController
{
	public function actionIndex(ParameterBag $params)
	{
		if ($params->search_id && !$this->filter('searchform', 'bool'))
		{
			return $this->rerouteController(__CLASS__, 'results', $params);
		}

		$visitor = \XF::visitor();
		if (!$visitor->canSearch($error))
		{
			return $this->noPermission($error);
		}

		$input = $this->convertShortSearchInputNames();

		$searcher = $this->app->search();
		$type = $input['search_type'] ?: $this->filter('type', 'str');

		$viewParams = [
			'tabs' => $searcher->getSearchTypeTabs(),
			'type' => $type,
			'isRelevanceSupported' => $searcher->isRelevanceSupported(),
			'input' => $input
		];

		$typeHandler = null;
		if ($type && $searcher->isValidContentType($type))
		{
			$typeHandler = $searcher->handler($type);
			if (!$typeHandler->getSearchFormTab())
			{
				$typeHandler = null;
			}
		}

		if ($typeHandler)
		{
			if ($sectionContext = $typeHandler->getSectionContext())
			{
				$this->setSectionContext($sectionContext);
			}

			$viewParams = array_merge($viewParams, $typeHandler->getSearchFormData());
			$templateName = $typeHandler->getTypeFormTemplate();
		}
		else
		{
			$viewParams['type'] = '';
			$templateName = 'search_form_all';
		}

		$viewParams['formTemplateName'] = $templateName;

		return $this->view('XF:Search\Form', 'search_form', $viewParams);
	}

	public function actionSearch()
	{
		$visitor = \XF::visitor();
		if (!$visitor->canSearch($error))
		{
			return $this->noPermission($error);
		}

		$filters = [
			'search_type' => 'str',
			'keywords' => 'str',
			'c' => 'array',
			'grouped' => 'bool',
			'order' => 'str'
		];

		$input = $this->filter($filters);
		$constraintInput = $this->filter('constraints', 'json-array');
		foreach ($filters AS $k => $type)
		{
			if (isset($constraintInput[$k]))
			{
				$cleaned = $this->app->inputFilterer()->filter($constraintInput[$k], $type);
				if (is_array($cleaned))
				{
					$input[$k] = array_merge($input[$k], $cleaned);
				}
				else
				{
					$input[$k] = $cleaned;
				}
			}
		}

		$query = $this->prepareSearchQuery($input, $constraints);

		if ($query->getErrors())
		{
			return $this->error($query->getErrors());
		}
		if (!strlen($query->getKeywords()) && !$query->getUserIds())
		{
			return $this->error(\XF::phrase('please_specify_search_query_or_name_of_member'));
		}

		return $this->runSearch($query, $constraints);
	}

	public function actionResults(ParameterBag $params)
	{
		/** @var \XF\Entity\Search $search */
		$search = $this->em()->find('XF:Search', $params->search_id);
		if (!$search || $search->user_id != \XF::visitor()->user_id)
		{
			if (!$this->filter('q', 'str'))
			{
				return $this->notFound();
			}

			$searchData = $this->convertShortSearchInputNames();
			$query = $this->prepareSearchQuery($searchData, $constraints);
			if ($query->getErrors())
			{
				return $this->notFound();
			}

			return $this->runSearch($query, $constraints);
		}

		$page = $this->filterPage();
		$perPage = $this->options()->searchResultsPerPage;

		$this->assertValidPage($page, $perPage, $search->result_count, 'search', $search);

		$searcher = $this->app()->search();
		$resultSet = $searcher->getResultSet($search->search_results);

		$resultSet->sliceResultsToPage($page, $perPage);

		if (!$resultSet->countResults())
		{
			return $this->message(\XF::phrase('no_results_found'));
		}

		$maxPage = ceil($search->result_count / $perPage);

		if ($search->search_order == 'date'
			&& $search->result_count > $perPage
			&& $page == $maxPage)
		{
			$lastResult = $resultSet->getLastResultData($lastResultType);
			$getOlderResultsDate = $searcher->handler($lastResultType)->getResultDate($lastResult);
		}
		else
		{
			$getOlderResultsDate = null;
		}

		$resultOptions = [
			'search' => $search,
			'term' => $search->search_query
		];
		$resultsWrapped = $searcher->wrapResultsForRender($resultSet, $resultOptions);

		$modTypes = [];
		foreach ($resultsWrapped AS $wrapper)
		{
			$handler = $wrapper->getHandler();
			$entity = $wrapper->getResult();
			if ($handler->canUseInlineModeration($entity))
			{
				$type = $handler->getContentType();
				if (!isset($modTypes[$type]))
				{
					$modTypes[$type] = $this->app->getContentTypePhrase($type);
				}
			}
		}

		$mod = $this->filter('mod', 'str');
		if ($mod && !isset($modTypes[$mod]))
		{
			$mod = '';
		}

		$viewParams = [
			'search' => $search,
			'results' => $resultsWrapped,

			'page' => $page,
			'perPage' => $perPage,

			'modTypes' => $modTypes,
			'activeModType' => $mod,

			'getOlderResultsDate' => $getOlderResultsDate
		];
		return $this->view('XF:Search\Results', 'search_results', $viewParams);
	}

	public function actionMember()
	{
		$userId = $this->filter('user_id', 'uint');
		$user = $this->assertRecordExists('XF:User', $userId, null, 'requested_member_not_found');

		$constraints = ['users' => $user->username];

		$searcher = $this->app->search();
		$query = $searcher->getQuery();
		$query->byUserId($user->user_id)
			->orderedBy('date');

		$content = $this->filter('content', 'str');
		if ($content && $searcher->isValidContentType($content))
		{
			$query->inType($content);
			$constraints['content'] = $content;
		}

		$before = $this->filter('before', 'uint');
		if ($before)
		{
			$query->olderThan($before);
		}

		$grouped = $this->filter('grouped', 'bool');
		if ($grouped && $content && $searcher->isValidContentType($content))
		{
			$typeHandler = $searcher->handler($content);
			$dummyConstraints = [];
			$query->forTypeHandler($typeHandler, $this->request, $dummyConstraints);

			$query->withGroupedResults();
		}

		return $this->runSearch($query, $constraints, false);
	}

	public function actionOlder(ParameterBag $params)
	{
		/** @var \XF\Entity\Search $search */
		$search = $this->em()->find('XF:Search', $params->search_id);
		if (!$search || $search->user_id != \XF::visitor()->user_id)
		{
			return $this->notFound();
		}

		$searchData = $this->convertSearchToQueryInput($search);
		$searchData['c']['older_than'] = $this->filter('before', 'uint');

		$query = $this->prepareSearchQuery($searchData, $constraints);
		if ($query->getErrors())
		{
			return $this->error($query->getErrors());
		}

		return $this->runSearch($query, $constraints);
	}

	protected function convertShortSearchInputNames()
	{
		$input = $this->filter([
			't' => 'str',
			'q' => 'str',
			'c' => 'array',
			'g' => 'bool',
			'o' => 'str'
		]);

		return [
			'search_type' => $input['t'] ?: null,
			'keywords' => $input['q'],
			'c' => $input['c'],
			'grouped' => $input['g'] ? 1 : 0,
			'order' => $input['o'] ?: null
		];
	}

	protected function convertSearchToQueryInput(\XF\Entity\Search $search)
	{
		return [
			'search_type' => $search->search_type,
			'keywords' => $search->search_query,
			'c' => $search->search_constraints,
			'grouped' => $search->search_grouping ? 1 : 0,
			'order' => $search->search_order
		];
	}

	protected function prepareSearchQuery(array $data, &$urlConstraints = [])
	{
		$searchRequest = new \XF\Http\Request($this->app->inputFilterer(), $data, [], []);
		$input = $searchRequest->filter([
			'search_type' => 'str',
			'keywords' => 'str',
			'c' => 'array',
			'c.title_only' => 'uint',
			'c.newer_than' => 'datetime',
			'c.older_than' => 'datetime',
			'c.users' => 'str',
			'c.content' => 'str',
			'grouped' => 'bool',
			'order' => 'str'
		]);

		$urlConstraints = $input['c'];

		$searcher = $this->app()->search();
		$query = $searcher->getQuery();

		if ($input['search_type'] && $searcher->isValidContentType($input['search_type']))
		{
			$typeHandler = $searcher->handler($input['search_type']);
			$query->forTypeHandler($typeHandler, $searchRequest, $urlConstraints);
		}

		if ($input['grouped'])
		{
			$query->withGroupedResults();
		}

		$input['keywords'] = $this->app->stringFormatter()->censorText($input['keywords'], '');
		if ($input['keywords'])
		{
			$query->withKeywords($input['keywords'], $input['c.title_only']);
		}

		if ($input['c.newer_than'])
		{
			$query->newerThan($input['c.newer_than']);
		}
		else
		{
			unset($urlConstraints['newer_than']);
		}
		if ($input['c.older_than'])
		{
			$query->olderThan($input['c.older_than']);
		}
		else
		{
			unset($urlConstraints['older_than']);
		}

		if ($input['c.users'])
		{
			$users = Arr::stringToArray($input['c.users'], '/,\s*/');
			if ($users)
			{
				/** @var \XF\Repository\User $userRepo */
				$userRepo = $this->repository('XF:User');
				$matchedUsers = $userRepo->getUsersByNames($users, $notFound);
				if ($notFound)
				{
					$query->error('users',
						\XF::phrase('following_members_not_found_x', ['members' => implode(', ', $notFound)])
					);
				}
				else
				{
					$query->byUserIds($matchedUsers->keys());
					$urlConstraints['users'] = implode(', ', $users);
				}
			}
		}

		if ($input['c.content'])
		{
			$query->inType($input['c.content']);
		}

		if ($input['order'])
		{
			$query->orderedBy($input['order']);
		}

		return $query;
	}

	protected function runSearch(\XF\Search\Query\Query $query, array $constraints, $allowCached = true)
	{
		$visitor = \XF::visitor();
		if (!$visitor->canSearch($error))
		{
			return $this->noPermission($error);
		}

		/** @var \XF\Repository\Search $searchRepo */
		$searchRepo = $this->repository('XF:Search');
		$search = $searchRepo->runSearch($query, $constraints, $allowCached);

		if (!$search)
		{
			return $this->message(\XF::phrase('no_results_found'));
		}

		return $this->redirect($this->buildLink('search', $search), '');
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('searching');
	}
}