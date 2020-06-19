<?php

namespace XF\AdminSearch;

class UserField extends AbstractField
{
	protected function getFinderName()
	{
		return 'XF:UserField';
	}

	protected function getRouteName()
	{
		return 'custom-user-fields/edit';
	}

	public function getRelatedPhraseGroups()
	{
		return ['user_field_title', 'user_field_desc'];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('userField');
	}
}