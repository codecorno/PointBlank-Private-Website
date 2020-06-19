<?php

namespace XF\Install\Upgrade;

class Version1000051 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.0.0 Release Candidate 1';
	}

	public function step1()
	{
		// rename and repurpose xf_ban_ip table to xf_ip_match for banning/discourager
		$this->executeUpgradeQuery("
			ALTER TABLE xf_ban_ip
			RENAME xf_ip_match
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_ip_match
			CHANGE banned_ip ip VARCHAR(25) NOT NULL,
			ADD match_type ENUM('banned','discouraged') NOT NULL DEFAULT 'banned' AFTER ip,
			DROP PRIMARY KEY,
			ADD PRIMARY KEY (ip, match_type)
		");

		// add support for long strings to xf_style_property_definition and increase property name length
		$this->executeUpgradeQuery("
			ALTER TABLE xf_style_property_definition
			CHANGE property_name
				property_name VARCHAR(100) NOT NULL,
			CHANGE scalar_type
				scalar_type ENUM('', 'longstring', 'color', 'number', 'boolean', 'template') NOT NULL DEFAULT  ''
		");

		// add description support to xf_style
		$this->executeUpgradeQuery("
			ALTER TABLE xf_style
			ADD description VARCHAR(100) NOT NULL DEFAULT ''
			AFTER title
		");

		// increase character limit for feed URLs to support the max limit (IE supports 2083 chars)
		$this->executeUpgradeQuery("
			ALTER TABLE xf_feed
			CHANGE url
				url VARCHAR(2083) NOT NULL
		");

		return true;
	}
}