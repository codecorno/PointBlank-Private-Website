<?php

namespace XF\Widget;

class ForumStatistics extends AbstractWidget
{
	public function render()
	{
		$viewParams = [
			'forumStatistics' => $this->app->forumStatistics
		];
		return $this->renderer('widget_forum_statistics', $viewParams);
	}

	public function getOptionsTemplate()
	{
		return null;
	}
}