<?php

namespace XF\Validator;

class Facebook extends AbstractValidator
{
	public function isValid($value, &$errorKey = null)
	{
		if (!preg_match('/^[a-z0-9\.-]+$/i', $value))
		{
			$errorKey = 'please_enter_valid_facebook_username_using_alphanumeric_dot_numbers';
			return false;
		}

		return true;
	}

	public function coerceValue($value)
	{
		if (preg_match('#facebook\.com/(\#!/)?profile\.php\?id=(?P<id>\d+)#i', $value, $match))
		{
			$value = $match['id'];
		}
		else if (preg_match('#facebook\.com/(\#!/)?(?P<id>[a-z0-9\.-]+)#i', $value, $match))
		{
			if (substr($match['id'], -4) != '.php')
			{
				$value = $match['id'];
			}
		}

		return $value;
	}
}