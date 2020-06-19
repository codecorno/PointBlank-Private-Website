<?php

namespace XF\Validator;

class Username extends AbstractValidator
{
	protected $options = [
		'allow_empty' => false,
		'allow_censored' => false,
		'check_unique' => true,
		'disallowed_contain' => [],
		'length_min' => 0,
		'length_max' => 0,
		'self_user_id' => null,
		'regex_match' => null
	];

	public function isValid($value, &$errorKey = null)
	{
		$username = $value;

		$usernameLength = utf8_strlen($username);
		if (!$usernameLength && !$this->getOption('allow_empty'))
		{
			$errorKey = 'empty';
			return false;
		}

		$minLength = $this->getOption('length_min');
		if ($minLength > 0 && $usernameLength < $minLength)
		{
			$errorKey = 'too_short';
			return false;
		}

		$maxLength = $this->getOption('length_max');
		if ($maxLength > 0 && $usernameLength > $maxLength)
		{
			$errorKey = 'too_long';
			return false;
		}

		$disallowedNames = $this->getOption('disallowed_contain');
		if ($disallowedNames)
		{
			foreach ($disallowedNames AS $name)
			{
				$name = trim($name);
				if ($name === '')
				{
					continue;
				}
				if (stripos($username, $name) !== false)
				{
					$errorKey = 'disallowed';
					return false;
				}
			}
		}

		$matchRegex = $this->getOption('regex_match');
		if ($matchRegex && \XF\Util\Php::isValidRegex($matchRegex))
		{
			if (!preg_match($matchRegex, $username))
			{
				$errorKey = 'regex';
				return false;
			}
		}

		if (!$this->getOption('allow_censored'))
		{
			$censoredUserName = $this->app->stringFormatter()->censorText($username);
			if ($censoredUserName !== $username)
			{
				$errorKey = 'censored';
				return false;
			}
		}

		if (strpos($username, ',') !== false)
		{
			$errorKey = 'comma';
			return false;
		}

		if ($this->app->isValid('Email', $username))
		{
			$errorKey = 'email';
			return false;
		}

		if ($this->getOption('check_unique'))
		{
			$existingUser = $this->app->em()->findOne('XF:User', ['username' => $username]);
			$selfUserId = $this->getOption('self_user_id');
			if ($existingUser && (!$selfUserId || $existingUser->user_id != $selfUserId))
			{
				$errorKey = 'duplicate';
				return false;
			}
		}

		return true;
	}

	public function setupOptionDefaults()
	{
		$options = $this->app->options();

		$this->options['length_min'] = $options->usernameLength['min'];
		$this->options['length_max'] = $options->usernameLength['max'];
		$this->options['disallowed_contain'] = preg_split('/\r?\n/', $options->usernameValidation['disallowedNames']);
		$this->options['regex_match'] = $options->usernameValidation['matchRegex'];
	}

	public function setOption($key, $value)
	{
		if ($key == 'admin_edit')
		{
			if (!$value)
			{
				throw new \LogicException("Admin_edit can only be enabled");
			}

			$this->setOption('allow_censored', true);
			$this->setOption('disallowed_contain', []);
			$this->setOption('length_min', 0);
			$this->setOption('length_max', 0);
			$this->setOption('regex_match', null);
		}
		else
		{
			parent::setOption($key, $value);
		}
	}

	public function coerceValue($value)
	{
		$username = $value;

		try
		{
			if (@preg_match('/\p{C}/u', $username))
			{
				$username = preg_replace('/\p{C}/u', '', $username);
			}
		}
		catch (\Exception $e) {}

		// standardize white space in names
		try
		{
			// if this matches, then \v isn't known (appears to be PCRE < 7.2) so don't strip
			if (!preg_match('/\v/', 'v'))
			{
				$newName = preg_replace('/\v+/u', ' ', $username);
				if (is_string($newName))
				{
					$username = $newName;
				}
			}
		}
		catch (\Exception $e) {}

		$username = preg_replace('/\s+/u', ' ', $username);
		$username = trim($username);

		return $username;
	}

	public function getPrintableErrorValue($errorKey)
	{
		switch ($errorKey)
		{
			case 'empty':
				return \XF::phrase('please_enter_valid_name');

			case 'too_short':
				return \XF::phrase('please_enter_name_that_is_at_least_x_characters_long', ['count' => $this->getOption('length_min')]);

			case 'too_long':
				return \XF::phrase('please_enter_name_that_is_at_most_x_characters_long', ['count' => $this->getOption('length_max')]);

			case 'disallowed':
				return \XF::phrase('please_enter_another_name_disallowed_words');

			case 'regex':
				return \XF::phrase('please_enter_another_name_required_format');

			case 'censored':
				return \XF::phrase('please_enter_name_that_does_not_contain_any_censored_words');

			case 'comma':
				return \XF::phrase('please_enter_name_that_does_not_contain_comma');

			case 'email':
				return \XF::phrase('please_enter_name_that_does_not_resemble_an_email_address');

			case 'duplicate':
				return \XF::phrase('usernames_must_be_unique');

			default: return null;
		}
	}
}