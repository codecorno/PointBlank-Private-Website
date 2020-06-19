<?php

namespace XF\Repository;

use XF\Mvc\Entity\ArrayCollection;
use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

abstract class AbstractPrefix extends Repository
{
	abstract protected function getRegistryKey();

	abstract protected function getClassIdentifier();

	public function getDefaultGroup()
	{
		$prefixGroup = $this->em->create($this->getClassIdentifier() . 'Group');
		$prefixGroup->setTrusted('prefix_group_id', 0);
		$prefixGroup->setTrusted('display_order', 0);
		$prefixGroup->setReadOnly(true);

		return $prefixGroup;
	}

	/**
	 * @param bool $getDefault
	 *
	 * @return Finder|\XF\Mvc\Entity\ArrayCollection
	 */
	public function findPrefixGroups($getDefault = false)
	{
		$groups = $this->finder($this->getClassIdentifier() . 'Group')
			->order('display_order');

		if ($getDefault)
		{
			$groups = $groups->fetch();

			$defaultGroup = $this->getDefaultGroup();
			$prefixGroups = $groups->toArray();
			$prefixGroups = $prefixGroups + [$defaultGroup];
			$groups = $this->em->getBasicCollection($prefixGroups);
		}

		return $groups;
	}

	public function getPrefixListData()
	{
		$prefixes = $this->findPrefixesForList()->fetch();
		$prefixGroups = $this->findPrefixGroups(true);

		return [
			'prefixGroups' => $prefixGroups,
			'prefixesGrouped' => $prefixes->groupBy('prefix_group_id'),
			'prefixTotal' => count($prefixes)
		];
	}

	public function getVisiblePrefixListData()
	{
		return $this->_getVisiblePrefixListData();
	}

	protected function _getVisiblePrefixListData(\Closure $isVisibleClosure = null)
	{
		$prefixes = $this->findPrefixesForList()->fetch();

		$visiblePrefixes = [];

		foreach ($prefixes AS $prefixId => $prefix)
		{
			/** @var \XF\Entity\ThreadPrefix $prefix */
			if ($isVisibleClosure)
			{
				$isVisible = $isVisibleClosure($prefix);
				if ($isVisible)
				{
					$visiblePrefixes[$prefixId] = $prefix;
				}
			}
			else
			{
				$visiblePrefixes[$prefixId] = $prefix;
			}
		}

		$visiblePrefixes = new ArrayCollection($visiblePrefixes);
		$prefixGroups = $this->findPrefixGroups(true);

		return [
			'prefixGroups' => $prefixGroups,
			'prefixesGrouped' => $visiblePrefixes->groupBy('prefix_group_id'),
			'prefixTotal' => count($visiblePrefixes)
		];
	}

	/**
	 * @return Finder
	 */
	public function findPrefixesForList()
	{
		return $this->finder($this->getClassIdentifier())
			->order(['materialized_order']);
	}

	public function getDefaultDisplayStyles()
	{
		return [
			'label label--hidden',
			'label label--primary',
			'label label--accent',
			'label label--red',
			'label label--green',
			'label label--olive',
			'label label--lightGreen',
			'label label--blue',
			'label label--royalBlue',
			'label label--skyBlue',
			'label label--gray',
			'label label--silver',
			'label label--yellow',
			'label label--orange'
		];
	}

	/**
	 * Rebuilds the 'materialized_order' field in the prefix table,
	 * based on the canonical display_order data in the prefix and prefix_group tables.
	 */
	public function rebuildPrefixMaterializedOrder()
	{
		$prefixes = $this->finder($this->getClassIdentifier())
			->with('PrefixGroup')
			->order([
				'PrefixGroup.display_order',
				'display_order'
			]);

		$db = $this->db();
		$ungroupedPrefixes = [];
		$updates = [];
		$i = 0;

		foreach ($prefixes AS $prefixId => $prefix)
		{
			if ($prefix->prefix_group_id)
			{
				if (++$i != $prefix->materialized_order)
				{
					$updates[$prefixId] = 'WHEN ' . $db->quote($prefixId) . ' THEN ' . $db->quote($i);
				}
			}
			else
			{
				$ungroupedPrefixes[$prefixId] = $prefix;
			}
		}

		foreach ($ungroupedPrefixes AS $prefixId => $prefix)
		{
			if (++$i != $prefix->materialized_order)
			{
				$updates[$prefixId] = 'WHEN ' . $db->quote($prefixId) . ' THEN ' . $db->quote($i);
			}
		}

		if (!empty($updates))
		{
			$structure = $this->em->getEntityStructure($this->getClassIdentifier());
			$table = $structure->table;

			$db->query('
				UPDATE `' . $table . '` SET materialized_order = CASE prefix_id
				' . implode(' ', $updates) . '
				END
				WHERE prefix_id IN(' . $db->quote(array_keys($updates)) . ')
			');
		}
	}

	public function getPrefixCacheData()
	{
		$prefixes = $this->finder($this->getClassIdentifier())
			->order(['materialized_order'])
			->fetch();

		$cache = [];
		foreach ($prefixes AS $prefix)
		{
			$cache[$prefix->prefix_id] = $prefix->css_class;
		}
		return $cache;
	}

	public function rebuildPrefixCache()
	{
		$cache = $this->getPrefixCacheData();
		\XF::registry()->set($this->getRegistryKey(), $cache);
		return $cache;
	}
}