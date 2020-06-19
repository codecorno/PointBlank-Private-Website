<?php

namespace XF\Install\Upgrade;

class Version1010031 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.1.0 Beta 1';
	}

	public function step1()
	{
		$db = $this->db();

		// new content types
		$db->query("
			INSERT IGNORE INTO xf_content_type
				(content_type, addon_id, fields)
			VALUES
				('attachment', 'XenForo', ''),
				('conversation_message', 'XenForo', '')
		");

		$db->query("
			INSERT IGNORE INTO xf_content_type_field
				(content_type, field_name, field_value)
			VALUES
				('attachment', 'stats_handler_class', 'XenForo_StatsHandler_Attachment'),
				('conversation', 'stats_handler_class', 'XenForo_StatsHandler_Conversation'),
				('conversation_message', 'report_handler_class', 'XenForo_ReportHandler_ConversationMessage'),
				('post', 'stats_handler_class', 'XenForo_StatsHandler_Post'),
				('thread', 'stats_handler_class', 'XenForo_StatsHandler_Thread'),
				('user', 'stats_handler_class', 'XenForo_StatsHandler_User'),
				('profile_post', 'stats_handler_class', 'XenForo_StatsHandler_ProfilePost'),

				('profile_post', 'search_handler_class', 'XenForo_Search_DataHandler_ProfilePost'),

				('post', 'moderator_log_handler_class', 'XenForo_ModeratorLogHandler_Post'),
				('thread', 'moderator_log_handler_class', 'XenForo_ModeratorLogHandler_Thread'),
				('profile_post', 'moderator_log_handler_class', 'XenForo_ModeratorLogHandler_ProfilePost'),

				('user', 'warning_handler_class', 'XenForo_WarningHandler_User'),
				('post', 'warning_handler_class', 'XenForo_WarningHandler_Post'),
				('profile_post', 'warning_handler_class', 'XenForo_WarningHandler_ProfilePost'),

				('conversation_message', 'attachment_handler_class', 'XenForo_AttachmentHandler_ConversationMessage')
		");

		$this->executeUpgradeQuery("
			CREATE TABLE xf_stats_daily (
				stats_date INT UNSIGNED NOT NULL,
				stats_type VARCHAR(25) NOT NULL,
				counter INT UNSIGNED NOT NULL,
				PRIMARY KEY (stats_date, stats_type)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_admin_log (
				admin_log_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id INT UNSIGNED NOT NULL,
				ip_address INT UNSIGNED NOT NULL DEFAULT 0,
				request_date INT UNSIGNED NOT NULL,
				request_url TEXT NOT NULL,
				request_data MEDIUMBLOB NOT NULL,
				PRIMARY KEY (admin_log_id),
				KEY request_date (request_date),
				KEY user_id_request_date (user_id, request_date)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_moderator_log (
				moderator_log_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				log_date INT UNSIGNED NOT NULL,
				user_id INT UNSIGNED NOT NULL,
				ip_address INT UNSIGNED NOT NULL DEFAULT 0,
				content_type VARCHAR(25) NOT NULL,
				content_id INT UNSIGNED NOT NULL,
				content_user_id INT UNSIGNED NOT NULL,
				content_username VARCHAR(50) NOT NULL,
				content_title VARCHAR(150) NOT NULL,
				content_url text NOT NULL,
				discussion_content_type VARCHAR(25) NOT NULL,
				discussion_content_id INT UNSIGNED NOT NULL,
				action VARCHAR(25) NOT NULL,
				action_params MEDIUMBLOB NOT NULL,
				PRIMARY KEY (moderator_log_id),
				KEY log_date (log_date),
				KEY content_type_id (content_type, content_id),
				KEY discussion_content_type_id (discussion_content_type, discussion_content_id),
				KEY user_id_log_date (user_id, log_date)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		// admin search
		$this->executeUpgradeQuery("
			CREATE TABLE xf_admin_search_type (
				search_type VARCHAR(25) NOT NULL,
				handler_class VARCHAR(50) NOT NULL,
				display_order INT UNSIGNED NOT NULL,
				PRIMARY KEY (search_type)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			INSERT INTO xf_admin_search_type
				(search_type, handler_class, display_order)
			VALUES
				('admin_navigation', 'XenForo_AdminSearchHandler_AdminNavigation', 0),
				('admin_template', 'XenForo_AdminSearchHandler_AdminTemplate', 710),
				('bb_code_media_site', 'XenForo_AdminSearchHandler_BbCodeMediaSite', 320),
				('feed', 'XenForo_AdminSearchHandler_Feed', 310),
				('language', 'XenForo_AdminSearchHandler_Language', 610),
				('node', 'XenForo_AdminSearchHandler_Node', 120),
				('notice', 'XenForo_AdminSearchHandler_Notice', 410),
				('option', 'XenForo_AdminSearchHandler_Option', 110),
				('phrase', 'XenForo_AdminSearchHandler_Phrase', 620),
				('promotion', 'XenForo_AdminSearchHandler_Promotion', 250),
				('smilie', 'XenForo_AdminSearchHandler_Smilie', 330),
				('style', 'XenForo_AdminSearchHandler_Style', 510),
				('style_property', 'XenForo_AdminSearchHandler_StyleProperty', 530),
				('template', 'XenForo_AdminSearchHandler_Template', 520),
				('user', 'XenForo_AdminSearchHandler_User', 210),
				('user_field', 'XenForo_AdminSearchHandler_UserField', 230),
				('user_group', 'XenForo_AdminSearchHandler_UserGroup', 220),
				('user_upgrade', 'XenForo_AdminSearchHandler_UserUpgrade', 260),
				('warning', 'XenForo_AdminSearchHandler_Warning', 240)
		");

		// misc
		$this->executeUpgradeQuery("ALTER TABLE xf_search ADD user_results MEDIUMBLOB NOT NULL AFTER search_grouping");
		$this->executeUpgradeQuery("ALTER TABLE xf_language ADD text_direction enum('LTR','RTL') NOT NULL DEFAULT 'LTR'");
		$this->executeUpgradeQuery("ALTER TABLE xf_trophy CHANGE criteria user_criteria MEDIUMBLOB NOT NULL");

		// new thread viewing permissions: insert for all groups that can view the board
		$this->applyGlobalPermission('forum', 'viewOthers', 'general', 'view');
		$this->applyGlobalPermission('forum', 'viewContent', 'general', 'view');

		// new conversation attachment permissions: insert for mods and admins only by default
		$db->query("
			INSERT IGNORE INTO xf_permission_entry
				(user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
			VALUES
				(3, 0, 'conversation', 'uploadAttachment', 'allow', 0),
				(4, 0, 'conversation', 'uploadAttachment', 'allow', 0)
		");

		// user group promotions
		$this->executeUpgradeQuery("
			CREATE TABLE xf_user_group_promotion (
				promotion_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				title VARCHAR(100) NOT NULL,
				active TINYINT NOT NULL DEFAULT 1,
				user_criteria MEDIUMBLOB NOT NULL,
				extra_user_group_ids VARBINARY(255) NOT NULL,
				PRIMARY KEY (promotion_id),
				KEY title (title)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_user_group_promotion_log (
				promotion_id int(10) unsigned NOT NULL,
				user_id int(10) unsigned NOT NULL,
				promotion_date int(10) unsigned NOT NULL,
				promotion_state enum('automatic','manual','disabled') NOT NULL default 'automatic',
				PRIMARY KEY (promotion_id, user_id),
				KEY promotion_date (promotion_date),
				KEY user_id_date (user_id, promotion_date)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		// notices
		$this->executeUpgradeQuery("
			CREATE TABLE xf_notice (
				notice_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				title VARCHAR(150) NOT NULL,
				message MEDIUMTEXT NOT NULL,
				active TINYINT UNSIGNED NOT NULL DEFAULT 1,
				display_order INT UNSIGNED NOT NULL DEFAULT 0,
				dismissible TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Notice may be hidden when read by users',
				wrap TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Wrap this notice in div.noticeContent',
				user_criteria MEDIUMBLOB NOT NULL,
				page_criteria MEDIUMBLOB NOT NULL,
				PRIMARY KEY (notice_id)
			) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_notice_dismissed (
				notice_id INT UNSIGNED NOT NULL,
				user_id INT UNSIGNED NOT NULL,
				dismiss_date INT UNSIGNED NOT NULL DEFAULT 0,
				PRIMARY KEY (notice_id, user_id)
			) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci
		");

		// custom user fields and ignore list
		$this->executeUpgradeQuery("
			CREATE TABLE xf_user_field (
				field_id VARCHAR(25) NOT NULL,
				display_group ENUM('personal','contact','preferences') NOT NULL DEFAULT 'personal',
				display_order INT UNSIGNED NOT NULL DEFAULT 1,
				field_type ENUM('textbox','textarea','select','radio','checkbox','multiselect') NOT NULL DEFAULT 'textbox',
				field_choices BLOB NOT NULL,
				match_type ENUM('none','number','alphanumeric','email','url','regex','callback') NOT NULL DEFAULT 'none',
				match_regex VARCHAR(250) NOT NULL DEFAULT '',
				match_callback_class VARCHAR(75) NOT NULL DEFAULT '',
				match_callback_method VARCHAR(75) NOT NULL DEFAULT '',
				max_length INT UNSIGNED NOT NULL DEFAULT 0,
				required TINYINT UNSIGNED NOT NULL DEFAULT 0,
				show_registration TINYINT UNSIGNED NOT NULL DEFAULT 0,
				user_editable ENUM('yes','once','never') NOT NULL DEFAULT 'yes',
				viewable_profile TINYINT NOT NULL DEFAULT 1,
				viewable_message TINYINT NOT NULL DEFAULT 0,
				display_template TEXT NOT NULL,
				PRIMARY KEY (field_id),
				KEY display_group_order (display_group, display_order)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_user_field_value (
				user_id INT UNSIGNED NOT NULL,
				field_id VARCHAR(25) NOT NULL,
				field_value MEDIUMTEXT NOT NULL,
				PRIMARY KEY (user_id, field_id),
				KEY field_id (field_id)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_user_ignored (
				user_id INT UNSIGNED NOT NULL,
				ignored_user_id INT UNSIGNED NOT NULL,
				PRIMARY KEY (user_id, ignored_user_id),
				KEY ignored_user_id (ignored_user_id)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			ALTER TABLE xf_user_profile
				ADD custom_fields MEDIUMBLOB NOT NULL,
				ADD ignored TEXT NOT NULL COMMENT 'Comma-separated integers from xf_user_ignored'
		");

		// conversation attachments
		$this->executeUpgradeQuery("ALTER TABLE xf_conversation_message ADD attach_count SMALLINT UNSIGNED NOT NULL DEFAULT 0");

		// bb code media site upgrades
		$this->executeUpgradeQuery("
			ALTER TABLE xf_bb_code_media_site
				ADD match_is_regex TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'If 1, match_urls will be treated as regular expressions rather than simple URL matches.' AFTER match_urls,
				ADD match_callback_class VARCHAR(75) NOT NULL DEFAULT '' AFTER match_is_regex,
				ADD match_callback_method VARCHAR(50) NOT NULL DEFAULT '' AFTER match_callback_class,
				ADD embed_html_callback_class VARCHAR(75) NOT NULL DEFAULT '' AFTER embed_html,
				ADD embed_html_callback_method VARCHAR(50) NOT NULL DEFAULT '' AFTER embed_html_callback_class,
				ADD addon_id VARCHAR(25) NOT NULL DEFAULT ''
		");

		$this->executeUpgradeQuery('
			INSERT INTO xf_bb_code_media_site
				(media_site_id, site_title, site_url, match_urls, embed_html, match_is_regex)
			VALUES
				(\'metacafe\', \'Metacafe\', \'http://www.metacafe.com\', \'#metacafe\\.com/watch/(?P<id>\\d+\/[a-z0-9_]+)/#siU\', \'<embed flashVars="playerVars=autoPlay=no"\n	src="http://www.metacafe.com/fplayer/{$id}.swf"\n	width="500" height="300" wmode="transparent"\n	allowFullScreen="true" allowScriptAccess="always"\n	pluginspage="http://www.macromedia.com/go/getflashplayer"\n	type="application/x-shockwave-flash">\n</embed>\', 1),
				(\'dailymotion\', \'Dailymotion\', \'http://www.dailymotion.com\', \'dailymotion.com/video/{$id:alphanum}\', \'<iframe frameborder="0" width="500" height="300" src="http://www.dailymotion.com/embed/video/{$id}?width=500&hideInfos=1"></iframe>\', 0),
				(\'liveleak\', \'Liveleak\', \'http://www.liveleak.com\', \'liveleak.com/view?i={$id}\', \'<object width="500" height="300">\n	<param name="movie" value="http://www.liveleak.com/e/{$id}"></param>\n	<param name="wmode" value="transparent"></param>\n	<param name="allowscriptaccess" value="always"></param>\n	<embed src="http://www.liveleak.com/e/{$id}" type="application/x-shockwave-flash" wmode="transparent" allowscriptaccess="always" width="500" height="300"></embed>\n</object>\', 0)
		');

		// basic warning stuff
		$this->executeUpgradeQuery("
			CREATE TABLE xf_warning (
				warning_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				content_type VARCHAR(25) NOT NULL,
				content_id INT UNSIGNED NOT NULL,
				content_title VARCHAR(255) NOT NULL,
				user_id INT UNSIGNED NOT NULL,
				warning_date INT UNSIGNED NOT NULL,
				warning_user_id INT UNSIGNED NOT NULL,
				warning_definition_id INT UNSIGNED NOT NULL,
				title VARCHAR(255) NOT NULL,
				notes TEXT NOT NULL,
				points SMALLINT UNSIGNED NOT NULL,
				expiry_date INT UNSIGNED NOT NULL,
				is_expired TINYINT UNSIGNED NOT NULL,
				extra_user_group_ids VARBINARY(255) NOT NULL,
				PRIMARY KEY (warning_id),
				KEY content_type_id (content_type, content_id),
				KEY user_id_date (user_id, warning_date),
				KEY expiry (expiry_date)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_warning_definition (
				warning_definition_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				points_default SMALLINT UNSIGNED NOT NULL,
				expiry_type ENUM('never','days','weeks','months','years') NOT NULL,
				expiry_default SMALLINT UNSIGNED NOT NULL,
				extra_user_group_ids VARBINARY(255) NOT NULL,
				is_editable TINYINT UNSIGNED NOT NULL,
				PRIMARY KEY (warning_definition_id),
				KEY points_default (points_default)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_warning_action (
				warning_action_id INT UNSIGNED NOT NULL auto_increment,
				points SMALLINT UNSIGNED NOT NULL,
				action ENUM('ban_length','ban_points','discourage','groups') NOT NULL,
				ban_length_type ENUM('permanent','days','weeks','months','years') NOT NULL,
				ban_length SMALLINT UNSIGNED NOT NULL,
				extra_user_group_ids VARBINARY(255) NOT NULL,
				PRIMARY KEY (warning_action_id),
				KEY points (points)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_warning_action_trigger (
				action_trigger_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				warning_action_id INT UNSIGNED NOT NULL,
				user_id INT UNSIGNED NOT NULL,
				trigger_points SMALLINT UNSIGNED NOT NULL,
				action_date INT UNSIGNED NOT NULL,
				action ENUM('ban_points','discourage','groups') NOT NULL,
				min_unban_date INT UNSIGNED NOT NULL DEFAULT 0,
				PRIMARY KEY (action_trigger_id),
				KEY user_id_points (user_id, trigger_points)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		// warning view based on viewing IPs
		$this->applyGlobalPermission('general', 'viewWarning', 'general', 'viewIps');

		// general give/manage warnings based on spam cleaner
		$this->applyGlobalPermission('general', 'warn', 'general', 'cleanSpam');
		$this->applyGlobalPermission('general', 'manageWarning', 'general', 'cleanSpam');

		// forum warning based on deleting posts
		$this->applyGlobalPermission('forum', 'warn', 'forum', 'deleteAnyPost');
		$this->applyContentPermission('forum', 'warn', 'forum', 'deleteAnyPost');

		// profile post warning based on deleting profile posts
		$this->applyGlobalPermission('profilePost', 'warn', 'profilePost', 'deleteAny');

		// default warnings
		$db->query("
			INSERT IGNORE INTO xf_warning_definition
				(warning_definition_id, points_default, expiry_type, expiry_default, extra_user_group_ids, is_editable)
			VALUES
				(1, 1, 'months', 1, '', 1),
				(2, 1, 'months', 1, '', 1),
				(3, 1, 'months', 1, '', 1),
				(4, 1, 'months', 1, '', 1)
		");

		$db->query("
			INSERT IGNORE INTO xf_phrase
				(language_id, title, phrase_text, global_cache, addon_id)
			VALUES
				(0, 'warning_definition_1_title', 'Inappropriate content', 0, ''),
				(0, 'warning_definition_1_conversation_title', 'Inappropriate content', 0, ''),
				(0, 'warning_definition_1_conversation_text', '{name},\n\nYour message ([url={url}]{title}[/url]) contains inappropriate content. Please do not discuss content of this nature on our site. This does not follow our rules. Your message may have been removed or altered.\n\nYour account''s access may be limited based on these actions. Please keep this in mind when posting or using our site.', 0, ''),
				(0, 'warning_definition_2_title', 'Inappropriate behavior', 0, ''),
				(0, 'warning_definition_2_conversation_title', 'Inappropriate behavior', 0, ''),
				(0, 'warning_definition_2_conversation_text', '{name},\n\nYour actions in this message ([url={url}]{title}[/url]) are not appropriate. We cannot allow users to be abusive, overly aggressive, threatening, or to \"troll\". This does not follow our rules. Your message may have been removed or altered.\n\nYour account''s access may be limited based on these actions. Please keep this in mind when posting or using our site.', 0, ''),
				(0, 'warning_definition_3_title', 'Inappropriate language', 0, ''),
				(0, 'warning_definition_3_conversation_title', 'Inappropriate language', 0, ''),
				(0, 'warning_definition_3_conversation_text', '{name},\n\nYour message ([url={url}]{title}[/url]) contains inappropriate language. This does not follow our rules. Your message may have been removed or altered.\n\nYour account''s access may be limited based on these actions. Please keep this in mind when posting or using our site.', 0, ''),
				(0, 'warning_definition_4_title', 'Inappropriate advertising / spam', 0, ''),
				(0, 'warning_definition_4_conversation_title', 'Inappropriate advertising / spam', 0, ''),
				(0, 'warning_definition_4_conversation_text', '{name},\n\nYour message ([url={url}]{title}[/url]) contains inappropriate advertising or spam. This does not follow our rules. Your message may have been removed or altered.\n\nYour account''s access may be limited based on these actions. Please keep this in mind when posting or using our site.', 0, '')
		");

		// smilie sprite mode
		$this->executeUpgradeQuery("
			ALTER TABLE xf_smilie
				ADD sprite_mode TINYINT UNSIGNED NOT NULL DEFAULT 0,
				ADD sprite_params TEXT NOT NULL
		");

		return true;
	}

	public function step2()
	{
		// thread prefixes, find new threads
		$this->executeUpgradeQuery("
			CREATE TABLE xf_forum_prefix (
				node_id INT UNSIGNED NOT NULL,
				prefix_id INT UNSIGNED NOT NULL,
				PRIMARY KEY (node_id, prefix_id),
				KEY prefix_id (prefix_id)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_thread_prefix (
				prefix_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				prefix_group_id INT UNSIGNED NOT NULL,
				display_order INT UNSIGNED NOT NULL,
				materialized_order INT UNSIGNED NOT NULL COMMENT 'Internally-set order, based on prefix_group.display_order, prefix.display_order',
				css_class VARCHAR(50) NOT NULL DEFAULT '',
				allowed_user_group_ids blob NOT NULL,
				PRIMARY KEY (prefix_id),
				KEY materialized_order (materialized_order)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");
		$this->executeUpgradeQuery("
			CREATE TABLE xf_thread_prefix_group (
				prefix_group_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
				display_order INT UNSIGNED NOT NULL,
				PRIMARY KEY (prefix_group_id)
			) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci
		");

		$this->executeUpgradeQuery("
			ALTER TABLE xf_forum
  				ADD count_messages TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'If not set, messages posted (directly) within this forum will not contribute to user message totals.',
				ADD find_new TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Include posts from this forum when running /find-new/threads',
				ADD prefix_cache MEDIUMBLOB NOT NULL COMMENT 'Serialized data from xf_forum_prefix, [group_id][prefix_id] => prefix_id',
				ADD default_prefix_id INT UNSIGNED NOT NULL DEFAULT 0
		");

		$this->executeUpgradeQuery("ALTER TABLE xf_thread ADD prefix_id INT UNSIGNED NOT NULL DEFAULT 0");

		return true;
	}

	public function step3()
	{
		// additional warning parts and useful index
		$this->executeUpgradeQuery('ALTER TABLE xf_user ADD warning_points INT UNSIGNED NOT NULL DEFAULT 0');

		$this->executeUpgradeQuery('
			ALTER TABLE xf_profile_post
				ADD warning_id INT UNSIGNED NOT NULL DEFAULT 0,
				ADD warning_message VARCHAR(255) NOT NULL DEFAULT \'\',
				ADD INDEX user_id (user_id)
		');

		return true;
	}

	public function step4()
	{
		// additional warning parts - the biggest query, add useful index
		$this->executeUpgradeQuery('
			ALTER TABLE xf_post
				ADD warning_id INT UNSIGNED NOT NULL DEFAULT 0,
				ADD warning_message VARCHAR(255) NOT NULL DEFAULT \'\',
				ADD INDEX user_id (user_id)
		');

		return true;
	}

	public function step5()
	{
		$db = $this->db();

		if (!$this->executeUpgradeQuery('SELECT 1 FROM xf_identity_service LIMIT 1'))
		{
			return true; // data already removed
		}

		$identities = $db->fetchAll("
			SELECT ident.identity_service_id,
				title.phrase_text AS title,
				hint.phrase_text AS hint
			FROM xf_identity_service AS ident
			LEFT JOIN xf_phrase AS title ON
				(title.language_id = 0 AND title.title = CONCAT('identity_service_name_', ident.identity_service_id))
			LEFT JOIN xf_phrase AS hint ON
				(title.language_id = 0 AND title.title = CONCAT('identity_service_hint_', ident.identity_service_id))
		");

		$db->beginTransaction();

		$displayOrder = 0;
		foreach ($identities AS $identity)
		{
			$displayOrder += 10;
			$fieldId = $identity['identity_service_id'];

			$insert = [
				'field_id' => $fieldId,
				'display_group' => 'contact',
				'display_order' => $displayOrder,
				'field_type' => 'textbox',
				'field_choices' => '',
				'display_template' => ''
			];
			switch ($fieldId)
			{
				case 'aim':
					$insert['match_type'] = 'regex';
					$insert['match_regex'] = '^[a-zA-Z0-9@\. ]+$';
					$insert['max_length'] = '80';
					break;

				case 'msn':
					$insert['match_type'] = 'email';
					break;

				case 'icq':
					$insert['match_type'] = 'number';
					break;

				case 'skype':
					$insert['match_type'] = 'regex';
					$insert['match_regex'] = '^[a-zA-Z0-9-_\.,]{3,30}$';
					$insert['max_length'] = '30';
					break;

				case 'facebook':
					$insert['match_type'] = 'callback';
					$insert['match_callback_class'] = 'XenForo_Helper_UserField';
					$insert['match_callback_method'] = 'verifyFacebook';
					break;

				case 'twitter':
					$insert['match_type'] = 'callback';
					$insert['match_callback_class'] = 'XenForo_Helper_UserField';
					$insert['match_callback_method'] = 'verifyTwitter';
					break;
			}

			try
			{
				$this->db()->insert('xf_user_field', $insert);
				$saved = true;
			}
			catch (\Exception $e)
			{
				$saved = false;
			}

			if ($saved)
			{
				$db->query("
					INSERT INTO xf_phrase
						(language_id, title, phrase_text, global_cache, addon_id)
					VALUES
						(0, ?, ?, 1, ''),
						(0, ?, ?, 0, '')
				", [
					"user_field_$fieldId", strval($identity['title']),
					"user_field_{$fieldId}_desc", strval($identity['hint']),
				]);
			}
		}

		$db->commit();

		return true;
	}

	public function step6($position, array $stepData)
	{
		// convert identity services to fields
		$perPage = 250;

		$db = $this->db();

		if (!$this->executeUpgradeQuery('SELECT 1 FROM xf_identity_service LIMIT 1'))
		{
			return true; // data already removed
		}

		if (!isset($stepData['max']))
		{
			$stepData['max'] = $db->fetchOne('SELECT MAX(user_id) FROM xf_user');
		}

		$userIds = $db->fetchAllColumn($db->limit(
			'
				SELECT user_id
				FROM xf_user AS user
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
			FROM xf_user_identity
			WHERE user_id IN (' . $db->quote($userIds) . ')
		');
		$identitiesGrouped = [];
		while ($result = $queryResults->fetch())
		{
			$identitiesGrouped[$result['user_id']][$result['identity_service_id']] = $result['account_name'];
		}

		$db->beginTransaction();

		foreach ($identitiesGrouped AS $userId => $identities)
		{
			$userIdQuoted = $db->quote($userId);
			$rows = [];
			foreach ($identities AS $fieldId => $value)
			{
				$rows[] = '(' . $userIdQuoted . ', ' . $db->quote($fieldId) . ', ' . $db->quote($value) . ')';
			}

			$db->query('
				INSERT INTO xf_user_field_value
					(user_id, field_id, field_value)
				VALUES
					' . implode(',', $rows) . '
				ON DUPLICATE KEY UPDATE
					field_value = VALUES(field_value)
			');
			$db->query('
				UPDATE xf_user_profile SET
					custom_fields = ?
				WHERE user_id = ?
			', [serialize($identities), $userId]);
		}

		$db->commit();

		$nextPosition = end($userIds);

		return [
			$nextPosition,
			"$nextPosition / $stepData[max]",
			$stepData
		];
	}

	public function step7()
	{
		$db = $this->db();

		// remove identity services
		$this->executeUpgradeQuery('DROP TABLE xf_identity_service');
		$this->executeUpgradeQuery('DROP TABLE xf_user_identity');
		$this->executeUpgradeQuery('ALTER TABLE xf_user_profile DROP identities');
		$this->executeUpgradeQuery("DELETE FROM xf_phrase WHERE title LIKE 'identity_service_%'");

		// switch ident service admin perm to custom user fields
		$db->query("
			UPDATE IGNORE xf_admin_permission_entry SET
				admin_permission_id = 'userField'
			WHERE admin_permission_id = 'identityService'
		");

		return true;
	}
}