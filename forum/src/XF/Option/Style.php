<?php

namespace XF\Option;

class Style extends AbstractOption
{
	public static function renderRadio(\XF\Entity\Option $option, array $htmlParams)
	{
		/** @var \XF\Repository\Style $styleRepo */
		$styleRepo = \XF::repository('XF:Style');

		$forEmailStyle = ($option->option_id == 'defaultEmailStyleId');

		$choices = [];
		if ($forEmailStyle)
		{
			$choices[0] = \XF::phrase('use_default_style');
		}
		foreach ($styleRepo->getStyleTree(false)->getFlattened() AS $entry)
		{
			if ($entry['record']->user_selectable || $forEmailStyle)
			{
				$choices[$entry['record']->style_id] = $entry['record']->title;
			}
		}

		return self::getRadioRow($option, $htmlParams, $choices);
	}

	protected static $triggered = false;

	/**
	 * This can be used as a verification callback to force a style update (CSS rebuild).
	 *
	 * @param mixed $value
	 * @param \XF\Entity\Option $option
	 * @return bool
	 */
	public static function triggerStyleUpdate(&$value, \XF\Entity\Option $option)
	{
		if ($option->isInsert())
		{
			return true;
		}

		if (!self::$triggered)
		{
			\XF::repository('XF:Style')->updateAllStylesLastModifiedDateLater();
			self::$triggered = true;
		}

		return true;
	}
}