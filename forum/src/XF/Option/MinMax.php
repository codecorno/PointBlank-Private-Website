<?php

namespace XF\Option;

class MinMax extends AbstractOption
{
	public static function verifyOption(array &$value, \XF\Entity\Option $option)
	{
		if (isset($value['min']) && isset($value['max']))
		{
			$value['min'] = intval($value['min']);
			$value['max'] = intval($value['max']);

			if ($value['max'] < $value['min'])
			{
				$swap = $value['min'];
				$value['min'] = $value['max'];
				$value['max'] = $swap;
			}
		}

		return true;
	}
}
