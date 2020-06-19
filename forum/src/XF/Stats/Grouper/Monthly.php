<?php

namespace XF\Stats\Grouper;

class Monthly extends AbstractGrouper
{
	public function getGrouping($timestamp)
	{
		return gmdate('Y-m', $timestamp);
	}

	public function getLabel($groupValue, $timestamp)
	{
		return $this->language->date($timestamp, 'M Y');
	}

	public function getDefaultStartDate()
	{
		return strtotime('-2 years');
	}
}