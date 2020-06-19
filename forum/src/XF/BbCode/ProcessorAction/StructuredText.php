<?php

namespace XF\BbCode\ProcessorAction;

class StructuredText implements FiltererInterface
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
		$hooks->addStringHook('structuredToBbCode');
	}

	public function structuredToBbCode($string, array $options)
	{
		if (!empty($options['plain']) || !empty($options['stopAutoLink']))
		{
			return $string;
		}

		// note: assume that the autolinker pick up links
		$string = $this->formatter->convertStructuredTextMentionsToBbCode($string);

		return $string;
	}

	public static function factory(\XF\App $app)
	{
		return new static($app->stringFormatter());
	}
}