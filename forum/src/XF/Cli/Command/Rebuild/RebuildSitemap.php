<?php

namespace XF\Cli\Command\Rebuild;

class RebuildSitemap extends AbstractRebuildCommand
{
	protected function getRebuildName()
	{
		return 'sitemap';
	}

	protected function getRebuildDescription()
	{
		return 'Rebuilds XML sitemap.';
	}

	protected function getRebuildClass()
	{
		return 'XF:Sitemap';
	}
}