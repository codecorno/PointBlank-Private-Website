<?php

namespace XF\Option;

class AdsDisallowedTemplates extends AbstractOption
{
	public static function verifyOption(&$value, \XF\Entity\Option $option)
	{
		if ($option->isInsert())
		{
			return true;
		}

		/** @var \XF\Repository\Advertising $repo */
		$repo = \XF::repository('XF:Advertising');
		$repo->writeAdsTemplate($value ?: false);

		return true;
	}
}