<?php

namespace XF\Install\Upgrade;

class Version1020010 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.2.0 Alpha';
	}

	public function step1()
	{
		$this->executeUpgradeQuery('
			DROP TABLE IF EXISTS xf_permission_cache_content_type
		');
		$this->executeUpgradeQuery('
			DROP TABLE IF EXISTS xf_permission_cache_global_group
		');

		$this->executeUpgradeQuery("
			CREATE TABLE xf_deferred (
				deferred_id int(10) unsigned NOT NULL auto_increment,
				unique_key VARBINARY(50) default NULL,
				execute_class varchar(75) NOT NULL,
				execute_data mediumblob NOT NULL,
				manual_execute tinyint(4) NOT NULL,
				trigger_date int(11) NOT NULL,
				PRIMARY KEY  (deferred_id),
				UNIQUE KEY unique_key (unique_key),
				KEY trigger_date (trigger_date),
				KEY manual_execute_date (manual_execute,trigger_date)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE `xf_draft` (
				`draft_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`draft_key` VARBINARY(75) NOT NULL,
				`user_id` int(10) unsigned NOT NULL,
				`last_update` int(10) unsigned NOT NULL,
				`message` mediumtext NOT NULL,
				`extra_data` mediumblob NOT NULL,
				PRIMARY KEY (`draft_id`),
				UNIQUE KEY `draft_key_user` (`draft_key`,`user_id`),
				KEY `last_update` (`last_update`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_edit_history (
				edit_history_id int(10) unsigned NOT NULL auto_increment,
				content_type VARBINARY(25) NOT NULL,
				content_id int(10) unsigned NOT NULL,
				edit_user_id int(10) unsigned NOT NULL,
				edit_date int(10) unsigned NOT NULL,
				old_text mediumtext NOT NULL,
				PRIMARY KEY  (edit_history_id),
				KEY content_type (content_type,content_id,edit_date),
				KEY edit_date (edit_date)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_forum_watch (
				`user_id` int(10) unsigned NOT NULL,
				`node_id` int(10) unsigned NOT NULL,
				`notify_on` enum('','thread','message') NOT NULL,
				`send_alert` tinyint(3) unsigned NOT NULL,
				`send_email` tinyint(3) unsigned NOT NULL,
				PRIMARY KEY (`user_id`,`node_id`),
				KEY `node_id_notify_on` (`node_id`,`notify_on`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_registration_spam_cache (
				cache_key VARBINARY(128) NOT NULL DEFAULT '',
				decision VARCHAR(25) NOT NULL DEFAULT '',
				timeout INT UNSIGNED NOT NULL DEFAULT 0,
				PRIMARY KEY (cache_key)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE `xf_content_spam_cache` (
				`spam_cache_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`content_type` VARBINARY(25) NOT NULL,
				`content_id` int(10) unsigned NOT NULL,
				`spam_params` mediumblob NOT NULL,
				`insert_date` int(11) NOT NULL,
				PRIMARY KEY (`spam_cache_id`),
				UNIQUE KEY `content_type` (`content_type`,`content_id`),
				KEY `insert_date` (`insert_date`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_bb_code_parse_cache (
				bb_code_parse_cache_id int(10) unsigned NOT NULL AUTO_INCREMENT,
				content_type VARBINARY(25) NOT NULL,
				content_id int(10) unsigned NOT NULL,
				parse_tree mediumblob NOT NULL,
				cache_version int(10) unsigned NOT NULL,
				cache_date int(10) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (bb_code_parse_cache_id),
				UNIQUE KEY content_type_id (content_type,content_id),
				KEY cache_version (cache_version),
				KEY cache_date (cache_date)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE `xf_route_filter` (
				`route_filter_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`route_type` VARBINARY(25) NOT NULL,
				`prefix` varchar(25) NOT NULL,
				`find_route` varchar(255) NOT NULL,
				`replace_route` varchar(255) NOT NULL,
				`enabled` tinyint(3) unsigned NOT NULL DEFAULT '0',
				`url_to_route_only` tinyint(3) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (`route_filter_id`),
				KEY `route_type_prefix` (`route_type`,`prefix`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE `xf_mail_queue` (
				`mail_queue_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`mail_data` mediumblob NOT NULL,
				`queue_date` int(10) unsigned NOT NULL,
				PRIMARY KEY (`mail_queue_id`),
				KEY `queue_date` (`queue_date`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		
		$this->executeUpgradeQuery("
			CREATE TABLE xf_template_history (
				`template_history_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`title` VARBINARY(50) NOT NULL,
				`style_id` int(11) unsigned NOT NULL,
				`template` mediumtext NOT NULL,
				`edit_date` int(11) unsigned NOT NULL,
				`log_date` int(11) unsigned NOT NULL,
				PRIMARY KEY (`template_history_id`),
				KEY `log_date` (`log_date`),
				KEY `style_id_title` (`style_id`,`title`),
				KEY `title` (`title`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE `xf_template_modification` (
				`modification_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`addon_id` VARBINARY(25) NOT NULL,
				`template` VARBINARY(50) NOT NULL,
				`modification_key` VARBINARY(50) NOT NULL,
				`description` varchar(255) NOT NULL,
				`execution_order` int(10) unsigned NOT NULL,
				`enabled` tinyint(3) unsigned NOT NULL,
				`action` varchar(25) NOT NULL,
				`find` text NOT NULL,
				`replace` text NOT NULL,
				PRIMARY KEY (`modification_id`),
				UNIQUE KEY `modification_key` (`modification_key`),
				KEY `addon_id` (`addon_id`),
				KEY `template_order` (`template`,`execution_order`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE `xf_template_modification_log` (
				`template_id` int(10) unsigned NOT NULL,
				`modification_id` int(10) unsigned NOT NULL,
				`status` varchar(25) NOT NULL,
				`apply_count` int(10) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (`template_id`,`modification_id`),
				KEY `modification_id` (`modification_id`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE `xf_admin_template_modification` (
				`modification_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`addon_id` VARBINARY(25) NOT NULL,
				`template` VARBINARY(50) NOT NULL,
				`modification_key` VARBINARY(50) NOT NULL,
				`description` varchar(255) NOT NULL,
				`execution_order` int(10) unsigned NOT NULL,
				`enabled` tinyint(3) unsigned NOT NULL,
				`action` varchar(25) NOT NULL,
				`find` text NOT NULL,
				`replace` text NOT NULL,
				PRIMARY KEY (`modification_id`),
				UNIQUE KEY `modification_key` (`modification_key`),
				KEY `addon_id` (`addon_id`),
				KEY `template_order` (`template`,`execution_order`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE `xf_admin_template_modification_log` (
				`template_id` int(10) unsigned NOT NULL,
				`modification_id` int(10) unsigned NOT NULL,
				`status` varchar(25) NOT NULL,
				`apply_count` int(10) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (`template_id`,`modification_id`),
				KEY `modification_id` (`modification_id`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE `xf_email_template_modification` (
				`modification_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`addon_id` VARBINARY(25) NOT NULL,
				`template` VARBINARY(50) NOT NULL,
				`modification_key` VARBINARY(50) NOT NULL,
				`description` varchar(255) NOT NULL,
				`execution_order` int(10) unsigned NOT NULL,
				`enabled` tinyint(3) unsigned NOT NULL,
				`search_location` varchar(25) NOT NULL,
				`action` varchar(25) NOT NULL,
				`find` text NOT NULL,
				`replace` text NOT NULL,
				PRIMARY KEY (`modification_id`),
				UNIQUE KEY `modification_key` (`modification_key`),
				KEY `addon_id` (`addon_id`),
				KEY `template_order` (`template`,`execution_order`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE `xf_email_template_modification_log` (
				`template_id` int(10) unsigned NOT NULL,
				`modification_id` int(10) unsigned NOT NULL,
				`status` varchar(25) NOT NULL,
				`apply_count` int(10) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (`template_id`,`modification_id`),
				KEY `modification_id` (`modification_id`)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		return true;
	}

	public function step2()
	{
		$this->applyGlobalPermission('general', 'editProfile');
		$this->applyGlobalPermission('forum', 'editOwnThreadTitle', 'forum', 'editOwnPost');
		$this->applyGlobalPermission('signature', 'basicText', 'general', 'editSignature');
		$this->applyGlobalPermission('signature', 'extendedText', 'general', 'editSignature');
		$this->applyGlobalPermission('signature', 'align', 'general', 'editSignature');
		$this->applyGlobalPermission('signature', 'list', 'general', 'editSignature');
		$this->applyGlobalPermission('signature', 'image', 'general', 'editSignature');
		$this->applyGlobalPermission('signature', 'link', 'general', 'editSignature');
		$this->applyGlobalPermission('signature', 'basicText', 'general', 'editSignature');
		$this->applyGlobalPermission('signature', 'media', 'general', 'editSignature');
		$this->applyGlobalPermission('signature', 'block', 'general', 'editSignature');
		$this->applyGlobalPermissionInt('signature', 'maxPrintable', -1, 'general', 'editSignature');
		$this->applyGlobalPermissionInt('signature', 'maxLines', -1, 'general', 'editSignature');
		$this->applyGlobalPermissionInt('signature', 'maxLinks', -1, 'general', 'editSignature');
		$this->applyGlobalPermissionInt('signature', 'maxImages', -1, 'general', 'editSignature');
		$this->applyGlobalPermissionInt('signature', 'maxSmilies', -1, 'general', 'editSignature');
		$this->applyGlobalPermissionInt('signature', 'maxTextSize', -1, 'general', 'editSignature');
		$this->applyGlobalPermissionInt('general', 'maxTaggedUsers', 5, 'forum', 'postReply');

		$this->executeUpgradeQuery("
			INSERT INTO xf_content_type
				(content_type, addon_id, fields)
			VALUES
				('page',  'XenForo', '')
		");

		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_content_type_field
				(content_type, field_name, field_value)
			VALUES
				('post', 'edit_history_handler_class', 'XenForo_EditHistoryHandler_Post'),
				('page', 'search_handler_class', 'XenForo_Search_DataHandler_Page'),
				('conversation', 'spam_handler_class', 'XenForo_SpamHandler_Conversation')
		");

		return true;
	}

	public function step3()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE `xf_session_activity` ADD `robot_key` VARBINARY(25) NOT NULL DEFAULT ''
		");

		$this->executeUpgradeQuery("
			ALTER TABLE  `xf_template`
				ADD `disable_modifications` TINYINT UNSIGNED NOT NULL DEFAULT  '0',
				ADD  `last_edit_date` INT UNSIGNED NOT NULL DEFAULT  '0'
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_ban_email CHANGE banned_email banned_email VARCHAR(120) NOT NULL
		");

		$this->executeUpgradeQuery("
			ALTER TABLE  `xf_user_group`
				ADD  `banner_css_class` VARCHAR( 75 ) NOT NULL DEFAULT  '',
				ADD  `banner_text` VARCHAR( 100 ) NOT NULL DEFAULT  ''
		");

		return true;
	}

	public function step4()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE `xf_node` ADD `breadcrumb_data` BLOB NULL DEFAULT NULL
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_forum
				ADD require_prefix TINYINT UNSIGNED NOT NULL DEFAULT '0',
				ADD allowed_watch_notifications VARCHAR(10) NOT NULL DEFAULT 'all',
				ADD default_sort_order VARCHAR(25) NOT NULL DEFAULT 'last_post_date',
				ADD default_sort_direction VARCHAR(5) NOT NULL DEFAULT 'desc'
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_code_event_listener ADD hint VARCHAR(255) NOT NULL DEFAULT ''
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_option
				CHANGE edit_format edit_format ENUM('textbox','spinbox','onoff','radio','select','checkbox','template','callback','onofftextbox') NOT NULL
		");

		return true;
	}

	public function step5()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE  `xf_report` ADD  `report_count` INT UNSIGNED NOT NULL DEFAULT  '0'
		");
		$this->executeUpgradeQuery("
			ALTER TABLE `xf_report_comment` ADD  `is_report` TINYINT UNSIGNED NOT NULL DEFAULT  '1'
		");

		$ids = $this->db()->fetchAllColumn("(SELECT user_id FROM xf_moderator) UNION (SELECT user_id FROM xf_admin)");
		if ($ids)
		{
			$this->executeUpgradeQuery("
				UPDATE xf_report_comment SET is_report = 0 WHERE user_id IN (" . $this->db()->quote($ids) . ")
			");
		}

		return true;
	}

	public function step6()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE `xf_liked_content` ADD INDEX `like_user_content_type_id` (`like_user_id`, `content_type`, `content_id`)
		");

		return true;
	}

	public function step7()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE  `xf_user`
				ADD `is_staff` TINYINT UNSIGNED NOT NULL DEFAULT  '0',
				ADD INDEX message_count (  `message_count` ),
				ADD INDEX trophy_points (  `trophy_points` ),
				ADD INDEX like_count (  `like_count` ),
				ADD INDEX register_date (  `register_date` ),
				ADD INDEX `staff_username` (  `is_staff` ,  `username` )
		");

		return true;
	}

	public function step8()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE `xf_user_profile` ADD INDEX  `dob` (  `dob_month` ,  `dob_day` ,  `dob_year` )
		");
		$this->executeUpgradeQuery("
			ALTER TABLE xf_user_option ADD enable_flash_uploader TINYINT UNSIGNED NOT NULL DEFAULT 1
		");

		$this->executeUpgradeQuery("
			UPDATE xf_moderator AS moderator
			INNER JOIN xf_user AS user ON (moderator.user_id = user.user_id)
			SET user.is_staff = 1
		");

		return true;
	}

	public function step9()
	{
		$this->executeUpgradeQuery("
			UPDATE xf_user_upgrade
			SET description = REPLACE(REPLACE(description, '\r', ''), '\n', '<br />\n')
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_user_upgrade_expired
				ADD extra MEDIUMBLOB NOT NULL,
				ADD original_end_date INT UNSIGNED NOT NULL DEFAULT 0
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_user_upgrade_log
				ADD subscriber_id VARCHAR(50) NOT NULL DEFAULT '',
				ADD INDEX subscriber_id (subscriber_id)
		");

		return true;
	}

	public function step10()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE xf_conversation_master
				ADD recipients mediumblob NOT NULL,
				ADD INDEX user_id (user_id)
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_conversation_user
				ADD is_starred TINYINT UNSIGNED NOT NULL DEFAULT '0',
				ADD INDEX owner_starred_date (owner_user_id, is_starred, last_message_date)
		");

		return true;
	}

	public function step11()
	{
		$this->executeUpgradeQuery('
			ALTER TABLE xf_post
				ADD last_edit_date INT UNSIGNED NOT NULL DEFAULT 0,
				ADD last_edit_user_id INT UNSIGNED NOT NULL DEFAULT 0,
				ADD edit_count INT UNSIGNED NOT NULL DEFAULT 0
		');

		return true;
	}

	public function step12()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE `xf_ip` ADD INDEX log_date (`log_date`)
		");

		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_option
				(option_id, option_value, default_value, edit_format, edit_format_params, data_type, sub_options,
				can_backup, validation_class, validation_method, addon_id)
			VALUES
				('ipLogCleanUp', 'a:2:{s:7:\"enabled\";b:0;s:5:\"delay\";b:0;}', '', '', '', '', '',
				1, '', '', 'XenForo')
		");

		return true;
	}

	public function step13()
	{
		$this->executeUpgradeQuery("
			ALTER TABLE `xf_thread`
				DROP INDEX node_id_sticky_last_post_date,
				ADD INDEX  `node_id_sticky_state_last_post` (  `node_id` ,  `sticky` ,  `discussion_state` , `last_post_date` )
		");

		return true;
	}
}