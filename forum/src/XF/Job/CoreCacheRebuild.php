<?php

namespace XF\Job;

class CoreCacheRebuild extends AbstractJob
{
	protected $defaultData = [
	];

	public function run($maxRunTime)
	{
		\XF::repository('XF:Option')->updateOption('jsLastUpdate', \XF::$time);

		$this->app->get('addon.dataManager')->rebuildActiveAddOnCache();
		\XF::repository('XF:Banning')->rebuildBannedEmailCache();
		\XF::repository('XF:Banning')->rebuildBannedIpCache();
		\XF::repository('XF:Banning')->rebuildDiscouragedIpCache();
		\XF::repository('XF:BbCode')->rebuildBbCodeCache();
		\XF::repository('XF:BbCodeMediaSite')->rebuildBbCodeMediaSiteCache();
		\XF::repository('XF:ConnectedAccount')->rebuildProviderCount();
		\XF::repository('XF:ClassExtension')->rebuildExtensionCache();
		\XF::repository('XF:CodeEventListener')->rebuildListenerCache();
		\XF::repository('XF:Editor')->rebuildEditorDropdownCache();
		\XF::repository('XF:Navigation')->rebuildNavigationCache();
		\XF::repository('XF:NodeType')->rebuildNodeTypeCache();
		\XF::repository('XF:Notice')->rebuildNoticeCache();
		\XF::repository('XF:Option')->rebuildOptionCache();
		\XF::repository('XF:Reaction')->rebuildReactionCache();
		\XF::repository('XF:Reaction')->rebuildReactionSpriteCache();
		\XF::repository('XF:RouteFilter')->rebuildRouteFilterCache();
		\XF::repository('XF:Smilie')->rebuildSmilieCache();
		\XF::repository('XF:Smilie')->rebuildSmilieSpriteCache();
		\XF::repository('XF:Style')->updateAllStylesLastModifiedDateLater();
		\XF::repository('XF:ThreadField')->rebuildFieldCache();
		\XF::repository('XF:ThreadPrefix')->rebuildPrefixCache();
		\XF::repository('XF:UserField')->rebuildFieldCache();
		\XF::repository('XF:UserGroup')->rebuildDisplayStyleCache();
		\XF::repository('XF:UserGroup')->rebuildUserBannerCache();
		\XF::repository('XF:UserTitleLadder')->rebuildLadderCache();
		\XF::repository('XF:Widget')->rebuildWidgetCache();
		\XF::repository('XF:Widget')->recompileWidgets();

		return $this->complete();
	}

	public function getStatusMessage()
	{
		$actionPhrase = \XF::phrase('rebuilding');
		$typePhrase = \XF::phrase('core_caches');
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