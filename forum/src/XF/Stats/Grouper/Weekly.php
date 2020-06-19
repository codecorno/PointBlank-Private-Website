<?php

namespace XF\Stats\Grouper;

class Weekly extends AbstractGrouper
{
	public function getGrouping($timestamp)
	{
		return gmdate('o-W', $timestamp);
	}

	public function getLabel($groupValue, $timestamp)
	{
		list($year, $week) = explode('-', $groupValue);
		return "{$year}W{$week}";
	}

	public function getDefaultStartDate()
	{
		return strtotime('-1 year');
	}
}