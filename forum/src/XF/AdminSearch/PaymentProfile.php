<?php

namespace XF\AdminSearch;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Router;

class PaymentProfile extends AbstractFieldSearch
{
	protected $searchFields = ['title', 'display_title', 'provider_id'];

	public function getDisplayOrder()
	{
		return 45;
	}

	protected function getFinderName()
	{
		return 'XF:PaymentProfile';
	}

	protected function getContentIdName()
	{
		return 'profile_id';
	}

	protected function getRouteName()
	{
		return 'payment-profiles/edit';
	}

	protected function getTemplateParams(Router $router, Entity $record, array $templateParams)
	{
		return $templateParams + ['extra' => $record->provider_id];
	}

	public function isSearchable()
	{
		return \XF::visitor()->hasAdminPermission('payment');
	}
}