<?php

namespace XF\Validator;

class Twitter extends AbstractValidator
{
	public function isValid($value, &$errorKey = null)
	{
		if (!preg_match('/^[a-z0-9_]+$/i', $value))
		{
			$errorKey = 'please_enter_valid_twitter_name_using_alphanumeric';
			return false;
		}

		return true;
	}

	public function coerceValue($value)
	{
		if (is_string($value) && $value && $value[0] == '@')
		{
			$value = substr($value, 1);
		}
		else if (preg_match('#twitter\.com/(?P<id>[a-z0-9\_.]+)$#i', $value, $match))
		{
			$value = $match['id'];
		}

		return $value;
	}
}