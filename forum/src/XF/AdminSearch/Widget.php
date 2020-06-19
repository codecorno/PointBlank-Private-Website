<?php

namespace XF\AdminSearch;

class Widget extends AbstractPhrased
{
	protected function getFinderName()
	{
		return 'XF:Widget';
	}

	protected function getContentIdName()
	{
		return 'widget_key';
	}

	protected function getRouteName()
	{
		return 'widgets/edit';
	}

	public function getDisplayOrder()
	{
		return 50;
	}

	public function getRelatedPhraseGroups()
	{
		return ['widget'];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('widget');
	}
}