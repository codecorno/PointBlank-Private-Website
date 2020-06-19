<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Stats extends Repository
{
	protected $handlerCache = [];
	protected $statsTypes = [];
	protected $statsTypeMap = [];

	public function getStatsTypePhrases(array $only = null)
	{
		$phrases = [];

		foreach ($this->getStatsHandlers() AS $contentType => $handler)
		{
			$phrases = array_merge($phrases, $handler->getStatsTypes());
		}

		if (is_array($only))
		{
			$final = [];
			foreach ($only AS $k)
			{
				if (isset($phrases[$k]))
				{
					$final[$k] = $phrases[$k];
				}
			}

			$phrases = $final;
		}

		return $phrases;
	}

	public function getStatsTypeOptions()
	{
		$statsTypeOptions = [];

		foreach ($this->getStatsTypes() AS $contentType => $statsTypes)
		{
			foreach ($statsTypes AS $statsType => $statsTypePhrase)
			{
				$statsTypeOptions[$contentType][$statsType] = $statsTypePhrase;
			}
		}

		return $statsTypeOptions;
	}

	public function build($start, $end)
	{
		$db = $this->db();
		$db->beginTransaction();

		foreach ($this->getStatsHandlers() AS $contentType => $handler)
		{
			$data = $handler->getData($start, $end);

			foreach ($data AS $statsType => $records)
			{
				foreach ($records AS $date => $counter)
				{
					$db->insert('xf_stats_daily', [
						'stats_date' => $date,
						'stats_type' => $statsType,
						'counter' => $counter
					], false, "counter = $counter");
				}
			}
		}

		$db->commit();
	}

	public function getStatsTypes()
	{
		if (!$this->statsTypes)
		{
			foreach ($this->getStatsHandlers() AS $contentType => $handler)
			{
				$this->statsTypes[$contentType] = $handler->getStatsTypes();
			}
		}
		return $this->statsTypes;
	}

	/**
	 * @return \XF\Stats\AbstractHandler[]
	 */
	protected function getStatsTypeMap()
	{
		if (!$this->statsTypeMap)
		{
			foreach ($this->getStatsHandlers() AS $contentType => $handler)
			{
				foreach ($handler->getStatsTypes() AS $statsType => $null)
				{
					$this->statsTypeMap[$statsType] = $handler;
				}
			}
		}
		return $this->statsTypeMap;
	}

	/**
	 * @param string $stat
	 *
	 * @return null|\XF\Stats\AbstractHandler
	 */
	public function getStatsTypeHandler($stat)
	{
		$map = $this->getStatsTypeMap();
		return isset($map[$stat]) ? $map[$stat] : null;
	}

	/**
	 * @param string $stat
	 *
	 * @return \XF\Stats\AbstractHandler[]
	 */
	public function getStatsTypeHandlers(array $statTypes)
	{
		$map = $this->getStatsTypeMap();
		$output = [];
		foreach ($statTypes AS $type)
		{
			if (isset($map[$type]))
			{
				$output[$type] = $map[$type];
			}
		}

		return $output;
	}

	/**
	 * @return \XF\Stats\AbstractHandler[]
	 */
	public function getStatsHandlers()
	{
		foreach (\XF::app()->getContentTypeField('stats_handler_class') AS $contentType => $handlerClass)
		{
			if (isset($this->handlerCache[$contentType]))
			{
				continue;
			}

			if (class_exists($handlerClass))
			{
				$handlerClass = \XF::extendClass($handlerClass);
				$this->handlerCache[$contentType] = new $handlerClass($contentType, $this->app());
			}
		}

		return $this->handlerCache;
	}

	/**
	 * @param string $type
	 * @param bool $throw
	 *
	 * @return \XF\Stats\AbstractHandler|null
	 */
	public function getStatsHandler($type, $throw = false)
	{
		if (isset($this->handlerCache[$type]))
		{
			return $this->handlerCache[$type];
		}

		$handlerClass = $this->app()->getContentTypeFieldValue($type, 'stats_handler_class');
		if (!$handlerClass)
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("No stats handler for '$type'");
			}
			return null;
		}

		if (!class_exists($handlerClass))
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("Stats handler for '$type' does not exist: $handlerClass");
			}
			return null;
		}

		$handlerClass = \XF::extendClass($handlerClass);
		$this->handlerCache[$type] = new $handlerClass($type, $this->app());
		return $this->handlerCache[$type];
	}
}