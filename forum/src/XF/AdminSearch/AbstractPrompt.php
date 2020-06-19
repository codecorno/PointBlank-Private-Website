<?php

namespace XF\AdminSearch;

abstract class AbstractPrompt extends AbstractPhrased
{
	protected function getContentIdName()
	{
		return 'prompt_id';
	}

	public function getDisplayOrder()
	{
		return 40;
	}
}