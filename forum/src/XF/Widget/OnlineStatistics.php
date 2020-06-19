<?php

namespace XF\Widget;

class OnlineStatistics extends AbstractWidget
{
	public function render()
	{
		/** @var \XF\Repository\SessionActivity $activityRepo */
		$activityRepo = $this->repository('XF:SessionActivity');

		$viewParams = [
			'counts' => $activityRepo->getOnlineCounts()
		];
		return $this->renderer('widget_online_statistics', $viewParams);
	}

	public function getOptionsTemplate()
	{
		return null;
	}
}