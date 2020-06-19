<?php

namespace XF\Spam\Checker;

class StopForumSpam extends AbstractProvider implements UserCheckerInterface
{
	/** @var \XF\Entity\User */
	protected $user;

	public function getType()
	{
		return 'StopForumSpam';
	}

	public function check(\XF\Entity\User $user, array $extraParams = [])
	{
		$this->user = $user;

		$option = $this->app()->options()->stopForumSpam;
		$decision = 'allowed';

		$apiResponse = $this->getSfsApiResponse($apiUrl, $fromCache);
		if (!$apiResponse)
		{
			return;
		}

		$flagCount = $this->getSfsSpamFlagCount($apiResponse, $counts);
		if ($option['moderateThreshold'] && $flagCount >= (int)$option['moderateThreshold'])
		{
			$decision = 'moderated';
		}

		if ($option['denyThreshold'] && $flagCount >= (int)$option['denyThreshold'])
		{
			$decision = 'denied';
		}

		$this->logDecision($decision);

		if (!$fromCache)
		{
			// only update the cache if we didn't pull from the cache - this
			// prevents the cache from being kept indefinitely
			$cacheKey = $this->getSfsCacheKey($apiUrl);

			/** @var \XF\Spam\UserChecker $checker */
			$checker = $this->checker;
			$checker->cacheRegistrationResponse($cacheKey, $apiResponse, $decision);
		}

		if ($decision != 'allowed')
		{
			$parts = [];
			foreach ($counts AS $flag => $count)
			{
				$value = $count == 255 ? 'blacklisted': $count;
				$parts[] = "$flag: $value";
			}
			$this->logDetail('sfs_matched_x', [
				'matches' => implode(', ', $parts)
			]);
		}
	}

	public function submit(\XF\Entity\User $user, array $extraParams = [])
	{
		$this->user = $user;

		$submitUrl = $this->getSfsApiSubmitUrl();

		$client = $this->app->http()->client();

		try
		{
			$response = $client->get($submitUrl);
			if ($response && $response->getStatusCode() >= 400)
			{
				if (preg_match('#<p>(.+)</p>#siU', $response->getBody()->getContents(), $match))
				{
					// don't log this race condition
					if ($match[1] != 'recent duplicate entry')
					{
						$e = new \ErrorException("Error reporting to StopForumSpam: $match[1]");
						$this->app()->logException($e, false);
					}
				}
			}
		}
		catch (\GuzzleHttp\Exception\RequestException $e) {}
		// SFS can go down frequently, so don't log this
	}

	protected function getSfsApiResponse(&$apiUrl = '', &$fromCache = false)
	{
		$apiUrl = $this->getSfsApiUrl();
		$cacheKey = $this->getSfsCacheKey($apiUrl);

		/** @var \XF\Spam\UserChecker $checker */
		$checker = $this->checker;

		if ($result = $checker->getRegistrationResultFromCache($cacheKey))
		{
			$fromCache = true;
			return unserialize($result);
		}

		$client = $this->app->http()->client();

		try
		{
			$response = $client->get($apiUrl);
			$body = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);

			return $body;
		}
		catch (\GuzzleHttp\Exception\RequestException $e)
		{
			return false;
		}
	}

	protected function getSfsApiUrl()
	{
		$user = $this->user;
		$ip = $this->app()->request()->getIp();

		$email = '';
		$option = $this->app()->options()->stopForumSpam;
		if (!empty($option['hashEmail']) && $user->email)
		{
			// emailhash submission does not do any normalization so handle it manually
			$parts = explode('@', strtolower($user->email));

			// sanity check; we verify emails but just in case
			if (count($parts) === 2)
			{
				list($beforeAt, $afterAt) = $parts;

				// we're only interested in stuff before the + if it exists
				if (strpos($beforeAt, '+') !== false)
				{
					$beforeAt = explode('+', $beforeAt);
					$beforeAt = $beforeAt[0];
				}

				// known providers who ignore dots
				$ignoreDots = ['gmail.com', 'googlemail.com'];
				if (in_array($afterAt, $ignoreDots))
				{
					$beforeAt = str_replace('.', '', $beforeAt);
				}

				$email = '&emailhash=' . md5($beforeAt . '@' . $afterAt);
			}
		}

		if (!$email && $user->email)
		{
			$email = '&email=' . urlencode($user->email);
		}

		return 'https://api.stopforumspam.org/api?f=json&unix=1'
			. ($user->username ? '&username=' . urlencode($user->username) : '')
			. ($email ? $email : '')
			. ($ip ? '&ip=' . urlencode($ip) : '');
	}

	protected function getSfsApiSubmitUrl()
	{
		$user = $this->user;
		$ip = $user->getIp('register');

		return 'https://www.stopforumspam.com/add.php'
			. '?api_key=' . $this->app()->options()->stopForumSpam['apiKey']
			. ($user->username ? '&username=' . urlencode($user->username) : '')
			. ($user->email ? '&email=' . urlencode($user->email) : '')
			. '&ip=' . urlencode(\XF\Util\Ip::convertIpBinaryToString($ip));
	}

	protected function getSfsCacheKey($apiUrl)
	{
		return 'stopForumSpam_' . sha1($apiUrl);
	}

	protected function getSfsSpamFlagCount(array $data, &$counts = [])
	{
		$option = $this->app()->options()->stopForumSpam;

		$flagCount = 0;
		$counts = [];

		if (!empty($data['success']))
		{
			foreach (['username', 'email', 'emailhash', 'ip'] AS $flagName)
			{
				if (!empty($data[$flagName]))
				{
					$flag = $data[$flagName];

					if (!empty($flag['appears']))
					{
						if ($flag['frequency'])
						{
							if ($flagName == 'emailhash')
							{
								// consider emailhash flag to be same as email
								$flagName = 'email';
							}
							$counts[$flagName] = $flag['frequency'];
						}

						if (empty($option['frequencyCutOff']) || $flag['frequency'] >= $option['frequencyCutOff'])
						{
							if (empty($option['lastSeenCutOff']) || $flag['lastseen'] >= time() - $option['lastSeenCutOff'] * 86400)
							{
								$flagCount++;
							}
						}
					}
				}
			}
		}

		return $flagCount;
	}
}