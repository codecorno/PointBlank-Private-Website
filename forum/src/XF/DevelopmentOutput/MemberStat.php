<?php

namespace XF\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class MemberStat extends AbstractHandler
{
	protected function getTypeDir()
	{
		return 'member_stats';
	}

	public function export(Entity $memberStat)
	{
		if (!$this->isRelevant($memberStat))
		{
			return true;
		}

		$fileName = $this->getFileName($memberStat);

		$keys = [
			'criteria',
			'sort_order',
			'sort_direction',
			'permission_limit',
			'callback_class',
			'callback_method',
			'show_value',
			'overview_display',
			'active',
			'user_limit',
			'display_order',
			'cache_lifetime'
		];
		$json = $this->pullEntityKeys($memberStat, $keys);

		return $this->developmentOutput->writeFile($this->getTypeDir(), $memberStat->addon_id, $fileName, Json::jsonEncodePretty($json));
	}

	protected function getEntityForImport($name, $addOnId, $json, array $options)
	{
		/** @var \XF\Entity\MemberStat $memberStat */
		$memberStat = \XF::finder('XF:MemberStat')->where('member_stat_key', $name)->fetchOne();
		if (!$memberStat)
		{
			$memberStat = \XF::em()->create('XF:MemberStat');
		}

		$memberStat = $this->prepareEntityForImport($memberStat, $options);

		return $memberStat;
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$memberStat = $this->getEntityForImport($name, $addOnId, $json, $options);

		if ($memberStat->exists())
		{
			// persist the active state in case it is already set elsewhere
			$json['active'] = $memberStat->active;
		}

		$memberStat->bulkSetIgnore($json);
		$memberStat->member_stat_key = $name;
		$memberStat->addon_id = $addOnId;
		$memberStat->save();
		// this will update the metadata itself

		return $memberStat;
	}

	protected function getFileName(Entity $memberStat, $new = true)
	{
		$id = $new ? $memberStat->getValue('member_stat_key') : $memberStat->getExistingValue('member_stat_key');
		return "{$id}.json";
	}
}