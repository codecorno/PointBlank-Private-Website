<?php

namespace XF\Repository;

use XF\Mvc\Entity\Repository;

class CollectStats extends Repository
{
	public function getConfig()
	{
		$config = array_replace([
			'configured' => 0,
			'enabled' => 0,
			'installation_id' => ''
		], $this->options()->collectServerStats);

		return $config;
	}

	public function isEnabled()
	{
		$config = $this->getConfig();

		if ($config['configured'] && $config['enabled'])
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function collectStats()
	{
		if (!$this->isEnabled())
		{
			return [];
		}

		$config = $this->getConfig();

		return [
			'installation_id' => $config['installation_id'],
			'php' => $this->getPhpVersionString(),
			'mysql' => $this->getMySqlVersionString(),
			'xf' => \XF::$version
		];
	}

	public function getPhpVersionString()
	{
		return phpversion();
	}

	public function getMySqlVersionString()
	{
		return $this->db()->getServerVersion();
	}
}