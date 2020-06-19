<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\View;

class Tag extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		if (!$this->options()->enableTagging)
		{
			throw $this->exception($this->noPermission());
		}
	}

	public function actionIndex(ParameterBag $params)
	{
		if ($params->tag_url)
		{
			return $this->rerouteController('XF:Tag', 'Tag', $params);
		}

		$tagRepo = $this->getTagRepo();

		$tagList = $this->filter('tags', 'str');

		if ($this->isPost())
		{
			$tags = $tagRepo->splitTagList($tagList);

			if (!$tags)
			{
				return $this->error(\XF::phrase('please_enter_single_tag'));
			}
			else if (count($tags) == 1)
			{
				$tag = $this->finder('XF:Tag')->where('tag', $tags[0])->fetchOne();
				if ($tag)
				{
					return $this->redirect($this->buildLink('tags', $tag), '');
				}
				else
				{
					return $this->error(\XF::phrase('following_tags_not_found_x', ['tags' => $tagList]));
				}
			}
			else
			{
				if (!\XF::visitor()->canSearch())
				{
					return $this->error(\XF::phrase('please_enter_single_tag'));
				}

				$validTags = $tagRepo->getTags($tags, $notFound);
				if ($notFound)
				{
					return $this->error(\XF::phrase(
						'following_tags_not_found_x',
						['tags' => implode(', ', $notFound)]
					));
				}

				$searcher = $this->app()->search();
				$query = $searcher->getQuery();

				$tagIds = array_keys($validTags);
				$query->withTags($tagIds);
				$constraints = [
					'tag' => implode(' ', $tagIds)
				];

				/** @var \XF\Repository\Search $searchRepo */
				$searchRepo = $this->repository('XF:Search');
				$search = $searchRepo->runSearch($query, $constraints);

				if ($search)
				{
					return $this->redirect($this->buildLink('search', $search), '');
				}
				else
				{
					return $this->message(\XF::phrase('no_results_found'));
				}
			}
		}
		else
		{
			$cloudOption = $this->options()->tagCloud;
			if ($cloudOption['enabled'])
			{
				$cloudEntries = $tagRepo->getTagsForCloud($cloudOption['count'], $this->options()->tagCloudMinUses);
				$tagCloud = $tagRepo->getTagCloud($cloudEntries);
			}
			else
			{
				$tagCloud = [];
			}

			$viewParams = [
				'tags' => $tagList,
				'tagCloud' => $tagCloud,
				'canSearch' => \XF::visitor()->canSearch(),
				'tabs' => $this->app->search()->getSearchTypeTabs(),
			];
			return $this->view('XF:Tag\Search', 'tag_search', $viewParams);
		}
	}

	public function actionTag(ParameterBag $params)
	{
		if ($params->tag_url)
		{
			$tag = $this->finder('XF:Tag')->where('tag_url', $params->tag_url)->fetchOne();
		}
		else
		{
			$tag = null;
		}
		if (!$tag)
		{
			return $this->error(\XF::phrase('requested_tag_not_found'), 404);
		}

		$page = $this->filterPage($params->page);
		$perPage = $this->options()->searchResultsPerPage;

		$tagRepo = $this->getTagRepo();

		$cache = $tagRepo->getTagResultCache($tag->tag_id);
		if ($cache->requiresRefetch())
		{
			$limit = $this->options()->maximumSearchResults;
			$tagResults = $tagRepo->getTagSearchResults($tag->tag_id, $limit);
			$resultSet = $tagRepo->getTagResultSet($tagResults)->limitToViewableResults();

			if (!$resultSet->countResults())
			{
				return $this->message(\XF::phrase('no_results_found'));
			}

			$cache->results = $resultSet->getResults();

			if ($resultSet->countResults() > $perPage)
			{
				try
				{
					$cache->save();
				}
				catch (\XF\Db\DuplicateKeyException $e)
				{

				}
			}

			$resultSet->sliceResultsToPage($page, $perPage, false); // already limited to viewable
		}
		else
		{
			$resultSet = $tagRepo->getTagResultSet($cache->results);
			$resultSet->sliceResultsToPage($page, $perPage);
		}

		$totalResults = count($cache->results);

		$this->assertValidPage($page, $perPage, $totalResults, 'tags', $tag);
		$this->assertCanonicalUrl($this->buildLink('tags', $tag, ['page' => $page]));

		$resultOptions = [];
		$resultsWrapped = $tagRepo->wrapResultsForRender($resultSet, $resultOptions);

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
			'tag' => $tag,
			'cache' => $cache,
			'results' => $resultsWrapped,

			'modTypes' => $modTypes,
			'activeModType' => $mod,

			'page' => $page,
			'perPage' => $perPage,
			'totalResults' => $totalResults
		];
		return $this->view('XF:Tag\View', 'tag_view', $viewParams);
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('viewing_tags');
	}

	/**
	 * @return \XF\Repository\Tag
	 */
	protected function getTagRepo()
	{
		return $this->repository('XF:Tag');
	}
}