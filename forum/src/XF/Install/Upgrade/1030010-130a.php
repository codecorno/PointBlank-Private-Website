<?php

namespace XF\Install\Upgrade;

class Version1030010 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.3.0 Alpha';
	}

	public function step1()
	{
		$this->executeUpgradeQuery("
			CREATE TABLE `xf_image_proxy` (
				`image_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`url` text NOT NULL,
				`url_hash` varbinary(32) NOT NULL,
				`file_size` int(10) unsigned NOT NULL DEFAULT '0',
				`file_name` varchar(250) NOT NULL DEFAULT '',
				`mime_type` varchar(100) NOT NULL DEFAULT '',
				`fetch_date` int(10) unsigned NOT NULL DEFAULT '0',
				`first_request_date` int(10) unsigned NOT NULL DEFAULT '0',
				`last_request_date` int(10) unsigned NOT NULL DEFAULT '0',
				`views` int(10) unsigned NOT NULL DEFAULT '0',
				`pruned` int(10) unsigned NOT NULL DEFAULT '0',
				`is_processing` int(10) unsigned NOT NULL DEFAULT '0',
				`failed_date` int(10) unsigned NOT NULL DEFAULT '0',
				`fail_count` smallint(5) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (`image_id`),
				UNIQUE KEY `url_hash` (`url_hash`),
				KEY `pruned_fetch_date` (`pruned`,`fetch_date`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE `xf_link_proxy` (
				`link_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`url` text NOT NULL,
				`url_hash` varbinary(32) NOT NULL,
				`first_request_date` int(10) unsigned NOT NULL DEFAULT '0',
				`last_request_date` int(10) unsigned NOT NULL DEFAULT '0',
				`hits` int(10) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (`link_id`),
				UNIQUE KEY `url_hash` (`url_hash`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_smilie_category (
				smilie_category_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				display_order INT UNSIGNED NOT NULL,
				PRIMARY KEY (smilie_category_id)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_bb_code (
				`bb_code_id` varbinary(25) NOT NULL,
				`bb_code_mode` varchar(25) NOT NULL,
				`has_option` varchar(25) NOT NULL,
				`replace_html` text NOT NULL,
				`replace_html_email` text NOT NULL,
				`replace_text` text NOT NULL,
				`callback_class` varchar(75) NOT NULL DEFAULT '',
				`callback_method` varchar(50) NOT NULL DEFAULT '',
				`option_regex` text NOT NULL,
				`trim_lines_after` tinyint(3) unsigned NOT NULL DEFAULT '0',
				`plain_children` tinyint(3) unsigned NOT NULL DEFAULT '0',
				`disable_smilies` tinyint(3) unsigned NOT NULL DEFAULT '0',
				`disable_nl2br` tinyint(3) unsigned NOT NULL DEFAULT '0',
				`disable_autolink` tinyint(3) unsigned NOT NULL DEFAULT '0',
				`allow_signature` tinyint(3) unsigned NOT NULL DEFAULT '1',
				`editor_icon_url` varchar(200) NOT NULL DEFAULT '',
				`sprite_mode` tinyint(3) unsigned NOT NULL DEFAULT '0',
				`sprite_params` blob NOT NULL,
				`example` text NOT NULL,
				`active` tinyint(3) unsigned NOT NULL DEFAULT '1',
				`addon_id` varbinary(25) NOT NULL DEFAULT '',
				PRIMARY KEY (`bb_code_id`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE `xf_user_change_log` (
			  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) unsigned NOT NULL,
			  `edit_user_id` int(11) unsigned NOT NULL,
			  `edit_date` int(10) unsigned NOT NULL,
			  `field` varchar(100) NOT NULL DEFAULT '',
			  `old_value` text NOT NULL,
			  `new_value` text NOT NULL,
			  PRIMARY KEY (`log_id`),
			  KEY `user_id` (`user_id`),
			  KEY `edit_date` (`edit_date`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_user_change_temp (
				user_change_temp_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`user_id` INT UNSIGNED NOT NULL,
				`change_key` varbinary(50)  NULL,
				`action_type` varbinary(50) NOT NULL,
				`action_modifier` VARBINARY( 255 ) NULL,
				`new_value` mediumblob,
				`old_value` mediumblob,
				`create_date` int(10) unsigned,
				`expiry_date` int(10) unsigned DEFAULT NULL,
				UNIQUE KEY (`user_id`,`change_key`),
				KEY `change_key` (`change_key`),
				KEY `expiry_date` (`expiry_date`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE `xf_spam_trigger_log` (
				`trigger_log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`content_type` varbinary(25) NOT NULL,
				`content_id` int(10) unsigned DEFAULT NULL,
				`log_date` int(10) unsigned NOT NULL,
				`user_id` int(10) unsigned NOT NULL,
				`ip_address` varbinary(16) NOT NULL,
				`result` varbinary(25) NOT NULL,
				`details` mediumblob NOT NULL,
				`request_state` mediumblob NOT NULL,
				PRIMARY KEY (`trigger_log_id`),
				UNIQUE KEY `content_type` (`content_type`,`content_id`),
				KEY `log_date` (`log_date`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		// the table storage is different - just replace it
		$this->executeUpgradeQuery("DROP TABLE xf_registration_spam_cache");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_registration_spam_cache (
				cache_key VARBINARY(128) NOT NULL DEFAULT '',
				result MEDIUMBLOB NOT NULL,
				timeout INT UNSIGNED NOT NULL DEFAULT 0,
				PRIMARY KEY (cache_key),
				KEY timeout (timeout)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_user
				CHANGE user_state user_state ENUM('valid', 'email_confirm', 'email_confirm_edit', 'moderated', 'email_bounce') NOT NULL DEFAULT 'valid'
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_user_field ADD moderator_editable TINYINT UNSIGNED NOT NULL DEFAULT '0'
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_phrase CHANGE title title VARBINARY(100) NOT NULL
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_session_activity
				DROP INDEX view_date,
				ADD INDEX view_date (view_date) USING BTREE
		");

		// smilie table enhancements
		$this->executeUpgradeQuery("
			ALTER TABLE xf_smilie
			  ADD smilie_category_id INT UNSIGNED NOT NULL DEFAULT 0,
			  ADD display_order INT UNSIGNED NOT NULL DEFAULT 1,
			  ADD display_in_editor TINYINT UNSIGNED NOT NULL DEFAULT 1,
			  ADD INDEX display_order (display_order)
		");
		$this->executeUpgradeQuery("UPDATE xf_smilie SET display_order = smilie_id");

		$this->applyGlobalPermission('profilePost', 'comment', 'profilePost', 'post');
		$this->applyGlobalPermission('conversation', 'receive', 'conversation', 'start');
		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_permission_entry
				(user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
			VALUES
				(2, 0, 'conversation', 'receive', 'allow', 0)
		");

		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_content_type_field
				(content_type, field_name, field_value)
			VALUES
				('user', 'report_handler_class', 'XenForo_ReportHandler_User')
		");

		return true;
	}

	public function step2()
	{
		$this->db()->emptyTable('xf_session_activity');
		$this->executeUpgradeQuery("
			ALTER TABLE xf_session_activity
				CHANGE unique_key unique_key_old INT UNSIGNED NOT NULL,
				ADD unique_key VARBINARY(16) NOT NULL,
				CHANGE ip ip_old INT UNSIGNED NOT NULL DEFAULT 0,
				ADD ip VARBINARY(16) NOT NULL DEFAULT ''
		");
		$this->executeUpgradeQuery("
			ALTER TABLE xf_session_activity
				DROP PRIMARY KEY,
				ADD PRIMARY KEY (user_id, unique_key),
				DROP unique_key_old,
				DROP ip_old
		");
	}

	public function step3()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_admin_log
				CHANGE ip_address ip_address_old INT UNSIGNED NOT NULL DEFAULT 0,
				ADD ip_address VARBINARY(16) NOT NULL DEFAULT ''
		");
		$this->executeUpgradeQuery("
			UPDATE xf_admin_log SET ip_address = UNHEX(LPAD(HEX(ip_address_old), 8, '0'))
		");
		$this->executeUpgradeQuery("
			ALTER TABLE xf_admin_log DROP ip_address_old
		");
	}

	public function step4()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_moderator_log
				CHANGE ip_address ip_address_old INT UNSIGNED NOT NULL DEFAULT 0,
				ADD ip_address VARBINARY(16) NOT NULL DEFAULT ''
		");
		$this->executeUpgradeQuery("
			UPDATE xf_moderator_log SET ip_address = UNHEX(LPAD(HEX(ip_address_old), 8, '0'))
		");
		$this->executeUpgradeQuery("
			ALTER TABLE xf_moderator_log DROP ip_address_old
		");
	}

	public function step5()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_error_log
				CHANGE ip_address ip_address_old INT UNSIGNED NOT NULL DEFAULT 0,
				ADD ip_address VARBINARY(16) NOT NULL DEFAULT ''
		");
		$this->executeUpgradeQuery("
			UPDATE xf_error_log SET ip_address = UNHEX(LPAD(HEX(ip_address_old), 8, '0'))
		");
		$this->executeUpgradeQuery("
			ALTER TABLE xf_error_log DROP ip_address_old
		");
	}

	public function step6()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_login_attempt
				CHANGE ip_address ip_address_old INT UNSIGNED NOT NULL,
				ADD ip_address VARBINARY(16) NOT NULL
		");
		$this->executeUpgradeQuery("
			UPDATE xf_login_attempt SET ip_address = UNHEX(LPAD(HEX(ip_address_old), 8, '0'))
		");
		$this->executeUpgradeQuery("
			ALTER TABLE xf_login_attempt
				DROP ip_address_old,
				ADD attempt_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				DROP KEY login_check,
				ADD KEY login_check (login, ip_address, attempt_date)
		");
	}

	public function step7()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_ip_match
				CHANGE ip ip VARCHAR(43) NOT NULL,
				ADD first_byte BINARY(1) NOT NULL,
				CHANGE start_range start_range_old INT UNSIGNED NOT NULL,
				ADD start_range VARBINARY(16) NOT NULL,
				CHANGE end_range end_range_old INT UNSIGNED NOT NULL,
				ADD end_range VARBINARY(16) NOT NULL
		");
		$this->executeUpgradeQuery("
			UPDATE xf_ip_match SET
				first_byte = UNHEX(LPAD(HEX(first_octet), 2, '0')),
				start_range = UNHEX(LPAD(HEX(start_range_old), 8, '0')),
				end_range = UNHEX(LPAD(HEX(end_range_old), 8, '0'))
		");
		$this->executeUpgradeQuery("
			ALTER TABLE xf_ip_match
				DROP first_octet,
				DROP start_range_old,
				DROP end_range_old,
				DROP KEY start_range,
				ADD KEY start_range (start_range)
		");

		try
		{
			\XF::registry()->delete('bannedIps');
			\XF::registry()->delete('discouragedIps');
		}
		catch (\Exception $e) {}
	}

	public function step8()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_ip
				CHANGE ip ip_old INT UNSIGNED NOT NULL,
				ADD ip VARBINARY(16) NOT NULL
		");
	}

	public function step9()
	{
		$this->executeUpgradeQuery("
			UPDATE xf_ip SET ip = UNHEX(LPAD(HEX(ip_old), 8, '0'))
		");
	}

	public function step10()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_ip
				DROP ip_old,
				DROP KEY ip_log_date,
				ADD KEY ip_log_date (ip, log_date)
		");
	}

	public function step11()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE `xf_warning_action`
				CHANGE `action` `action` VARBINARY( 25 ) NOT NULL ,
				CHANGE `ban_length_type` `action_length_type` VARBINARY( 25 ) NOT NULL ,
				CHANGE `ban_length` `action_length` SMALLINT( 5 ) UNSIGNED NOT NULL
		");
		$this->executeUpgradeQuery("
			ALTER TABLE  `xf_warning_action_trigger` CHANGE  `action`  `action` VARBINARY( 25 ) NOT NULL
		");
	}

	public function step12()
	{
		$this->executeUpgradeQuery("
			UPDATE xf_warning_action_trigger
			SET action = 'ban'
			WHERE action = 'ban_points'
		");
		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_user_change_temp
				(user_id, change_key, action_type, action_modifier, new_value, old_value, create_date, expiry_date)
			SELECT user_id, CONCAT('warning_action_', warning_action_id, '_discourage'),
				'field', 'is_discouraged', '1', '0', action_date, NULL
			FROM xf_warning_action_trigger
			WHERE action = 'discourage'
		");
		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_user_change_temp
				(user_id, change_key, action_type, action_modifier, new_value, old_value, create_date, expiry_date)
			SELECT user_id, CONCAT('warning_action_', warning_action_id, '_groups'),
				'groups', CONCAT('warning_action_', warning_action_id), '', '', action_date, NULL
			FROM xf_warning_action_trigger
			WHERE action = 'groups'
		");

		$this->executeUpgradeQuery("
			UPDATE xf_warning_action
			SET action_length_type = 'points', action_length = 0
			WHERE action IN ('ban_points', 'discourage', 'groups')
		");
		$this->executeUpgradeQuery("
			UPDATE xf_warning_action
			SET action = 'ban'
			WHERE action IN ('ban_length', 'ban_points')
		");
	}

	public function step13()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_user_profile
				DROP facebook_auth_id,
				ADD external_auth MEDIUMBLOB NOT NULL
		");
	}

	public function step14($position, array $stepData)
	{
		$perPage = 250;
		$db = $this->db();

		if (!isset($stepData['max']))
		{
			$stepData['max'] = $db->fetchOne('SELECT MAX(user_id) FROM xf_user_external_auth');
		}

		$userIds = $db->fetchAllColumn($db->limit(
			'
				SELECT DISTINCT user_id
				FROM xf_user_external_auth AS user
				WHERE user_id > ?
				ORDER BY user_id
			', $perPage
		), $position);
		if (!$userIds)
		{
			return true;
		}

		$queryResults = $db->query('
			SELECT *
			FROM xf_user_external_auth
			WHERE user_id IN (' . $db->quote($userIds) . ')
			ORDER BY user_id, provider
		');
		$authGrouped = [];
		while ($result = $queryResults->fetch())
		{
			$authGrouped[$result['user_id']][$result['provider']] = $result['provider_key'];
		}

		$db->beginTransaction();

		foreach ($authGrouped AS $userId => $cache)
		{
			$db->query('
				UPDATE xf_user_profile SET
					external_auth = ?
				WHERE user_id = ?
			', [serialize($cache), $userId]);
		}

		$db->commit();

		$nextPosition = end($userIds);

		return [
			$nextPosition,
			"$nextPosition / $stepData[max]",
			$stepData
		];
	}

	public function step15()
	{
		$db = $this->db();

		$values = $db->fetchOne("SELECT option_value FROM xf_option WHERE option_id = 'censorWords'");
		$values = @unserialize($values);

		$output = [];

		if ($values && is_array($values))
		{
			$oldFormat = false;

			if (!empty($values['exact']))
			{
				$oldFormat = true;

				foreach ($values['exact'] AS $word => $replace)
				{
					$cache = $this->buildCensorCacheValue(
						$word, is_int($replace) ? '' : $replace
					);
					if ($cache)
					{
						$output[] = $cache;
					}
				}
			}
			if (!empty($values['any']))
			{
				$oldFormat = true;

				foreach ($values['any'] AS $word => $replace)
				{
					$word = '*' . $word . '*';
					$cache = $this->buildCensorCacheValue(
						$word, is_int($replace) ? '' : $replace
					);
					if ($cache)
					{
						$output[] = $cache;
					}
				}
			}

			if (!$oldFormat)
			{
				// likely already converted
				$output = $values;
			}
		}

		$db->query("
			UPDATE xf_option
			SET option_value = ?
			WHERE option_id = 'censorWords'
		", [serialize($output)]);
	}

	protected function buildCensorCacheValue($find, $replace)
	{
		$find = trim(strval($find));
		if ($find === '')
		{
			return false;
		}

		$prefixWildCard = preg_match('#^\*#', $find);
		$suffixWildCard = preg_match('#\*$#', $find);

		$replace = is_int($replace) ? '' : trim(strval($replace));
		if ($replace === '')
		{
			$replace = utf8_strlen($find);
			if ($prefixWildCard)
			{
				$replace--;
			}
			if ($suffixWildCard)
			{
				$replace--;
			}
		}

		$regexFind = $find;
		if ($prefixWildCard)
		{
			$regexFind = substr($regexFind, 1);
		}
		if ($suffixWildCard)
		{
			$regexFind = substr($regexFind, 0, -1);
		}

		if (!strlen($regexFind))
		{
			return false;
		}

		$regex = '#'
			. ($prefixWildCard ? '' : '(?<=\W|^)')
			. preg_quote($regexFind, '#')
			. ($suffixWildCard ? '' : '(?=\W|$)')
			. '#iu';

		return [
			'word' => $find,
			'regex' => $regex,
			'replace' => $replace
		];
	}

	public function step16()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_conversation_message
				ADD INDEX user_id (user_id),
				ADD INDEX message_date (message_date)
		");
	}

	public function step17()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_conversation_recipient
				ADD INDEX user_id (user_id)
		");
	}

	public function step18()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_thread
				ADD INDEX user_id (user_id),
				ADD INDEX post_date (post_date)
		");
	}

	public function step19()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_post
				ADD INDEX post_date (post_date)
		");
	}

	public function step20()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_attachment_data
				ADD INDEX upload_date (upload_date)
		");
	}

	public function step21()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_liked_content
				ADD INDEX like_date (like_date)
		");
	}

	public function step22()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_profile_post
				ADD INDEX post_date (post_date)
		");
	}

	public function step23()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_profile_post_comment
				ADD INDEX user_id (user_id),
				ADD INDEX comment_date (comment_date)
		");
	}

	public function step24()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_poll_vote
				ADD INDEX user_id (user_id)
		");
	}

	public function step25()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_report_comment
				ADD INDEX user_id (user_id)
		");
	}

	public function step26()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_notice_dismissed
				ADD INDEX user_id (user_id)
		");
	}

	public function step27()
	{
		// note: metacafe and liveleak do not support HTTPS at this time
		$this->executeUpgradeQuery("
			UPDATE xf_bb_code_media_site
			SET embed_html = REPLACE(embed_html, 'http:', 'https:')
			WHERE media_site_id IN ('facebook', 'vimeo', 'youtube', 'dailymotion')
		");

		$oldRegex = '#metacafe\\\\.com/watch/(?P' . '<id>\\\\d+)/#siU';
		$newRegex = '#metacafe\\\\.com/watch/(?P' . '<id>[a-z0-9-]+)(/|$)#siU';

		$this->executeUpgradeQuery("
			UPDATE xf_bb_code_media_site
			SET match_urls = IF(match_urls = ?, ?, match_urls),
				embed_html = REPLACE(embed_html, '{\$id:digits}', '{\$id}')
			WHERE media_site_id = 'metacafe'
		", [$oldRegex, $newRegex]);
	}
}