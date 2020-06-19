<?php

namespace XF\Spam;

class UserChecker extends AbstractChecker
{
	public function check(\XF\Entity\User $user, array $extraParams = [])
	{
		foreach ($this->providers AS $provider)
		{
			$provider->check($user, $extraParams);
		}
	}

	public function submit(\XF\Entity\User $user, array $extraParams = [])
	{
		foreach ($this->providers AS $provider)
		{
			$provider->submit($user, $extraParams);
		}
	}

	public function getRegistrationResultFromCache($cacheKey)
	{
		return $this->app()->db()->fetchOne('
			SELECT result
			FROM xf_registration_spam_cache
			WHERE cache_key = ?
				AND timeout >= ?
		', [$cacheKey, time()]);
	}

	public function cacheRegistrationResponse($cacheKey, $result, $decision)
	{
		$cacheLifetime = ($decision == 'allowed' ? 30 : 3600);

		$this->app()->db()->insert(
			'xf_registration_spam_cache',
			[
				'cache_key' => $cacheKey,
				'result' => is_scalar($result) ? $result : serialize($result),
				'timeout' => time() + $cacheLifetime
			],
			false,
			'result = VALUES(result), timeout = VALUES(timeout)'
		);
	}
}