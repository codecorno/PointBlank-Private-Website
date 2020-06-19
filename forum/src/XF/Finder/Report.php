<?php

namespace XF\Finder;

use XF\Mvc\Entity\Finder;

class Report extends Finder
{
	public function isActive()
	{
		$this->where('report_state', ['open', 'assigned']);

		return $this;
	}

	public function inTimeFrame($timeFrame = null)
	{
		if ($timeFrame)
		{
			if (!is_array($timeFrame))
			{
				$timeFrom = $timeFrame;
				$timeTo = time();
			}
			else
			{
				$timeFrom = $timeFrame[0];
				$timeTo = $timeFrame[1];
			}

			$this->where(['last_modified_date', '>=', $timeFrom]);
			$this->where(['last_modified_date', '<=', $timeTo]);
		}

		return $this;
	}

	public function forContentUser($contentUser)
	{
		if (isset($contentUser['user_id']))
		{
			$userId = $contentUser['user_id'];
		}
		else
		{
			$userId = $contentUser;
		}
		$this->where('content_user_id', $userId);

		return $this;
	}
}