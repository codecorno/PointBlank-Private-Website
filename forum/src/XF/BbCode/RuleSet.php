<?php

namespace XF\BbCode;

class RuleSet
{
	const OPTION_KEYS_NO = 0;
	const OPTION_KEYS_ONLY = 1;
	const OPTION_KEYS_BOTH = 2;

	protected $tags = [];

	protected $context = null;
	protected $subContext = null;

	public function __construct($context, $subContext = null, $addDefault = true)
	{
		$this->context = $context;
		$this->subContext = $subContext;

		if ($addDefault)
		{
			$this->addDefaultTags();
		}
	}

	public function getContext()
	{
		return $this->context;
	}

	public function getSubContext()
	{
		return $this->subContext;
	}

	public function addDefaultTags()
	{
		$this->addTag('b', ['hasOption' => false]);
		$this->addTag('i', ['hasOption' => false]);
		$this->addTag('u', ['hasOption' => false]);
		$this->addTag('s', ['hasOption' => false]);

		$this->addTag('color', [
			'hasOption' => true,
			'optionMatch' => '/^(rgb\(\s*\d+%?\s*,\s*\d+%?\s*,\s*\d+%?\s*\)|#[a-f0-9]{6}|#[a-f0-9]{3}|[a-z]+)$/i'
		]);

		$this->addTag('font', [
			'hasOption' => true,
			'optionMatch' => '/^[a-z0-9 \-]+$/i' // regex matched to HTML->BB code regex
		]);

		$this->addTag('size', [
			'hasOption' => true,
			'optionMatch' => '/^[0-9]+(px)?$/i'
		]);

		$this->addTag('url', [
			'supportOptionKeys' => RuleSet::OPTION_KEYS_BOTH,
			'parseValidate' => [$this, 'parseValidateLink'],
			'stopAutoLink' => true
		]);
		$this->addTag('email', [
			'parseValidate' => [$this, 'parseValidateLink'],
			'stopAutoLink' => true
		]);

		$this->addTag('left', [
			'hasOption' => false
		]);
		$this->addTag('center', [
			'hasOption' => false
		]);
		$this->addTag('right', [
			'hasOption' => false
		]);

		$this->addTag('indent', [
			'optionMatch' => '/^[0-9]+$/'
		]);

		$this->addTag('img', [
			'supportOptionKeys' => RuleSet::OPTION_KEYS_ONLY,
			'plain' => true,
			'stopSmilies' => true,
			'stopAutoLink' => true
		]);

		$this->addTag('quote', []);

		$this->addTag('code', [
			'parseValidate' => [$this, 'parseValidateCode'],
			'stopSmilies' => true,
			'stopAutoLink' => true
		]);

		$this->addTag('icode', [
			'parseValidate' => [$this, 'parseValidateCode'],
			'stopSmilies' => true,
			'stopAutoLink' => true
		]);

		$this->addTag('php', [
			'hasOption' => false,
			'plain' => true,
			'stopSmilies' => true,
			'stopAutoLink' => true
		]);

		$this->addTag('html', [
			'hasOption' => false,
			'plain' => true,
			'stopSmilies' => true,
			'stopAutoLink' => true
		]);

		$this->addTag('list', []);

		$this->addTag('plain', [
			'hasOption' => false,
			'plain' => true,
			'stopSmilies' => true,
			'stopAutoLink' => true
		]);

		$this->addTag('media', [
			'hasOption' => true,
			'plain' => true,
			'stopSmilies' => true,
			'stopAutoLink' => true
		]);

		$this->addTag('spoiler', []);

		$this->addTag('ispoiler', []);

		$this->addTag('attach', [
			'supportOptionKeys' => RuleSet::OPTION_KEYS_BOTH,
			'plain' => true,
			'stopSmilies' => true,
			'stopAutoLink' => true
		]);

		$this->addTag('user', [
			'hasOption' => true,
			'plain' => true,
			'stopSmilies' => true,
			'stopAutoLink' => true
		]);

		$this->addTag('table', []);
		$this->addTag('tr', [
			'validParents' => ['table']
		]);
		$this->addTag('th', [
			'validParents' => ['tr']
		]);
		$this->addTag('td', [
			'validParents' => ['tr']
		]);
	}

	public function addTag($tag, array $config)
	{
		$this->tags[$tag] = $config;
	}

	public function modifyTag($tag, array $modification)
	{
		if (isset($this->tags[$tag]))
		{
			$this->tags[$tag] = array_merge($this->tags[$tag], $modification);
		}
	}

	public function removeTag($tag)
	{
		unset($this->tags[$tag]);
	}

	public function getTag($tag)
	{
		return isset($this->tags[$tag]) ? $this->tags[$tag] : null;
	}

	public function getTags()
	{
		return $this->tags;
	}

	public function getCustomTagConfig(array $tag)
	{
		$output = [];

		if ($tag['has_option'] == 'yes')
		{
			$output['hasOption'] = true;
		}
		else if ($tag['has_option'] == 'no')
		{
			$output['hasOption'] = false;
		}

		if (strlen($tag['option_regex']))
		{
			$output['optionMatch'] = $tag['option_regex'];
		}

		if ($tag['disable_smilies'])
		{
			$output['stopSmilies'] = true;
		}

		if ($tag['disable_autolink'] || $tag['plain_children'])
		{
			$output['stopAutoLink'] = true;
		}

		if ($tag['plain_children'])
		{
			$output['plain'] = true;
		}

		return $output;
	}

	public function validateTag($tag, $option = null, &$parsingModifiers = [], array $tagStack = [])
	{
		$parsingModifiers = [];

		$definition = $this->getTag($tag);
		if (!is_array($definition))
		{
			return false;
		}

		if (!empty($definition['validParents']))
		{
			if (!$tagStack)
			{
				return false;
			}

			$lastTag = $tagStack[0];
			if (!in_array($lastTag['tag'], $definition['validParents']))
			{
				return false;
			}
		}

		if (!$this->validateOption($option, $definition))
		{
			return false;
		}

		if ($option !== null && !empty($definition['optionMatch']) && is_scalar($option))
		{
			if (!preg_match($definition['optionMatch'], $option))
			{
				return false;
			}
		}

		$parsingModifiers = $definition;

		if (isset($definition['parseValidate']))
		{
			$validated = call_user_func($definition['parseValidate'], $tag, $option, $tagStack);
			if ($validated === false)
			{
				return false;
			}

			if (is_array($validated))
			{
				$parsingModifiers = array_merge($parsingModifiers, $validated);
			}
		}

		return true;
	}

	public function validateOption($option, array $definition)
	{
		if (isset($definition['hasOption']))
		{
			if (!$definition['hasOption'])
			{
				// no option permitted
				return $option === null ? true : false;
			}
			else if ($option === null)
			{
				// option required but not set
				return false;
			}
			else
			{
				return $this->validateOptionKeys($option, $definition);
			}
		}
		else if ($option !== null)
		{
			return $this->validateOptionKeys($option, $definition);
		}
		else
		{
			return true;
		}
	}

	public function validateOptionKeys($option, array $definition)
	{
		if (isset($definition['supportOptionKeys']))
		{
			switch ($definition['supportOptionKeys'])
			{
				case RuleSet::OPTION_KEYS_BOTH:
					return is_array($option) || is_scalar($option);

				case RuleSet::OPTION_KEYS_ONLY:
					return is_array($option);

				case RuleSet::OPTION_KEYS_NO:
				default:
					return is_scalar($option);
			}
		}
		else
		{
			return is_scalar($option);
		}
	}

	public function parsePlainNoOption($tag, $option)
	{
		if ($option === null)
		{
			return ['plain' => true];
		}

		return true;
	}

	public function parseValidateLink($tag, $option, array $tagStack)
	{
		foreach ($tagStack AS $tagAbove)
		{
			switch ($tagAbove['tag'])
			{
				case 'url':
				case 'email':
					// can't nest these
					return false;
			}
		}

		return self::parsePlainNoOption($tag, $option);
	}

	public function parseValidateCode($tag, $option)
	{
		if ($option && strtolower($option) == 'rich')
		{
			return true;
		}

		return ['plain' => true];
	}
}