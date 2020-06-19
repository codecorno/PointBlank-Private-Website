<?php

namespace XF\Import\DataHelper;

class Forum extends AbstractHelper
{
	public function importForumWatch($nodeId, $userId, array $config = [])
	{
		$this->importForumWatchBulk($nodeId, [$userId => $config]);
	}

	public function importForumWatchBulk($nodeId, array $userConfigs)
	{
		$insert = [];

		foreach ($userConfigs AS $userId => $config)
		{
			$insert[] = [
				'user_id' => $userId,
				'node_id' => $nodeId,
				'notify_on' => empty($config['notify_on']) ? '' : $config['notify_on'],
				'send_alert' => empty($config['send_alert']) ? 0 : 1,
				'send_email' => empty($config['send_email']) ? 0 : 1
			];
		}

		if ($insert)
		{
			$this->db()->insertBulk(
				'xf_forum_watch',
				$insert,
				false,
				'notify_on = VALUES(notify_on), send_alert = VALUES(send_alert), send_email = VALUES(send_email)'
			);
		}
	}
}