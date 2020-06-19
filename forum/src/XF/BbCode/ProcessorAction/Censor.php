<?php

namespace XF\BbCode\ProcessorAction;

use XF\BbCode\Processor;

class Censor implements FiltererInterface
{
	/**
	 * @var \XF\Str\Formatter
	 */
	protected $formatter;

	public function __construct(\XF\Str\Formatter $formatter)
	{
		$this->formatter = $formatter;
	}

	public function addFiltererHooks(FiltererHooks $hooks)
	{
		$hooks->addStringHook('censorText')
			->addTagOptionHook('censorTagOption');
	}

	public function censorText($string, array $options)
	{
		return $this->formatter->censorText($string);
	}

	public function censorTagOption($optionValue, array $tag, array $options)
	{
		if (is_array($optionValue))
		{
			foreach ($optionValue AS &$value)
			{
				$value = $this->formatter->censorText($value);
			}

			return $optionValue;
		}
		else
		{
			return $this->formatter->censorText($optionValue);
		}
	}

	public static function factory(\XF\App $app)
	{
		return new static($app->stringFormatter());
	}
}