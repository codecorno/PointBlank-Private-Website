<?php

namespace XF\Pub\Route;

class SpamCleaner
{
	public static function build(&$prefix, array &$route, &$action, &$data, array &$params)
	{
		if (isset($data['ip_id']))
		{
			$params['ip_id'] = $data['ip_id'];
		}

		return null; // default processing otherwise
	}
}