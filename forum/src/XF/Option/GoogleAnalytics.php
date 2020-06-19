<?php

namespace XF\Option;

class GoogleAnalytics extends AbstractOption
{
	public static function verifyWebPropertyOption(&$wpId, \XF\Entity\Option $option)
	{
		if ($wpId !== '' && !preg_match('/^UA-\d+-\d+$/', $wpId))
		{
			$option->error(\XF::phrase('please_enter_your_google_analytics_web_property_id_in_format'), $option->option_id);
			return false;
		}

		return true;
	}
}