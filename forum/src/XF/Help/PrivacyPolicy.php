<?php

namespace XF\Help;

use XF\Mvc\Controller;
use XF\Mvc\Reply\View;

class PrivacyPolicy
{
	public static function renderPrivacyPolicy(Controller $controller, View &$response)
	{
		$privacyPolicyUrl = \XF::app()->container('privacyPolicyUrl');
		if (!$privacyPolicyUrl)
		{
			throw $controller->exception(
				$controller->redirectPermanently($controller->buildLink('index'))
			);
		}

		$option = \XF::options()->privacyPolicyUrl;
		if ($option['type'] == 'custom')
		{
			throw $controller->exception(
				$controller->redirectPermanently($option['custom'])
			);
		}
	}
}