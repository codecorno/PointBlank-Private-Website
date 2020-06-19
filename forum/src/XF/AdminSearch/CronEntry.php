<?php

namespace XF\AdminSearch;

class CronEntry extends AbstractPhrased
{
	protected function getFinderName()
	{
		return 'XF:CronEntry';
	}

	protected function getContentIdName()
	{
		return 'entry_id';
	}

	protected function getRouteName()
	{
		return 'cron/edit';
	}

	public function getDisplayOrder()
	{
		return 50;
	}

	public function getRelatedPhraseGroups()
	{
		return ['cron_entry'];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('cron');
	}
}