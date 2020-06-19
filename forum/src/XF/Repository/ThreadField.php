<?php

namespace XF\Repository;

class ThreadField extends AbstractField
{
	protected function getRegistryKey()
	{
		return 'threadFieldsInfo';
	}

	protected function getClassIdentifier()
	{
		return 'XF:ThreadField';
	}

	public function getDisplayGroups()
	{
		return [
			'before' => \XF::phrase('before_message'),
			'after' => \XF::phrase('after_message'),
			'thread_status' => \XF::phrase('thread_status_block')
		];
	}

	public function getThreadFieldValues($threadId)
	{
		$fields = $this->db()->fetchAll('
			SELECT field_value.*, field.field_type
			FROM xf_thread_field_value AS field_value
			INNER JOIN xf_thread_field AS field ON (field.field_id = field_value.field_id)
			WHERE field_value.thread_id = ?
		', $threadId);

		$values = [];
		foreach ($fields AS $field)
		{
			if ($field['field_type'] == 'checkbox' || $field['field_type'] == 'multiselect')
			{
				$values[$field['field_id']] = \XF\Util\Php::safeUnserialize($field['field_value']);
			}
			else
			{
				$values[$field['field_id']] = $field['field_value'];
			}
		}
		return $values;
	}

	public function rebuildThreadFieldValuesCache($threadId)
	{
		$cache = $this->getThreadFieldValues($threadId);

		$this->db()->update('xf_thread',
			['custom_fields' => json_encode($cache)],
			'thread_id = ?', $threadId
		);
	}
}