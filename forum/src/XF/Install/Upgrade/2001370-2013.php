<?php

namespace XF\Install\Upgrade;

class Version2001370 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.0.13';
	}

	public function step1()
	{
		$db = $this->db();

		$updateTables = [
			'xf_notice' => 'notice_id',
			'xf_trophy' => 'trophy_id',
			'xf_user_group_promotion' => 'promotion_id'
		];
		foreach ($updateTables AS $table => $primaryKey)
		{
			$updateList = $db->fetchAll("
				SELECT *
				FROM `{$table}`
				WHERE user_criteria LIKE '%\_\_userField\_%'
			");
			foreach ($updateList AS $update)
			{
				$userCriteria = @unserialize($update['user_criteria']);

				if (!is_array($userCriteria))
				{
					continue;
				}

				foreach ($userCriteria AS $k => $criterion)
				{
					if (!empty($criterion['rule'])
						&& (strpos($criterion['rule'], '__userField_') === 0)
					)
					{
						$userCriteria[$k]['rule'] = str_replace('__userField_', 'user_field_', $criterion['rule']);
					}
				}

				$db->update($table, [
					'user_criteria' => serialize($userCriteria)
				], "{$primaryKey} = ?", $update[$primaryKey]);
			}
		}
	}
}