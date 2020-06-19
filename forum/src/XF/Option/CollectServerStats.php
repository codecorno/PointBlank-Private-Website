<?php

namespace XF\Option;

class CollectServerStats extends AbstractOption
{
	public static function verifyOption(array &$value, \XF\Entity\Option $option)
	{
		if ($option->isInsert())
		{
			return true;
		}

		if (empty($value['configured']) || empty($value['enabled']))
		{
			\XF::app()->jobManager()->cancelUniqueJob('xfCollectStats');
			return true;
		}

		if (empty($option->option_value['installation_id']))
		{
			// no existing installation_id so generate a new one
			$value['installation_id'] = \XF::generateRandomString(32);
		}
		else
		{
			// we have an existing installation_id so prevent it from being changed (silently)
			if ($value['installation_id'] !== $option->option_value['installation_id'])
			{
				$value['installation_id'] = $option->option_value['installation_id'];
			}
		}

		if ($value['enabled'] !== $option->option_value['enabled'])
		{
			\XF::app()->jobManager()->enqueueUnique('xfCollectStats', 'XF:CollectStats', [], false);
		}

		return true;
	}
}