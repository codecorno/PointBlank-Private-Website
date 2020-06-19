<?php

namespace XF\Install\Data;

use XF\Db\Schema\Create;

class MySql
{
	public function getTables()
	{
		$tables = [];

		$tables['xf_addon'] = function(Create $table)
		{
			$table->addColumn('addon_id', 'varbinary', 50);
			$table->addColumn('title', 'varchar', 75);
			$table->addColumn('version_string', 'varchar', 30)->setDefault('');
			$table->addColumn('version_id', 'int')->setDefault(0);
			$table->addColumn('json_hash', 'varbinary', 64)->setDefault('');
			$table->addColumn('active', 'tinyint', 3);
			$table->addColumn('is_legacy', 'tinyint')->setDefault(0);
			$table->addColumn('is_processing', 'tinyint')->setDefault(0);
			$table->addColumn('last_pending_action', 'varchar', 50)->nullable();
			$table->addPrimaryKey('addon_id');
			$table->addKey('title');
		};

		$tables['xf_addon_install_batch'] = function(Create $table)
		{
			$table->addColumn('batch_id', 'int')->autoIncrement();
			$table->addColumn('start_date', 'int')->setDefault(0);
			$table->addColumn('complete_date', 'int')->setDefault(0);
			$table->addColumn('addon_ids', 'mediumblob');
			$table->addColumn('results', 'blob');
		};

		$tables['xf_admin'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('extra_user_group_ids', 'varbinary', 255);
			$table->addColumn('last_login', 'int')->setDefault(0);
			$table->addColumn('permission_cache', 'mediumblob')->nullable();
			$table->addColumn('admin_language_id', 'int')->setDefault(0);
			$table->addColumn('is_super_admin', 'tinyint')->setDefault(0);
			$table->addPrimaryKey('user_id');
		};

		$tables['xf_admin_log'] = function(Create $table)
		{
			$table->addColumn('admin_log_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('ip_address', 'varbinary', 16)->setDefault('');
			$table->addColumn('request_date', 'int');
			$table->addColumn('request_url', 'text');
			$table->addColumn('request_data', 'mediumblob');
			$table->addKey('request_date');
			$table->addKey(['user_id', 'request_date']);
		};

		$tables['xf_admin_navigation'] = function(Create $table)
		{
			$table->addColumn('navigation_id', 'varbinary', 50);
			$table->addColumn('parent_navigation_id', 'varbinary', 50);
			$table->addColumn('display_order', 'int')->setDefault(0);
			$table->addColumn('link', 'varchar', 50)->setDefault('');
			$table->addColumn('icon', 'varchar', 50)->setDefault('');
			$table->addColumn('admin_permission_id', 'varbinary', 25)->setDefault('');
			$table->addColumn('debug_only', 'tinyint', 3)->setDefault(0);
			$table->addColumn('development_only', 'tinyint', 3)->setDefault(0);
			$table->addColumn('hide_no_children', 'tinyint', 3)->setDefault(0);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('navigation_id');
			$table->addKey(['parent_navigation_id', 'display_order']);
		};

		$tables['xf_admin_permission'] = function(Create $table)
		{
			$table->addColumn('admin_permission_id', 'varbinary', 25);
			$table->addColumn('display_order', 'int')->setDefault(0);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('admin_permission_id');
			$table->addKey('display_order');
		};

		$tables['xf_admin_permission_entry'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('admin_permission_id', 'varbinary', 25);
			$table->addPrimaryKey(['user_id', 'admin_permission_id']);
		};

		$tables['xf_advertising'] = function(Create $table)
		{
			$table->addColumn('ad_id', 'int')->autoIncrement();
			$table->addColumn('title', 'varchar', 150);
			$table->addColumn('position_id', 'varbinary', 50);
			$table->addColumn('ad_html', 'text');
			$table->addColumn('display_order', 'int')->setDefault(0);
			$table->addColumn('display_criteria', 'blob');
			$table->addColumn('active', 'tinyint');
		};

		$tables['xf_advertising_position'] = function(Create $table)
		{
			$table->addColumn('position_id', 'varbinary', 50);
			$table->addColumn('arguments', 'blob');
			$table->addColumn('active', 'tinyint');
			$table->addColumn('addon_id', 'varbinary', 50);
			$table->addPrimaryKey('position_id');
		};

		$tables['xf_api_attachment_key'] = function (Create $table)
		{
			$table->addColumn('attachment_key', 'varbinary', 32)->primaryKey();
			$table->addColumn('create_date', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('temp_hash', 'varbinary', 32);
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('context', 'blob');
			$table->addKey('create_date');
		};

		$tables['xf_api_key'] = function (Create $table)
		{
			$table->addColumn('api_key_id', 'int')->autoIncrement();
			$table->addColumn('api_key', 'varbinary', 32);
			$table->addColumn('api_key_hash', 'varbinary', 20);
			$table->addColumn('title', 'varchar', 50)->setDefault('');
			$table->addColumn('is_super_user', 'tinyint', 3);
			$table->addColumn('user_id', 'int')->setDefault(0);
			$table->addColumn('allow_all_scopes', 'tinyint', 3);
			$table->addColumn('scopes', 'mediumblob');
			$table->addColumn('active', 'tinyint', 3);
			$table->addColumn('creation_user_id', 'int')->setDefault(0);
			$table->addColumn('creation_date', 'int')->setDefault(0);
			$table->addColumn('last_use_date', 'int')->setDefault(0);
			$table->addUniqueKey('api_key_hash');
		};

		$tables['xf_api_key_scope'] = function (Create $table)
		{
			$table->addColumn('api_key_id', 'int');
			$table->addColumn('api_scope_id', 'varbinary', 50);
			$table->addPrimaryKey(['api_key_id', 'api_scope_id']);
		};

		$tables['xf_api_scope'] = function (Create $table)
		{
			$table->addColumn('api_scope_id', 'varbinary', 50);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('api_scope_id');
		};

		$tables['xf_approval_queue'] = function(Create $table)
		{
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('content_date', 'int')->setDefault(0);
			$table->addPrimaryKey(['content_type', 'content_id']);
			$table->addKey('content_date');
		};

		$tables['xf_attachment'] = function(Create $table)
		{
			$table->addColumn('attachment_id', 'int')->autoIncrement();
			$table->addColumn('data_id', 'int');
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('attach_date', 'int');
			$table->addColumn('temp_hash', 'varchar', 32)->setDefault('');
			$table->addColumn('unassociated', 'tinyint', 3);
			$table->addColumn('view_count', 'int')->setDefault(0);
			$table->addKey(['content_type', 'content_id', 'attach_date'], 'content_type_id_date');
			$table->addKey(['temp_hash', 'attach_date']);
			$table->addKey(['unassociated', 'attach_date']);
		};

		$tables['xf_attachment_data'] = function(Create $table)
		{
			$table->addColumn('data_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('upload_date', 'int');
			$table->addColumn('filename', 'varchar', 100);
			$table->addColumn('file_size', 'int');
			$table->addColumn('file_hash', 'varchar', 32);
			$table->addColumn('file_path', 'varchar', 250)->setDefault('');
			$table->addColumn('width', 'int')->setDefault(0);
			$table->addColumn('height', 'int')->setDefault(0);
			$table->addColumn('thumbnail_width', 'int')->setDefault(0);
			$table->addColumn('thumbnail_height', 'int')->setDefault(0);
			$table->addColumn('attach_count', 'int')->setDefault(0);
			$table->addKey(['user_id', 'upload_date']);
			$table->addKey('attach_count');
			$table->addKey('upload_date');
		};

		$tables['xf_attachment_view'] = function(Create $table)
		{
			$table->engine('MEMORY');

			$table->addColumn('attachment_id', 'int');
			$table->addColumn('total', 'int');
			$table->addPrimaryKey('attachment_id');
		};

		$tables['xf_ban_email'] = function(Create $table)
		{
			$table->addColumn('banned_email', 'varchar', 120);
			$table->addColumn('create_user_id', 'int')->setDefault(0);
			$table->addColumn('create_date', 'int')->setDefault(0);
			$table->addColumn('reason', 'varchar', 255)->setDefault('');
			$table->addColumn('last_triggered_date', 'int')->setDefault(0);
			$table->addPrimaryKey('banned_email');
			$table->addKey('create_date');
		};

		$tables['xf_bb_code'] = function(Create $table)
		{
			$table->addColumn('bb_code_id', 'varbinary', 25);
			$table->addColumn('bb_code_mode', 'varchar', 25);
			$table->addColumn('has_option', 'varchar', 25);
			$table->addColumn('replace_html', 'text');
			$table->addColumn('replace_html_email', 'text');
			$table->addColumn('replace_text', 'text');
			$table->addColumn('callback_class', 'varchar', 100)->setDefault('');
			$table->addColumn('callback_method', 'varchar', 75)->setDefault('');
			$table->addColumn('option_regex', 'text');
			$table->addColumn('trim_lines_after', 'tinyint', 3)->setDefault(0);
			$table->addColumn('plain_children', 'tinyint', 3)->setDefault(0);
			$table->addColumn('disable_smilies', 'tinyint', 3)->setDefault(0);
			$table->addColumn('disable_nl2br', 'tinyint', 3)->setDefault(0);
			$table->addColumn('disable_autolink', 'tinyint', 3)->setDefault(0);
			$table->addColumn('allow_empty', 'tinyint', 3)->setDefault(0);
			$table->addColumn('allow_signature', 'tinyint', 3)->setDefault(1);
			$table->addColumn('editor_icon_type', 'varchar', 25)->setDefault('');
			$table->addColumn('editor_icon_value', 'varchar', 150)->setDefault('');
			$table->addColumn('active', 'tinyint', 3)->setDefault(1);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('bb_code_id');
		};

		$tables['xf_bb_code_media_site'] = function(Create $table)
		{
			$table->addColumn('media_site_id', 'varbinary', 25);
			$table->addColumn('site_title', 'varchar', 50);
			$table->addColumn('site_url', 'varchar', 100)->setDefault('');
			$table->addColumn('match_urls', 'text');
			$table->addColumn('match_is_regex', 'tinyint', 3)->setDefault(0)->comment('If 1, match_urls will be treated as regular expressions rather than simple URL matches.');
			$table->addColumn('oembed_enabled', 'tinyint', 3)->setDefault(0);
			$table->addColumn('oembed_api_endpoint', 'varbinary', 250)->setDefault('');
			$table->addColumn('oembed_url_scheme', 'varbinary', 250)->setDefault('');
			$table->addColumn('oembed_retain_scripts', 'tinyint', 3)->setDefault(0);
			$table->addColumn('match_callback_class', 'varchar', 100)->setDefault('');
			$table->addColumn('match_callback_method', 'varchar', 75)->setDefault('');
			$table->addColumn('embed_html_callback_class', 'varchar', 100)->setDefault('');
			$table->addColumn('embed_html_callback_method', 'varchar', 75)->setDefault('');
			$table->addColumn('supported', 'tinyint', 3)->setDefault(1)->comment('If 0, this media type will not be listed as available, but will still be usable.');
			$table->addColumn('active', 'tinyint', 3)->setDefault(1);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('media_site_id');
		};

		$tables['xf_bookmark_item'] = function(Create $table)
		{
			$table->addColumn('bookmark_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('bookmark_date', 'int');
			$table->addColumn('message', 'text');
			$table->addColumn('labels', 'mediumblob');
			$table->addKey(['content_type', 'content_id']);
			$table->addUniqueKey(['user_id', 'content_type', 'content_id']);
		};

		$tables['xf_bookmark_label'] = function(Create $table)
		{
			$table->addColumn('label_id', 'int')->autoIncrement();
			$table->addColumn('label', 'varchar', 100);
			$table->addColumn('label_url', 'varchar', 100);
			$table->addColumn('user_id', 'int');
			$table->addColumn('use_count', 'int')->setDefault(0);
			$table->addColumn('last_use_date', 'int')->setDefault(0);
			$table->addUniqueKey(['label', 'user_id']);
			$table->addUniqueKey(['label_url', 'user_id']);
			$table->addKey('use_count');
		};

		$tables['xf_bookmark_label_use'] = function(Create $table)
		{
			$table->addColumn('label_id', 'int');
			$table->addColumn('bookmark_id', 'int');
			$table->addColumn('use_date', 'int')->setDefault(0);
			$table->addPrimaryKey(['label_id', 'bookmark_id']);
		};

		$tables['xf_captcha_question'] = function(Create $table)
		{
			$table->addColumn('captcha_question_id', 'int')->autoIncrement();
			$table->addColumn('question', 'varchar', 250);
			$table->addColumn('answers', 'blob')->comment('Serialized array of possible correct answers.');
			$table->addColumn('active', 'tinyint', 3)->setDefault(1);
			$table->addKey('active');
		};

		$tables['xf_captcha_log'] = function(Create $table)
		{
			$table->addColumn('hash', 'varbinary', 40);
			$table->addColumn('captcha_type', 'varchar', 250);
			$table->addColumn('captcha_data', 'varchar', 250);
			$table->addColumn('captcha_date', 'int');
			$table->addPrimaryKey('hash');
			$table->addKey('captcha_date');
		};

		$tables['xf_category'] = function(Create $table)
		{
			$table->addColumn('node_id', 'int');
			$table->addPrimaryKey('node_id');
		};

		$tables['xf_change_log'] = function(Create $table)
		{
			$table->addColumn('log_id', 'int')->autoIncrement();
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('edit_user_id', 'int');
			$table->addColumn('edit_date', 'int');
			$table->addColumn('field', 'varchar', 100)->setDefault('');
			$table->addColumn('old_value', 'text');
			$table->addColumn('new_value', 'text');
			$table->addColumn('protected', 'tinyint')->setDefault(0);
			$table->addKey(['content_type', 'content_id', 'edit_date'], 'content_type_content_id_date');
			$table->addKey(['content_type', 'edit_date'], 'content_type_date');
			$table->addKey('edit_date');
		};

		$tables['xf_class_extension'] = function(Create $table)
		{
			$table->addColumn('extension_id', 'int')->autoIncrement();
			$table->addColumn('from_class', 'varchar', 100);
			$table->addColumn('to_class', 'varchar', 100);
			$table->addColumn('execute_order', 'int');
			$table->addColumn('active', 'tinyint', 3);
			$table->addColumn('addon_id', 'varbinary', 50);
			$table->addUniqueKey(['from_class', 'to_class'], 'from_class');
		};

		$tables['xf_code_event'] = function(Create $table)
		{
			$table->addColumn('event_id', 'varbinary', 50);
			$table->addColumn('description', 'text');
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('event_id');
		};

		$tables['xf_code_event_listener'] = function(Create $table)
		{
			$table->addColumn('event_listener_id', 'int')->autoIncrement();
			$table->addColumn('event_id', 'varbinary', 50);
			$table->addColumn('execute_order', 'int');
			$table->addColumn('description', 'text');
			$table->addColumn('callback_class', 'varchar', 100);
			$table->addColumn('callback_method', 'varchar', 75);
			$table->addColumn('active', 'tinyint', 3);
			$table->addColumn('addon_id', 'varbinary', 50);
			$table->addColumn('hint', 'varchar', 255)->setDefault('');
			$table->addKey(['event_id', 'execute_order']);
			$table->addKey(['addon_id', 'event_id']);
		};

		$tables['xf_connected_account_provider'] = function(Create $table)
		{
			$table->addColumn('provider_id', 'varbinary', 25);
			$table->addColumn('provider_class', 'varchar', 100);
			$table->addColumn('display_order', 'smallint', 5);
			$table->addColumn('options', 'mediumblob');
			$table->addPrimaryKey('provider_id');
		};

		$tables['xf_content_spam_cache'] = function(Create $table)
		{
			$table->addColumn('spam_cache_id', 'int')->autoIncrement();
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('spam_params', 'mediumblob');
			$table->addColumn('insert_date', 'int');
			$table->addUniqueKey(['content_type', 'content_id'], 'content_type');
			$table->addKey('insert_date');
		};

		$tables['xf_content_type_field'] = function(Create $table)
		{
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('field_name', 'varbinary', 50);
			$table->addColumn('field_value', 'varchar', 75);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey(['content_type', 'field_name']);
			$table->addKey('field_name');
		};

		$tables['xf_conversation_master'] = function(Create $table)
		{
			$table->addColumn('conversation_id', 'int')->autoIncrement();
			$table->addColumn('title', 'varchar', 150);
			$table->addColumn('user_id', 'int');
			$table->addColumn('username', 'varchar', 50);
			$table->addColumn('start_date', 'int');
			$table->addColumn('open_invite', 'tinyint', 3)->setDefault(0);
			$table->addColumn('conversation_open', 'tinyint', 3)->setDefault(1);
			$table->addColumn('reply_count', 'int')->setDefault(0);
			$table->addColumn('recipient_count', 'int')->setDefault(0);
			$table->addColumn('first_message_id', 'int');
			$table->addColumn('last_message_date', 'int');
			$table->addColumn('last_message_id', 'int');
			$table->addColumn('last_message_user_id', 'int');
			$table->addColumn('last_message_username', 'varchar', 50);
			$table->addColumn('recipients', 'mediumblob');
			$table->addKey('user_id');
			$table->addKey('start_date');
		};

		$tables['xf_conversation_message'] = function(Create $table)
		{
			$table->addColumn('message_id', 'int')->autoIncrement();
			$table->addColumn('conversation_id', 'int');
			$table->addColumn('message_date', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('username', 'varchar', 50);
			$table->addColumn('message', 'mediumtext');
			$table->addColumn('attach_count', 'smallint', 5)->setDefault(0);
			$table->addColumn('ip_id', 'int')->setDefault(0);
			$table->addColumn('embed_metadata', 'blob')->nullable();
			$table->addColumn('reaction_score', 'int')->unsigned(false)->setDefault(0);
			$table->addColumn('reactions', 'blob')->nullable();
			$table->addColumn('reaction_users', 'blob');
			$table->addKey(['conversation_id', 'message_date']);
			$table->addKey('message_date');
			$table->addKey('user_id');
		};

		$tables['xf_conversation_recipient'] = function(Create $table)
		{
			$table->addColumn('conversation_id', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('recipient_state', 'enum')->values(['active','deleted','deleted_ignored']);
			$table->addColumn('last_read_date', 'int');
			$table->addPrimaryKey(['conversation_id', 'user_id']);
			$table->addKey('user_id');
		};

		$tables['xf_conversation_user'] = function(Create $table)
		{
			$table->addColumn('conversation_id', 'int');
			$table->addColumn('owner_user_id', 'int');
			$table->addColumn('is_unread', 'tinyint', 3);
			$table->addColumn('reply_count', 'int');
			$table->addColumn('last_message_date', 'int');
			$table->addColumn('last_message_id', 'int');
			$table->addColumn('last_message_user_id', 'int');
			$table->addColumn('last_message_username', 'varchar', 50);
			$table->addColumn('is_starred', 'tinyint', 3)->setDefault(0);
			$table->addPrimaryKey(['conversation_id', 'owner_user_id']);
			$table->addKey(['owner_user_id', 'last_message_date']);
			$table->addKey(['owner_user_id', 'is_unread']);
			$table->addKey(['owner_user_id', 'is_starred', 'last_message_date'], 'owner_starred_date');
		};

		$tables['xf_css_cache'] = function(Create $table)
		{
			$table->addColumn('cache_id', 'int')->autoIncrement();
			$table->addColumn('style_id', 'int');
			$table->addColumn('language_id', 'int');
			$table->addColumn('title', 'varbinary', 150);
			$table->addColumn('modifier_key', 'varbinary', 32);
			$table->addColumn('output', 'mediumblob');
			$table->addColumn('cache_date', 'int');
			$table->addUniqueKey(['style_id', 'language_id', 'title', 'modifier_key'], 'style_language_title_modifier');
		};

		$tables['xf_cron_entry'] = function(Create $table)
		{
			$table->addColumn('entry_id', 'varbinary', 25);
			$table->addColumn('cron_class', 'varchar', 100);
			$table->addColumn('cron_method', 'varchar', 75);
			$table->addColumn('run_rules', 'mediumblob');
			$table->addColumn('active', 'tinyint', 3);
			$table->addColumn('next_run', 'int');
			$table->addColumn('addon_id', 'varbinary', 50);
			$table->addPrimaryKey('entry_id');
			$table->addKey(['active', 'next_run']);
		};

		$tables['xf_data_registry'] = function(Create $table)
		{
			$table->addColumn('data_key', 'varbinary', 25);
			$table->addColumn('data_value', 'mediumblob');
			$table->addPrimaryKey('data_key');
		};

		$tables['xf_deletion_log'] = function(Create $table)
		{
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('delete_date', 'int');
			$table->addColumn('delete_user_id', 'int');
			$table->addColumn('delete_username', 'varchar', 50);
			$table->addColumn('delete_reason', 'varchar', 100)->setDefault('');
			$table->addPrimaryKey(['content_type', 'content_id']);
			$table->addKey(['delete_user_id', 'delete_date'], 'delete_user_id_date');
		};

		$tables['xf_draft'] = function(Create $table)
		{
			$table->addColumn('draft_id', 'int')->autoIncrement();
			$table->addColumn('draft_key', 'varbinary', 75);
			$table->addColumn('user_id', 'int');
			$table->addColumn('last_update', 'int');
			$table->addColumn('message', 'mediumtext');
			$table->addColumn('extra_data', 'mediumblob');
			$table->addUniqueKey(['draft_key', 'user_id'], 'draft_key_user');
			$table->addKey('last_update');
		};

		$tables['xf_edit_history'] = function(Create $table)
		{
			$table->addColumn('edit_history_id', 'int')->autoIncrement();
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('edit_user_id', 'int');
			$table->addColumn('edit_date', 'int');
			$table->addColumn('old_text', 'mediumtext');
			$table->addKey(['content_type', 'content_id', 'edit_date'], 'content_type');
			$table->addKey('edit_date');
			$table->addKey('edit_user_id');
		};

		$tables['xf_editor_dropdown'] = function(Create $table)
		{
			$table->addColumn('cmd', 'varbinary', 50);
			$table->addColumn('icon', 'varchar', 50)->setDefault('')->comment('Optional icon');
			$table->addColumn('buttons', 'blob');
			$table->addColumn('display_order', 'int')->setDefault(0);
			$table->addColumn('active', 'tinyint')->setDefault(1);
			$table->addPrimaryKey('cmd');
		};

		$tables['xf_email_bounce_log'] = function(Create $table)
		{
			$table->addColumn('bounce_id', 'int')->autoIncrement();
			$table->addColumn('log_date', 'int');
			$table->addColumn('email_date', 'int');
			$table->addColumn('message_type', 'varchar', 25);
			$table->addColumn('action_taken', 'varchar', 25);
			$table->addColumn('user_id', 'int')->nullable();
			$table->addColumn('recipient', 'varchar', 255)->nullable();
			$table->addColumn('raw_message', 'mediumblob');
			$table->addColumn('status_code', 'varchar', 25)->nullable();
			$table->addColumn('diagnostic_info', 'text')->nullable();
			$table->addKey('log_date');
		};

		$tables['xf_email_bounce_soft'] = function(Create $table)
		{
			$table->addColumn('bounce_soft_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('bounce_date', 'date');
			$table->addColumn('bounce_total', 'smallint', 5);
			$table->addUniqueKey(['user_id', 'bounce_date'], 'user_id');
			$table->addKey('bounce_date');
		};

		$tables['xf_error_log'] = function(Create $table)
		{
			$table->addColumn('error_id', 'int')->autoIncrement();
			$table->addColumn('exception_date', 'int');
			$table->addColumn('user_id', 'int')->nullable();
			$table->addColumn('ip_address', 'varbinary', 16)->setDefault('');
			$table->addColumn('exception_type', 'varchar', 75);
			$table->addColumn('message', 'text');
			$table->addColumn('filename', 'varchar', 255);
			$table->addColumn('line', 'int');
			$table->addColumn('trace_string', 'mediumtext');
			$table->addColumn('request_state', 'mediumblob');
			$table->addKey('exception_date');
		};

		$tables['xf_feed'] = function(Create $table)
		{
			$table->addColumn('feed_id', 'int')->autoIncrement();
			$table->addColumn('title', 'varchar', 250);
			$table->addColumn('url', 'varchar', 2083);
			$table->addColumn('frequency', 'int')->setDefault(1800);
			$table->addColumn('node_id', 'int');
			$table->addColumn('user_id', 'int')->setDefault(0);
			$table->addColumn('prefix_id', 'int')->setDefault(0);
			$table->addColumn('title_template', 'varchar', 250)->setDefault('');
			$table->addColumn('message_template', 'mediumtext');
			$table->addColumn('discussion_visible', 'tinyint', 3)->setDefault(1);
			$table->addColumn('discussion_open', 'tinyint', 3)->setDefault(1);
			$table->addColumn('discussion_sticky', 'tinyint', 3)->setDefault(0);
			$table->addColumn('last_fetch', 'int')->setDefault(0);
			$table->addColumn('active', 'int')->setDefault(0);
			$table->addKey('active');
		};

		$tables['xf_feed_log'] = function(Create $table)
		{
			$table->addColumn('feed_id', 'int');
			$table->addColumn('unique_id', 'varbinary', 250);
			$table->addColumn('hash', 'char', 32)->comment('MD5(title + content)');
			$table->addColumn('thread_id', 'int');
			$table->addPrimaryKey(['feed_id', 'unique_id']);
		};

		$tables['xf_file_check'] = function(Create $table)
		{
			$table->addColumn('check_id', 'int')->autoIncrement();
			$table->addColumn('check_date', 'int');
			$table->addColumn('check_state', 'enum')->values(['pending','success','failure'])->setDefault('pending');
			$table->addColumn('check_hash', 'varbinary', 64)->setDefault('');
			$table->addColumn('total_missing', 'int')->setDefault(0);
			$table->addColumn('total_inconsistent', 'int')->setDefault(0);
			$table->addColumn('total_checked', 'int')->setDefault(0);
		};

		$tables['xf_find_new'] = function(Create $table)
		{
			$table->addColumn('find_new_id', 'int')->autoIncrement();
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('filters', 'mediumblob');
			$table->addColumn('filter_hash', 'varbinary', 32)->setDefault('');
			$table->addColumn('user_id', 'int');
			$table->addColumn('results', 'mediumblob');
			$table->addColumn('cache_date', 'int');
			$table->addKey('cache_date');
			$table->addKey(['content_type', 'user_id', 'cache_date'], 'type_user_date');
		};

		$tables['xf_find_new_default'] = function(Create $table)
		{
			$table->addColumn('find_new_default_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('filters', 'blob');
			$table->addUniqueKey(['user_id', 'content_type'], 'user_content');
		};

		$tables['xf_flood_check'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('flood_action', 'varchar', 25);
			$table->addColumn('flood_time', 'int');
			$table->addPrimaryKey(['user_id', 'flood_action']);
			$table->addKey('flood_time');
		};

		$tables['xf_forum'] = function(Create $table)
		{
			$table->addColumn('node_id', 'int');
			$table->addColumn('discussion_count', 'int')->setDefault(0);
			$table->addColumn('message_count', 'int')->setDefault(0);
			$table->addColumn('last_post_id', 'int')->setDefault(0)->comment('Most recent post_id');
			$table->addColumn('last_post_date', 'int')->setDefault(0)->comment('Date of most recent post');
			$table->addColumn('last_post_user_id', 'int')->setDefault(0)->comment('User_id of user posting most recently');
			$table->addColumn('last_post_username', 'varchar', 50)->setDefault('')->comment('Username of most recently-posting user');
			$table->addColumn('last_thread_id', 'int')->setDefault(0)->comment('Most recent thread_id');
			$table->addColumn('last_thread_title', 'varchar', 150)->setDefault('')->comment('Title of thread most recent post is in');
			$table->addColumn('last_thread_prefix_id', 'int')->setDefault(0);
			$table->addColumn('moderate_threads', 'tinyint', 3)->setDefault(0);
			$table->addColumn('moderate_replies', 'tinyint', 3)->setDefault(0);
			$table->addColumn('allow_posting', 'tinyint', 3)->setDefault(1);
			$table->addColumn('allow_poll', 'tinyint', 3)->setDefault(1);
			$table->addColumn('count_messages', 'tinyint', 3)->setDefault(1)->comment('If not set, messages posted (directly) within this forum will not contribute to user message totals.');
			$table->addColumn('find_new', 'tinyint', 3)->setDefault(1)->comment('Include posts from this forum when running /find-new/threads');
			$table->addColumn('field_cache', 'mediumblob')->comment('Serialized data from xf_thread_field');
			$table->addColumn('prefix_cache', 'mediumblob')->comment('Serialized data from xf_forum_prefix, [group_id][prefix_id] => prefix_id');
			$table->addColumn('prompt_cache', 'mediumblob')->comment('JSON data from xf_forum_prompt');
			$table->addColumn('default_prefix_id', 'int')->setDefault(0);
			$table->addColumn('default_sort_order', 'varchar', 25)->setDefault('last_post_date');
			$table->addColumn('default_sort_direction', 'varchar', 5)->setDefault('desc');
			$table->addColumn('list_date_limit_days', 'smallint', 5)->setDefault(0);
			$table->addColumn('require_prefix', 'tinyint', 3)->setDefault(0);
			$table->addColumn('allowed_watch_notifications', 'varchar', 10)->setDefault('all');
			$table->addColumn('min_tags', 'smallint', 5)->setDefault(0);
			$table->addPrimaryKey('node_id');
		};

		$tables['xf_forum_field'] = function(Create $table)
		{
			$table->addColumn('node_id', 'int');
			$table->addColumn('field_id', 'varbinary', 25);
			$table->addPrimaryKey(['node_id', 'field_id']);
			$table->addKey('field_id');
		};

		$tables['xf_forum_prefix'] = function(Create $table)
		{
			$table->addColumn('node_id', 'int');
			$table->addColumn('prefix_id', 'int');
			$table->addPrimaryKey(['node_id', 'prefix_id']);
			$table->addKey('prefix_id');
		};

		$tables['xf_forum_prompt'] = function(Create $table)
		{
			$table->addColumn('node_id', 'int');
			$table->addColumn('prompt_id', 'int');
			$table->addPrimaryKey(['node_id', 'prompt_id']);
		};

		$tables['xf_forum_read'] = function(Create $table)
		{
			$table->addColumn('forum_read_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('node_id', 'int');
			$table->addColumn('forum_read_date', 'int');
			$table->addUniqueKey(['user_id', 'node_id']);
			$table->addKey('node_id');
			$table->addKey('forum_read_date');
		};

		$tables['xf_forum_watch'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('node_id', 'int');
			$table->addColumn('notify_on', 'enum')->values(['','thread','message']);
			$table->addColumn('send_alert', 'tinyint', 3);
			$table->addColumn('send_email', 'tinyint', 3);
			$table->addPrimaryKey(['user_id', 'node_id']);
			$table->addKey(['node_id', 'notify_on']);
		};

		$tables['xf_help_page'] = function(Create $table)
		{
			$table->addColumn('page_id', 'varbinary', 50);
			$table->addColumn('page_name', 'varchar', 50);
			$table->addColumn('display_order', 'int')->setDefault(0);
			$table->addColumn('callback_class', 'varchar', 100)->setDefault('');
			$table->addColumn('callback_method', 'varchar', 75)->setDefault('');
			$table->addColumn('advanced_mode', 'tinyint')->setDefault(0);
			$table->addColumn('active', 'tinyint')->setDefault(1);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('page_id');
			$table->addUniqueKey('page_name');
			$table->addKey('display_order');
		};

		$tables['xf_image_proxy'] = function(Create $table)
		{
			$table->addColumn('image_id', 'int')->autoIncrement();
			$table->addColumn('url', 'text');
			$table->addColumn('url_hash', 'varbinary', 32);
			$table->addColumn('file_size', 'int')->setDefault(0);
			$table->addColumn('file_name', 'varchar', 250)->setDefault('');
			$table->addColumn('mime_type', 'varchar', 100)->setDefault('');
			$table->addColumn('fetch_date', 'int')->setDefault(0);
			$table->addColumn('first_request_date', 'int')->setDefault(0);
			$table->addColumn('last_request_date', 'int')->setDefault(0);
			$table->addColumn('views', 'int')->setDefault(0);
			$table->addColumn('pruned', 'int')->setDefault(0);
			$table->addColumn('is_processing', 'int')->setDefault(0);
			$table->addColumn('failed_date', 'int')->setDefault(0);
			$table->addColumn('fail_count', 'smallint', 5)->setDefault(0);
			$table->addUniqueKey('url_hash');
			$table->addKey(['pruned', 'fetch_date']);
			$table->addKey('last_request_date');
			$table->addKey('is_processing');
		};

		$tables['xf_image_proxy_referrer'] = function(Create $table)
		{
			$table->addColumn('referrer_id', 'int')->autoIncrement();
			$table->addColumn('image_id', 'int');
			$table->addColumn('referrer_hash', 'varbinary', 32);
			$table->addColumn('referrer_url', 'text');
			$table->addColumn('hits', 'int');
			$table->addColumn('first_date', 'int');
			$table->addColumn('last_date', 'int');
			$table->addUniqueKey(['image_id', 'referrer_hash'], 'image_id_hash');
			$table->addKey('last_date');
		};

		$tables['xf_ip'] = function(Create $table)
		{
			$table->addColumn('ip_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('action', 'varbinary', 25)->setDefault('');
			$table->addColumn('ip', 'varbinary', 16);
			$table->addColumn('log_date', 'int');
			$table->addKey(['user_id', 'log_date']);
			$table->addKey(['ip', 'log_date']);
			$table->addKey(['content_type', 'content_id']);
			$table->addKey('log_date');
		};

		$tables['xf_ip_match'] = function(Create $table)
		{
			$table->addColumn('ip', 'varchar', 43);
			$table->addColumn('match_type', 'enum')->values(['banned','discouraged'])->setDefault('banned');
			$table->addColumn('first_byte', 'binary', 1);
			$table->addColumn('start_range', 'varbinary', 16);
			$table->addColumn('end_range', 'varbinary', 16);
			$table->addColumn('create_user_id', 'int')->setDefault(0);
			$table->addColumn('create_date', 'int')->setDefault(0);
			$table->addColumn('reason', 'varchar', 255)->setDefault('');
			$table->addColumn('last_triggered_date', 'int')->setDefault(0);
			$table->addPrimaryKey(['ip', 'match_type']);
			$table->addKey('start_range');
			$table->addKey('create_date');
		};

		$tables['xf_job'] = function(Create $table)
		{
			$table->addColumn('job_id', 'int')->autoIncrement();
			$table->addColumn('unique_key', 'varbinary', 50)->nullable();
			$table->addColumn('execute_class', 'varchar', 100);
			$table->addColumn('execute_data', 'mediumblob');
			$table->addColumn('manual_execute', 'tinyint');
			$table->addColumn('trigger_date', 'int');
			$table->addColumn('last_run_date', 'int')->nullable();
			$table->addUniqueKey('unique_key');
			$table->addKey('trigger_date');
			$table->addKey(['manual_execute', 'trigger_date'], 'manual_execute_date');
		};

		$tables['xf_json_convert_error'] = function(Create $table)
		{
			$table->addColumn('error_id', 'int')->autoIncrement();
			$table->addColumn('table_name', 'varbinary', 100);
			$table->addColumn('column_name', 'varbinary', 100);
			$table->addColumn('pk_id', 'int');
			$table->addColumn('original_value', 'mediumblob');
			$table->addUniqueKey(['table_name', 'column_name', 'pk_id'], 'table_column_pk');
		};

		$tables['xf_language'] = function(Create $table)
		{
			$table->addColumn('language_id', 'int')->autoIncrement();
			$table->addColumn('parent_id', 'int');
			$table->addColumn('parent_list', 'varbinary', 100);
			$table->addColumn('title', 'varchar', 50);
			$table->addColumn('date_format', 'varchar', 30);
			$table->addColumn('time_format', 'varchar', 15);
			$table->addColumn('currency_format', 'varchar', 30);
			$table->addColumn('decimal_point', 'varchar', 1);
			$table->addColumn('thousands_separator', 'varchar', 1);
			$table->addColumn('phrase_cache', 'mediumblob');
			$table->addColumn('language_code', 'varchar', 25)->setDefault('');
			$table->addColumn('text_direction', 'enum')->values(['LTR','RTL'])->setDefault('LTR');
			$table->addColumn('week_start', 'tinyint', 3)->setDefault(0)->comment('Week start day. 0 = Sunday, 6 = Saturday');
			$table->addColumn('label_separator', 'varchar', 15)->setDefault(':');
			$table->addColumn('comma_separator', 'varchar', 15)->setDefault(', ');
			$table->addColumn('ellipsis', 'varchar', 15)->setDefault('...');
			$table->addColumn('parenthesis_open', 'varchar', 15)->setDefault('(');
			$table->addColumn('parenthesis_close', 'varchar', 15)->setDefault(')');
		};

		$tables['xf_link_proxy'] = function(Create $table)
		{
			$table->addColumn('link_id', 'int')->autoIncrement();
			$table->addColumn('url', 'text');
			$table->addColumn('url_hash', 'varbinary', 32);
			$table->addColumn('first_request_date', 'int')->setDefault(0);
			$table->addColumn('last_request_date', 'int')->setDefault(0);
			$table->addColumn('hits', 'int')->setDefault(0);
			$table->addUniqueKey('url_hash');
			$table->addKey('last_request_date');
		};

		$tables['xf_link_proxy_referrer'] = function(Create $table)
		{
			$table->addColumn('referrer_id', 'int')->autoIncrement();
			$table->addColumn('link_id', 'int');
			$table->addColumn('referrer_hash', 'varbinary', 32);
			$table->addColumn('referrer_url', 'text');
			$table->addColumn('hits', 'int');
			$table->addColumn('first_date', 'int');
			$table->addColumn('last_date', 'int');
			$table->addUniqueKey(['link_id', 'referrer_hash'], 'link_id_hash');
			$table->addKey('last_date');
		};

		$tables['xf_link_forum'] = function(Create $table)
		{
			$table->addColumn('node_id', 'int');
			$table->addColumn('link_url', 'varchar', 150);
			$table->addColumn('redirect_count', 'int')->setDefault(0);
			$table->addPrimaryKey('node_id');
		};

		$tables['xf_login_attempt'] = function(Create $table)
		{
			$table->addColumn('attempt_id', 'int')->autoIncrement();
			$table->addColumn('login', 'varchar', 60);
			$table->addColumn('ip_address', 'varbinary', 16);
			$table->addColumn('attempt_date', 'int');
			$table->addKey(['login', 'ip_address', 'attempt_date'], 'login_check');
			$table->addKey('attempt_date');
			$table->addKey(['ip_address', 'attempt_date']);
		};

		$tables['xf_mail_queue'] = function(Create $table)
		{
			$table->addColumn('mail_queue_id', 'int')->autoIncrement();
			$table->addColumn('mail_data', 'mediumblob');
			$table->addColumn('queue_date', 'int');
			$table->addColumn('send_date', 'int')->setDefault(0);
			$table->addColumn('fail_date', 'int')->nullable()->setDefault(null);
			$table->addColumn('fail_count', 'int')->setDefault(0);
			$table->addKey(['send_date', 'queue_date']);
		};

		$tables['xf_member_stat'] = function(Create $table)
		{
			$table->addColumn('member_stat_id', 'int')->autoIncrement();
			$table->addColumn('member_stat_key', 'varbinary', 50);
			$table->addColumn('criteria', 'blob')->nullable();
			$table->addColumn('callback_class', 'varchar', 100)->setDefault('');
			$table->addColumn('callback_method', 'varchar', 75)->setDefault('');
			$table->addColumn('sort_order', 'varchar', 50)->setDefault('message_count');
			$table->addColumn('sort_direction', 'varchar', 5)->setDefault('desc');
			$table->addColumn('permission_limit', 'varbinary', 51)->setDefault('');
			$table->addColumn('show_value', 'tinyint')->setDefault(1);
			$table->addColumn('user_limit', 'int')->setDefault(20);
			$table->addColumn('display_order', 'int')->setDefault(0);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addColumn('overview_display', 'tinyint')->setDefault(1);
			$table->addColumn('active', 'tinyint')->setDefault(1);
			$table->addColumn('cache_lifetime', 'int')->setDefault(60);
			$table->addColumn('cache_expiry', 'int')->setDefault(0);
			$table->addColumn('cache_results', 'blob')->nullable();
			$table->addUniqueKey('member_stat_key');
			$table->addKey('display_order');
		};

		$tables['xf_moderator'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('is_super_moderator', 'tinyint', 3);
			$table->addColumn('extra_user_group_ids', 'varbinary', 255);
			$table->addPrimaryKey('user_id');
		};

		$tables['xf_moderator_content'] = function(Create $table)
		{
			$table->addColumn('moderator_id', 'int')->autoIncrement();
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('user_id', 'int');
			$table->addUniqueKey(['content_type', 'content_id', 'user_id'], 'content_user_id');
			$table->addKey('user_id');
		};

		$tables['xf_moderator_log'] = function(Create $table)
		{
			$table->addColumn('moderator_log_id', 'int')->autoIncrement();
			$table->addColumn('log_date', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('ip_address', 'varbinary', 16)->setDefault('');
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('content_user_id', 'int');
			$table->addColumn('content_username', 'varchar', 50);
			$table->addColumn('content_title', 'varchar', 150);
			$table->addColumn('content_url', 'text');
			$table->addColumn('discussion_content_type', 'varchar', 25);
			$table->addColumn('discussion_content_id', 'int');
			$table->addColumn('action', 'varchar', 25);
			$table->addColumn('action_params', 'mediumblob');
			$table->addKey('log_date');
			$table->addKey(['content_type', 'content_id'], 'content_type_id');
			$table->addKey(['discussion_content_type', 'discussion_content_id'], 'discussion_content_type_id');
			$table->addKey(['user_id', 'log_date']);
		};

		$tables['xf_navigation'] = function(Create $table)
		{
			$table->addColumn('navigation_id', 'varbinary', 50);
			$table->addColumn('parent_navigation_id', 'varbinary', 50);
			$table->addColumn('display_order', 'int')->setDefault(0);
			$table->addColumn('navigation_type_id', 'varbinary', 25);
			$table->addColumn('type_config', 'mediumtext');
			$table->addColumn('condition_expression', 'mediumblob');
			$table->addColumn('condition_setup', 'mediumblob');
			$table->addColumn('data_expression', 'mediumblob');
			$table->addColumn('data_setup', 'mediumblob');
			$table->addColumn('global_setup', 'mediumblob');
			$table->addColumn('enabled', 'tinyint')->setDefault(0);
			$table->addColumn('is_customized', 'tinyint')->setDefault(0);
			$table->addColumn('default_value', 'mediumblob');
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('navigation_id');
			$table->addKey(['parent_navigation_id', 'display_order']);
		};

		$tables['xf_navigation_type'] = function(Create $table)
		{
			$table->addColumn('navigation_type_id', 'varbinary', 25);
			$table->addColumn('handler_class', 'varchar', 100);
			$table->addColumn('display_order', 'int')->setDefault(0);
			$table->addPrimaryKey('navigation_type_id');
		};

		$tables['xf_news_feed'] = function(Create $table)
		{
			$table->addColumn('news_feed_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int')->comment('The user who performed the action');
			$table->addColumn('username', 'varchar', 50)->setDefault('')->comment('Corresponds to user_id');
			$table->addColumn('content_type', 'varbinary', 25)->comment('eg: thread');
			$table->addColumn('content_id', 'int');
			$table->addColumn('action', 'varchar', 25)->comment('eg: edit');
			$table->addColumn('event_date', 'int');
			$table->addColumn('extra_data', 'mediumblob')->comment('Serialized. Stores any extra data relevant to the action');
			$table->addKey(['user_id', 'event_date'], 'userId_eventDate');
			$table->addKey(['content_type', 'content_id'], 'contentType_contentId');
			$table->addKey('event_date');
		};

		$tables['xf_node'] = function(Create $table)
		{
			$table->addColumn('node_id', 'int')->autoIncrement();
			$table->addColumn('title', 'varchar', 50);
			$table->addColumn('description', 'text');
			$table->addColumn('node_name', 'varchar', 50)->nullable()->comment('Unique column used as string ID by some node types');
			$table->addColumn('node_type_id', 'varbinary', 25);
			$table->addColumn('parent_node_id', 'int')->setDefault(0);
			$table->addColumn('display_order', 'int')->setDefault(1);
			$table->addColumn('display_in_list', 'tinyint', 3)->setDefault(1)->comment('If 0, hidden from node list. Still counts for lft/rgt.');
			$table->addColumn('lft', 'int')->setDefault(0)->comment('Nested set info \'left\' value');
			$table->addColumn('rgt', 'int')->setDefault(0)->comment('Nested set info \'right\' value');
			$table->addColumn('depth', 'int')->setDefault(0)->comment('Depth = 0: no parent');
			$table->addColumn('style_id', 'int')->setDefault(0)->comment('Style override for specific node');
			$table->addColumn('effective_style_id', 'int')->setDefault(0)->comment('Style override; pushed down tree');
			$table->addColumn('breadcrumb_data', 'blob')->nullable();
			$table->addColumn('navigation_id', 'varbinary', 50)->setDefault('');
			$table->addColumn('effective_navigation_id', 'varbinary', 50)->setDefault('');
			$table->addUniqueKey(['node_name', 'node_type_id'], 'node_name_unique');
			$table->addKey('parent_node_id');
			$table->addKey('display_order');
			$table->addKey(['display_in_list', 'lft'], 'display_in_list');
			$table->addKey('lft');
		};

		$tables['xf_node_type'] = function(Create $table)
		{
			$table->addColumn('node_type_id', 'varbinary', 25);
			$table->addColumn('entity_identifier', 'varchar', 75);
			$table->addColumn('permission_group_id', 'varchar', 25)->setDefault('');
			$table->addColumn('admin_route', 'varchar', 75);
			$table->addColumn('public_route', 'varchar', 75);
			$table->addColumn('handler_class', 'varchar', 100)->setDefault('');
			$table->addPrimaryKey('node_type_id');
		};

		$tables['xf_notice'] = function(Create $table)
		{
			$table->addColumn('notice_id', 'int')->autoIncrement();
			$table->addColumn('title', 'varchar', 150);
			$table->addColumn('message', 'mediumtext');
			$table->addColumn('active', 'tinyint', 3)->setDefault(1);
			$table->addColumn('display_order', 'int')->setDefault(0);
			$table->addColumn('dismissible', 'tinyint', 3)->setDefault(1)->comment('Notice may be hidden when read by users');
			$table->addColumn('user_criteria', 'mediumblob');
			$table->addColumn('page_criteria', 'mediumblob');
			$table->addColumn('display_image', 'enum')->values(['','avatar','image'])->setDefault('');
			$table->addColumn('image_url', 'varchar', 200)->setDefault('');
			$table->addColumn('visibility', 'enum')->values(['','wide','medium','narrow'])->setDefault('');
			$table->addColumn('notice_type', 'varchar', 25)->setDefault('block');
			$table->addColumn('display_style', 'varchar', 25)->setDefault('');
			$table->addColumn('css_class', 'varchar', 50)->setDefault('');
			$table->addColumn('display_duration', 'int')->setDefault(0);
			$table->addColumn('delay_duration', 'int')->setDefault(0);
			$table->addColumn('auto_dismiss', 'tinyint', 3)->setDefault(0);
		};

		$tables['xf_notice_dismissed'] = function(Create $table)
		{
			$table->addColumn('notice_id', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('dismiss_date', 'int')->setDefault(0);
			$table->addPrimaryKey(['notice_id', 'user_id']);
			$table->addKey('user_id');
		};

		$tables['xf_oembed'] = function(Create $table)
		{
			$table->addColumn('oembed_id', 'int')->autoIncrement();
			$table->addColumn('media_site_id', 'varbinary', 25);
			$table->addColumn('media_id', 'varbinary', 250);
			$table->addColumn('media_hash', 'varbinary', 32);
			$table->addColumn('title', 'mediumtext')->nullable();
			$table->addColumn('fetch_date', 'int')->setDefault(0);
			$table->addColumn('first_request_date', 'int')->setDefault(0);
			$table->addColumn('last_request_date', 'int')->setDefault(0);
			$table->addColumn('views', 'int')->setDefault(0);
			$table->addColumn('pruned', 'int')->setDefault(0);
			$table->addColumn('is_processing', 'int')->setDefault(0);
			$table->addColumn('failed_date', 'int')->setDefault(0);
			$table->addColumn('fail_count', 'smallint', 5)->setDefault(0);
			$table->addUniqueKey('media_hash');
			$table->addKey(['pruned', 'fetch_date']);
			$table->addKey('last_request_date');
			$table->addKey('is_processing');
		};

		$tables['xf_oembed_referrer'] = function(Create $table)
		{
			$table->addColumn('referrer_id', 'int')->autoIncrement();
			$table->addColumn('oembed_id', 'int');
			$table->addColumn('referrer_hash', 'varbinary', 32);
			$table->addColumn('referrer_url', 'text');
			$table->addColumn('hits', 'int');
			$table->addColumn('first_date', 'int');
			$table->addColumn('last_date', 'int');
			$table->addUniqueKey(['oembed_id', 'referrer_hash'], 'oembed_id_hash');
			$table->addKey('last_date');
		};

		$tables['xf_option'] = function(Create $table)
		{
			$table->addColumn('option_id', 'varbinary', 50);
			$table->addColumn('option_value', 'mediumblob');
			$table->addColumn('default_value', 'mediumblob');
			$table->addColumn('edit_format', 'enum')->values(['textbox','spinbox','onoff','radio','select','checkbox','template','callback','onofftextbox','username']);
			$table->addColumn('edit_format_params', 'mediumtext');
			$table->addColumn('data_type', 'enum')->values(['string','integer','numeric','array','boolean','positive_integer','unsigned_integer','unsigned_numeric']);
			$table->addColumn('sub_options', 'mediumtext');
			$table->addColumn('validation_class', 'varchar', 100);
			$table->addColumn('validation_method', 'varchar', 75);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('option_id');
		};

		$tables['xf_option_group'] = function(Create $table)
		{
			$table->addColumn('group_id', 'varbinary', 50);
			$table->addColumn('icon', 'varchar', 50)->setDefault('')->comment('Optional icon');
			$table->addColumn('display_order', 'int');
			$table->addColumn('debug_only', 'tinyint', 3);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('group_id');
			$table->addKey('display_order');
		};

		$tables['xf_option_group_relation'] = function(Create $table)
		{
			$table->addColumn('option_id', 'varbinary', 50);
			$table->addColumn('group_id', 'varbinary', 50);
			$table->addColumn('display_order', 'int');
			$table->addPrimaryKey(['option_id', 'group_id']);
			$table->addKey(['group_id', 'display_order']);
		};

		$tables['xf_page'] = function(Create $table)
		{
			$table->addColumn('node_id', 'int');
			$table->addColumn('publish_date', 'int');
			$table->addColumn('modified_date', 'int')->setDefault(0);
			$table->addColumn('view_count', 'int')->setDefault(0);
			$table->addColumn('log_visits', 'tinyint', 3)->setDefault(0);
			$table->addColumn('list_siblings', 'tinyint', 3)->setDefault(0);
			$table->addColumn('list_children', 'tinyint', 3)->setDefault(0);
			$table->addColumn('callback_class', 'varchar', 100)->setDefault('');
			$table->addColumn('callback_method', 'varchar', 75)->setDefault('');
			$table->addColumn('advanced_mode', 'tinyint')->setDefault(0);
			$table->addPrimaryKey('node_id');
		};

		$tables['xf_payment_profile'] = function(Create $table)
		{
			$table->addColumn('payment_profile_id', 'int')->autoIncrement();
			$table->addColumn('provider_id', 'varbinary', 25);
			$table->addColumn('title', 'varchar', 100)->setDefault('');
			$table->addColumn('display_title', 'varchar', 100)->setDefault('');
			$table->addColumn('options', 'blob')->nullable();
			$table->addColumn('active', 'tinyint', 3)->setDefault(1);
		};

		$tables['xf_payment_provider'] = function(Create $table)
		{
			$table->addColumn('provider_id', 'varbinary', 25);
			$table->addColumn('provider_class', 'varchar', 100);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('provider_id');
		};

		$tables['xf_payment_provider_log'] = function(Create $table)
		{
			$table->addColumn('provider_log_id', 'int')->autoIncrement();
			$table->addColumn('purchase_request_key', 'varbinary', 32)->nullable();
			$table->addColumn('provider_id', 'varbinary', 25);
			$table->addColumn('transaction_id', 'varchar', 100)->nullable();
			$table->addColumn('subscriber_id', 'varchar', 100)->nullable();
			$table->addColumn('log_type', 'enum')->values(['payment','cancel','info','error']);
			$table->addColumn('log_message', 'varchar', 255)->setDefault('');
			$table->addColumn('log_details', 'mediumblob');
			$table->addColumn('log_date', 'int')->setDefault(0);
			$table->addKey('transaction_id');
			$table->addKey('subscriber_id');
			$table->addKey('log_date');
		};

		$tables['xf_permission'] = function(Create $table)
		{
			$table->addColumn('permission_id', 'varbinary', 25);
			$table->addColumn('permission_group_id', 'varbinary', 25);
			$table->addColumn('permission_type', 'enum')->values(['flag','integer']);
			$table->addColumn('interface_group_id', 'varbinary', 50);
			$table->addColumn('depend_permission_id', 'varbinary', 25);
			$table->addColumn('display_order', 'int');
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey(['permission_id', 'permission_group_id']);
			$table->addKey('display_order');
		};

		$tables['xf_permission_cache_content'] = function(Create $table)
		{
			$table->addColumn('permission_combination_id', 'int');
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('cache_value', 'mediumblob');
			$table->addPrimaryKey(['permission_combination_id', 'content_type', 'content_id']);
		};

		$tables['xf_permission_combination'] = function(Create $table)
		{
			$table->addColumn('permission_combination_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('user_group_list', 'mediumblob');
			$table->addColumn('cache_value', 'mediumblob');
			$table->addKey('user_id');
		};

		$tables['xf_permission_combination_user_group'] = function(Create $table)
		{
			$table->addColumn('user_group_id', 'int');
			$table->addColumn('permission_combination_id', 'int');
			$table->addPrimaryKey(['user_group_id', 'permission_combination_id']);
			$table->addKey('permission_combination_id');
		};

		$tables['xf_permission_entry'] = function(Create $table)
		{
			$table->addColumn('permission_entry_id', 'int')->autoIncrement();
			$table->addColumn('user_group_id', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('permission_group_id', 'varbinary', 25);
			$table->addColumn('permission_id', 'varbinary', 25);
			$table->addColumn('permission_value', 'enum')->values(['unset','allow','deny','use_int']);
			$table->addColumn('permission_value_int', 'int', 11)->unsigned(false);
			$table->addUniqueKey(['user_group_id', 'user_id', 'permission_group_id', 'permission_id'], 'unique_permission');
		};

		$tables['xf_permission_entry_content'] = function(Create $table)
		{
			$table->addColumn('permission_entry_id', 'int')->autoIncrement();
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('user_group_id', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('permission_group_id', 'varbinary', 25);
			$table->addColumn('permission_id', 'varbinary', 25);
			$table->addColumn('permission_value', 'enum')->values(['unset','reset','content_allow','deny','use_int']);
			$table->addColumn('permission_value_int', 'int', 11)->unsigned(false);
			$table->addUniqueKey(['user_group_id', 'user_id', 'content_type', 'content_id', 'permission_group_id', 'permission_id'], 'user_group_id_unique');
			$table->addKey(['content_type', 'content_id']);
		};

		$tables['xf_permission_interface_group'] = function(Create $table)
		{
			$table->addColumn('interface_group_id', 'varbinary', 50);
			$table->addColumn('display_order', 'int');
			$table->addColumn('is_moderator', 'tinyint', 3)->setDefault(0);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('interface_group_id');
			$table->addKey('display_order');
		};

		$tables['xf_phrase'] = function(Create $table)
		{
			$table->addColumn('phrase_id', 'int')->autoIncrement();
			$table->addColumn('language_id', 'int');
			$table->addColumn('title', 'varbinary', 100);
			$table->addColumn('phrase_text', 'mediumtext');
			$table->addColumn('global_cache', 'tinyint', 3)->setDefault(0);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addColumn('version_id', 'int')->setDefault(0);
			$table->addColumn('version_string', 'varchar', 30)->setDefault('');
			$table->addUniqueKey(['title', 'language_id'], 'title');
			$table->addKey(['language_id', 'global_cache']);
		};

		$tables['xf_phrase_compiled'] = function(Create $table)
		{
			$table->addColumn('language_id', 'int');
			$table->addColumn('title', 'varbinary', 100);
			$table->addColumn('phrase_text', 'mediumtext');
			$table->addPrimaryKey(['language_id', 'title']);
		};

		$tables['xf_phrase_map'] = function(Create $table)
		{
			$table->addColumn('phrase_map_id', 'int')->autoIncrement();
			$table->addColumn('language_id', 'int');
			$table->addColumn('title', 'varbinary', 100);
			$table->addColumn('phrase_id', 'int');
			$table->addColumn('phrase_group', 'varbinary', 50)->nullable();
			$table->addUniqueKey(['language_id', 'title']);
			$table->addKey('title');
			$table->addKey('phrase_id');
			$table->addKey(['phrase_group', 'language_id'], 'group_language');
		};

		$tables['xf_poll'] = function(Create $table)
		{
			$table->addColumn('poll_id', 'int')->autoIncrement();
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('question', 'varchar', 100);
			$table->addColumn('responses', 'mediumblob');
			$table->addColumn('voter_count', 'int')->setDefault(0);
			$table->addColumn('public_votes', 'tinyint', 3)->setDefault(0);
			$table->addColumn('max_votes', 'tinyint', 3)->setDefault(1);
			$table->addColumn('close_date', 'int')->setDefault(0);
			$table->addColumn('change_vote', 'tinyint', 3)->setDefault(0);
			$table->addColumn('view_results_unvoted', 'tinyint', 3)->setDefault(1);
			$table->addUniqueKey(['content_type', 'content_id']);
		};

		$tables['xf_poll_response'] = function(Create $table)
		{
			$table->addColumn('poll_response_id', 'int')->autoIncrement();
			$table->addColumn('poll_id', 'int');
			$table->addColumn('response', 'varchar', 100);
			$table->addColumn('response_vote_count', 'int')->setDefault(0);
			$table->addColumn('voters', 'mediumblob');
			$table->addKey(['poll_id', 'poll_response_id'], 'poll_id_response_id');
		};

		$tables['xf_poll_vote'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('poll_response_id', 'int');
			$table->addColumn('poll_id', 'int');
			$table->addColumn('vote_date', 'int')->setDefault(0);
			$table->addPrimaryKey(['poll_response_id', 'user_id']);
			$table->addKey(['poll_id', 'user_id']);
			$table->addKey('user_id');
		};

		$tables['xf_post'] = function(Create $table)
		{
			$table->addColumn('post_id', 'int')->autoIncrement();
			$table->addColumn('thread_id', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('username', 'varchar', 50);
			$table->addColumn('post_date', 'int');
			$table->addColumn('message', 'mediumtext');
			$table->addColumn('ip_id', 'int')->setDefault(0);
			$table->addColumn('message_state', 'enum')->values(['visible','moderated','deleted'])->setDefault('visible');
			$table->addColumn('attach_count', 'smallint', 5)->setDefault(0);
			$table->addColumn('position', 'int');
			$table->addColumn('reaction_score', 'int')->unsigned(false)->setDefault(0);
			$table->addColumn('reactions', 'blob')->nullable();
			$table->addColumn('reaction_users', 'blob');
			$table->addColumn('warning_id', 'int')->setDefault(0);
			$table->addColumn('warning_message', 'varchar', 255)->setDefault('');
			$table->addColumn('last_edit_date', 'int')->setDefault(0);
			$table->addColumn('last_edit_user_id', 'int')->setDefault(0);
			$table->addColumn('edit_count', 'int')->setDefault(0);
			$table->addColumn('embed_metadata', 'blob')->nullable();
			$table->addKey(['thread_id', 'post_date']);
			$table->addKey(['thread_id', 'position']);
			$table->addKey('user_id');
			$table->addKey('post_date');
		};

		$tables['xf_profile_post'] = function(Create $table)
		{
			$table->addColumn('profile_post_id', 'int')->autoIncrement();
			$table->addColumn('profile_user_id', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('username', 'varchar', 50);
			$table->addColumn('post_date', 'int');
			$table->addColumn('message', 'mediumtext');
			$table->addColumn('ip_id', 'int')->setDefault(0);
			$table->addColumn('message_state', 'enum')->values(['visible','moderated','deleted'])->setDefault('visible');
			$table->addColumn('attach_count', 'smallint', 5)->setDefault(0);
			$table->addColumn('reaction_score', 'int')->unsigned(false)->setDefault(0);
			$table->addColumn('reactions', 'blob')->nullable();
			$table->addColumn('reaction_users', 'blob');
			$table->addColumn('comment_count', 'int')->setDefault(0);
			$table->addColumn('first_comment_date', 'int')->setDefault(0);
			$table->addColumn('last_comment_date', 'int')->setDefault(0);
			$table->addColumn('latest_comment_ids', 'blob');
			$table->addColumn('warning_id', 'int')->setDefault(0);
			$table->addColumn('warning_message', 'varchar', 255)->setDefault('');
			$table->addColumn('embed_metadata', 'blob')->nullable();
			$table->addKey(['profile_user_id', 'post_date']);
			$table->addKey('user_id');
			$table->addKey('post_date');
		};

		$tables['xf_profile_post_comment'] = function(Create $table)
		{
			$table->addColumn('profile_post_comment_id', 'int')->autoIncrement();
			$table->addColumn('profile_post_id', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('username', 'varchar', 50);
			$table->addColumn('comment_date', 'int');
			$table->addColumn('message', 'mediumtext');
			$table->addColumn('ip_id', 'int')->setDefault(0);
			$table->addColumn('message_state', 'enum')->values(['visible','moderated','deleted'])->setDefault('visible');
			$table->addColumn('reaction_score', 'int')->unsigned(false)->setDefault(0);
			$table->addColumn('reactions', 'blob')->nullable();
			$table->addColumn('reaction_users', 'blob');
			$table->addColumn('warning_id', 'int')->setDefault(0);
			$table->addColumn('warning_message', 'varchar', 255)->setDefault('');
			$table->addColumn('embed_metadata', 'blob')->nullable();
			$table->addKey(['profile_post_id', 'comment_date']);
			$table->addKey('user_id');
			$table->addKey('comment_date');
		};

		$tables['xf_purchasable'] = function(Create $table)
		{
			$table->addColumn('purchasable_type_id', 'varchar', 50);
			$table->addColumn('purchasable_class', 'varchar', 100);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('purchasable_type_id');
		};

		$tables['xf_purchase_request'] = function(Create $table)
		{
			$table->addColumn('purchase_request_id', 'int')->autoIncrement();
			$table->addColumn('request_key', 'varbinary', 32);
			$table->addColumn('user_id', 'int')->setDefault(0);
			$table->addColumn('provider_id', 'varbinary', 25);
			$table->addColumn('payment_profile_id', 'int');
			$table->addColumn('purchasable_type_id', 'varchar', 50);
			$table->addColumn('cost_amount', 'decimal', '10,2');
			$table->addColumn('cost_currency', 'varchar', 3);
			$table->addColumn('extra_data', 'blob')->nullable();
			$table->addColumn('provider_metadata', 'varbinary', 100)->nullable();
			$table->addUniqueKey('request_key');
			$table->addKey(['provider_id', 'provider_metadata'], 'provider_id_metadata');
		};

		$tables['xf_reaction'] = function(Create $table)
		{
			$table->addColumn('reaction_id', 'int')->autoIncrement();
			$table->addColumn('text_color', 'varchar', 100);
			$table->addColumn('image_url', 'varchar', 200);
			$table->addColumn('image_url_2x', 'varchar', 200)->setDefault('');
			$table->addColumn('sprite_mode', 'tinyint', 3)->setDefault(0);
			$table->addColumn('sprite_params', 'blob');
			$table->addColumn('reaction_score', 'int')->unsigned(false)->setDefault(1);
			$table->addColumn('display_order', 'int')->setDefault(1);
			$table->addColumn('active', 'tinyint', 3)->setDefault(1);
			$table->addKey('display_order');
		};

		$tables['xf_reaction_content'] = function (Create $table) {
			$table->addColumn('reaction_content_id', 'int')->autoIncrement();
			$table->addColumn('reaction_id', 'int')->setDefault(1);
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('reaction_user_id', 'int');
			$table->addColumn('reaction_date', 'int');
			$table->addColumn('content_user_id', 'int');
			$table->addColumn('is_counted', 'tinyint', 3)->setDefault(1);
			$table->addUniqueKey(['content_type', 'content_id', 'reaction_user_id'], 'content_type_id_user_id');
			$table->addKey(['content_type', 'content_id', 'reaction_date'], 'content_type_id_reaction_date');
			$table->addKey(['content_user_id', 'reaction_date']);
			$table->addKey('reaction_date');
		};

		$tables['xf_registration_spam_cache'] = function(Create $table)
		{
			$table->addColumn('cache_key', 'varbinary', 128)->setDefault('');
			$table->addColumn('result', 'mediumblob');
			$table->addColumn('timeout', 'int')->setDefault(0);
			$table->addPrimaryKey('cache_key');
			$table->addKey('timeout');
		};

		$tables['xf_report'] = function(Create $table)
		{
			$table->addColumn('report_id', 'int')->autoIncrement();
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('content_user_id', 'int');
			$table->addColumn('content_info', 'mediumblob');
			$table->addColumn('first_report_date', 'int');
			$table->addColumn('report_state', 'enum')->values(['open','assigned','resolved','rejected']);
			$table->addColumn('assigned_user_id', 'int');
			$table->addColumn('comment_count', 'int')->setDefault(0);
			$table->addColumn('last_modified_date', 'int');
			$table->addColumn('last_modified_user_id', 'int')->setDefault(0);
			$table->addColumn('last_modified_username', 'varchar', 50)->setDefault('');
			$table->addColumn('report_count', 'int')->setDefault(0);
			$table->addUniqueKey(['content_type', 'content_id']);
			$table->addKey('report_state');
			$table->addKey(['assigned_user_id', 'report_state'], 'assigned_user_id_state');
			$table->addKey('last_modified_date');
			$table->addKey(['content_user_id', 'last_modified_date'], 'content_user_id_modified');
		};

		$tables['xf_report_comment'] = function(Create $table)
		{
			$table->addColumn('report_comment_id', 'int')->autoIncrement();
			$table->addColumn('report_id', 'int');
			$table->addColumn('comment_date', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('username', 'varchar', 50);
			$table->addColumn('message', 'mediumtext');
			$table->addColumn('state_change', 'enum')->values(['','open','assigned','resolved','rejected'])->setDefault('');
			$table->addColumn('is_report', 'tinyint', 3)->setDefault(1);
			$table->addKey(['report_id', 'comment_date'], 'report_id_date');
			$table->addKey('user_id');
		};

		$tables['xf_route'] = function(Create $table)
		{
			$table->addColumn('route_id', 'int')->autoIncrement();
			$table->addColumn('route_type', 'varbinary', 25);
			$table->addColumn('route_prefix', 'varbinary', 50);
			$table->addColumn('sub_name', 'varbinary', 50);
			$table->addColumn('format', 'varchar', 255)->setDefault('');
			$table->addColumn('build_class', 'varchar', 100)->setDefault('');
			$table->addColumn('build_method', 'varbinary', 75)->setDefault('');
			$table->addColumn('controller', 'varbinary', 255);
			$table->addColumn('context', 'varbinary', 255)->setDefault('');
			$table->addColumn('action_prefix', 'varbinary', 255)->setDefault('');
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addUniqueKey(['route_type', 'route_prefix', 'sub_name'], 'route_type');
		};

		$tables['xf_route_filter'] = function(Create $table)
		{
			$table->addColumn('route_filter_id', 'int')->autoIncrement();
			$table->addColumn('prefix', 'varchar', 25);
			$table->addColumn('find_route', 'varchar', 255);
			$table->addColumn('replace_route', 'varchar', 255);
			$table->addColumn('enabled', 'tinyint', 3)->setDefault(0);
			$table->addColumn('url_to_route_only', 'tinyint', 3)->setDefault(0);
			$table->addKey('prefix', 'route_type_prefix');
		};

		$tables['xf_search'] = function(Create $table)
		{
			$table->addColumn('search_id', 'int')->autoIncrement();
			$table->addColumn('search_results', 'mediumblob');
			$table->addColumn('result_count', 'smallint', 5);
			$table->addColumn('search_type', 'varchar', 25);
			$table->addColumn('search_query', 'varchar', 200);
			$table->addColumn('search_constraints', 'mediumblob');
			$table->addColumn('search_order', 'varchar', 50);
			$table->addColumn('search_grouping', 'tinyint')->setDefault(0);
			$table->addColumn('user_results', 'mediumblob');
			$table->addColumn('warnings', 'mediumblob');
			$table->addColumn('user_id', 'int');
			$table->addColumn('search_date', 'int');
			$table->addColumn('query_hash', 'varchar', 32)->setDefault('');
			$table->addKey('search_date');
			$table->addKey('query_hash');
		};

		$tables['xf_search_index'] = function(Create $table)
		{
			$table->engine('MyISAM');

			$table->addColumn('content_type', 'varchar', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('title', 'varchar', 250)->setDefault('');
			$table->addColumn('message', 'mediumtext');
			$table->addColumn('metadata', 'mediumtext');
			$table->addColumn('user_id', 'int')->setDefault(0);
			$table->addColumn('item_date', 'int');
			$table->addColumn('discussion_id', 'int')->setDefault(0);
			$table->addPrimaryKey(['content_type', 'content_id']);
			$table->addFullTextKey(['title', 'message', 'metadata']);
			$table->addFullTextKey(['title', 'metadata']);
			$table->addKey(['user_id', 'item_date']);
		};

		$tables['xf_session'] = function(Create $table)
		{
			$table->engine('MyISAM');

			$table->addColumn('session_id', 'varbinary', 32);
			$table->addColumn('session_data', 'mediumblob');
			$table->addColumn('expiry_date', 'int');
			$table->addPrimaryKey('session_id');
			$table->addKey('expiry_date');
		};

		$tables['xf_session_activity'] = function(Create $table)
		{
			$table->engine('MEMORY');

			$table->addColumn('user_id', 'int');
			$table->addColumn('unique_key', 'varbinary', 16);
			$table->addColumn('ip', 'varbinary', 16)->setDefault('');
			$table->addColumn('controller_name', 'varchar', 100);
			$table->addColumn('controller_action', 'varchar', 75);
			$table->addColumn('view_state', 'enum')->values(['valid','error']);
			$table->addColumn('params', 'varbinary', 100);
			$table->addColumn('view_date', 'int');
			$table->addColumn('robot_key', 'varbinary', 25)->setDefault('');
			$table->addPrimaryKey(['user_id', 'unique_key'])->using('btree');
			$table->addKey('view_date')->using('btree');
		};

		$tables['xf_session_admin'] = function(Create $table)
		{
			$table->engine('MyISAM');

			$table->addColumn('session_id', 'varbinary', 32);
			$table->addColumn('session_data', 'mediumblob');
			$table->addColumn('expiry_date', 'int');
			$table->addPrimaryKey('session_id');
			$table->addKey('expiry_date');
		};

		$tables['xf_session_install'] = function(Create $table)
		{
			$table->engine('MyISAM');

			$table->addColumn('session_id', 'varbinary', 32);
			$table->addColumn('session_data', 'mediumblob');
			$table->addColumn('expiry_date', 'int');
			$table->addPrimaryKey('session_id');
			$table->addKey('expiry_date');
		};

		$tables['xf_sitemap'] = function(Create $table)
		{
			$table->addColumn('sitemap_id', 'int');
			$table->addColumn('is_active', 'tinyint', 3);
			$table->addColumn('file_count', 'smallint', 5);
			$table->addColumn('entry_count', 'int')->setDefault(0);
			$table->addColumn('is_compressed', 'tinyint', 3);
			$table->addColumn('complete_date', 'int')->nullable();
			$table->addPrimaryKey('sitemap_id');
			$table->addKey('is_active');
		};

		$tables['xf_smilie'] = function(Create $table)
		{
			$table->addColumn('smilie_id', 'int')->autoIncrement();
			$table->addColumn('title', 'varchar', 50);
			$table->addColumn('smilie_text', 'text');
			$table->addColumn('image_url', 'varchar', 200);
			$table->addColumn('image_url_2x', 'varchar', 200)->setDefault('');
			$table->addColumn('sprite_mode', 'tinyint', 3)->setDefault(0);
			$table->addColumn('sprite_params', 'blob');
			$table->addColumn('smilie_category_id', 'int')->setDefault(0);
			$table->addColumn('display_order', 'int')->setDefault(1);
			$table->addColumn('display_in_editor', 'tinyint', 3)->setDefault(1);
			$table->addKey('display_order');
		};

		$tables['xf_smilie_category'] = function(Create $table)
		{
			$table->addColumn('smilie_category_id', 'int')->autoIncrement();
			$table->addColumn('display_order', 'int');
		};

		$tables['xf_spam_cleaner_log'] = function(Create $table)
		{
			$table->addColumn('spam_cleaner_log_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int')->setDefault(0);
			$table->addColumn('username', 'varchar', 50)->setDefault('');
			$table->addColumn('applying_user_id', 'int')->setDefault(0);
			$table->addColumn('applying_username', 'varchar', 50)->setDefault('');
			$table->addColumn('application_date', 'int')->setDefault(0);
			$table->addColumn('data', 'mediumblob')->comment('Serialized array containing log data for undo purposes');
			$table->addColumn('restored_date', 'int')->setDefault(0);
			$table->addKey('application_date');
			$table->addKey('user_id');
			$table->addKey('applying_user_id');
		};

		$tables['xf_spam_trigger_log'] = function(Create $table)
		{
			$table->addColumn('trigger_log_id', 'int')->autoIncrement();
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int')->nullable();
			$table->addColumn('log_date', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('ip_address', 'varbinary', 16);
			$table->addColumn('result', 'varbinary', 25);
			$table->addColumn('details', 'mediumblob');
			$table->addColumn('request_state', 'mediumblob');
			$table->addUniqueKey(['content_type', 'content_id'], 'content_type');
			$table->addKey('log_date');
		};

		$tables['xf_stats_daily'] = function(Create $table)
		{
			$table->addColumn('stats_date', 'int');
			$table->addColumn('stats_type', 'varbinary', 50);
			$table->addColumn('counter', 'bigint');
			$table->addPrimaryKey(['stats_date', 'stats_type']);
		};

		$tables['xf_style'] = function(Create $table)
		{
			$table->addColumn('style_id', 'int')->autoIncrement();
			$table->addColumn('parent_id', 'int');
			$table->addColumn('parent_list', 'varbinary', 100)->comment('IDs of ancestor styles in order, eg: this,parent,grandparent,root');
			$table->addColumn('title', 'varchar', 50);
			$table->addColumn('description', 'varchar', 100)->setDefault('');
			$table->addColumn('properties', 'mediumblob')->comment('Serialized array of materialized style properties for this style');
			$table->addColumn('last_modified_date', 'int')->setDefault(0);
			$table->addColumn('user_selectable', 'tinyint', 3)->setDefault(1)->comment('Unselectable styles are unselectable by non-admin visitors');
			$table->addColumn('designer_mode', 'varbinary', 50)->nullable()->setDefault(null);
			$table->addUniqueKey('designer_mode');
		};

		$tables['xf_style_property'] = function(Create $table)
		{
			$table->addColumn('property_id', 'int')->autoIncrement();
			$table->addColumn('property_name', 'varbinary', 50);
			$table->addColumn('style_id', 'int');
			$table->addColumn('group_name', 'varbinary', 50)->nullable();
			$table->addColumn('title', 'varchar', 100);
			$table->addColumn('description', 'varchar', 255)->setDefault('');
			$table->addColumn('property_type', 'varchar', 25);
			$table->addColumn('css_components', 'blob');
			$table->addColumn('value_type', 'varchar', 25)->setDefault('');
			$table->addColumn('value_parameters', 'text');
			$table->addColumn('depends_on', 'varbinary', 50)->setDefault('');
			$table->addColumn('value_group', 'varbinary', 50)->setDefault('');
			$table->addColumn('property_value', 'mediumblob');
			$table->addColumn('display_order', 'int')->setDefault(0);
			$table->addColumn('addon_id', 'varbinary', 50);
			$table->addUniqueKey(['style_id', 'property_name']);
		};

		$tables['xf_style_property_group'] = function(Create $table)
		{
			$table->addColumn('property_group_id', 'int')->autoIncrement();
			$table->addColumn('group_name', 'varbinary', 50);
			$table->addColumn('style_id', 'int');
			$table->addColumn('title', 'varchar', 100);
			$table->addColumn('description', 'varchar', 255)->setDefault('');
			$table->addColumn('display_order', 'int')->setDefault(0);
			$table->addColumn('addon_id', 'varbinary', 50);
			$table->addUniqueKey(['group_name', 'style_id']);
		};

		$tables['xf_style_property_map'] = function(Create $table)
		{
			$table->addColumn('property_map_id', 'int')->autoIncrement();
			$table->addColumn('style_id', 'int');
			$table->addColumn('property_name', 'varbinary', 50);
			$table->addColumn('property_id', 'int');
			$table->addColumn('parent_property_id', 'int')->nullable();
			$table->addUniqueKey(['style_id', 'property_name'], 'style_id');
			$table->addKey('parent_property_id');
		};

		$tables['xf_tag'] = function(Create $table)
		{
			$table->addColumn('tag_id', 'int')->autoIncrement();
			$table->addColumn('tag', 'varchar', 100);
			$table->addColumn('tag_url', 'varchar', 100);
			$table->addColumn('use_count', 'int')->setDefault(0);
			$table->addColumn('last_use_date', 'int')->setDefault(0);
			$table->addColumn('permanent', 'tinyint')->setDefault(0);
			$table->addUniqueKey('tag');
			$table->addUniqueKey('tag_url');
			$table->addKey('use_count');
		};

		$tables['xf_tag_content'] = function(Create $table)
		{
			$table->addColumn('tag_content_id', 'int')->autoIncrement();
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('tag_id', 'int');
			$table->addColumn('add_user_id', 'int');
			$table->addColumn('add_date', 'int');
			$table->addColumn('visible', 'tinyint', 3);
			$table->addColumn('content_date', 'int');
			$table->addUniqueKey(['content_type', 'content_id', 'tag_id'], 'content_type_id_tag');
			$table->addKey(['tag_id', 'content_date']);
		};

		$tables['xf_tag_result_cache'] = function(Create $table)
		{
			$table->addColumn('result_cache_id', 'int')->autoIncrement();
			$table->addColumn('tag_id', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('cache_date', 'int');
			$table->addColumn('expiry_date', 'int');
			$table->addColumn('results', 'mediumblob');
			$table->addUniqueKey(['tag_id', 'user_id']);
			$table->addKey('expiry_date', 'expiration_date');
		};

		$tables['xf_template'] = function(Create $table)
		{
			$table->addColumn('template_id', 'int')->autoIncrement();
			$table->addColumn('type', 'varbinary', 20);
			$table->addColumn('title', 'varbinary', 100);
			$table->addColumn('style_id', 'int');
			$table->addColumn('template', 'mediumtext')->comment('User-editable HTML and template syntax');
			$table->addColumn('template_parsed', 'mediumblob');
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addColumn('version_id', 'int')->setDefault(0);
			$table->addColumn('version_string', 'varchar', 30)->setDefault('');
			$table->addColumn('last_edit_date', 'int')->setDefault(0);
			$table->addUniqueKey(['type', 'title', 'style_id']);
		};

		$tables['xf_template_history'] = function(Create $table)
		{
			$table->addColumn('template_history_id', 'int')->autoIncrement();
			$table->addColumn('type', 'varbinary', 20);
			$table->addColumn('title', 'varbinary', 100);
			$table->addColumn('style_id', 'int');
			$table->addColumn('template', 'mediumtext');
			$table->addColumn('edit_date', 'int');
			$table->addColumn('log_date', 'int');
			$table->addKey('log_date');
			$table->addKey(['type', 'title', 'style_id']);
			$table->addKey('title');
		};

		$tables['xf_template_map'] = function(Create $table)
		{
			$table->addColumn('template_map_id', 'int')->autoIncrement();
			$table->addColumn('style_id', 'int');
			$table->addColumn('type', 'varbinary', 20);
			$table->addColumn('title', 'varbinary', 100);
			$table->addColumn('template_id', 'int');
			$table->addUniqueKey(['style_id', 'type', 'title']);
			$table->addKey('template_id');
			$table->addKey(['type', 'title']);
		};

		$tables['xf_template_modification'] = function(Create $table)
		{
			$table->addColumn('modification_id', 'int')->autoIncrement();
			$table->addColumn('addon_id', 'varbinary', 50);
			$table->addColumn('type', 'varbinary', 20);
			$table->addColumn('template', 'varbinary', 100);
			$table->addColumn('modification_key', 'varbinary', 50);
			$table->addColumn('description', 'varchar', 255);
			$table->addColumn('execution_order', 'int');
			$table->addColumn('enabled', 'tinyint', 3);
			$table->addColumn('action', 'varchar', 25);
			$table->addColumn('find', 'text');
			$table->addColumn('replace', 'text');
			$table->addUniqueKey('modification_key');
			$table->addKey('addon_id');
			$table->addKey(['template', 'execution_order'], 'template_order');
		};

		$tables['xf_template_modification_log'] = function(Create $table)
		{
			$table->addColumn('template_id', 'int');
			$table->addColumn('modification_id', 'int');
			$table->addColumn('status', 'varchar', 25);
			$table->addColumn('apply_count', 'int')->setDefault(0);
			$table->addPrimaryKey(['template_id', 'modification_id']);
			$table->addKey('modification_id');
		};

		$tables['xf_template_phrase'] = function(Create $table)
		{
			$table->addColumn('template_id', 'int');
			$table->addColumn('phrase_title', 'varbinary', 100);
			$table->addPrimaryKey(['template_id', 'phrase_title']);
			$table->addKey('phrase_title');
		};

		$tables['xf_tfa_attempt'] = function(Create $table)
		{
			$table->addColumn('attempt_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('attempt_date', 'int');
			$table->addKey('attempt_date');
			$table->addKey(['user_id', 'attempt_date']);
		};

		$tables['xf_tfa_provider'] = function(Create $table)
		{
			$table->addColumn('provider_id', 'varbinary', 25);
			$table->addColumn('provider_class', 'varchar', 100);
			$table->addColumn('priority', 'smallint', 5);
			$table->addColumn('active', 'tinyint', 3);
			$table->addColumn('options', 'blob')->nullable();
			$table->addPrimaryKey('provider_id');
		};

		$tables['xf_thread'] = function(Create $table)
		{
			$table->addColumn('thread_id', 'int')->autoIncrement();
			$table->addColumn('node_id', 'int');
			$table->addColumn('title', 'varchar', 150);
			$table->addColumn('reply_count', 'int')->setDefault(0);
			$table->addColumn('view_count', 'int')->setDefault(0);
			$table->addColumn('user_id', 'int');
			$table->addColumn('username', 'varchar', 50);
			$table->addColumn('post_date', 'int');
			$table->addColumn('sticky', 'tinyint', 3)->setDefault(0);
			$table->addColumn('discussion_state', 'enum')->values(['visible','moderated','deleted'])->setDefault('visible');
			$table->addColumn('discussion_open', 'tinyint', 3)->setDefault(1);
			$table->addColumn('discussion_type', 'varchar', 25)->setDefault('');
			$table->addColumn('first_post_id', 'int');
			$table->addColumn('first_post_reaction_score', 'int')->unsigned(false)->setDefault(0);
			$table->addColumn('first_post_reactions', 'blob')->nullable();
			$table->addColumn('last_post_date', 'int');
			$table->addColumn('last_post_id', 'int');
			$table->addColumn('last_post_user_id', 'int');
			$table->addColumn('last_post_username', 'varchar', 50);
			$table->addColumn('prefix_id', 'int')->setDefault(0);
			$table->addColumn('tags', 'mediumblob');
			$table->addColumn('custom_fields', 'mediumblob');
			$table->addKey(['node_id', 'last_post_date']);
			$table->addKey(['node_id', 'sticky', 'discussion_state', 'last_post_date'], 'node_id_sticky_state_last_post');
			$table->addKey('last_post_date');
			$table->addKey('post_date');
			$table->addKey('user_id');
		};

		$tables['xf_thread_field'] = function(Create $table)
		{
			$table->addColumn('field_id', 'varbinary', 25);
			$table->addColumn('display_group', 'varbinary', 25)->setDefault('before');
			$table->addColumn('display_order', 'int')->setDefault(1);
			$table->addColumn('field_type', 'varbinary', 25)->setDefault('textbox');
			$table->addColumn('field_choices', 'blob');
			$table->addColumn('match_type', 'varbinary', 25)->setDefault('none');
			$table->addColumn('match_params', 'blob');
			$table->addColumn('max_length', 'int')->setDefault(0);
			$table->addColumn('required', 'tinyint', 3)->setDefault(0);
			$table->addColumn('display_template', 'text');
			$table->addColumn('editable_user_group_ids', 'blob');
			$table->addPrimaryKey('field_id');
			$table->addKey(['display_group', 'display_order'], 'display_group_order');
		};

		$tables['xf_thread_field_value'] = function(Create $table)
		{
			$table->addColumn('thread_id', 'int');
			$table->addColumn('field_id', 'varbinary', 25);
			$table->addColumn('field_value', 'mediumtext');
			$table->addPrimaryKey(['thread_id', 'field_id']);
			$table->addKey('field_id');
		};

		$tables['xf_thread_prefix'] = function(Create $table)
		{
			$table->addColumn('prefix_id', 'int')->autoIncrement();
			$table->addColumn('prefix_group_id', 'int');
			$table->addColumn('display_order', 'int');
			$table->addColumn('materialized_order', 'int')->comment('Internally-set order, based on prefix_group.display_order, prefix.display_order');
			$table->addColumn('css_class', 'varchar', 50)->setDefault('');
			$table->addColumn('allowed_user_group_ids', 'blob');
			$table->addKey('materialized_order');
		};

		$tables['xf_thread_prefix_group'] = function(Create $table)
		{
			$table->addColumn('prefix_group_id', 'int')->autoIncrement();
			$table->addColumn('display_order', 'int');
		};

		$tables['xf_thread_prompt'] = function(Create $table)
		{
			$table->addColumn('prompt_id', 'int')->autoIncrement();
			$table->addColumn('prompt_group_id', 'int');
			$table->addColumn('display_order', 'int');
			$table->addColumn('materialized_order', 'int')->comment('Internally-set order, based on prompt_group.display_order, prompt.display_order');
			$table->addKey('materialized_order');
		};

		$tables['xf_thread_prompt_group'] = function(Create $table)
		{
			$table->addColumn('prompt_group_id', 'int')->autoIncrement();
			$table->addColumn('display_order', 'int');
		};

		$tables['xf_thread_read'] = function(Create $table)
		{
			$table->addColumn('thread_read_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('thread_id', 'int');
			$table->addColumn('thread_read_date', 'int');
			$table->addUniqueKey(['user_id', 'thread_id']);
			$table->addKey('thread_id');
			$table->addKey('thread_read_date');
		};

		$tables['xf_thread_redirect'] = function(Create $table)
		{
			$table->addColumn('thread_id', 'int');
			$table->addColumn('target_url', 'text');
			$table->addColumn('redirect_key', 'varchar', 50)->setDefault('');
			$table->addColumn('expiry_date', 'int')->setDefault(0);
			$table->addPrimaryKey('thread_id');
			$table->addKey(['redirect_key', 'expiry_date']);
		};

		$tables['xf_thread_reply_ban'] = function(Create $table)
		{
			$table->addColumn('thread_reply_ban_id', 'int')->autoIncrement();
			$table->addColumn('thread_id', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('ban_date', 'int');
			$table->addColumn('expiry_date', 'int')->nullable();
			$table->addColumn('reason', 'varchar', 100)->setDefault('');
			$table->addColumn('ban_user_id', 'int');
			$table->addUniqueKey(['thread_id', 'user_id'], 'thread_id');
			$table->addKey('expiry_date');
			$table->addKey('user_id');
		};

		$tables['xf_thread_user_post'] = function(Create $table)
		{
			$table->addColumn('thread_id', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('post_count', 'int');
			$table->addPrimaryKey(['thread_id', 'user_id']);
			$table->addKey('user_id');
		};

		$tables['xf_thread_view'] = function(Create $table)
		{
			$table->engine('MEMORY');

			$table->addColumn('thread_id', 'int');
			$table->addColumn('total', 'int');
			$table->addPrimaryKey('thread_id');
		};

		$tables['xf_thread_watch'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('thread_id', 'int');
			$table->addColumn('email_subscribe', 'tinyint', 3)->setDefault(0);
			$table->addPrimaryKey(['user_id', 'thread_id']);
			$table->addKey(['thread_id', 'email_subscribe']);
		};

		$tables['xf_trophy'] = function(Create $table)
		{
			$table->addColumn('trophy_id', 'int')->autoIncrement();
			$table->addColumn('trophy_points', 'int');
			$table->addColumn('user_criteria', 'mediumblob');
		};

		$tables['xf_unfurl_result'] = function(Create $table)
		{
			$table->addColumn('result_id', 'int')->autoIncrement();
			$table->addColumn('url', 'text');
			$table->addColumn('url_hash', 'varbinary', 32);
			$table->addColumn('title', 'text')->nullable();
			$table->addColumn('description', 'text')->nullable();
			$table->addColumn('image_url', 'text')->nullable();
			$table->addColumn('favicon_url', 'text')->nullable();
			$table->addColumn('last_request_date', 'int')->setDefault(0);
			$table->addColumn('pending', 'tinyint')->setDefault(0);
			$table->addColumn('error_count', 'int')->setDefault(0);
			$table->addUniqueKey('url_hash');
			$table->addKey('last_request_date');
		};

		$tables['xf_upgrade_check'] = function(Create $table)
		{
			$table->addColumn('check_id', 'int')->autoIncrement();
			$table->addColumn('error_code', 'varchar', 50)->nullable();
			$table->addColumn('check_date', 'int')->nullable();
			$table->addColumn('board_url_valid', 'tinyint')->nullable();
			$table->addColumn('branding_valid', 'tinyint')->nullable();
			$table->addColumn('license_expired', 'tinyint')->nullable();
			$table->addColumn('invalid_add_ons', 'blob')->nullable();
			$table->addColumn('available_updates', 'blob')->nullable();
			$table->addKey('check_date');
		};

		$tables['xf_upgrade_job'] = function(Create $table)
		{
			$table->addColumn('unique_key', 'varbinary', 50)->primaryKey();
			$table->addColumn('execute_class', 'varchar', 100);
			$table->addColumn('execute_data', 'mediumblob');
			$table->addColumn('immediate', 'tinyint');
			$table->addKey('immediate');
		};

		$tables['xf_upgrade_log'] = function(Create $table)
		{
			$table->addColumn('version_id', 'int');
			$table->addColumn('last_step', 'smallint')->nullable();
			$table->addColumn('completion_date', 'int')->setDefault(0);
			$table->addColumn('log_type', 'enum')->values(['install','upgrade'])->setDefault('upgrade');
			$table->addPrimaryKey('version_id');
		};

		$tables['xf_user'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int')->autoIncrement();
			$table->addColumn('username', 'varchar', 50);
			$table->addColumn('email', 'varchar', 120);
			$table->addColumn('custom_title', 'varchar', 50)->setDefault('');
			$table->addColumn('language_id', 'int');
			$table->addColumn('style_id', 'int')->comment('0 = use system default');
			$table->addColumn('timezone', 'varchar', 50)->comment('Example: \'Europe/London\'');
			$table->addColumn('visible', 'tinyint', 3)->setDefault(1)->comment('Show browsing activity to others');
			$table->addColumn('activity_visible', 'tinyint', 3)->setDefault(1);
			$table->addColumn('user_group_id', 'int');
			$table->addColumn('secondary_group_ids', 'varbinary', 255);
			$table->addColumn('display_style_group_id', 'int')->setDefault(0)->comment('User group ID that provides user styling');
			$table->addColumn('permission_combination_id', 'int');
			$table->addColumn('message_count', 'int')->setDefault(0);
			$table->addColumn('conversations_unread', 'smallint', 5)->setDefault(0);
			$table->addColumn('register_date', 'int')->setDefault(0);
			$table->addColumn('last_activity', 'int')->setDefault(0);
			$table->addColumn('trophy_points', 'int')->setDefault(0);
			$table->addColumn('alerts_unread', 'smallint', 5)->setDefault(0);
			$table->addColumn('avatar_date', 'int')->setDefault(0);
			$table->addColumn('avatar_width', 'smallint', 5)->setDefault(0);
			$table->addColumn('avatar_height', 'smallint', 5)->setDefault(0);
			$table->addColumn('avatar_highdpi', 'tinyint', 3)->setDefault(0);
			$table->addColumn('gravatar', 'varchar', 120)->setDefault('')->comment('If specified, this is an email address corresponding to the user\'s \'Gravatar\'');
			$table->addColumn('user_state', 'enum')->values(['valid','email_confirm','email_confirm_edit','moderated','email_bounce','rejected','disabled'])->setDefault('valid');
			$table->addColumn('is_moderator', 'tinyint', 3)->setDefault(0);
			$table->addColumn('is_admin', 'tinyint', 3)->setDefault(0);
			$table->addColumn('is_banned', 'tinyint', 3)->setDefault(0);
			$table->addColumn('reaction_score', 'int')->unsigned(false)->setDefault(0);
			$table->addColumn('warning_points', 'int')->setDefault(0);
			$table->addColumn('is_staff', 'tinyint', 3)->setDefault(0);
			$table->addColumn('secret_key', 'varbinary', 32);
			$table->addColumn('privacy_policy_accepted', 'int')->setDefault(0);
			$table->addColumn('terms_accepted', 'int')->setDefault(0);
			$table->addUniqueKey('username');
			$table->addKey('email');
			$table->addKey('user_state');
			$table->addKey('last_activity');
			$table->addKey('message_count');
			$table->addKey('trophy_points');
			$table->addKey('reaction_score');
			$table->addKey('register_date');
			$table->addKey(['is_staff', 'username'], 'staff_username');
		};

		$tables['xf_user_alert'] = function(Create $table)
		{
			$table->addColumn('alert_id', 'int')->autoIncrement();
			$table->addColumn('alerted_user_id', 'int')->comment('User being alerted');
			$table->addColumn('user_id', 'int')->setDefault(0)->comment('User who did the action that caused the alert');
			$table->addColumn('username', 'varchar', 50)->setDefault('')->comment('Corresponds to user_id');
			$table->addColumn('content_type', 'varbinary', 25)->comment('eg: trophy');
			$table->addColumn('content_id', 'int')->setDefault(0);
			$table->addColumn('action', 'varbinary', 30)->comment('eg: edit');
			$table->addColumn('event_date', 'int');
			$table->addColumn('view_date', 'int')->setDefault(0)->comment('Time when this was viewed by the alerted user');
			$table->addColumn('extra_data', 'mediumblob')->comment('Serialized. Stores any extra data relevant to the alert');
			$table->addColumn('depends_on_addon_id', 'varbinary', 50)->setDefault('');
			$table->addKey(['alerted_user_id', 'event_date'], 'alertedUserId_eventDate');
			$table->addKey(['content_type', 'content_id'], 'contentType_contentId');
			$table->addKey(['view_date', 'event_date'], 'viewDate_eventDate');
			$table->addKey('user_id');
		};

		$tables['xf_user_alert_optout'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('alert', 'varbinary', 50);
			$table->addPrimaryKey(['user_id', 'alert']);
		};

		$tables['xf_user_authenticate'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('scheme_class', 'varchar', 100);
			$table->addColumn('data', 'mediumblob');
			$table->addPrimaryKey('user_id');
		};

		$tables['xf_user_ban'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('ban_user_id', 'int');
			$table->addColumn('ban_date', 'int')->setDefault(0);
			$table->addColumn('end_date', 'int')->setDefault(0);
			$table->addColumn('user_reason', 'varchar', 255);
			$table->addColumn('triggered', 'tinyint', 3)->setDefault(1);
			$table->addPrimaryKey('user_id');
			$table->addKey('ban_date');
			$table->addKey('end_date');
		};

		$tables['xf_user_change_temp'] = function(Create $table)
		{
			$table->addColumn('user_change_temp_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('change_key', 'varbinary', 50)->nullable();
			$table->addColumn('action_type', 'varbinary', 50);
			$table->addColumn('action_modifier', 'varbinary', 255)->nullable();
			$table->addColumn('new_value', 'mediumblob')->nullable();
			$table->addColumn('old_value', 'mediumblob')->nullable();
			$table->addColumn('create_date', 'int')->nullable();
			$table->addColumn('expiry_date', 'int')->nullable();
			$table->addUniqueKey(['user_id', 'change_key'], 'user_id');
			$table->addKey('change_key');
			$table->addKey('expiry_date');
		};

		$tables['xf_user_confirmation'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('confirmation_type', 'varchar', 25);
			$table->addColumn('confirmation_key', 'varchar', 16);
			$table->addColumn('confirmation_date', 'int');
			$table->addPrimaryKey(['user_id', 'confirmation_type']);
			$table->addKey('confirmation_date');
		};

		$tables['xf_user_connected_account'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('provider', 'varbinary', 25);
			$table->addColumn('provider_key', 'varbinary', 150);
			$table->addColumn('extra_data', 'mediumblob');
			$table->addPrimaryKey(['user_id', 'provider']);
			$table->addUniqueKey(['provider', 'provider_key'], 'provider');
		};

		$tables['xf_user_field'] = function(Create $table)
		{
			$table->addColumn('field_id', 'varbinary', 25);
			$table->addColumn('display_group', 'enum')->values(['personal','contact','preferences'])->setDefault('personal');
			$table->addColumn('display_order', 'int')->setDefault(1);
			$table->addColumn('field_type', 'varbinary', 25)->setDefault('textbox');
			$table->addColumn('field_choices', 'blob');
			$table->addColumn('match_type', 'varbinary', 25)->setDefault('none');
			$table->addColumn('match_params', 'blob');
			$table->addColumn('max_length', 'int')->setDefault(0);
			$table->addColumn('required', 'tinyint', 3)->setDefault(0);
			$table->addColumn('show_registration', 'tinyint', 3)->setDefault(0);
			$table->addColumn('user_editable', 'enum')->values(['yes','once','never'])->setDefault('yes');
			$table->addColumn('viewable_profile', 'tinyint')->setDefault(1);
			$table->addColumn('viewable_message', 'tinyint')->setDefault(0);
			$table->addColumn('display_template', 'text');
			$table->addColumn('moderator_editable', 'tinyint', 3)->setDefault(0);
			$table->addPrimaryKey('field_id');
			$table->addKey(['display_group', 'display_order'], 'display_group_order');
		};

		$tables['xf_user_field_value'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('field_id', 'varbinary', 25);
			$table->addColumn('field_value', 'mediumtext');
			$table->addPrimaryKey(['user_id', 'field_id']);
			$table->addKey('field_id');
		};

		$tables['xf_user_follow'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('follow_user_id', 'int')->comment('User being followed');
			$table->addColumn('follow_date', 'int')->setDefault(0);
			$table->addPrimaryKey(['user_id', 'follow_user_id']);
			$table->addKey('follow_user_id');
		};

		$tables['xf_user_group'] = function(Create $table)
		{
			$table->addColumn('user_group_id', 'int')->autoIncrement();
			$table->addColumn('title', 'varchar', 50);
			$table->addColumn('display_style_priority', 'int')->setDefault(0);
			$table->addColumn('username_css', 'text');
			$table->addColumn('user_title', 'varchar', 100)->setDefault('');
			$table->addColumn('banner_css_class', 'varchar', 75)->setDefault('');
			$table->addColumn('banner_text', 'varchar', 100)->setDefault('');
			$table->addKey('title');
		};

		$tables['xf_user_group_change'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('change_key', 'varbinary', 50);
			$table->addColumn('group_ids', 'varbinary', 255);
			$table->addPrimaryKey(['user_id', 'change_key']);
			$table->addKey('change_key');
		};

		$tables['xf_user_group_promotion'] = function(Create $table)
		{
			$table->addColumn('promotion_id', 'int')->autoIncrement();
			$table->addColumn('title', 'varchar', 100);
			$table->addColumn('active', 'tinyint')->setDefault(1);
			$table->addColumn('user_criteria', 'mediumblob');
			$table->addColumn('extra_user_group_ids', 'varbinary', 255);
			$table->addKey('title');
		};

		$tables['xf_user_group_promotion_log'] = function(Create $table)
		{
			$table->addColumn('promotion_id', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('promotion_date', 'int');
			$table->addColumn('promotion_state', 'enum')->values(['automatic','manual','disabled'])->setDefault('automatic');
			$table->addPrimaryKey(['promotion_id', 'user_id']);
			$table->addKey('promotion_date');
			$table->addKey(['user_id', 'promotion_date'], 'user_id_date');
		};

		$tables['xf_user_group_relation'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('user_group_id', 'int');
			$table->addColumn('is_primary', 'tinyint', 3);
			$table->addPrimaryKey(['user_id', 'user_group_id']);
			$table->addKey(['user_group_id', 'is_primary']);
		};

		$tables['xf_user_ignored'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('ignored_user_id', 'int');
			$table->addPrimaryKey(['user_id', 'ignored_user_id']);
			$table->addKey('ignored_user_id');
		};

		$tables['xf_user_option'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('show_dob_year', 'tinyint', 3)->setDefault(1)->comment('Show date of month year (thus: age)');
			$table->addColumn('show_dob_date', 'tinyint', 3)->setDefault(1)->comment('Show date of birth day and month');
			$table->addColumn('content_show_signature', 'tinyint', 3)->setDefault(1)->comment('Show user\'s signatures with content');
			$table->addColumn('receive_admin_email', 'tinyint', 3)->setDefault(1);
			$table->addColumn('email_on_conversation', 'tinyint', 3)->setDefault(1)->comment('Receive an email upon receiving a conversation message');
			$table->addColumn('push_on_conversation', 'tinyint', 3)->setDefault(1);
			$table->addColumn('is_discouraged', 'tinyint', 3)->setDefault(0)->comment('If non-zero, this user will be subjected to annoying random system failures.');
			$table->addColumn('creation_watch_state', 'enum')->values(['','watch_no_email','watch_email'])->setDefault('');
			$table->addColumn('interaction_watch_state', 'enum')->values(['','watch_no_email','watch_email'])->setDefault('');
			$table->addColumn('alert_optout', 'text')->comment('Comma-separated list of alerts from which the user has opted out. Example: \'post_like,user_trophy\'');
			$table->addColumn('push_optout', 'text')->comment('Comma-separated list of alerts from which the user has opted out for push notifications. Example: \'post_like,user_trophy\'');
			$table->addColumn('use_tfa', 'tinyint', 3)->setDefault(0);
			$table->addPrimaryKey('user_id');
		};

		$tables['xf_user_privacy'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('allow_view_profile', 'enum')->values(['everyone','members','followed','none'])->setDefault('everyone');
			$table->addColumn('allow_post_profile', 'enum')->values(['everyone','members','followed','none'])->setDefault('everyone');
			$table->addColumn('allow_send_personal_conversation', 'enum')->values(['everyone','members','followed','none'])->setDefault('everyone');
			$table->addColumn('allow_view_identities', 'enum')->values(['everyone','members','followed','none'])->setDefault('everyone');
			$table->addColumn('allow_receive_news_feed', 'enum')->values(['everyone','members','followed','none'])->setDefault('everyone');
			$table->addPrimaryKey('user_id');
		};

		$tables['xf_user_profile'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('dob_day', 'tinyint', 3)->setDefault(0);
			$table->addColumn('dob_month', 'tinyint', 3)->setDefault(0);
			$table->addColumn('dob_year', 'smallint', 5)->setDefault(0);
			$table->addColumn('signature', 'text');
			$table->addColumn('website', 'text');
			$table->addColumn('location', 'varchar', 50)->setDefault('');
			$table->addColumn('following', 'text')->comment('Comma-separated integers from xf_user_follow');
			$table->addColumn('ignored', 'text')->comment('Comma-separated integers from xf_user_ignored');
			$table->addColumn('avatar_crop_x', 'int')->setDefault(0)->comment('X-Position from which to start the square crop on the m avatar');
			$table->addColumn('avatar_crop_y', 'int')->setDefault(0)->comment('Y-Position from which to start the square crop on the m avatar');
			$table->addColumn('about', 'text');
			$table->addColumn('custom_fields', 'mediumblob');
			$table->addColumn('connected_accounts', 'mediumblob');
			$table->addColumn('password_date', 'int')->setDefault(1);
			$table->addPrimaryKey('user_id');
			$table->addKey(['dob_month', 'dob_day', 'dob_year'], 'dob');
		};

		$tables['xf_user_push_optout'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('push', 'varbinary', 50);
			$table->addPrimaryKey(['user_id', 'push']);
		};

		$tables['xf_user_push_subscription'] = function(Create $table)
		{
			$table->addColumn('endpoint_id', 'int')->autoIncrement();
			$table->addColumn('endpoint_hash', 'varbinary', 32);
			$table->addColumn('endpoint', 'text');
			$table->addColumn('user_id', 'int');
			$table->addColumn('data', 'mediumblob')->nullable();
			$table->addColumn('last_seen', 'int');
			$table->addUniqueKey('endpoint_hash');
			$table->addKey('user_id');
		};

		$tables['xf_user_reject'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('reject_date', 'int');
			$table->addColumn('reject_user_id', 'int')->setDefault(0);
			$table->addColumn('reject_reason', 'varchar', 200)->setDefault('');
			$table->addPrimaryKey('user_id');
			$table->addKey('reject_date');
		};

		$tables['xf_user_remember'] = function(Create $table)
		{
			$table->addColumn('remember_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('remember_key', 'varbinary', 32);
			$table->addColumn('start_date', 'int');
			$table->addColumn('expiry_date', 'int');
			$table->addUniqueKey(['user_id', 'remember_key']);
			$table->addKey('expiry_date');
		};

		$tables['xf_user_tfa'] = function(Create $table)
		{
			$table->addColumn('user_tfa_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('provider_id', 'varbinary', 25);
			$table->addColumn('provider_data', 'mediumblob');
			$table->addColumn('last_used_date', 'int')->setDefault(0);
			$table->addUniqueKey(['user_id', 'provider_id'], 'user_id');
		};

		$tables['xf_user_tfa_trusted'] = function(Create $table)
		{
			$table->addColumn('tfa_trusted_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('trusted_key', 'varbinary', 32);
			$table->addColumn('trusted_until', 'int');
			$table->addUniqueKey(['user_id', 'trusted_key'], 'user_id');
			$table->addKey('trusted_until');
		};

		$tables['xf_user_title_ladder'] = function(Create $table)
		{
			$table->addColumn('minimum_level', 'int');
			$table->addColumn('title', 'varchar', 250);
			$table->addPrimaryKey('minimum_level');
		};

		$tables['xf_user_trophy'] = function(Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('trophy_id', 'int');
			$table->addColumn('award_date', 'int');
			$table->addPrimaryKey(['trophy_id', 'user_id']);
			$table->addKey(['user_id', 'award_date']);
		};

		$tables['xf_user_upgrade'] = function(Create $table)
		{
			$table->addColumn('user_upgrade_id', 'int')->autoIncrement();
			$table->addColumn('title', 'varchar', 50);
			$table->addColumn('description', 'text');
			$table->addColumn('display_order', 'int')->setDefault(0);
			$table->addColumn('extra_group_ids', 'varbinary', 255)->setDefault('');
			$table->addColumn('recurring', 'tinyint', 3)->setDefault(0);
			$table->addColumn('cost_amount', 'decimal', '10,2');
			$table->addColumn('cost_currency', 'varchar', 3);
			$table->addColumn('length_amount', 'tinyint', 3);
			$table->addColumn('length_unit', 'enum')->values(['day','month','year',''])->setDefault('');
			$table->addColumn('disabled_upgrade_ids', 'varbinary', 255)->setDefault('');
			$table->addColumn('can_purchase', 'tinyint', 3)->setDefault(1);
			$table->addColumn('payment_profile_ids', 'varbinary', 255)->setDefault('');
			$table->addKey('display_order');
		};

		$tables['xf_user_upgrade_active'] = function(Create $table)
		{
			$table->addColumn('user_upgrade_record_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('purchase_request_key', 'varbinary', 32)->nullable();
			$table->addColumn('user_upgrade_id', 'int');
			$table->addColumn('extra', 'mediumblob');
			$table->addColumn('start_date', 'int');
			$table->addColumn('end_date', 'int')->setDefault(0);
			$table->addUniqueKey(['user_id', 'user_upgrade_id'], 'user_id_upgrade_id');
			$table->addKey('end_date');
			$table->addKey('start_date');
		};

		$tables['xf_user_upgrade_expired'] = function(Create $table)
		{
			$table->addColumn('user_upgrade_record_id', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('purchase_request_key', 'varbinary', 32)->nullable();
			$table->addColumn('user_upgrade_id', 'int');
			$table->addColumn('extra', 'mediumblob');
			$table->addColumn('start_date', 'int');
			$table->addColumn('end_date', 'int')->setDefault(0);
			$table->addColumn('original_end_date', 'int')->setDefault(0);
			$table->addPrimaryKey('user_upgrade_record_id');
			$table->addKey('end_date');
		};

		$tables['xf_warning'] = function(Create $table)
		{
			$table->addColumn('warning_id', 'int')->autoIncrement();
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('content_title', 'varchar', 255);
			$table->addColumn('user_id', 'int');
			$table->addColumn('warning_date', 'int');
			$table->addColumn('warning_user_id', 'int');
			$table->addColumn('warning_definition_id', 'int');
			$table->addColumn('title', 'varchar', 255);
			$table->addColumn('notes', 'text');
			$table->addColumn('points', 'smallint', 5);
			$table->addColumn('expiry_date', 'int');
			$table->addColumn('is_expired', 'tinyint', 3);
			$table->addColumn('extra_user_group_ids', 'varbinary', 255);
			$table->addKey(['content_type', 'content_id'], 'content_type_id');
			$table->addKey(['user_id', 'warning_date'], 'user_id_date');
			$table->addKey(['is_expired', 'expiry_date'], 'is_expired_expiry');
			$table->addKey('warning_user_id');
		};

		$tables['xf_warning_action'] = function(Create $table)
		{
			$table->addColumn('warning_action_id', 'int')->autoIncrement();
			$table->addColumn('points', 'smallint', 5);
			$table->addColumn('action', 'varbinary', 25);
			$table->addColumn('action_length_type', 'varbinary', 25);
			$table->addColumn('action_length', 'smallint', 5);
			$table->addColumn('extra_user_group_ids', 'varbinary', 255);
			$table->addKey('points');
		};

		$tables['xf_warning_action_trigger'] = function(Create $table)
		{
			$table->addColumn('action_trigger_id', 'int')->autoIncrement();
			$table->addColumn('warning_action_id', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('trigger_points', 'smallint', 5);
			$table->addColumn('action_date', 'int');
			$table->addColumn('action', 'varbinary', 25);
			$table->addColumn('min_unban_date', 'int')->setDefault(0);
			$table->addKey(['user_id', 'trigger_points'], 'user_id_points');
		};

		$tables['xf_warning_definition'] = function(Create $table)
		{
			$table->addColumn('warning_definition_id', 'int')->autoIncrement();
			$table->addColumn('points_default', 'smallint', 5);
			$table->addColumn('expiry_type', 'enum')->values(['never','days','weeks','months','years']);
			$table->addColumn('expiry_default', 'smallint', 5);
			$table->addColumn('extra_user_group_ids', 'varbinary', 255);
			$table->addColumn('is_editable', 'tinyint', 3);
			$table->addKey('points_default');
		};

		$tables['xf_widget_position'] = function(Create $table)
		{
			$table->addColumn('position_id', 'varbinary', 50);
			$table->addColumn('active', 'tinyint', 3);
			$table->addColumn('addon_id', 'varbinary', 50);
			$table->addPrimaryKey('position_id');
		};

		$tables['xf_widget_definition'] = function(Create $table)
		{
			$table->addColumn('definition_id', 'varbinary', 25);
			$table->addColumn('definition_class', 'varchar', 100);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('definition_id');
		};

		$tables['xf_widget'] = function (Create $table)
		{
			$table->addColumn('widget_id', 'int')->autoIncrement();
			$table->addColumn('widget_key', 'varbinary', 50);
			$table->addColumn('definition_id', 'varbinary', 25);
			$table->addColumn('positions', 'blob');
			$table->addColumn('options', 'blob');
			$table->addColumn('display_condition', 'mediumtext');
			$table->addColumn('condition_expression', 'mediumblob');
			$table->addUniqueKey('widget_key');
		};

		return $tables;
	}

	public function getData()
	{
		$data = [];

		$data['xf_addon'] = "
			INSERT INTO xf_addon
				(
					addon_id,
					title,
					version_string,
					version_id,
					json_hash,
					active
				)
			VALUES
				('XF', 'XenForo', '" . \XF::$version . "', '" . \XF::$versionId . "', '', 1)";

		$data['xf_style'] = "
			INSERT INTO xf_style
				(style_id, parent_id, parent_list, title, properties)
			VALUES
				(1, 0, '1,0', 'Default style', '[]')";

		$utf8Ellipsis = chr(0xE2) . chr(0x80) . chr(0xA6); // http://www.fileformat.info/info/unicode/char/2026/index.htm

		$data['xf_language'] = "
			INSERT INTO xf_language
				(language_id, parent_id, parent_list, title, 
				date_format, time_format, currency_format, 
				decimal_point, thousands_separator, 
				label_separator, comma_separator, ellipsis, parenthesis_open, parenthesis_close,
				phrase_cache, language_code)
			VALUES
				(1, 0, '1,0', 'English (US)', 
				'M j, Y', 'g:i A', '{symbol}{value}', 
				'.', ',', 
				':', ', ', '" . $utf8Ellipsis . "', '(', ')',
				'', 'en-US')
		";

		$data['xf_navigation_type'] = "
			INSERT INTO `xf_navigation_type`
				(`navigation_type_id`, `handler_class`, `display_order`)
			VALUES
				('basic', 'XF:BasicType', 10),
				('callback', 'XF:CallbackType', 1000),
				('node', 'XF:NodeType', 100)
		";

		$data['xf_node_type'] = "
			INSERT INTO xf_node_type
				(node_type_id, entity_identifier, permission_group_id, admin_route, public_route, handler_class)
			VALUES
				('Category', 'XF:Category', 'category', 'categories', 'categories', 'XF:Category'),
				('Forum', 'XF:Forum', 'forum', 'forums', 'forums', 'XF:Forum'),
				('LinkForum', 'XF:LinkForum', 'linkForum', 'link-forums', 'link-forums', 'XF:LinkForum'),
				('Page', 'XF:Page', 'page', 'pages', 'pages', 'XF:Page')
		";

		$data['xf_user_field'] = "
			INSERT INTO xf_user_field
				(field_id, display_group, display_order, field_type, field_choices, match_type, match_params, max_length, display_template)
			VALUES
				('skype', 'contact', 50, 'textbox', '', 'regex', '{\"regex\":\"^[a-zA-Z0-9-_.,@:]+$\"}', 30, ''),
				('facebook', 'contact', 70, 'textbox', '', 'validator', '{\"validator\":\"Facebook\"}', 0, ''),
				('twitter', 'contact', 80, 'textbox', '', 'validator', '{\"validator\":\"Twitter\"}', 0, '')
		";

		$data['xf_warning_definition'] = "
			INSERT INTO xf_warning_definition
				(warning_definition_id, points_default, expiry_type, expiry_default, extra_user_group_ids, is_editable)
			VALUES
				(1, 1, 'months', 1, '', 1),
				(2, 1, 'months', 1, '', 1),
				(3, 1, 'months', 1, '', 1),
				(4, 1, 'months', 1, '', 1)
		";

		$data['xf_phrase'] = "
			INSERT INTO xf_phrase
				(language_id, title, phrase_text, global_cache, addon_id)
			VALUES
				(0, 'user_field_title.skype', 'Skype', 0, ''),
				(0, 'user_field_desc.skype', '', 0, ''),
				(0, 'user_field_title.facebook', 'Facebook', 0, ''),
				(0, 'user_field_desc.facebook', '', 0, ''),
				(0, 'user_field_title.twitter', 'Twitter', 0, ''),
				(0, 'user_field_desc.twitter', '', 0, ''),
				
				(0, 'trophy_description.1', 'Post a message somewhere on the site to receive this.', 0, ''),
				(0, 'trophy_title.1', 'First message', 0, ''),
				(0, 'trophy_description.2', '30 messages posted. You must like it here!', 0, ''),
				(0, 'trophy_title.2', 'Keeps coming back', 0, ''),
				(0, 'trophy_description.3', 'You''ve posted 100 messages. I hope this took you more than a day!', 0, ''),
				(0, 'trophy_title.3', 'Can''t stop!', 0, ''),
				(0, 'trophy_description.4', '1,000 messages? Impressive!', 0, ''),
				(0, 'trophy_title.4', 'Addicted', 0, ''),
				(0, 'trophy_description.5', 'Somebody out there reacted positively to one of your messages. Keep posting like that for more!', 0, ''),
				(0, 'trophy_title.5', 'Somebody likes you', 0, ''),
				(0, 'trophy_description.6', 'Your messages have been positively reacted to 25 times.', 0, ''),
				(0, 'trophy_title.6', 'I like it a lot', 0, ''),
				(0, 'trophy_description.7', 'Content you have posted has attracted a positive reaction score of 100.', 0, ''),
				(0, 'trophy_title.7', 'Seriously likeable!', 0, ''),
				(0, 'trophy_description.8', 'Your content has been positively reacted to 250 times.', 0, ''),
				(0, 'trophy_title.8', 'Can''t get enough of your stuff', 0, ''),
				(0, 'trophy_description.9', 'Content you have posted has attracted 500 positive reactions.', 0, ''),
				(0, 'trophy_title.9', 'I LOVE IT!', 0, ''),
				
				(0, 'warning_title.1', 'Inappropriate content', 0, ''),
				(0, 'warning_conv_title.1', 'Inappropriate content', 0, ''),
				(0, 'warning_conv_text.1', '{name},\n\nYour message ([url={url}]{title}[/url]) contains inappropriate content:\n[quote]{content}[/quote]\n\nPlease do not discuss content of this nature on our site. This does not follow our rules. Your message may have been removed or altered.\n\nYour account''s access may be limited based on these actions. Please keep this in mind when posting or using our site.', 0, ''),
				(0, 'warning_title.2', 'Inappropriate behavior', 0, ''),
				(0, 'warning_conv_title.2', 'Inappropriate behavior', 0, ''),
				(0, 'warning_conv_text.2', '{name},\n\nYour actions in this message ([url={url}]{title}[/url]) are not appropriate:\n[quote]{content}[/quote]\n\nWe cannot allow users to be abusive, overly aggressive, threatening, or to \"troll\". This does not follow our rules. Your message may have been removed or altered.\n\nYour account''s access may be limited based on these actions. Please keep this in mind when posting or using our site.', 0, ''),
				(0, 'warning_title.3', 'Inappropriate language', 0, ''),
				(0, 'warning_conv_title.3', 'Inappropriate language', 0, ''),
				(0, 'warning_conv_text.3', '{name},\n\nYour message ([url={url}]{title}[/url]) contains inappropriate language:\n[quote]{content}[/quote]\n\nThis does not follow our rules. Your message may have been removed or altered.\n\nYour account''s access may be limited based on these actions. Please keep this in mind when posting or using our site.', 0, ''),
				(0, 'warning_title.4', 'Inappropriate advertising / spam', 0, ''),
				(0, 'warning_conv_title.4', 'Inappropriate advertising / spam', 0, ''),
				(0, 'warning_conv_text.4', '{name},\n\nYour message ([url={url}]{title}[/url]) contains inappropriate advertising or spam:\n[quote]{content}[/quote]\n\nThis does not follow our rules. Your message may have been removed or altered.\n\nYour account''s access may be limited based on these actions. Please keep this in mind when posting or using our site.', 0, ''),
				
				(0, 'widget.member_wrapper_find_member', '', 0, ''),
				(0, 'widget.member_wrapper_newest_members', '', 0, ''),
				(0, 'widget.online_list_online_statistics', '', 0, ''),
				(0, 'widget.whats_new_new_posts', '', 0, ''),
				(0, 'widget.whats_new_new_profile_posts', '', 0, ''),
				(0, 'widget.forum_overview_members_online', '', 0, ''),
				(0, 'widget.forum_overview_new_posts', '', 0, ''),
				(0, 'widget.forum_overview_new_profile_posts', '', 0, ''),
				(0, 'widget.forum_overview_forum_statistics', '', 0, ''),
				(0, 'widget.forum_overview_share_page', '', 0, ''),
				
				(0, 'editor_dropdown.xfInsert', 'Insert', 0, ''),
				(0, 'editor_dropdown.xfList', 'List', 0, ''),
				
				(0, 'reaction_title.1', 'Like', 0, ''),
				(0, 'reaction_title.2', 'Love', 0, ''),
				(0, 'reaction_title.3', 'Haha', 0, ''),
				(0, 'reaction_title.4', 'Wow', 0, ''),
				(0, 'reaction_title.5', 'Sad', 0, ''),
				(0, 'reaction_title.6', 'Angry', 0, '')
		";

		$data['xf_payment_provider'] = "
			INSERT INTO xf_payment_provider
				(provider_id, provider_class, addon_id)
			VALUES
				('braintree', 'XF:Braintree', 'XF'),
				('paypal', 'XF:PayPal', 'XF'),
				('stripe', 'XF:Stripe', 'XF'),
				('twocheckout', 'XF:TwoCheckout', 'XF')
		";

		$data['xf_purchasable'] = "
			INSERT INTO xf_purchasable
				(purchasable_type_id, purchasable_class, addon_id)
			VALUES
				('user_upgrade', 'XF:UserUpgrade', 'XF')
		";

		$data['xf_tfa_provider'] = "
			INSERT INTO `xf_tfa_provider`
				(`provider_id`, `provider_class`, `priority`, `active`)
			VALUES
				('backup', 'XF:Backup', 1000, 1),
				('email', 'XF:Email', 900, 1),
				('totp', 'XF:Totp', 10, 1),
				('authy', 'XF:Authy', 5, 1)
		";

		$data['xf_connected_account_provider'] = "
			INSERT INTO `xf_connected_account_provider`
				(`provider_id`, `provider_class`, `display_order`, `options`)
			VALUES
				('facebook', 'XF:Provider\\\\Facebook', 10, ''),
				('twitter', 'XF:Provider\\\\Twitter', 20, ''),
				('google', 'XF:Provider\\\\Google', 30, ''),
				('github', 'XF:Provider\\\\GitHub', 40, ''),
				('linkedin', 'XF:Provider\\\\Linkedin', 50, ''),
				('microsoft', 'XF:Provider\\\\Microsoft', 60, ''),
				('yahoo', 'XF:Provider\\\\Yahoo', 70, '')
		";

		$data['xf_user_group'] = "
			INSERT INTO xf_user_group
				(user_group_id, title, display_style_priority, username_css, user_title)
			VALUES
				(1, 'Unregistered / Unconfirmed', 0, '', 'Guest'),
				(2, 'Registered', 0, '', ''),
				(3, 'Administrative', 1000, '', 'Administrator'),
				(4, 'Moderating', 900, '', 'Moderator')
		";

		$data['xf_permission_combination'] = "
			INSERT INTO xf_permission_combination
				(permission_combination_id, user_id, user_group_list, cache_value)
			VALUES
				(1, 0, '1', ''),
				(2, 0, '2', '')
		";

		$data['xf_permission_combination_user_group'] = "
			INSERT INTO xf_permission_combination_user_group
				(user_group_id, permission_combination_id)
			VALUES
				(1, 1),
				(2, 2)
		";

		$data['xf_permission_entry'] = "
			INSERT INTO xf_permission_entry
				(user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
			VALUES
				(1, 0, 'forum', 'viewContent', 'allow', 0),
				(1, 0, 'forum', 'viewOthers', 'allow', 0),
				(1, 0, 'general', 'submitWithoutApproval', 'allow', 0),
				(1, 0, 'general', 'editProfile', 'allow', 0),
				(1, 0, 'general', 'search', 'allow', 0),
				(1, 0, 'general', 'view', 'allow', 0),
				(1, 0, 'general', 'viewNode', 'allow', 0),
				(1, 0, 'general', 'viewProfile', 'allow', 0),
				(1, 0, 'general', 'viewMemberList', 'allow', 0),
				(1, 0, 'general', 'usePush', 'allow', 0),
				(1, 0, 'general', 'useContactForm', 'allow', 0),
				(1, 0, 'profilePost', 'view', 'allow', 0),
				(2, 0, 'avatar', 'allowed', 'allow', 0),
				(2, 0, 'conversation', 'start', 'allow', 0),
				(2, 0, 'conversation', 'receive', 'allow', 0),
				(2, 0, 'conversation', 'maxRecipients', 'use_int', 5),
				(2, 0, 'conversation', 'editOwnMessage', 'allow', 0),
				(2, 0, 'conversation', 'editOwnMessageTimeLimit', 'use_int', 5),
				(2, 0, 'conversation', 'react', 'allow', 0),
				(2, 0, 'forum', 'deleteOwnPost', 'allow', 0),
				(2, 0, 'forum', 'editOwnPost', 'allow', 0),
				(2, 0, 'forum', 'editOwnThreadTitle', 'allow', 0),
				(2, 0, 'forum', 'editOwnPostTimeLimit', 'use_int', -1),
				(2, 0, 'forum', 'postReply', 'allow', 0),
				(2, 0, 'forum', 'postThread', 'allow', 0),
				(2, 0, 'forum', 'tagOwnThread', 'allow', 0),
				(2, 0, 'forum', 'uploadAttachment', 'allow', 0),
			    (2, 0, 'forum', 'uploadVideo', 'allow', 0),
				(2, 0, 'forum', 'viewAttachment', 'allow', 0),
				(2, 0, 'forum', 'viewContent', 'allow', 0),
				(2, 0, 'forum', 'viewOthers', 'allow', 0),
				(2, 0, 'forum', 'votePoll', 'allow', 0),
				(2, 0, 'forum', 'react', 'allow', 0),
				(2, 0, 'general', 'editProfile', 'allow', 0),
				(2, 0, 'general', 'editSignature', 'allow', 0),
				(2, 0, 'general', 'submitWithoutApproval', 'allow', 0),
				(2, 0, 'general', 'createTag', 'allow', 0),
				(2, 0, 'general', 'search', 'allow', 0),
				(2, 0, 'general', 'view', 'allow', 0),
				(2, 0, 'general', 'viewNode', 'allow', 0),
				(2, 0, 'general', 'viewProfile', 'allow', 0),
				(2, 0, 'general', 'viewMemberList', 'allow', 0),
				(2, 0, 'general', 'report', 'allow', 0),
				(2, 0, 'general', 'useContactForm', 'allow', 0),
				(2, 0, 'general', 'maxMentionedUsers', 'use_int', 5),
				(2, 0, 'signature', 'basicText', 'allow', 0),
				(2, 0, 'signature', 'extendedText', 'allow', 0),
				(2, 0, 'signature', 'align', 'allow', 0),
				(2, 0, 'signature', 'list', 'allow', 0),
				(2, 0, 'signature', 'image', 'allow', 0),
				(2, 0, 'signature', 'link', 'allow', 0),
				(2, 0, 'signature', 'media', 'allow', 0),
				(2, 0, 'signature', 'block', 'allow', 0),
				(2, 0, 'signature', 'maxPrintable', 'use_int', -1),
				(2, 0, 'signature', 'maxLines', 'use_int', -1),
				(2, 0, 'signature', 'maxLinks', 'use_int', -1),
				(2, 0, 'signature', 'maxImages', 'use_int', -1),
				(2, 0, 'signature', 'maxSmilies', 'use_int', -1),
				(2, 0, 'signature', 'maxTextSize', 'use_int', -1),
				(2, 0, 'profilePost', 'deleteOwn', 'allow', 0),
				(2, 0, 'profilePost', 'editOwn', 'allow', 0),
				(2, 0, 'profilePost', 'manageOwn', 'allow', 0),
				(2, 0, 'profilePost', 'post', 'allow', 0),
				(2, 0, 'profilePost', 'comment', 'allow', 0),
				(2, 0, 'profilePost', 'view', 'allow', 0),
				(2, 0, 'profilePost', 'react', 'allow', 0),
				(3, 0, 'avatar', 'allowed', 'allow', 0),
				(3, 0, 'conversation', 'maxRecipients', 'use_int', -1),
				(3, 0, 'conversation', 'editAnyMessage', 'allow', 0),
				(3, 0, 'conversation', 'alwaysInvite', 'allow', 0),
				(3, 0, 'conversation', 'uploadAttachment', 'allow', 0),
			    (3, 0, 'conversation', 'uploadVideo', 'allow', 0),
				(3, 0, 'general', 'maxMentionedUsers', 'use_int', -1),
				(3, 0, 'forum', 'deleteOwnThread', 'allow', 0),
				(3, 0, 'forum', 'manageOthersTagsOwnThread', 'allow', 0),
				(3, 0, 'forum', 'editOwnPostTimeLimit', 'use_int', -1),
				(3, 0, 'general', 'bypassFloodCheck', 'allow', 0),
				(3, 0, 'general', 'editCustomTitle', 'allow', 0),
				(4, 0, 'conversation', 'maxRecipients', 'use_int', -1),
				(4, 0, 'conversation', 'uploadAttachment', 'allow', 0),
				(4, 0, 'general', 'maxMentionedUsers', 'use_int', -1),
				(4, 0, 'forum', 'editOwnPostTimeLimit', 'use_int', -1),
				(4, 0, 'general', 'bypassFloodCheck', 'allow', 0),
				(4, 0, 'general', 'editCustomTitle', 'allow', 0),
				(2, 0, 'bookmark', 'view', 'allow', 0),
				(2, 0, 'bookmark', 'create', 'allow', 0)
		";

		$data['xf_node'] = "
			INSERT INTO xf_node
				(node_id, title, description, node_type_id, parent_node_id, display_order, lft, rgt, depth, breadcrumb_data)
			VALUES
				(1, 'Main category', '', 'Category', 0, 1, 1, 4, 0, '[]'),
				(2, 'Main forum', '', 'Forum', 1, 1, 2, 3, 1, '{\"1\":{\"node_id\":1,\"title\":\"Main category\",\"depth\":0,\"lft\":1,\"node_name\":null,\"node_type_id\":\"Category\"}}')
		";

		$data['xf_category'] = "
			INSERT INTO xf_category
				(node_id)
			VALUES
				(1)
		";

		$data['xf_forum'] = "
			INSERT INTO xf_forum
				(node_id, discussion_count, message_count, last_post_id, last_post_date, last_post_user_id, last_post_username, field_cache, prefix_cache, prompt_cache)
			VALUES
				(2, 0, 0, 0, 0, 0, '', '', '', '')
		";

		$data['xf_trophy'] = '
			REPLACE INTO xf_trophy
				(trophy_id, trophy_points, user_criteria)
			VALUES
				(1, 1, \'[{"rule":"messages_posted","data":{"messages":"1"}}]\'),
				(2, 5, \'[{"rule":"messages_posted","data":{"messages":"30"}}]\'),
				(3, 10, \'[{"rule":"messages_posted","data":{"messages":"100"}}]\'),
				(4, 20, \'[{"rule":"messages_posted","data":{"messages":"1000"}}]\'),
				(5, 2, \'[{"rule":"reaction_score","data":{"reactions":"1"}}]\'),
				(6, 10, \'[{"rule":"reaction_score","data":{"reactions":"25"}}]\'),
				(7, 15, \'[{"rule":"reaction_score","data":{"reactions":"100"}}]\'),
				(8, 20, \'[{"rule":"reaction_score","data":{"reactions":"250"}}]\'),
				(9, 30, \'[{"rule":"reaction_score","data":{"reactions":"500"}}]\')
		';

		$data['xf_user_title_ladder'] = "
			INSERT INTO xf_user_title_ladder
				(minimum_level, title)
			VALUES
				(0, 'New member'),
				(5, 'Member'),
				(25, 'Active member'),
				(45, 'Well-known member')
		";

		$data['xf_reaction'] = '
			INSERT INTO `xf_reaction` (`reaction_id`, `text_color`, `image_url`, `image_url_2x`, `sprite_mode`, `sprite_params`, `reaction_score`, `display_order`, `active`)
			VALUES
				(1, \'@xf-linkColor\', \'styles/default/xenforo/reactions/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"32\",\"h\":\"32\",\"x\":\"0\",\"y\":\"0\",\"bs\":\"100%\"}\', 1, 100, 1),
				(2, \'#E81C27\', \'styles/default/xenforo/reactions/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"32\",\"h\":\"32\",\"x\":\"0\",\"y\":\"-32\",\"bs\":\"100%\"}\', 1, 200, 1),
				(3, \'#FDCA47\', \'styles/default/xenforo/reactions/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"32\",\"h\":\"32\",\"x\":\"0\",\"y\":\"-64\",\"bs\":\"100%\"}\', 1, 300, 1),
				(4, \'#FDCA47\', \'styles/default/xenforo/reactions/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"32\",\"h\":\"32\",\"x\":\"0\",\"y\":\"-96\",\"bs\":\"100%\"}\', 0, 400, 1),
				(5, \'#FDCA47\', \'styles/default/xenforo/reactions/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"32\",\"h\":\"32\",\"x\":\"0\",\"y\":\"-128\",\"bs\":\"100%\"}\', 0, 500, 1),
				(6, \'#FF4D4D\', \'styles/default/xenforo/reactions/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"32\",\"h\":\"32\",\"x\":\"0\",\"y\":\"-160\",\"bs\":\"100%\"}\', 0, 600, 1);
		';

		$data['xf_smilie'] = '
			INSERT INTO `xf_smilie` (`smilie_id`, `title`, `smilie_text`, `image_url`, `image_url_2x`, `sprite_mode`, `sprite_params`, `smilie_category_id`, `display_order`, `display_in_editor`)
			VALUES
				(1, \'Smile\', \':)\n:-)\n(:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"0\",\"bs\":\"100%\"}\', 0, 10, 1),
				(2, \'Wink\', \';)\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-176\",\"bs\":\"100%\"}\', 0, 20, 1),
				(3, \'Frown\', \':(\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-264\",\"bs\":\"100%\"}\', 0, 30, 1),
				(4, \'Mad\', \':mad:\n>:(\n:@\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-66\",\"bs\":\"100%\"}\', 0, 40, 1),
				(5, \'Confused\', \':confused:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-154\",\"bs\":\"100%\"}\', 0, 50, 1),
				(6, \'Cool\', \':cool:\n8-)\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-44\",\"bs\":\"100%\"}\', 0, 60, 1),
				(7, \'Stick out tongue\', \':p\n:P\n:-p\n:-P\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-132\",\"bs\":\"100%\"}\', 0, 70, 1),
				(8, \'Big grin\', \':D\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-22\",\"bs\":\"100%\"}\', 0, 80, 1),
				(9, \'Eek!\', \':eek:\n:o\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-242\",\"bs\":\"100%\"}\', 0, 90, 1),
				(10, \'Oops!\', \':oops:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-220\",\"bs\":\"100%\"}\', 0, 100, 1),
				(11, \'Roll eyes\', \':rolleyes:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-110\",\"bs\":\"100%\"}\', 0, 110, 1),
				(12, \'Er... what?\', \'o_O\nO_o\no.O\nO.o\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-308\",\"bs\":\"100%\"}\', 0, 120, 1),
				(13, \'Cautious\', \':cautious:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-418\",\"bs\":\"100%\"}\', 0, 130, 1),
				(14, \'Censored\', \':censored:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-660\",\"bs\":\"100%\"}\', 0, 140, 1),
				(15, \'Crying\', \':cry:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-484\",\"bs\":\"100%\"}\', 0, 150, 1),
				(16, \'Love\', \':love:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-506\",\"bs\":\"100%\"}\', 0, 160, 1),
				(17, \'Laugh\', \':LOL:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-396\",\"bs\":\"100%\"}\', 0, 170, 1),
				(18, \'ROFL\', \':ROFLMAO:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-286\",\"bs\":\"100%\"}\', 0, 180, 1),
				(19, \'Sick\', \':sick:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-682\",\"bs\":\"100%\"}\', 0, 190, 1),
				(20, \'Sleep\', \':sleep:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-352\",\"bs\":\"100%\"}\', 0, 200, 1),
				(21, \'Sneaky\', \':sneaky:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-704\",\"bs\":\"100%\"}\', 0, 210, 1),
				(22, \'Thumbs up\', \'(y)\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-550\",\"bs\":\"100%\"}\', 0, 220, 1),
				(23, \'Thumbs down\', \'(n)\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-572\",\"bs\":\"100%\"}\', 0, 230, 1),
				(24, \'Unsure\', \':unsure:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-528\",\"bs\":\"100%\"}\', 0, 240, 1),
				(25, \'Whistling\', \':whistle:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-748\",\"bs\":\"100%\"}\', 0, 250, 1),
				(26, \'Coffee\', \':coffee:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-198\",\"bs\":\"100%\"}\', 0, 260, 1),
				(27, \'Giggle\', \':giggle:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-330\",\"bs\":\"100%\"}\', 0, 270, 1),
				(28, \'Alien\', \':alien:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-616\",\"bs\":\"100%\"}\', 0, 280, 1),
				(29, \'Devil\', \':devilish:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-638\",\"bs\":\"100%\"}\', 0, 290, 1),
				(30, \'Geek\', \':geek:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-88\",\"bs\":\"100%\"}\', 0, 300, 1),
				(31, \'Poop\', \':poop:\', \'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"22\",\"h\":\"22\",\"x\":\"0\",\"y\":\"-462\",\"bs\":\"100%\"}\', 0, 310, 1);
		';

		$data['xf_widget'] = "
			INSERT INTO `xf_widget` (`widget_key`, `definition_id`, `options`, `positions`, `display_condition`, `condition_expression`)
			VALUES
				('member_wrapper_find_member',			'find_member',			'[]',	'{\"member_wrapper_sidenav\":\"10\"}', '', ''),
				('member_wrapper_newest_members',		'newest_members',		'[]',	'{\"member_wrapper_sidenav\":\"20\"}', '', ''),
				
				('online_list_online_statistics',		'online_statistics',	'[]',	'{\"online_list_sidebar\":\"10\"}', '', ''),
				
				('whats_new_new_posts',					'new_posts',			'{\"limit\": 10, \"style\": \"full\"}',	'{\"whats_new_overview\":\"10\"}', '', ''),
				('whats_new_new_profile_posts',			'new_profile_posts',	'{\"style\": \"full\"}', '{\"whats_new_overview\":\"10000\"}', '', ''),
				
				('forum_overview_members_online',		'members_online',		'[]',	'{\"forum_list_sidebar\":\"20\",\"forum_new_posts_sidebar\":\"20\"}', '', ''),
				('forum_overview_new_posts',			'new_posts',			'[]',	'{\"forum_list_sidebar\":\"30\"}', '', ''),
				('forum_overview_new_profile_posts',	'new_profile_posts',	'[]',	'{\"forum_list_sidebar\":\"40\",\"forum_new_posts_sidebar\":\"30\"}', '', ''),
				('forum_overview_forum_statistics',		'forum_statistics',		'[]',	'{\"forum_list_sidebar\":\"50\",\"forum_new_posts_sidebar\":\"40\"}', '', ''),
				('forum_overview_share_page',			'share_page',			'[]',	'{\"forum_list_sidebar\":\"60\"}', '', '')
		";

		$data['xf_editor_dropdown'] = "
			INSERT INTO xf_editor_dropdown
				(cmd, icon, buttons, display_order, active)
			VALUES
				('xfInsert', 'fa-ellipsis-h', '[\"xfMedia\",\"xfQuote\",\"xfSpoiler\",\"xfInlineSpoiler\",\"xfCode\",\"xfInlineCode\"]', 10, 1),
				('xfList', 'fa-list', '[\"formatOL\",\"formatUL\",\"indent\",\"outdent\"]', 20, 1)	
		";

		return $data;
	}
}