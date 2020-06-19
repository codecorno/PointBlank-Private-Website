<?php

namespace XF\AdminSearch;

class MemberStat extends AbstractPhrased
{
	protected function getFinderName()
	{
		return 'XF:MemberStat';
	}

	protected function getContentIdName()
	{
		return 'member_stat_key';
	}

	protected function getRouteName()
	{
		return 'member-stats/edit';
	}

	public function getDisplayOrder()
	{
		return 50;
	}

	public function getRelatedPhraseGroups()
	{
		return ['member_stat'];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('user');
	}
}