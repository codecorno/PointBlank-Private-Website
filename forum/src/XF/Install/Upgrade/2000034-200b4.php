<?php

namespace XF\Install\Upgrade;

use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Version2000034 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.0.0 Beta 4';
	}

	public function step1()
	{
		$sm = $this->schemaManager();

		$sm->alterTable('xf_member_stat', function(Alter $alter)
		{
			$alter->changeColumn('sort_order', 'varchar', 50)->setDefault('message_count');
		});
	}

	public function step2()
	{
		$sm = $this->schemaManager();

		$sm->alterTable('xf_thread_field', function(Alter $alter)
		{
			$alter->addColumn('editable_user_group_ids', 'blob');
		});

		$db = $this->db();
		$db->beginTransaction();

		$fields = $db->fetchAll("
			SELECT *
			FROM xf_thread_field
		");
		foreach ($fields AS $field)
		{
			if (!isset($field['user_editable']))
			{
				continue;
			}

			if ($field['user_editable'])
			{
				$update = '-1'; // not a perfect conversion
			}
			else if ($field['moderator_editable'])
			{
				$update = '4';
			}
			else
			{
				$update = '';
			}

			$db->update('xf_thread_field',
				['editable_user_group_ids' => $update],
				'field_id = ?',
				$field['field_id']
			);
		}

		$db->commit();

		$sm->alterTable('xf_thread_field', function(Alter $alter)
		{
			$alter->dropColumns(['user_editable', 'moderator_editable']);
		});
	}
}