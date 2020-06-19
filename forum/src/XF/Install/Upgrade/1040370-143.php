<?php

namespace XF\Install\Upgrade;

class Version1040370 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.4.3';
	}

	public function step1()
	{
		$this->executeUpgradeQuery('
			UPDATE xf_bb_code_media_site
			SET embed_html = \'<div class="fb-post" data-href="https://www.facebook.com/video.php?v={$id}" data-width="500"><div class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/video.php?v={$id}">https://www.facebook.com/video.php?v={$id}</a></div></div>\'
			WHERE media_site_id = \'facebook\'
				AND embed_html LIKE \'%<iframe%\'
		');

		$this->executeUpgradeQuery("
			DELETE FROM xf_data_registry
			WHERE data_key IN ('trophyUserTitles', 'routeFilters')
		");
	}

	public function step2()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_stats_daily
			CHANGE counter counter BIGINT UNSIGNED NOT NULL
		");
	}
}