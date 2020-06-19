<?php

namespace XF\Install\Upgrade;

class Version1050032 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.5.0 Beta 2';
	}

	public function step1()
	{
		$this->executeUpgradeQuery('
			UPDATE xf_bb_code_media_site
			SET match_urls = REPLACE(match_urls, \'\nyoutube.com/user/*/{$id}\n\', \'\n\')
			WHERE media_site_id = \'youtube\'
		');
	}

	public function step2()
	{
		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_content_type_field
				(content_type, field_name, field_value)
			VALUES
				('profile_post_comment', 'stats_handler_class', 'XenForo_StatsHandler_ProfilePostComment')
		");
	}
}