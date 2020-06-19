<?php

namespace XF\Option;

class GuestTimeZone extends AbstractOption
{
	public static function renderOption(\XF\Entity\Option $option, array $htmlParams)
	{
		/** @var \XF\Data\TimeZone $tzData */
		$tzData = \XF::app()->data('XF:TimeZone');

		return self::getSelectRow($option, $htmlParams, $tzData->getTimeZoneOptions());
	}
}