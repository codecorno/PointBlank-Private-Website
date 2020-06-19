<?php

namespace XF\Install\Upgrade;

use XF\Db\Schema\Alter;

class Version2010035 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.1.0 Beta 5';
	}

	public function step1()
	{
		// all the relevant caches will be updated later
		$this->executeUpgradeQuery("
			UPDATE xf_widget
			SET positions = REPLACE(positions, '\"find_threads_sidebar\"', '\"find_threads_sidenav\"')
		");
	}

	public function step2()
	{
		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_phrase
				(language_id, title, phrase_text, global_cache, addon_id, version_id, version_string)
			SELECT 0, CONCAT('reaction_title.', r.reaction_id), r.title, 0, '', 0, ''
			FROM xf_reaction AS r
			ORDER BY reaction_id
		");

		$this->alterTable('xf_reaction', function(Alter $alter)
		{
			$alter->dropColumns('title');
		});
	}
}