<?php

namespace XF\Spam\Checker;

abstract class AbstractProvider
{
	/** @var \XF\Spam\AbstractChecker */
	protected $checker;

	protected $app;

	public function __construct(\XF\Spam\AbstractChecker $checker, \XF\App $app)
	{
		$this->checker = $checker;
		$this->app = $app;
	}

	abstract protected function getType();

	protected function logDecision($decision)
	{
		$this->checker->logDecision($this->getType(), $decision);
	}

	protected function logDetail($phraseKey, array $data = [])
	{
		$this->checker->logDetail($this->getType(), $phraseKey, $data);
	}

	protected function logParam($key, $value)
	{
		$this->checker->logParam($key, $value);
	}

	protected function getContentSpamCheckParams($contentType, $contentIds)
	{
		if (!is_array($contentIds))
		{
			$contentIds = [$contentIds];
		}

		$db = $this->app()->db();
		$pairs = $db->fetchPairs("
				SELECT content_id, spam_params
				FROM xf_content_spam_cache
				WHERE content_type = ?
					AND content_id IN (" . $db->quote($contentIds) . ")
			", $contentType);
		foreach ($pairs AS &$value)
		{
			$value = @unserialize($value);
		}

		return $pairs;
	}

	/**
	 * @return \XF\App
	 */
	protected function app()
	{
		return $this->app;
	}
}