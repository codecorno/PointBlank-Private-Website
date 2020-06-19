<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Search extends Repository
{
	public function runSearch(\XF\Search\Query\Query $query, array $constraints = [], $allowCached = true)
	{
		$user = \XF::visitor();

		/** @var \XF\Entity\Search $search */
		$search = $this->em->create('XF:Search');
		$search->setupFromQuery($query, $constraints);
		$search->user_id = $user->user_id;

		if ($allowCached && $this->allowUserUseCachedResults($user))
		{
			$previous = $this->getPreviousSearch($query, $user);
		}
		else
		{
			$previous = null;
		}

		if ($previous)
		{
			$search = $previous;
		}
		else
		{
			$results = $this->app()->search()->search($query);
			if (!$results)
			{
				return null;
			}

			$search->search_results = $results;
			$search->save();
		}

		return $search;
	}

	public function allowUserUseCachedResults(\XF\Entity\User $user)
	{
		if (\XF::$debugMode)
		{
			return false;
		}

		if ($user->is_moderator || $user->is_admin)
		{
			return false;
		}

		return true;
	}

	public function getPreviousSearch(\XF\Search\Query\Query $query, \XF\Entity\User $user = null)
	{
		$user = $user ?: \XF::visitor();

		$finder = $this->finder('XF:Search')
			->where([
				'query_hash' => $query->getUniqueQueryHash(),
				'search_type' => $query->getHandlerType() ?: '', // mostly just a sanity on the hash
				'user_id' => $user->user_id
			])
			->where('search_date', '>=', \XF::$time - 3600)
			->order('search_date', 'desc');

		return $finder->fetchOne();
	}

	public function pruneSearches($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = \XF::$time - 86400;
		}

		return $this->db()->delete('xf_search', 'search_date < ?', $cutOff);
	}
}