<?php

namespace XF\Option;

class EnableTrophies extends AbstractOption
{
	public static function verifyOption(&$value, \XF\Entity\Option $option)
	{
		if ($option->isInsert())
		{
			return true;
		}

		if (!$value)
		{
			$trophyStat = \XF::em()->findOne('XF:MemberStat', ['member_stat_key' => 'most_points']);
			if ($trophyStat)
			{
				$trophyStat->getBehavior('XF:DevOutputWritable')
					->setOption('write_dev_output', false);

				$trophyStat->active = 0;
				$trophyStat->save(false, false);
			}
		}

		return true;
	}
}