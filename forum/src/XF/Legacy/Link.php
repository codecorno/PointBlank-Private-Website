<?php

namespace XF\Legacy;

class Link
{
	public static function buildPublicLink($type, $data = null, array $extraParams = [])
	{
		$app = \XF::app();

		/** @var \XF\Mvc\Router $router */
		$router = $app['router.public'];
		return $router->buildLink($type, $data, $extraParams);
	}

	public static function buildAdminLink($type, $data = null, array $extraParams = [])
	{
		$app = \XF::app();

		/** @var \XF\Mvc\Router $router */
		$router = $app['router.admin'];
		return $router->buildLink($type, $data, $extraParams);
	}
}