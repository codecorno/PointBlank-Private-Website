<?php

namespace XF\Pub\Route;

class EmailStop
{
	public static function build(&$prefix, array &$route, &$action, &$data, array &$params)
	{
		if ($data instanceof \XF\Entity\User)
		{
			$params['c'] = $data->email_confirm_key;
		}

		return null; // default processing otherwise
	}
}