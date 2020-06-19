<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class GotoPage extends AbstractController
{
	public function actionPost(ParameterBag $params)
	{
		$params->offsetSet('post_id', $this->filter('id', 'uint'));
		return $this->rerouteController('XF:Post', 'index', $params);
	}

	public function actionConvMessage(ParameterBag $params)
	{
		$params->offsetSet('message_id', $this->filter('id', 'uint'));
		return $this->rerouteController('XF:Conversation', 'messages', $params);
	}

	public static function getActivityDetails(array $activities) {}
}