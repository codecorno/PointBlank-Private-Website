<?php

namespace XF\Stats\Grouper;

class Daily extends AbstractGrouper
{
	public function getGrouping($timestamp)
	{
		return gmdate('Y-m-d', $timestamp);
	}

	public function getLabel($groupValue, $timestamp)
	{
		return $this->language->date($timestamp);
	}

	public function getDefaultStartDate()
	{
		return strtotime('-1 month');
	}
}