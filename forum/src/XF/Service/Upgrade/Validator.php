<?php

namespace XF\Service\Upgrade;

use XF\Service\AbstractService;

class Validator extends AbstractService
{
	/**
	 * @var \XFUpgrader
	 */
	protected $upgrader;

	protected function setup()
	{
		require_once(\XF::getRootDirectory() . '/src/XF/Install/_upgrader/core.php');

		$this->upgrader = new \XFUpgrader();
	}

	public function canAttempt(&$error = null)
	{
		return $this->upgrader->canAttempt($error);
	}

	public function validateUpgradeFile($file, &$error = null)
	{
		return $this->upgrader->validateFile($file, $error);
	}
}