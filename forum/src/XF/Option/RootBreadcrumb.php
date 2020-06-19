<?php

namespace XF\Option;

class RootBreadcrumb extends AbstractOption
{
	public static function renderOptions(\XF\Entity\Option $option, array $htmlParams)
	{
		/** @var \XF\Repository\Navigation $navRepo */
		$navRepo = \XF::repository('XF:Navigation');

		$choices = [
			'' => \XF::phrase('none')
		];
		foreach ($navRepo->getTopLevelEntries() AS $entry)
		{
			if ($entry->navigation_id != \XF::app()->get('defaultNavigationId') && $entry->enabled)
			{
				$choices[$entry->navigation_id] = $entry->title;
			}
		}

		return self::getRadioRow($option, $htmlParams, $choices);
	}
}