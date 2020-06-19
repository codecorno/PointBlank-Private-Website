<?php

namespace XF\Job;

class StylePropertyRebuild extends AbstractJob
{
	protected $defaultData = [];

	public function run($maxRunTime)
	{
		/** @var \XF\Service\StyleProperty\Rebuild $rebuildService */
		$rebuildService = $this->app->service('XF:StyleProperty\Rebuild');

		$rebuildService->rebuildFullPropertyMap();
		$rebuildService->rebuildPropertyStyleCache();

		return $this->complete();
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('rebuilding');
		$typePhrase = \XF::phrase('style_properties');
		return sprintf('%s... %s', $actionPhrase, $typePhrase);
	}

	public function canCancel()
	{
		return false;
	}

	public function canTriggerByChoice()
	{
		return false;
	}
}