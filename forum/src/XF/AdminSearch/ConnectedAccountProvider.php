<?php

namespace XF\AdminSearch;

class ConnectedAccountProvider extends AbstractPhrased
{
	protected function getFinderName()
	{
		return 'XF:ConnectedAccountProvider';
	}

	protected function getContentIdName()
	{
		return 'provider_id';
	}

	protected function getRouteName()
	{
		return 'connected-accounts/edit';
	}

	public function getDisplayOrder()
	{
		return 50;
	}

	public function getRelatedPhraseGroups()
	{
		return ['con_acc', 'con_acc_desc'];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('user');
	}
}