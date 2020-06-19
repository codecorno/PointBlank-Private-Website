<?php

namespace XF\AdminSearch;

class ThreadField extends AbstractField
{
	protected function getFinderName()
	{
		return 'XF:ThreadField';
	}

	protected function getRouteName()
	{
		return 'custom-thread-fields/edit';
	}

	public function getRelatedPhraseGroups()
	{
		return ['thread_field_title', 'thread_field_desc'];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('thread');
	}
}