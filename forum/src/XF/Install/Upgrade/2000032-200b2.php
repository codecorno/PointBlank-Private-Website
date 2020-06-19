<?php

namespace XF\Install\Upgrade;

use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Version2000032 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.0.0 Beta 2';
	}

	public function step1()
	{
		$sm = $this->schemaManager();

		$sm->alterTable('xf_job', function(Alter $alter)
		{
			$alter->addColumn('last_run_date', 'int')->nullable();
		});

		$this->executeUpgradeQuery("
			UPDATE xf_permission_entry_content
			SET permission_value = 'content_allow'
			WHERE permission_group_id = 'forum'
				AND permission_id = 'inlineMod'
		");
		$this->executeUpgradeQuery("
			DELETE FROM xf_permission_entry_content
			WHERE permission_value = ''
		");
	}

	public function step2($position, array $stepData)
	{
		// after this, we will have orphaned combinations -- these will get cleaned up later

		$db = $this->db();
		$maxRunTime = \XF::config('jobMaxRunTime');
		$s = microtime(true);

		$db->query("
			SET @@session.group_concat_max_len = 102400
		");

		$matches = $db->fetchAll("
			SELECT user_id, user_group_list, COUNT(*) AS total,
				GROUP_CONCAT(permission_combination_id ORDER BY permission_combination_id) AS ids
			FROM xf_permission_combination 
			GROUP BY user_id, user_group_list 
			HAVING total > 1
		");
		if (!$matches)
		{
			return true;
		}

		$completed = true;

		foreach ($matches AS $match)
		{
			$ids = explode(',', $match['ids']);

			$oldest = array_shift($ids);
			if (!$ids)
			{
				// shouldn't happen, but just in case
				continue;
			}

			$ids = array_map('intval', $ids);

			$db->update('xf_user',
				['permission_combination_id' => $oldest],
				'permission_combination_id IN (' . $db->quote($ids) . ')'
			);
			$db->delete('xf_permission_combination', 'permission_combination_id IN (' . $db->quote($ids) . ')');

			if ($maxRunTime && (microtime(true) - $s) > $maxRunTime)
			{
				$completed = false;
				break;
			}
		}

		if ($completed)
		{
			return true;
		}

		$position++;

		return [
			$position,
			str_repeat(',', $position),
			$stepData
		];
	}
}