<?php

namespace XF\ControllerPlugin;

use XF\Mvc\Entity\Entity;

class Sort extends AbstractPlugin
{
	public function sortTree(
		\XF\Tree $sortTreeData, $sourceData, $parentColumn = 'parent_id', array $options = []
	)
	{
		$options = array_replace([
			'orderColumn' => 'display_order',
			'jump' => 100,
			'preSaveCallback' => null
		], $options);

		$sortEntry = function($id, $parentId, &$lastOrder, array $children)
			use (&$sortEntry, $parentColumn, &$sortTree, $sourceData, $options)
		{
			if (!isset($sourceData[$id]))
			{
				return;
			}

			$orderColumn = $options['orderColumn'];

			$lastOrder += $options['jump'];

			/** @var Entity $entry */
			$entry = $sourceData[$id];
			$entry->{$parentColumn} = $parentId;
			$entry->{$orderColumn} = $lastOrder;

			if ($options['preSaveCallback'])
			{
				$cb = $options['preSaveCallback'];
				$cb($entry);
			}

			$entry->saveIfChanged();

			if ($children)
			{
				$sortTree($children, $id);
			}
		};

		$sortTree = function(array $children, $parentId) use ($sortEntry)
		{
			$lastOrder = 0;

			foreach ($children AS $id => $subTree)
			{
				$sortEntry($id, $parentId, $lastOrder, $subTree->children());
			}
		};

		$sortTree($sortTreeData->children(), $sortTreeData->getRoot());
	}

	public function buildSortTree(array $sortData, $rootValue = 0, $parentField = 'parent_id')
	{
		$treeData = [];
		foreach ($sortData AS $value)
		{
			if (!is_array($value) || !isset($value['id']))
			{
				continue;
			}

			$treeData[$value['id']] = $value;
		}

		return new \XF\Tree($treeData, $parentField, $rootValue);
	}

	public function sortFlat($sortData, $sourceData, array $options = [])
	{
		$options = array_replace([
			'orderColumn' => 'display_order',
			'jump' => 100,
			'preSaveCallback' => null
		], $options);

		$sortEntry = function($id, &$lastOrder)
		use (&$sortData, $sourceData, $options)
		{
			if (!isset($sourceData[$id]))
			{
				return;
			}

			$orderColumn = $options['orderColumn'];

			$lastOrder += $options['jump'];

			/** @var Entity $entry */
			$entry = $sourceData[$id];
			$entry->{$orderColumn} = $lastOrder;

			if ($options['preSaveCallback'])
			{
				$cb = $options['preSaveCallback'];
				$cb($entry);
			}

			$entry->saveIfChanged();
		};

		$sorter = function($sortData) use ($sortEntry)
		{
			$lastOrder = 0;

			foreach ($sortData AS $data)
			{
				$sortEntry($data['id'], $lastOrder);
			}
		};

		$sorter($sortData);
	}
}