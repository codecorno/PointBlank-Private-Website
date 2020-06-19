<?php

namespace XF\BbCode\ProcessorAction;

interface ProcessorAwareInterface
{
	public function setProcessor(\XF\BbCode\Processor $processor);
}