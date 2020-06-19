<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class FindNewDefault extends Repository
{
	public function getUserDefault($userId, $contentType)
	{
		if (!$userId || !$contentType)
		{
			return null;
		}

		return $this->em->findOne('XF:FindNewDefault', [
			'user_id' => $userId,
			'content_type' => $contentType
		]);
	}

	public function getUserDefaultFilters($userId, $contentType)
	{
		$default = $this->getUserDefault($userId, $contentType);
		return ($default ? $default->filters : null);
	}

	public function saveUserDefaultFilters($userId, $contentType, array $filters)
	{
		if (!$userId || !$contentType)
		{
			return null;
		}

		$default = $this->getUserDefault($userId, $contentType);
		if (!$default)
		{
			$default = $this->em->create('XF:FindNewDefault');
			$default->user_id = $userId;
			$default->content_type = $contentType;
		}

		$default->filters = $filters;
		$default->save();

		return $default;
	}
}