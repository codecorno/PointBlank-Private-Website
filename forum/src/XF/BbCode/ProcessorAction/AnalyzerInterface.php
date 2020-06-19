<?php

namespace XF\BbCode\ProcessorAction;

interface AnalyzerInterface
{
	public function addAnalysisHooks(AnalyzerHooks $hooks);
}