<?php

namespace XF\Stats;

class User extends AbstractHandler
{
	public function getStatsTypes()
	{
		return [
			'user_registration' => \XF::phrase('user_registrations'),
			'user_activity' => \XF::phrase('users_active')
		];
	}

	public function getData($start, $end)
	{
		$db = $this->db();

		$userRegistrations = $db->fetchPairs(
			$this->getBasicDataQuery('xf_user', 'register_date'),
			[$start, $end]
		);

		// this will only ever fetch the past 24 hours
		$usersActive = $db->fetchPairs('
			SELECT ' . ($start - $start % 86400) . ',
				COUNT(*)
			FROM xf_user
			WHERE last_activity > ?
		', \XF::$time - 86400); // 24 hours ago

		return [
			'user_registration' => $userRegistrations,
			'user_activity' => $usersActive
		];
	}
}