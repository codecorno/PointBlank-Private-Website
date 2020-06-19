<?php

namespace XF\Install\Upgrade;

class Version1050010 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.5.0 Alpha';
	}

	public function step1()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_notice
			ADD display_image ENUM('', 'avatar', 'image') NOT NULL DEFAULT '',
			ADD image_url VARCHAR(200) NOT NULL DEFAULT '',
			ADD visibility ENUM('','wide','medium','narrow') NOT NULL DEFAULT '',
			ADD notice_type ENUM('block', 'floating') NOT NULL DEFAULT 'block',
			ADD display_style ENUM('', 'primary', 'secondary', 'dark', 'light', 'custom') NOT NULL DEFAULT '',
			ADD css_class VARCHAR(50) NOT NULL DEFAULT '',
			ADD display_duration INT(10) UNSIGNED NOT NULL DEFAULT 0,
			ADD delay_duration INT(10) UNSIGNED NOT NULL DEFAULT 0,
			ADD auto_dismiss TINYINT(3) UNSIGNED NOT NULL DEFAULT 0
		");
	}

	public function step2()
	{
		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_content_type
				(content_type, addon_id, fields)
			VALUES
				('profile_post_comment', 'XenForo', ''),
				('tag', 'XenForo', '')
		");

		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_content_type_field
				(content_type, field_name, field_value)
			VALUES
				('profile_post_comment', 'alert_handler_class', 'XenForo_AlertHandler_ProfilePostComment'),
				('profile_post_comment', 'like_handler_class', 'XenForo_LikeHandler_ProfilePostComment'),
				('profile_post_comment', 'moderation_queue_handler_class', 'XenForo_ModerationQueueHandler_ProfilePostComment'),
				('profile_post_comment', 'moderator_log_handler_class', 'XenForo_ModeratorLogHandler_ProfilePostComment'),
				('profile_post_comment', 'news_feed_handler_class', 'XenForo_NewsFeedHandler_ProfilePostComment'),
				('profile_post_comment', 'report_handler_class', 'XenForo_ReportHandler_ProfilePostComment'),
				('profile_post_comment', 'search_handler_class', 'XenForo_Search_DataHandler_ProfilePostComment'),
				('profile_post_comment', 'spam_handler_class', 'XenForo_SpamHandler_ProfilePostComment'),
				('profile_post_comment', 'warning_handler_class', 'XenForo_WarningHandler_ProfilePostComment'),

				('tag', 'sitemap_handler_class', 'XenForo_SitemapHandler_Tag'),

				('thread', 'tag_handler_class', 'XenForo_TagHandler_Thread')
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_profile_post_comment
			ADD message_state ENUM('visible', 'moderated', 'deleted') NOT NULL DEFAULT 'visible',
			ADD likes INT UNSIGNED NOT NULL DEFAULT 0,
			ADD like_users BLOB NOT NULL,
			ADD warning_id INT UNSIGNED NOT NULL DEFAULT 0,
			ADD warning_message VARCHAR(255) NOT NULL DEFAULT ''
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_profile_post
			CHANGE COLUMN latest_comment_ids latest_comment_ids BLOB NOT NULL
		");
	}

	public function step3()
	{
		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_content_type_field
				(content_type, field_name, field_value)
			VALUES
				('user', 'moderator_log_handler_class', 'XenForo_ModeratorLogHandler_User')
		");
	}

	public function step4()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_admin
			ADD admin_language_id INT(10) UNSIGNED NOT NULL DEFAULT 0
		");
	}

	public function step5()
	{
		$this->executeUpgradeQuery("
			CREATE TABLE xf_tfa_attempt (
				`attempt_id` int(11) NOT NULL AUTO_INCREMENT,
				`user_id` int(10) unsigned NOT NULL,
				`attempt_date` int(10) unsigned NOT NULL,
				PRIMARY KEY (`attempt_id`),
				KEY `attempt_date` (`attempt_date`),
				KEY `user_id_attempt_date` (`user_id`,`attempt_date`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		$this->executeUpgradeQuery("
			CREATE TABLE xf_tfa_provider (
				`provider_id` varbinary(25) NOT NULL,
				`provider_class` varchar(75) NOT NULL,
				`priority` smallint(5) unsigned NOT NULL,
				`active` tinyint(3) unsigned NOT NULL,
				PRIMARY KEY (`provider_id`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		$this->executeUpgradeQuery("
			CREATE TABLE xf_user_tfa (
				`user_tfa_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`user_id` int(10) unsigned NOT NULL,
				`provider_id` varbinary(25) NOT NULL,
				`provider_data` mediumblob NOT NULL,
				`last_used_date` int(10) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (`user_tfa_id`),
				UNIQUE KEY `user_id` (`user_id`,`provider_id`)
			) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		$this->executeUpgradeQuery("
			CREATE TABLE xf_user_tfa_trusted (
				`tfa_trusted_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`user_id` int(10) unsigned NOT NULL,
				`trusted_key` varbinary(32) NOT NULL,
				`trusted_until` int(10) unsigned NOT NULL,
				PRIMARY KEY (`tfa_trusted_id`),
				UNIQUE KEY `user_id` (`user_id`,`trusted_key`),
				KEY `trusted_until` (`trusted_until`)
			) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		$this->executeUpgradeQuery("
			INSERT IGNORE INTO `xf_tfa_provider`
				(`provider_id`, `provider_class`, `priority`, `active`)
			VALUES
				('backup', 'XenForo_Tfa_Backup', 1000, 1),
				('email', 'XenForo_Tfa_Email', 900, 1),
				('totp', 'XenForo_Tfa_Totp', 10, 1)
		");
	}

	public function step6()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE `xf_user_option` ADD `use_tfa` TINYINT UNSIGNED NOT NULL DEFAULT '0'
		");
	}

	public function step7()
	{
		$this->executeUpgradeQuery("
			CREATE TABLE `xf_image_proxy_referrer` (
				`referrer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`image_id` int(10) unsigned NOT NULL,
				`referrer_hash` varbinary(32) NOT NULL,
				`referrer_url` text NOT NULL,
				`hits` int(11) DEFAULT NULL,
				`first_date` int(11) DEFAULT NULL,
				`last_date` int(11) DEFAULT NULL,
				PRIMARY KEY (`referrer_id`),
				UNIQUE KEY `image_id_hash` (`image_id`,`referrer_hash`),
				KEY `last_date` (`last_date`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		$this->executeUpgradeQuery("
			CREATE TABLE `xf_link_proxy_referrer` (
				`referrer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`link_id` int(10) unsigned NOT NULL,
				`referrer_hash` varbinary(32) NOT NULL,
				`referrer_url` text NOT NULL,
				`hits` int(11) DEFAULT NULL,
				`first_date` int(11) DEFAULT NULL,
				`last_date` int(11) DEFAULT NULL,
				PRIMARY KEY (`referrer_id`),
				UNIQUE KEY `link_id_hash` (`link_id`,`referrer_hash`),
				KEY `last_date` (`last_date`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
	}

	public function step8()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_user_alert 
			CHANGE COLUMN action action VARBINARY(30) NOT NULL COMMENT 'eg: edit'
		");
	}

	public function step9()
	{
		$this->executeUpgradeQuery("
			CREATE TABLE `xf_tag` (
				`tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`tag` varchar(100) NOT NULL,
				`tag_url` varchar(100) NOT NULL,
				`use_count` int(10) unsigned NOT NULL DEFAULT '0',
				`last_use_date` int(10) unsigned NOT NULL DEFAULT '0',
				`permanent` TINYINT NOT NULL DEFAULT '0',
				PRIMARY KEY (`tag_id`),
				UNIQUE KEY `tag` (`tag`),
				UNIQUE KEY `tag_url` (`tag_url`),
				KEY `use_count` (`use_count`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		$this->executeUpgradeQuery("
			CREATE TABLE `xf_tag_content` (
				`tag_content_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`content_type` varbinary(25) NOT NULL,
				`content_id` int(11) NOT NULL,
				`tag_id` int(10) unsigned NOT NULL,
				`add_user_id` int(10) unsigned NOT NULL,
				`add_date` int(10) unsigned NOT NULL,
				`visible` tinyint(3) unsigned NOT NULL,
				`content_date` int(10) unsigned NOT NULL,
				PRIMARY KEY (`tag_content_id`),
				UNIQUE KEY `content_type_id_tag` (`content_type`,`content_id`,`tag_id`),
				KEY `tag_id_content_date` (`tag_id`,`content_date`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		$this->executeUpgradeQuery("
			CREATE TABLE xf_tag_result_cache (
				`result_cache_id` int(11) NOT NULL AUTO_INCREMENT,
				`tag_id` int(10) unsigned NOT NULL,
				`user_id` int(10) unsigned NOT NULL,
				`cache_date` int(10) unsigned NOT NULL,
				`expiry_date` int(10) unsigned NOT NULL,
				`results` mediumblob NOT NULL,
				PRIMARY KEY (`result_cache_id`),
				UNIQUE KEY `tag_id_user_id` (`tag_id`,`user_id`),
				KEY `expiration_date` (`expiry_date`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		$this->executeUpgradeQuery("
			ALTER TABLE  `xf_forum` ADD  `min_tags` SMALLINT UNSIGNED NOT NULL DEFAULT  '0'
		");

		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_admin_permission_entry
				(user_id, admin_permission_id)
			SELECT user_id, 'tag'
			FROM xf_admin_permission_entry
			WHERE admin_permission_id = 'thread'
		");

		$this->applyGlobalPermission('general', 'createTag', 'forum', 'postThread');

		$this->applyGlobalPermission('forum', 'tagOwnThread', 'forum', 'postThread');
		$this->applyGlobalPermission('forum', 'manageOthersTagsOwnThread', 'forum', 'deleteOwnThread');
		$this->applyGlobalPermission('forum', 'manageAnyTag', 'forum', 'manageAnyThread');

		$this->applyContentPermission('forum', 'tagOwnThread', 'forum', 'postThread');
		$this->applyContentPermission('forum', 'manageOthersTagsOwnThread', 'forum', 'deleteOwnThread');
		$this->applyContentPermission('forum', 'manageAnyTag', 'forum', 'manageAnyThread');
	}

	public function step10()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_thread
			ADD tags MEDIUMBLOB NOT NULL
		");
	}

	public function step11()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_attachment_data
			ADD file_path VARCHAR(250) NOT NULL DEFAULT '' AFTER file_hash
		");
	}
}