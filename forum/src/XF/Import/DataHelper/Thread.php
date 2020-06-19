<?php

namespace XF\Import\DataHelper;

class Thread extends AbstractHelper
{
	public function importThreadWatch($threadId, $userId, $email = false)
	{
		$this->importThreadWatchBulk($threadId, [$userId => $email]);
	}

	public function importThreadWatchBulk($threadId, array $userConfigs)
	{
		$insert = [];

		foreach ($userConfigs AS $userId => $config)
		{
			if (is_scalar($config))
			{
				$config = ['email_subscribe' => (bool)$config];
			}

			$insert[] = [
				'user_id' => $userId,
				'thread_id' => $threadId,
				'email_subscribe' => empty($config['email_subscribe']) ? 0 : 1
			];
		}

		if ($insert)
		{
			$this->db()->insertBulk(
				'xf_thread_watch',
				$insert,
				false,
				'email_subscribe = VALUES(email_subscribe)'
			);
		}
	}
}