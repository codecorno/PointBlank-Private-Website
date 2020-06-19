<?php

namespace XF\Help;

use XF\Mvc\Controller;
use XF\Mvc\Reply\View;

class Trophies
{
	public static function renderTrophies(Controller $controller, View &$response)
	{
		if (!$controller->options()->enableTrophies)
		{
			throw $controller->exception($controller->redirect($controller->buildLink('help')));
		}

		/** @var \XF\Repository\Trophy $trophyRepo */
		$trophyRepo = $controller->repository('XF:Trophy');
		$trophies = $trophyRepo->findTrophiesForList();
		$response->setParam('trophies', $trophies->fetch());
	}
}