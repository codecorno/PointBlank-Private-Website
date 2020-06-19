<?php

namespace XF\Install\Upgrade;

class Version1000036 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.0.0 Beta 6';
	}

	public function step1()
	{
		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_permission_entry
				(user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
			SELECT user_group_id, user_id, 'forum', 'like', 'allow', 0
			FROM xf_permission_entry
			WHERE permission_group_id = 'general'
				AND permission_id = 'view'
				AND permission_value = 'allow'
				AND (user_group_id > 1 OR user_id > 0)
		");

		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_permission_entry
				(user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
			SELECT user_group_id, user_id, 'profilePost', 'like', 'allow', 0
			FROM xf_permission_entry
			WHERE permission_group_id = 'profilePost'
				AND permission_id = 'view'
				AND permission_value = 'allow'
				AND (user_group_id > 1 OR user_id > 0)
		");
		// the permission cache will be rebuilt at the end

		$this->executeUpgradeQuery("
			ALTER TABLE xf_forum
				ADD moderate_messages TINYINT UNSIGNED NOT NULL DEFAULT 0,
				ADD allow_posting TINYINT UNSIGNED NOT NULL DEFAULT 1
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_user_option
				ADD enable_rte TINYINT UNSIGNED NOT NULL DEFAULT 1
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_language
				ADD language_code VARCHAR(25) NOT NULL DEFAULT ''
		");
		$this->executeUpgradeQuery("
			UPDATE xf_language
			SET language_code = 'en-US'
			WHERE language_id = 1
		");

		// create tables for feeder
		$this->executeUpgradeQuery("
			CREATE TABLE xf_feed (
				feed_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				title VARCHAR(250) NOT NULL,
				url VARCHAR(250) NOT NULL,
				frequency INT UNSIGNED NOT NULL DEFAULT 1800,
				node_id INT UNSIGNED NOT NULL,
				user_id INT UNSIGNED NOT NULL DEFAULT 0,
				title_template VARCHAR(250) NOT NULL DEFAULT '',
				message_template MEDIUMTEXT NOT NULL,
				discussion_visible TINYINT UNSIGNED NOT NULL DEFAULT 1,
				discussion_open TINYINT UNSIGNED NOT NULL DEFAULT 1,
				discussion_sticky TINYINT UNSIGNED NOT NULL DEFAULT 0,
				last_fetch INT UNSIGNED NOT NULL DEFAULT 0,
				active INT UNSIGNED NOT NULL DEFAULT 0,
				PRIMARY KEY (feed_id),
				KEY active (active)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		$this->executeUpgradeQuery("
			CREATE TABLE xf_feed_log (
				feed_id INT UNSIGNED NOT NULL,
				unique_id VARCHAR(250) NOT NULL,
				hash CHAR(32) NOT NULL COMMENT 'MD5(title + content)',
				thread_id INT UNSIGNED NOT NULL,
				PRIMARY KEY (feed_id,unique_id)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		// tables for QA CAPTCHA
		$this->executeUpgradeQuery("
			CREATE TABLE xf_captcha_question (
				captcha_question_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				question VARCHAR(250) NOT NULL,
				answers BLOB NOT NULL COMMENT 'Serialized array of possible correct answers.',
				active TINYINT UNSIGNED NOT NULL DEFAULT 1,
				PRIMARY KEY (captcha_question_id),
				KEY active (active)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		$this->executeUpgradeQuery("
			CREATE TABLE xf_captcha_log (
				hash CHAR(40) NOT NULL,
				captcha_type VARCHAR(250) NOT NULL,
				captcha_data VARCHAR(250) NOT NULL,
				captcha_date INT UNSIGNED NOT NULL,
				PRIMARY KEY (hash),
				KEY captcha_date (captcha_date)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		return true;
	}
}