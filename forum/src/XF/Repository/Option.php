<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Option extends Repository
{
	/**
	 * @param array $options
	 *
	 * @return Finder
	 */
	public function findOptionGroupList(array $options = [])
	{
		$options = array_merge([
			'with_debug' => \XF::$debugMode,
			'active_only' => true
		], $options);

		$finder = $this->finder('XF:OptionGroup')
			->order(['display_order', 'group_id']);

		if (!$options['with_debug'])
		{
			$finder->where('debug_only', 0);
		}
		if ($options['active_only'])
		{
			$finder->whereAddOnActive();
		}

		return $finder;
	}

	/**
	 * @return Finder
	 */
	public function findAllGroups()
	{
		$finder = $this->finder('XF:OptionGroup')
			->order(['display_order', 'group_id']);

		return $finder;
	}

	/**
	 * @param \XF\Entity\OptionGroup $group
	 * @param array $options
	 *
	 * @return Finder
	 */
	public function findOptionsInGroup(\XF\Entity\OptionGroup $group, array $options = [])
	{
		$finder = $this->finder('XF:Option')
			->with('AddOn')
			->with("Relations|$group->group_id", true)
			->order(["Relations|$group->group_id.display_order", 'option_id']);

		$options = array_merge([
			'active_only' => true
		], $options);

		if ($options['active_only'])
		{
			$finder->whereAddOnActive();
		}

		return $finder;
	}

	/**
	 * @param $addOnId
	 *
	 * @return array
	 */
	public function getGroupsAndOptionsForAddOn($addOnId)
	{
		/** @var \XF\Mvc\Entity\ArrayCollection $relations */
		$relationFinder = $this->finder('XF:OptionGroupRelation')
			->with('Option', true)
			->with('OptionGroup', true)
			->where('Option.addon_id', $addOnId)
			->order(['OptionGroup.display_order', 'display_order', 'Option.option_id']);

		if (!\XF::$debugMode)
		{
			$relationFinder->where('OptionGroup.debug_only', 0);
		}

		$relations = $relationFinder->fetch();

		$options = $relations->pluck(function(\XF\Entity\OptionGroupRelation $optionGroupRelation)
		{
			return [$optionGroupRelation->getIdentifier(), $optionGroupRelation->Option];
		}, true);

		$groups = $relations->pluckNamed('OptionGroup', 'group_id');

		return [$groups, $options];
	}

	public function updateOptions(array $values)
	{
		$options = $this->em->findByIds('XF:Option', array_keys($values));

		$this->em->beginTransaction();

		foreach ($options AS $option)
		{
			$option->option_value = $values[$option->option_id];
			$option->save();
		}

		$this->em->commit();

		return $options;
	}

	public function updateOption($name, $value)
	{
		$option = $this->em->find('XF:Option', $name);
		if (!$option)
		{
			throw new \InvalidArgumentException("Unknown option $name");
		}

		$option->option_value = $value;
		$option->save();

		return true;
	}

	public function updateOptionSkipVerify($name, $value)
	{
		$option = $this->em->find('XF:Option', $name);
		if (!$option)
		{
			throw new \InvalidArgumentException("Unknown option $name");
		}

		$option->setOption('verify_value', false);
		$option->option_value = $value;
		$option->save();

		return true;
	}

	public function getOptionCacheData()
	{
		$options = $this->finder('XF:Option')->fetch();
		$optionArray = [];

		foreach ($options AS $option)
		{
			$optionArray[$option['option_id']] = $option['option_value'];
		}

		return $optionArray;
	}

	public function rebuildOptionCache()
	{
		$cache = $this->getOptionCacheData();
		\XF::registry()->set('options', $cache);

		$this->app()->options = new \ArrayObject($cache, \ArrayObject::ARRAY_AS_PROPS);

		return $cache;
	}

	public function canAddOption()
	{
		return \XF::$developmentMode;
	}
}