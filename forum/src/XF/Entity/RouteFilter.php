<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null route_filter_id
 * @property string prefix
 * @property string find_route
 * @property string replace_route
 * @property bool enabled
 * @property bool url_to_route_only
 *
 * GETTERS
 * @property mixed find_route_readable
 * @property mixed replace_route_readable
 */
class RouteFilter extends Entity
{
	public function getFindRouteReadable()
	{
		return urldecode($this->find_route);
	}

	public function getReplaceRouteReadable()
	{
		return urldecode($this->replace_route);
	}

	protected function verifyRoute(&$value)
	{
		$value = trim($value);
		$value = ltrim($value, '/');

		if (strpos($value, '/') === false)
		{
			$value .= '/';
		}

		$value = preg_replace_callback(
			'/[^\x21-\xff]/u',
			function ($match)
			{
				return urlencode($match[0]);
			},
			$value
		);
		$value = preg_replace('/%(?![0-9a-f]{2})/i', '%25', $value);

		return true;
	}

	protected function _preSave()
	{
		if (substr($this->find_route, -1) == '/' && substr($this->replace_route, -1) != '/')
		{
			$this->replace_route = $this->replace_route . '/';
		}

		if ($this->isChanged('find_route'))
		{
			if (!preg_match('#^([^\?&=/\. \#\[\]:;{}]+)(/|$)#', $this->find_route, $match))
			{
				$this->error(\XF::phrase('find_route_must_start_with_route_prefix'), 'find_route');
			}
			else
			{
				$this->prefix = $match[1];
			}
		}

		if ($this->isChanged('replace_route'))
		{
			if (!preg_match('#^([^\?&=/\. \#\[\]:;{}]+)(/|$)#', $this->replace_route, $match))
			{
				$this->error(\XF::phrase('replace_route_must_start_with_route_prefix'), 'replace_route');
			}
		}

		if (!$this->url_to_route_only)
		{
			$fromCount = $this->countWildcards($this->find_route);
			$toCount = $this->countWildcards($this->replace_route);

			if ($fromCount != $toCount)
			{
				$this->error(\XF::phrase('find_and_replace_fields_must_have_same_number_of_wildcards'), 'replace_route');
			}
		}
	}

	protected function countWildcards($string)
	{
		return preg_match_all('/\{([a-z0-9_]+)(:([^}]+))?\}/i', $string, $null);
	}

	protected function _postSave()
	{
		$this->rebuildRouteFilterCaches();
	}

	protected function _postDelete()
	{
		$this->rebuildRouteFilterCaches();
	}

	protected function rebuildRouteFilterCaches()
	{
		$repo = $this->getRouteFilterRepo();

		\XF::runOnce('routeFilterCachesRebuild', function() use ($repo)
		{
			$repo->rebuildRouteFilterCache();
		});
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_route_filter';
		$structure->shortName = 'XF:RouteFilter';
		$structure->primaryKey = 'route_filter_id';
		$structure->columns = [
			'route_filter_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'prefix' => ['type' => self::STR, 'maxLength' => 25, 'default' => ''],
			'find_route' => ['type' => self::STR, 'maxLength' => 255, 'required' => true,
				'verify' => 'verifyRoute'
			],
			'replace_route' => ['type' => self::STR, 'maxLength' => 255,
				'required' => 'please_enter_a_replacement_value',
				'verify' => 'verifyRoute'
			],
			'enabled' => ['type' => self::BOOL, 'default' => true],
			'url_to_route_only' => ['type' => self::BOOL, 'default' => false]
		];
		$structure->getters = [
			'find_route_readable' => true,
			'replace_route_readable' => true,
		];
		$structure->relations = [];

		return $structure;
	}

	/**
	 * @return \XF\Repository\RouteFilter
	 */
	protected function getRouteFilterRepo()
	{
		return $this->repository('XF:RouteFilter');
	}
}