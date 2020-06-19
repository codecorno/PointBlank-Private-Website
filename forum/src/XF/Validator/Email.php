<?php

namespace XF\Validator;

class Email extends AbstractValidator
{
	protected $options = [
		'banned' => [],
		'allow_empty' => false,
		'allow_local' => false,
		'check_typos' => false
	];

	public function isValid($value, &$errorKey = null)
	{
		if ($value === '')
		{
			if ($this->options['allow_empty'])
			{
				return true;
			}

			$errorKey = 'empty';
			return false;
		}

		if (!\Swift_Validate::email($value))
		{
			$errorKey = 'invalid';
			return false;
		}

		if (!$this->options['allow_local'])
		{
			list ($local, $domain) = explode('@', $value);

			if (strpos($domain, '.') === false)
			{
				$errorKey = 'local';
				return false;
			}
		}

		if (preg_match('/["\'\s\\\\]/', $value))
		{
			// intentionally reject some odd (though technically valid) emails
			$errorKey = 'invalid';
			return false;
		}

		if ($this->options['banned'])
		{
			/** @var \XF\Repository\Banning $banRepo */
			$banRepo = $this->app->repository('XF:Banning');

			if ($banRepo->isEmailBanned($value, $this->options['banned']))
			{
				$errorKey = 'banned';
				return false;
			}
		}

		if ($this->options['check_typos'])
		{
			// This is a very basic function and is really just trying to catch simple typos.
			// Most significantly, gamil.com since this can trigger an SFS action unexpectedly.
			$matches = [
				'gamil.com',
				'gmial.com',
				'gmail.cmo',
				'gmail.co',
				'gmail.cm',
				'gnail.com',
				'gmai.com',
				'hotnail.com',
				'hotmail.cmo',
				'hotmail.co',
				'hotmail.cm',
				'yahooo.com',
				'yaho.com',
				'yahoo.co',
				'yahoo.cmo',
				'yahoo.cm'
			];

			$regex = implode('|', array_map('preg_quote', $matches));
			if (preg_match('/@(' . $regex . ')$/i', $value))
			{
				$errorKey = 'typo';
				return false;
			}
		}

		return true;
	}
}