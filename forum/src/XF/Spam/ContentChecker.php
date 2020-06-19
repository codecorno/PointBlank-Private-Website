<?php

namespace XF\Spam;

class ContentChecker extends AbstractChecker
{
	public function check(\XF\Entity\User $user, $message, array $extraParams = [])
	{
		foreach ($this->providers AS $provider)
		{
			$provider->check($user, $message, $extraParams);
		}
	}

	public function submitSpam($contentType, $contentIds)
	{
		foreach ($this->providers AS $provider)
		{
			$provider->submitSpam($contentType, $contentIds);
		}
	}

	public function submitHam($contentType, $contentIds)
	{
		foreach ($this->providers AS $provider)
		{
			$provider->submitHam($contentType, $contentIds);
		}
	}

	public function logContentSpamCheck($contentType, $contentId)
	{
		if (!$this->params)
		{
			return;
		}

		$this->app()->db()->insert('xf_content_spam_cache', [
			'content_type' => $contentType,
			'content_id' => $contentId,
			'spam_params' => serialize($this->params),
			'insert_date' => time()
		], false, 'spam_params = VALUES(spam_params), insert_date = VALUES(insert_date)');
	}
}