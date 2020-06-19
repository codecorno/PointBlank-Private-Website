<?php

namespace XF\Option;

class Language extends AbstractOption
{
	public static function renderRadio(\XF\Entity\Option $option, array $htmlParams)
	{
		/** @var \XF\Repository\Language $languageRepo */
		$languageRepo = \XF::repository('XF:Language');

		$choices = [];
		foreach ($languageRepo->getLanguageTree(false)->getFlattened() AS $entry)
		{
			$choices[$entry['record']->language_id] = $entry['record']->title;
		}

		return self::getRadioRow($option, $htmlParams, $choices);
	}
}