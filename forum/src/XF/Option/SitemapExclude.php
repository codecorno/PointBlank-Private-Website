<?php

namespace XF\Option;

class SitemapExclude extends AbstractOption
{
	public static function renderCheckbox(\XF\Entity\Option $option, array $htmlParams)
	{
		/** @var \XF\Repository\SitemapLog $sitemapRepo */
		$sitemapRepo = \XF::repository('XF:SitemapLog');
		$sitemapHandlers = $sitemapRepo->getSitemapHandlers();

		$value = [];
		$choices = [];
		foreach ($sitemapHandlers AS $type => $sitemapHandler)
		{
			if (empty($option->option_value[$type]))
			{
				$value[] = $type;
			}
			$choices[$type] = \XF::app()->getContentTypePhrase($type);
		}

		return self::getCheckboxRow($option, $htmlParams, $choices, $value);
	}

	public static function verifyOption(array &$choices, \XF\Entity\Option $option)
	{
		if ($option->isInsert())
		{
			// insert - just trust the default value
			return true;
		}

		$exclusions = [];

		/** @var \XF\Repository\SitemapLog $sitemapRepo */
		$sitemapRepo = \XF::repository('XF:SitemapLog');
		$sitemapHandlers = $sitemapRepo->getSitemapHandlers();

		foreach ($sitemapHandlers AS $type => $sitemapHandler)
		{
			if (!in_array($type, $choices))
			{
				$exclusions[$type] = true;
			}
		}

		$choices = $exclusions;

		return true;
	}
}