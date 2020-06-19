<?php

namespace XF\Repository;

class UserField extends AbstractField
{
	protected function getRegistryKey()
	{
		return 'userFieldsInfo';
	}

	protected function getClassIdentifier()
	{
		return 'XF:UserField';
	}

	public function getDisplayGroups()
	{
		return [
			'personal' => \XF::phrase('personal_details'),
			'contact' => \XF::phrase('contact_details'),
			'preferences' => \XF::phrase('preferences')
		];
	}

	public function getUserFieldValues($userId)
	{
		$fields = $this->db()->fetchAll('
			SELECT field_value.*, field.field_type
			FROM xf_user_field_value AS field_value
			INNER JOIN xf_user_field AS field ON (field.field_id = field_value.field_id)
			WHERE field_value.user_id = ?
		', $userId);

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

	public function rebuildUserFieldValuesCache($userId)
	{
		$cache = $this->getUserFieldValues($userId);
		
		$this->db()->update('xf_user_profile',
			['custom_fields' => json_encode($cache)],
			'user_id = ?', $userId
		);
	}
}