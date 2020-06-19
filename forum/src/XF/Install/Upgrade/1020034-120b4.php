<?php

namespace XF\Install\Upgrade;

class Version1020034 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.2.0 Beta 4';
	}

	public function step1()
	{
		$db = $this->db();

		$combinationIds = $db->fetchAllColumn("
			SELECT p.permission_combination_id
			FROM xf_permission_combination AS p
			LEFT JOIN (SELECT DISTINCT u.permission_combination_id FROM xf_user AS u) AS up
				ON (p.permission_combination_id = up.permission_combination_id)
			WHERE up.permission_combination_id IS NULL
				AND p.user_group_list <> '1'
				AND p.permission_combination_id <> 1
		");

		if ($combinationIds)
		{
			$combinationCondition = 'permission_combination_id IN (' . $db->quote($combinationIds) . ')';

			$db->delete('xf_permission_combination', $combinationCondition);
			$db->delete('xf_permission_combination_user_group', $combinationCondition);
			$db->delete('xf_permission_cache_content', $combinationCondition);
		}
	}

	public function step2()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE `xf_user_ban` ADD `triggered` TINYINT UNSIGNED NOT NULL DEFAULT '1'
		");
	}

	public function step3()
	{
		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_content_type
				(content_type, addon_id, fields)
			VALUES
				('page',  'XenForo', '')
		");

		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_content_type_field
				(content_type, field_name, field_value)
			VALUES
				('page', 'search_handler_class', 'XenForo_Search_DataHandler_Page')
		");
	}
}