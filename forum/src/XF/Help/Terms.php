<?php

namespace XF\Help;

use XF\Mvc\Controller;
use XF\Mvc\Reply\View;

class Terms
{
	public static function renderTerms(Controller $controller, View &$response)
	{
		$tosUrl = \XF::app()->container('tosUrl');
		if (!$tosUrl)
		{
			throw $controller->exception(
				$controller->redirectPermanently($controller->buildLink('index'))
			);
		}

		$option = \XF::options()->tosUrl;
		if ($option['type'] == 'custom')
		{
			throw $controller->exception(
				$controller->redirectPermanently($option['custom'])
			);
		}
	}
}