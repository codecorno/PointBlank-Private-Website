<?php

namespace XF\Option;

class UsernameValidation extends AbstractOption
{
	public static function verifyOption(&$value, \XF\Entity\Option $option)
	{
		if ($value['matchRegex'] !== '')
		{
			if (!\XF\Util\Php::isValidRegex($value['matchRegex']))
			{
				$option->error(\XF::phrase('invalid_regular_expression'), $option->option_id);
				return false;
			}
		}

		return true;
	}
}
