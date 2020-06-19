<?php

namespace XF\Api\ControllerPlugin;

class Thread extends AbstractPlugin
{
	public function applyThreadListFilters(\XF\Finder\Thread $threadFinder, \XF\Entity\Forum $forum = null)
	{
		$filters = [];

		if ($forum)
		{
			$prefixId = $this->filter('prefix_id', 'uint');
			if ($prefixId)
			{
				$threadFinder->where('prefix_id', $prefixId);
				$filters['prefix_id'] = $prefixId;
			}
		}

		$starterId = $this->filter('starter_id', 'uint');
		if ($starterId)
		{
			$threadFinder->where('user_id', $starterId);
			$filters['starter_id'] = $starterId;
		}

		$lastDays = $this->filter('last_days', '?uint');
		if (is_int($lastDays))
		{
			if ($lastDays)
			{
				$threadFinder->where('last_post_date', '>=', \XF::$time - ($lastDays * 86400));
			}
			// 0 means no limit here -- bypass the forum default limit if there is one

			$filters['last_days'] = $lastDays;
		}

		return $filters;
	}

	public function applyThreadListSort(\XF\Finder\Thread $threadFinder, \XF\Entity\Forum $forum = null)
	{
		$order = $this->filter('order', 'str');
		if (!$order)
		{
			return null;
		}

		$direction = $this->filter('direction', 'str');
		if ($direction !== 'asc')
		{
			$direction = 'desc';
		}

		switch ($order)
		{
			case 'last_post_date':
			case 'post_date':
				$threadFinder->order($order, $direction);
				return [$order, $direction];
		}

		if ($forum)
		{
			switch ($order)
			{
				case 'title':
				case 'reply_count':
				case 'view_count':
				case 'first_post_reaction_score':
					$threadFinder->order($order, $direction);
					return [$order, $direction];
			}
		}

		return null;
	}
}