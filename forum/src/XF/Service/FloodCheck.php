<?php

namespace XF\Service;

class FloodCheck extends AbstractService
{
	public function checkFlooding($action, $userId, $floodingLimit = null)
	{
		if (!$userId)
		{
			return 0;
		}

		if ($floodingLimit === null)
		{
			$floodingLimit = $this->app->options()->floodCheckLength;
		}
		if ($floodingLimit <= 0)
		{
			return 0;
		}

		$time = \XF::$time;
		$floodLimitTime = $time - $floodingLimit;

		$db = $this->db();

		$updateResult = $db->query('
			UPDATE xf_flood_check
			SET flood_time = ?
			WHERE user_id = ?
				AND flood_action = ?
				AND flood_time <= ?
		', [$time, $userId, $action, $floodLimitTime]);
		if ($updateResult->rowsAffected())
		{
			// flood_time was more thant $floodingLimit ago -> no flooding
			return 0;
		}

		$insertResult = $db->query('
			INSERT IGNORE INTO xf_flood_check
				(user_id, flood_action, flood_time)
			VALUES
				(?, ?, ?)
		', [$userId, $action, $time]);
		if ($insertResult->rowsAffected())
		{
			// no flooding information stored -> no flooding
			return 0;
		}

		// flooding - get the time remaining
		$floodTime = $db->fetchOne('
			SELECT flood_time
			FROM xf_flood_check
			WHERE user_id = ?
				AND flood_action = ?
		', [$userId, $action]);

		// This is a sanity check. We got here so no DB updates have happened so we can never let the request go through.
		// We must ensure that we trigger a result that means we're flooding unless the DB can be updated to reflect
		// the current state.
		return max(1, $floodTime - $floodLimitTime);
	}

	public function pruneFloodCheckData($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = \XF::$time - 86400;
		}

		$this->db()->delete('xf_flood_check', 'flood_time < ?', $cutOff);
	}
}