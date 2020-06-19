<?php

namespace XF\BbCode\ProcessorAction;

class StripQuotes implements FiltererInterface
{
	public function addFiltererHooks(FiltererHooks $hooks)
	{
		$hooks->addTagHook('quote', 'filterQuoteTag');
	}

	public function filterQuoteTag(array $tag, array $options)
	{
		// remove everything within
		return '';
	}
}