<?php

namespace XF\Install\Upgrade;

class Version2010036 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.1.0 Beta 6';
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
				$userCriteria = @json_decode($update['user_criteria'], true);

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
					'user_criteria' => json_encode($userCriteria)
				], "{$primaryKey} = ?", $update[$primaryKey]);
			}
		}
	}
}