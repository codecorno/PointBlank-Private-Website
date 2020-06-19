<?php

namespace XF\Install\Upgrade;

class Version1010034 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.1.0 Beta 4';
	}

	public function step1()
	{
		// add prefix id to feed table
		$this->executeUpgradeQuery("
			ALTER TABLE xf_feed
				ADD prefix_id INT UNSIGNED NOT NULL DEFAULT 0
		");

		// add index to node table
		$this->executeUpgradeQuery("
			ALTER TABLE xf_node
				ADD INDEX display_in_list (display_in_list, lft)
		");

		// add ip logging to conversation messages
		$this->executeUpgradeQuery("
			ALTER TABLE xf_conversation_message
				ADD ip_id INT UNSIGNED NOT NULL DEFAULT 0
		");

		// add ip logging to profile post comments
		$this->executeUpgradeQuery("
			ALTER TABLE xf_profile_post_comment
				ADD ip_id INT UNSIGNED NOT NULL DEFAULT 0
		");

		// add new YouTube match URL
		$this->executeUpgradeQuery('
			UPDATE xf_bb_code_media_site
			SET match_urls = CONCAT(match_urls, \'\nyoutube.com/watch?feature=player_embedded&v={$id}\')
			WHERE media_site_id = \'youtube\'
		');

		return true;
	}
}