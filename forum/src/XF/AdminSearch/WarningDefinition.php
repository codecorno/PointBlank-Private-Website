<?php

namespace XF\AdminSearch;

class WarningDefinition extends AbstractPhrased
{
	protected function getFinderName()
	{
		return 'XF:WarningDefinition';
	}

	protected function getContentIdName()
	{
		return 'warning_definition_id';
	}

	protected function getRouteName()
	{
		return 'warnings/edit';
	}

	public function getDisplayOrder()
	{
		return 50;
	}

	public function getRelatedPhraseGroups()
	{
		return ['warning_title'];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('warning');
	}
}