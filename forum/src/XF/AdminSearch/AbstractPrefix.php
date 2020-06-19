<?php

namespace XF\AdminSearch;

abstract class AbstractPrefix extends AbstractPhrased
{
	protected function getContentIdName()
	{
		return 'prefix_id';
	}

	public function getDisplayOrder()
	{
		return 40;
	}
}