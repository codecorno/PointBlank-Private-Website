<?php

namespace XF;

class AdminNavigation
{
	/**
	 * @var array
	 */
	protected $entries;

	/**
	 * @var array|null
	 */
	protected $filtered;

	/**
	 * @var bool
	 */
	protected $isDebug;

	/**
	 * @var bool
	 */
	protected $isDevelopment;

	/**
	 * @var \XF\Entity\User
	 */
	protected $visitor;

	public function __construct(array $entries, $isDebug = null, $isDevelopment = null, \XF\Entity\User $visitor = null)
	{
		$this->entries = $entries;
		$this->setIsDebug($isDebug);
		$this->setIsDevelopment($isDevelopment);
		$this->setVisitor($visitor);
	}

	public function setIsDebug($isDebug)
	{
		if ($isDebug === null)
		{
			$isDebug = \XF::$debugMode;
		}
		$isDebug = boolval($isDebug);

		if ($isDebug !== $this->isDebug)
		{
			$this->isDebug = $isDebug;
			$this->filtered = null;
		}
	}

	public function getIsDebug()
	{
		return $this->isDebug;
	}

	public function setIsDevelopment($isDevelopment)
	{
		if ($isDevelopment === null)
		{
			$isDevelopment = \XF::$developmentMode;
		}
		$isDevelopment = boolval($isDevelopment);

		if ($isDevelopment !== $this->isDevelopment)
		{
			$this->isDevelopment = $isDevelopment;
			$this->filtered = null;
		}
	}

	public function getIsDevelopment()
	{
		return $this->isDevelopment;
	}

	public function setVisitor(\XF\Entity\User $visitor = null)
	{
		if (!$visitor)
		{
			$visitor = \XF::visitor();
		}

		if (!$this->visitor || $this->visitor->user_id != $visitor->user_id)
		{
			$this->visitor = $visitor;
			$this->filtered = null;
		}
	}

	public function getVisitor()
	{
		return $this->visitor;
	}

	public function getEntries()
	{
		return $this->entries;
	}

	public function getFiltered()
	{
		if (!is_array($this->filtered))
		{
			$this->filtered = $this->setupFiltered();
		}

		return $this->filtered;
	}

	protected function setupFiltered()
	{
		$map = [];
		foreach ($this->entries AS $id => $entry)
		{
			$map[$entry['parent_navigation_id']][$id] = $id;
		}

		$filtered = [];
		$this->setupFilteredRecurse('', 0, $map, $filtered);

		return $filtered;
	}

	protected function setupFilteredRecurse($rootId, $depth, array $map, array &$filtered)
	{
		if (!isset($map[$rootId]))
		{
			return [];
		}

		$isDebug = $this->isDebug;
		$isDevelopment = $this->isDevelopment;
		$visitor = $this->visitor;

		$validChildren = [];

		foreach ($map[$rootId] AS $id)
		{
			$entry = $this->entries[$id];

			if ($entry['debug_only'] && !$isDebug)
			{
				continue;
			}

			if ($entry['development_only'] && !$isDevelopment)
			{
				continue;
			}

			if ($entry['admin_permission_id'] && !$visitor->hasAdminPermission($entry['admin_permission_id']))
			{
				continue;
			}

			$entryChildren = $this->setupFilteredRecurse($id, $depth + 1, $map, $filtered);
			if ($entry['hide_no_children'] && !$entryChildren)
			{
				continue;
			}

			$filtered[$id] = $entry;
			$filtered[$id]['title'] = \XF::phrase($entry['phrase']);

			$validChildren[] = $id;
		}

		return $validChildren;
	}

	public function getTree($root = '')
	{
		return new \XF\Tree($this->getFiltered(), 'parent_navigation_id', $root);
	}
}