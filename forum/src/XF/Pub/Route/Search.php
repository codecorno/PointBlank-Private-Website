<?php

namespace XF\Pub\Route;

class Search
{
	public static function build(&$prefix, array &$route, &$action, &$data, array &$params)
	{
		if ($data instanceof \XF\Entity\Search && $data->search_query)
		{
			$params['q'] = $data->search_query;
			$params['t'] = $data->search_type;
			$params['c'] = $data->search_constraints;
			$params['o'] = $data->search_order;
			if ($data->search_grouping)
			{
				$params['g'] = 1;
			}
		}

		return null; // default processing otherwise
	}
}