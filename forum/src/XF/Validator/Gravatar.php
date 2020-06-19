<?php

namespace XF\Validator;

class Gravatar extends AbstractValidator
{
	protected $options = [
		'allow_empty' => true
	];

	public function isValid($value, &$errorKey = null)
	{
		if ($this->options['allow_empty'] && $value === '')
		{
			return true;
		}

		if (!$this->app->isValid('Email', $value))
		{
			$errorKey = 'invalid_email';
			return false;
		}

		try
		{
			$md5 = md5(strtolower(trim($value)));
			$url = "https://secure.gravatar.com/avatar/{$md5}?d=404";

			$response = $this->app->http()->client()->head($url, ['exceptions' => false]);
			if ($response->getStatusCode() !== 200)
			{
				$errorKey = 'not_found';
				return false;
			}
		}
		catch (\Exception $e)
		{
			if ($e instanceof \GuzzleHttp\Exception\RequestException)
			{
				if (strpos($e->getMessage(), 'Read timed out') === false)
				{
					\XF::logException($e, false);
				}
				$errorKey = 'communication_error';
				return false;
			}
			else
			{
				throw $e;
			}
		}

		return true;
	}

	public function getPrintableErrorValue($errorKey)
	{
		switch ($errorKey)
		{
			case 'invalid_email':
				return \XF::phrase('gravatars_require_valid_email_addresses');

			case 'not_found':
				return \XF::phrase('no_gravatar_found_for_specified_email_address');

			case 'communication_error':
				return \XF::phrase('there_was_problem_communicating_with_gravatar');

			default:
				return null;
		}
	}
}