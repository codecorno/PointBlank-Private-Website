<?php

namespace XF\Validator;

class Url extends AbstractValidator
{
	protected $options = [
		'allowed_schemes' => ['http', 'https'],
		'require_authority' => true,
		'allow_empty' => false
	];

	public function isValid($value, &$errorKey = null)
	{
		if ($this->options['allow_empty'] && $value === '')
		{
			return true;
		}

		if (!preg_match('#
			^
			(?P<scheme>[a-z][a-z0-9+.-]*)
			:
			(?P<authority>//
				(?:
					(?P<userinfo>[a-z0-9\-._~!$&\'()*+,;=:%]+)
					@
				)?
				(?P<host>[a-z0-9\-.]+)
				(?:
					:
					(?P<port>[0-9]+)
				)?
			)?
			(?P<path>
				/[^?\#]*
			)?
			(?:
				\?
				(?P<query>[^\#]*)
			)?
			(?:
				\#
				(?P<fragment>.*)
			)?
			$
		#ix', $value, $match))
		{
			$errorKey = 'invalid';
			return false;
		}

		$scheme = strtolower($match['scheme']);
		if ($this->options['allowed_schemes'] && !in_array($scheme, $this->options['allowed_schemes']))
		{
			$errorKey = 'disallowed_scheme';
			return false;
		}

		if ($this->options['require_authority'] && empty($match['authority']))
		{
			$errorKey = 'no_authority';
			return false;
		}

		return true;
	}

	public function coerceValue($value)
	{
		if ($value === 'http://')
		{
			$value = '';
		}
		else if (substr(strtolower($value), 0, 4) == 'www.')
		{
			$value = 'http://' . $value;
		}

		return $value;
	}
}