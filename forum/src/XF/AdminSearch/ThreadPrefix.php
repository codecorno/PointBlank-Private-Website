<?php

namespace XF\AdminSearch;

class ThreadPrefix extends AbstractPrefix
{
	protected function getFinderName()
	{
		return 'XF:ThreadPrefix';
	}

	public function getRelatedPhraseGroups()
	{
		return ['thread_prefix'];
	}

	protected function getRouteName()
	{
		return 'thread-prefixes/edit';
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('thread');
	}
}