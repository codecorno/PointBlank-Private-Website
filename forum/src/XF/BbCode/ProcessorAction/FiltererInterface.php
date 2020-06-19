<?php

namespace XF\BbCode\ProcessorAction;

interface FiltererInterface
{
	public function addFiltererHooks(FiltererHooks $hooks);
}