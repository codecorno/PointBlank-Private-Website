<?php

namespace XF\BbCode\ProcessorAction;

use XF\BbCode\Processor;

class AnalyzeUsage implements AnalyzerInterface
{
	/**
	 * @var \XF\Str\Formatter
	 */
	protected $formatter;

	protected $tagCount = [];
	protected $smilieCount = 0;
	protected $printableLength = 0;

	protected $attachments = [];
	protected $quotes = [];
	protected $unfurls = [];

	public function __construct(\XF\Str\Formatter $formatter)
	{
		$this->formatter = $formatter;
	}

	public function addAnalysisHooks(AnalyzerHooks $hooks)
	{
		$hooks->addSetupHook('initialize')
			->addGlobalTagHook('analyzeTagUsage')
			->addTagHook('quote', 'analyzeQuoteTag')
			->addTagHook('attach', 'analyzeAttachTag')
			->addStringHook('analyzeString')
			->addFinalHook('analyzeUnfurlUsage');
	}

	public function getTagCount($tag)
	{
		return isset($this->tagCount[$tag]) ? $this->tagCount[$tag] : 0;
	}

	public function getTotalTagCount()
	{
		return array_sum($this->tagCount);
	}

	public function getSmilieCount()
	{
		return $this->smilieCount;
	}

	public function getAttachments()
	{
		return $this->attachments;
	}

	public function getQuotes()
	{
		return $this->quotes;
	}

	public function getUnfurls()
	{
		return $this->unfurls;
	}

	public function getPrintableLength()
	{
		return $this->printableLength;
	}

	public function initialize()
	{
		$this->tagCount = [];
		$this->smiliesCount = 0;
		$this->printableLength = 0;
		$this->attachments = [];
		$this->quotes = [];
		$this->unfurls = [];
	}

	public function analyzeTagUsage(array $tag, array $options)
	{
		$this->incrementTagCount($tag['tag']);
	}

	public function analyzeString($string, array $options)
	{
		$this->printableLength += utf8_strlen($string);

		if (empty($options['stopSmilies']))
		{
			$this->formatter->replaceSmiliesInText($string, function()
			{
				$this->smilieCount++;
				return '';
			});
		}
	}

	public function analyzeUnfurlUsage($string, Processor $processor)
	{
		if (preg_match_all('#^\[URL\s+unfurl="true"\](.*)\[/URL]$#ium', $string, $matches, PREG_SET_ORDER))
		{
			/** @var \XF\Repository\Unfurl $unfurlRepo */
			$unfurlRepo = \XF::repository('XF:Unfurl');

			foreach ($matches AS $match)
			{
				$unfurl = $unfurlRepo->getUnfurlResultByUrl($match[1]);
				if ($unfurl)
				{
					$this->unfurls[$unfurl->result_id] = $unfurl->result_id;
				}
			}
		}
	}

	public function analyzeAttachTag(array $tag, array $options, $finalOutput, Processor $processor)
	{
		if (!$finalOutput)
		{
			// was stripped
			return;
		}

		$id = intval($processor->renderSubTreePlain($tag['children']));
		if ($id)
		{
			$this->attachments[$id] = $id;
		}
	}

	public function analyzeQuoteTag(array $tag, array $options, $finalOutput)
	{
		if (!$finalOutput)
		{
			// was stripped
			return;
		}

		if (!empty($tag['option']))
		{
			$optionParts = explode(',', $tag['option']);
			$attributes = [];
			foreach ($optionParts AS $part)
			{
				$pair = explode(':', trim($part), 2);
				if (isset($pair[1]))
				{
					$attributes[trim($pair[0])] = trim($pair[1]);
				}
				else
				{
					$attributes['source'] = $pair[0];
				}
			}

			if ($attributes)
			{
				$this->quotes[] = $attributes;
			}
		}
	}

	public function incrementTagCount($tag)
	{
		$this->adjustTagCount($tag, 1);
	}

	public function adjustTagCount($tag, $adjust)
	{
		if (!isset($this->tagCount[$tag]))
		{
			$this->tagCount[$tag] = 0;
		}

		$this->tagCount[$tag] += $adjust;
		if ($this->tagCount[$tag] <= 0)
		{
			unset($this->tagCount[$tag]);
		}
	}

	public static function factory(\XF\App $app)
	{
		return new static($app->stringFormatter());
	}
}