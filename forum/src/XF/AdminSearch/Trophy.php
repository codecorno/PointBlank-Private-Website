<?php

namespace XF\AdminSearch;

class Trophy extends AbstractPhrased
{
	protected function getFinderName()
	{
		return 'XF:Trophy';
	}

	protected function getContentIdName()
	{
		return 'trophy_id';
	}

	protected function getRouteName()
	{
		return 'trophies/edit';
	}

	public function getDisplayOrder()
	{
		return 50;
	}

	public function getRelatedPhraseGroups()
	{
		return ['trophy_title', 'trophy_desc'];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('trophy');
	}
}