<?php

namespace XF\Help;

use XF\Mvc\Controller;
use XF\Mvc\Reply\View;

class BbCodes
{
	public static function renderBbCodes(Controller $controller, View &$response)
	{
		$finder = $controller->repository('XF:BbCode')->findActiveBbCodes();
		$response->setParam('bbCodes', $finder->fetch());
	}
}