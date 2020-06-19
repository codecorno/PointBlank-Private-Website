<?php

namespace XF\Install\Upgrade;

class Version1040770 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.4.7';
	}

	public function step1()
	{
		$this->executeUpgradeQuery('
			UPDATE xf_bb_code_media_site SET
				match_urls = CONCAT(match_urls, \'\nfacebook.com/*/videos/{$id:digits}\')
			WHERE media_site_id = \'facebook\'
				AND match_is_regex = 0
		');
	}
}