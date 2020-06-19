<?php

namespace XF\Install\Upgrade;

use XF\Install\Data\MySql;

class Version1040010 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.4.0 Alpha';
	}

	public function step1()
	{
		$this->executeUpgradeQuery("
			CREATE TABLE xf_email_bounce_log (
				`bounce_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`log_date` int(10) unsigned NOT NULL,
				`email_date` int(10) unsigned NOT NULL,
				`message_type` varchar(25) NOT NULL,
				`action_taken` varchar(25) NOT NULL,
				`user_id` int(10) unsigned DEFAULT NULL,
				`recipient` varchar(255) DEFAULT NULL,
				`raw_message` mediumblob NOT NULL,
				`status_code` varchar(25) DEFAULT NULL,
				`diagnostic_info` text,
				PRIMARY KEY (`bounce_id`),
				KEY `log_date` (`log_date`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_email_bounce_soft (
				`bounce_soft_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`user_id` int(10) unsigned NOT NULL,
				`bounce_date` date NOT NULL,
				`bounce_total` smallint(5) unsigned NOT NULL,
				PRIMARY KEY (`bounce_soft_id`),
				UNIQUE KEY `user_id` (`user_id`,`bounce_date`),
				KEY `bounce_date` (`bounce_date`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_help_page (
				`page_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`page_name` varchar(50) NOT NULL,
				`display_order` int(10) unsigned NOT NULL DEFAULT '0',
				`callback_class` varchar(75) NOT NULL DEFAULT '',
				`callback_method` varchar(75) NOT NULL DEFAULT '',
				PRIMARY KEY (`page_id`),
				UNIQUE KEY `page_name` (`page_name`),
				KEY `display_order` (`display_order`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_sitemap (
				`sitemap_id` int(10) unsigned NOT NULL,
				`is_active` tinyint(3) unsigned NOT NULL,
				`file_count` smallint(5) unsigned NOT NULL,
				`entry_count` INT UNSIGNED NOT NULL DEFAULT '0',
				`is_compressed` tinyint(3) unsigned NOT NULL,
				`complete_date` int(10) unsigned DEFAULT NULL,
				PRIMARY KEY (`sitemap_id`),
				KEY `is_active` (`is_active`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_thread_reply_ban (
				`thread_reply_ban_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`thread_id` int(10) unsigned NOT NULL,
				`user_id` int(10) unsigned NOT NULL,
				`ban_date` int(10) unsigned NOT NULL,
				`expiry_date` int(10) unsigned DEFAULT NULL,
				`reason` varchar(100) NOT NULL DEFAULT '',
				`ban_user_id` int(10) unsigned NOT NULL,
				PRIMARY KEY (`thread_reply_ban_id`),
				UNIQUE KEY `thread_id` (`thread_id`,`user_id`),
				KEY `expiry_date` (`expiry_date`),
				KEY `user_id` (`user_id`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		$this->applyGlobalPermission('general', 'viewMemberList', 'general', 'view');
		$this->applyGlobalPermission('forum', 'threadReplyBan', 'forum', 'deleteAnyPost');
		$this->applyContentPermission('forum', 'threadReplyBan', 'forum', 'deleteAnyPost');

		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_admin_permission_entry
				(user_id, admin_permission_id)
			SELECT user_id, 'help'
			FROM xf_admin_permission_entry
			WHERE admin_permission_id = 'notice'
		");

		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_content_type_field
				(content_type, field_name, field_value)
			VALUES
				('node', 'sitemap_handler_class', 'XenForo_SitemapHandler_Node'),
				('thread', 'sitemap_handler_class', 'XenForo_SitemapHandler_Thread'),
				('user', 'sitemap_handler_class', 'XenForo_SitemapHandler_User'),

				('thread', 'alert_handler_class', 'XenForo_AlertHandler_Thread')
		");

		$regDefault = @unserialize($this->db()->fetchOne("
			SELECT option_value
			FROM xf_option
			WHERE option_id = 'registrationDefaults'
		"));
		if ($regDefault)
		{
			$regDefault['activity_visible'] = !empty($regDefault['visible']) ? '1' : '0';
			$this->db()->update('xf_option', [
				'option_value' => serialize($regDefault)
			], "option_id = 'registrationDefaults'");
		}
	}

	public function step2()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_bb_code ADD allow_empty TINYINT UNSIGNED NOT NULL DEFAULT '0'
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_forum
				CHANGE moderate_messages moderate_replies TINYINT UNSIGNED NOT NULL DEFAULT 0,
				ADD moderate_threads TINYINT UNSIGNED NOT NULL DEFAULT 0,
				ADD allow_poll TINYINT UNSIGNED NOT NULL DEFAULT 1,
				ADD list_date_limit_days SMALLINT UNSIGNED NOT NULL DEFAULT 0
		");
		$this->executeUpgradeQuery("
			UPDATE xf_forum SET moderate_threads = 1 WHERE moderate_replies = 1
		");

		$this->executeUpgradeQuery("RENAME TABLE xf_trophy_user_title TO xf_user_title_ladder");
		$this->executeUpgradeQuery("
			ALTER TABLE xf_user_title_ladder
				CHANGE minimum_points minimum_level INT UNSIGNED NOT NULL
		");
	}

	public function step3()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_poll ADD change_vote TINYINT UNSIGNED NOT NULL DEFAULT 0,
				ADD view_results_unvoted TINYINT UNSIGNED NOT NULL DEFAULT 1,
				ADD max_votes TINYINT UNSIGNED NOT NULL DEFAULT 1
		");
		$this->executeUpgradeQuery("
			UPDATE xf_poll SET max_votes = 0 WHERE multiple = 1
		");
		$this->executeUpgradeQuery("
			ALTER TABLE xf_poll DROP multiple
		");
	}

	public function step4()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_user ADD activity_visible TINYINT UNSIGNED NOT NULL DEFAULT 1
		");
	}

	public function step5()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_user_profile ADD password_date INT UNSIGNED NOT NULL DEFAULT 1
		");
	}
}