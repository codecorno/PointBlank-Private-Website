<?php

namespace XF\Pub\Controller;

class RecentActivity extends AbstractController
{
	public function actionIndex()
	{
		return $this->redirectPermanently($this->buildLink('whats-new/latest-activity'));
	}
}