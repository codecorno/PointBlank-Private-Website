<?php

namespace XF\Install\Upgrade;

use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Version2010010 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.1.0 Alpha';
	}

	public function step1()
	{
		$this->createTable('xf_addon_install_batch', function (Create $table) {
			$table->addColumn('batch_id', 'int')->autoIncrement();
			$table->addColumn('start_date', 'int')->setDefault(0);
			$table->addColumn('complete_date', 'int')->setDefault(0);
			$table->addColumn('addon_ids', 'mediumblob');
			$table->addColumn('results', 'blob');
		});

		$this->createTable('xf_api_attachment_key', function (Create $table) {
			$table->addColumn('attachment_key', 'varbinary', 32)->primaryKey();
			$table->addColumn('create_date', 'int');
			$table->addColumn('user_id', 'int');
			$table->addColumn('temp_hash', 'varbinary', 32);
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('context', 'blob');
			$table->addKey('create_date');
		});

		$this->createTable('xf_api_key', function (Create $table) {
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
		});

		$this->createTable('xf_api_key_scope', function (Create $table) {
			$table->addColumn('api_key_id', 'int');
			$table->addColumn('api_scope_id', 'varbinary', 50);
			$table->addPrimaryKey(['api_key_id', 'api_scope_id']);
		});

		$this->createTable('xf_api_scope', function (Create $table) {
			$table->addColumn('api_scope_id', 'varbinary', 50);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('api_scope_id');
		});

		$this->createTable('xf_bookmark_item', function (Create $table) {
			$table->addColumn('bookmark_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('content_id', 'int');
			$table->addColumn('bookmark_date', 'int');
			$table->addColumn('message', 'text');
			$table->addColumn('labels', 'mediumblob');
			$table->addKey(['content_type', 'content_id']);
			$table->addUniqueKey(['user_id', 'content_type', 'content_id']);
		});

		$this->createTable('xf_bookmark_label', function (Create $table) {
			$table->addColumn('label_id', 'int')->autoIncrement();
			$table->addColumn('label', 'varchar', 100);
			$table->addColumn('label_url', 'varchar', 100);
			$table->addColumn('user_id', 'int');
			$table->addColumn('use_count', 'int')->setDefault(0);
			$table->addColumn('last_use_date', 'int')->setDefault(0);
			$table->addUniqueKey(['label', 'user_id']);
			$table->addUniqueKey(['label_url', 'user_id']);
			$table->addKey('use_count');
		});

		$this->createTable('xf_bookmark_label_use', function (Create $table) {
			$table->addColumn('label_id', 'int');
			$table->addColumn('bookmark_id', 'int');
			$table->addColumn('use_date', 'int')->setDefault(0);
			$table->addPrimaryKey(['label_id', 'bookmark_id']);
		});

		$this->createTable('xf_editor_dropdown', function (Create $table) {
			$table->addColumn('cmd', 'varbinary', 50);
			$table->addColumn('icon', 'varchar', 50)->setDefault('')->comment('Optional icon');
			$table->addColumn('buttons', 'blob');
			$table->addColumn('display_order', 'int')->setDefault(0);
			$table->addColumn('active', 'tinyint')->setDefault(1);
			$table->addPrimaryKey('cmd');
		});

		// This table was actually added later in 2.1, but it's needed here for when people upgrade to 2.1
		$this->createTable('xf_json_convert_error', function(Create $table)
		{
			$table->addColumn('error_id', 'int')->autoIncrement();
			$table->addColumn('table_name', 'varbinary', 100);
			$table->addColumn('column_name', 'varbinary', 100);
			$table->addColumn('pk_id', 'int');
			$table->addColumn('original_value', 'mediumblob');
			$table->addUniqueKey(['table_name', 'column_name', 'pk_id'], 'table_column_pk');
		});

		$this->createTable('xf_reaction', function (Create $table) {
			$table->addColumn('reaction_id', 'int')->autoIncrement();
			$table->addColumn('title', 'varchar', 100);
			$table->addColumn('text_color', 'varchar', 100);
			$table->addColumn('image_url', 'varchar', 200);
			$table->addColumn('image_url_2x', 'varchar', 200)->setDefault('');
			$table->addColumn('sprite_mode', 'tinyint', 3)->setDefault(0);
			$table->addColumn('sprite_params', 'blob');
			$table->addColumn('reaction_score', 'int')->unsigned(false)->setDefault(1);
			$table->addColumn('display_order', 'int')->setDefault(1);
			$table->addColumn('active', 'tinyint', 3)->setDefault(1);
			$table->addKey('display_order');
		});

		$this->createTable('xf_unfurl_result', function (Create $table) {
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
		});

		$this->createTable('xf_upgrade_check', function (Create $table) {
			$table->addColumn('check_id', 'int')->autoIncrement();
			$table->addColumn('error_code', 'varchar', 50)->nullable();
			$table->addColumn('check_date', 'int')->nullable();
			$table->addColumn('board_url_valid', 'tinyint')->nullable();
			$table->addColumn('branding_valid', 'tinyint')->nullable();
			$table->addColumn('license_expired', 'tinyint')->nullable();
			$table->addColumn('invalid_add_ons', 'blob')->nullable();
			$table->addColumn('available_updates', 'blob')->nullable();
			$table->addKey('check_date');
		});

		$this->createTable('xf_user_push_optout', function (Create $table) {
			$table->addColumn('user_id', 'int');
			$table->addColumn('push', 'varbinary', 50);
			$table->addPrimaryKey(['user_id', 'push']);
		});

		$this->createTable('xf_user_push_subscription', function (Create $table) {
			$table->addColumn('endpoint_id', 'int')->autoIncrement();
			$table->addColumn('endpoint_hash', 'varbinary', 32);
			$table->addColumn('endpoint', 'text');
			$table->addColumn('user_id', 'int');
			$table->addColumn('data', 'mediumblob')->nullable();
			$table->addColumn('last_seen', 'int');
			$table->addUniqueKey('endpoint_hash');
			$table->addKey('user_id');
		});
	}

	public function step2()
	{
		$this->alterTable('xf_option_group', function (Alter $table) {
			$table->addColumn('icon', 'varchar', 50)
				->setDefault('')
				->comment('Optional icon')
				->after('group_id');
		});

		$this->alterTable('xf_tfa_provider', function (Alter $table) {
			$table->addColumn('options', 'blob')->nullable();
		});

		$this->alterTable('xf_widget', function (Alter $table) {
			$table->addColumn('display_condition', 'mediumtext');
			$table->addColumn('condition_expression', 'mediumblob');
		});

		$this->alterTable('xf_stats_daily', function (Alter $table) {
			$table->changeColumn('stats_type')->length(50);
		});
	}

	public function step3()
	{
		$this->alterTable('xf_profile_post', function (Alter $table) {
			$table->addColumn('embed_metadata', 'blob')->nullable();
		});
	}

	public function step4()
	{
		$this->alterTable('xf_profile_post_comment', function (Alter $table) {
			$table->addColumn('embed_metadata', 'blob')->nullable();
		});
	}

	public function step5()
	{
		// Add 'last_thread_id' to the xf_forum table
		// and update it to the correct value based on last_post_id

		$this->alterTable('xf_forum', function (Alter $table) {
			$table
				->addColumn('last_thread_id', 'int')
				->setDefault(0)
				->comment('Most recent thread_id')
				->after('last_post_username');
		});

		$this->executeUpgradeQuery('
			UPDATE xf_forum
			INNER JOIN xf_post ON (xf_post.post_id = xf_forum.last_post_id)
			SET last_thread_id = IFNULL(xf_post.thread_id, 0)
		');

		$this->alterTable('xf_node_type', function (Alter $table) {
			$table->addColumn('handler_class', 'varchar', 100)->setDefault('');
		});

		$db = $this->db();

		$nodeTypeHandlerMap = [
			'Category' => 'XF:Category',
			'Forum' => 'XF:Forum',
			'LinkForum' => 'XF:LinkForum',
			'Page' => 'XF:Page',
		];
		foreach ($nodeTypeHandlerMap AS $nodeTypeId => $handlerClass)
		{
			$db->update('xf_node_type', ['handler_class' => $handlerClass], 'node_type_id = ?', $nodeTypeId);
		}
	}

	public function step6()
	{
		$this->alterTable('xf_user_option', function (Alter $table) {
			$table->addColumn('push_on_conversation', 'tinyint', 3)->setDefault(1)->after('email_on_conversation');
			$table->addColumn('push_optout', 'text')->after('alert_optout')
				->comment('Comma-separated list of alerts from which the user has opted out for push notifications. Example: \'post_like,user_trophy\'');
		});

		$this->executeUpgradeQuery("
			UPDATE xf_user_option
			SET push_optout = alert_optout
		");

		$this->executeUpgradeQuery("
			INSERT IGNORE INTO xf_user_push_optout 
				(user_id, push)
			SELECT user_id, alert 
			FROM xf_user_alert_optout
		");

		$this->executeUpgradeQuery("
			INSERT INTO `xf_tfa_provider`
				(`provider_id`, `provider_class`, `priority`, `active`)
			VALUES
				('authy', 'XF:Authy', 5, 1)
		");

		// we stopped using this type/action combo in XF 2.0 but only removed the template in XF 2.1.
		$this->executeUpgradeQuery("
			UPDATE
				xf_user_alert
			SET
				content_type = 'trophy',
				action = 'award'
			WHERE
				content_type = 'user'
				AND action = 'trophy'
		");
	}

	public function step7()
	{
		// misc permission application

		$this->applyGlobalPermission('general', 'usePush', 'general', 'view');
		$this->applyGlobalPermission('bookmark', 'view', 'forum', 'postReply');
		$this->applyGlobalPermission('bookmark', 'create', 'forum', 'postReply');

		$this->applyContentPermission('forum', 'uploadVideo', 'forum', 'uploadAttachment');
		$this->applyGlobalPermission('conversation', 'uploadVideo', 'conversation', 'uploadAttachment');

		// setup the default editor button config

		$xfInsert = [
			'xfMedia',
			'xfQuote',
			'xfSpoiler',
			'xfInlineSpoiler',
			'xfCode',
			'xfInlineCode'
		];

		$xfList = [
			'formatOL',
			'formatUL',
			'indent',
			'outdent'
		];

		$this->executeUpgradeQuery("
			INSERT INTO xf_editor_dropdown
				(cmd, icon, buttons, display_order, active)
			VALUES
				('xfInsert', 'fa-ellipsis-h', ?, 10, 1),
				('xfList', 'fa-list', ?, 20, 1)		
		", [json_encode($xfInsert), json_encode($xfList)]);

		$this->executeUpgradeQuery("
			REPLACE INTO xf_phrase
				(language_id, title, phrase_text, global_cache, addon_id)
			VALUES
				(0, 'editor_dropdown.xfInsert', 'Insert', 0, ''),
				(0, 'editor_dropdown.xfList', 'List', 0, '')
		");

		/** @var \XF\Data\Editor $editorData */
		$editorData = $this->app->data('XF:Editor');

		$defaultButtons = $editorData->getDefaultToolbarButtons();
		$defaultButtonsXS = $editorData->getDefaultToolbarButtons(true);

		$this->executeUpgradeQuery("
			INSERT INTO xf_option
				(option_id, option_value, default_value, edit_format_params, sub_options, validation_class, validation_method)
			VALUES
				('editorToolbarConfig', '', '', '', '', '', ''),
				('editorDropdownConfig', '', '', '', '', '', '')
		");

		$optionValue = [
			'toolbarButtons' => $defaultButtons,
			'toolbarButtonsMD' => $defaultButtons,
			'toolbarButtonsSM' => $defaultButtons,
			'toolbarButtonsXS' => $defaultButtonsXS
		];

		$this->executeUpgradeQuery("
			UPDATE xf_option
			SET option_value = ?
			WHERE option_id = 'editorToolbarConfig'
		", json_encode($optionValue));

		$optionValue = $editorData->getEditableDropdownData();

		$this->executeUpgradeQuery("
			UPDATE xf_option
			SET option_value = ?
			WHERE option_id = 'editorDropdownConfig'
		", json_encode($optionValue));
	}

	// ################################## SERIALIZED TO JSON STEPS ###########################################

	public function step8()
	{
		// Convert fields that contain Entity::SERIALIZED(_ARRAY) to Entity::JSON(_ARRAY).
		// Commented-out fields are handled in separate steps, and are retained here for reference only.

		$serializedFields = [

			// SERIALIZED_ARRAY
			'XF:Admin' => ['permission_cache'],
			'XF:CaptchaQuestion' => ['answers'],
//			'XF:ConversationMaster' => ['recipients'],
//			'XF:ConversationMessage' => ['like_users'],
			'XF:CronEntry' => ['run_rules'],
//			'XF:Draft' => ['extra_data'],
//			'XF:ErrorLog' => ['request_state'],
			'XF:Forum' => ['field_cache', 'prefix_cache'],
			'XF:Language' => ['phrase_cache'],
//			'XF:NewsFeed' => ['extra_data'],
			'XF:Node' => ['breadcrumb_data'],
			'XF:Notice' => ['user_criteria', 'page_criteria'],
//			'XF:PermissionCacheContent' => ['cache_value'],
//			'XF:PermissionCombination' => ['cache_value'],
//			'XF:Poll' => ['responses'],
//			'XF:PollResponse' => ['voters'],
//			'XF:Post' => ['like_users'],
//			'XF:ProfilePost' => ['like_users', 'latest_comment_ids'],
//			'XF:ProfilePostComment' => ['like_users'],
//			'XF:Report' => ['content_info'],
			'XF:Smilie' => ['sprite_params'],
//			'XF:SpamCleanerLog' => ['data'],
//			'XF:SpamTriggerLog' => ['details', 'request_state'],
			'XF:Style' => ['properties'],
//			'XF:TagResultCache' => ['results'],
//			'XF:Thread' => ['custom_fields', 'tags'],
			'XF:Trophy' => ['user_criteria'],
//			'XF:UserAlert' => ['extra_data'],
//			'XF:UserConnectedAccount' => ['extra_data'],
//			'XF:UserGroupPromotion' => ['user_criteria'],
//			'XF:UserProfile' => ['ignored', 'custom_fields', 'connected_accounts'],
//			'XF:UserTfa' => ['provider_data'],
//			'XF:UserUpgradeActive' => ['extra'],
//			'XF:UserUpgradeExpired' => ['extra']
		];
		// note: user auth is intentionally unchanged

		// add-ons, don't forget entities that extend AbstractCategoryTree

		foreach ($serializedFields AS $entityName => $columns)
		{
			$this->entityColumnsToJson($entityName, $columns, 0, [], true);
		}
	}

	public function step9($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:ConversationMaster', ['recipients'], $position, $stepData);
	}

	public function step10($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:ConversationMessage', ['like_users'], $position, $stepData);
	}

	public function step11($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:Draft', ['extra_data'], $position, $stepData);
	}

	public function step12($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:ErrorLog', ['request_state'], $position, $stepData, false, 300);
	}

	public function step13($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:NewsFeed', ['extra_data'], $position, $stepData);
	}

	public function step14($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:PermissionCacheContent', ['cache_value'], $position, $stepData);
	}

	public function step15($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:PermissionCombination', ['cache_value'], $position, $stepData);
	}

	public function step16($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:Poll', ['responses'], $position, $stepData);
	}

	public function step17($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:PollResponse', ['voters'], $position, $stepData);
	}

	// note: subsequent steps involving "like_users" are correct - the columns haven't been renamed at this point

	public function step18($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:Post', ['like_users'], $position, $stepData);
	}

	public function step19($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:ProfilePost', ['like_users', 'latest_comment_ids'], $position, $stepData);
	}

	public function step20($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:ProfilePostComment', ['like_users'], $position, $stepData);
	}

	public function step21($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:Report', ['content_info'], $position, $stepData);
	}

	public function step22($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:SpamCleanerLog', ['data'], $position, $stepData);
	}

	public function step23($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:SpamTriggerLog', ['details', 'request_state'], $position, $stepData);
	}

	public function step24($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:TagResultCache', ['results'], $position, $stepData);
	}

	public function step25($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:Thread', ['custom_fields', 'tags'], $position, $stepData);
	}

	public function step26($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:UserAlert', ['extra_data'], $position, $stepData);
	}

	public function step27($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:UserConnectedAccount', ['extra_data'], $position, $stepData);
	}

	public function step28($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:UserGroupPromotion', ['user_criteria'], $position, $stepData);
	}

	public function step29($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:UserProfile', ['ignored', 'custom_fields', 'connected_accounts'], $position, $stepData);
	}

	public function step30($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:UserTfa', ['provider_data'], $position, $stepData);
	}

	public function step31($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:UserUpgradeActive', ['extra'], $position, $stepData);
	}

	public function step32($position, array $stepData)
	{
		return $this->entityColumnsToJson('XF:UserUpgradeExpired', ['extra'], $position, $stepData);
	}

	// ######################## REACTION MIGRATIONS ###################

	public function step33()
	{
		if (!$this->schemaManager()->tableExists('xf_liked_content'))
		{
			// if this table doesn't exist, then this query has been run already, so we can skip it.
			return;
		}

		$this->alterTable('xf_liked_content', function(Alter $table)
		{
			$table->renameTo('xf_reaction_content');
			$table->renameColumn('like_id', 'reaction_content_id');
			$table->addColumn('reaction_id', 'int')->setDefault(1)->after('reaction_content_id');
			$table->renameColumn('like_user_id', 'reaction_user_id');
			$table->renameColumn('like_date', 'reaction_date');

			$table->dropIndexes([
				'content_type_id_like_user_id',
				'like_user_content_type_id',
				'content_user_id_like_date',
				'like_date'
			]);

			$table->addUniqueKey(['content_type', 'content_id', 'reaction_user_id'], 'content_type_id_user_id');
			$table->addKey(['content_type', 'content_id', 'reaction_date'], 'content_type_id_reaction_date');
			$table->addKey(['content_user_id', 'reaction_date']);
			$table->addKey('reaction_date');
		});
	}

	public function step34()
	{
		$this->migrateTableToReactions('xf_post');
	}

	public function step35()
	{
		$this->migrateTableToReactions('xf_conversation_message');
	}

	public function step36()
	{
		$this->migrateTableToReactions('xf_profile_post');
	}

	public function step37()
	{
		$this->migrateTableToReactions('xf_profile_post_comment');
	}

	public function step38()
	{
		$this->renameLikeAlertOptionsToReactions(['post', 'profile_post', 'profile_post_comment', 'conversation_message']);
	}

	public function step39()
	{
		$this->renameLikeAlertsToReactions(['post', 'profile_post', 'profile_post_comment', 'conversation_message']);
	}

	public function step40()
	{
		// miscellaneous reaction migrations

		$this->renameLikePermissionsToReactions([
			'forum' => true, // global and content
			'conversation' => false, // global only
			'profilePost' => false // global only
		]);

		$this->renameLikeStatsToReactions(['post', 'profile_post', 'profile_post_comment']);

		$this->renameLikeCriteriaToReactions('xf_notice', 'notice_id');
		$this->renameLikeCriteriaToReactions('xf_trophy', 'trophy_id');
		$this->renameLikeCriteriaToReactions('xf_user_group_promotion', 'promotion_id');

		$this->executeUpgradeQuery("
			UPDATE xf_member_stat
			SET sort_order = 'reaction_score'
			WHERE sort_order = 'like_count'
		");

		$this->applyAdminPermission('reaction', 'bbCodeSmilie');
	}

	public function step41()
	{
		if (!$this->schemaManager()->columnExists('xf_thread', 'first_post_likes'))
		{
			// column is gone, so assume this has been run
			return;
		}

		$this->alterTable('xf_thread', function (Alter $table)
		{
			$table->changeColumn('first_post_likes')->renameTo('first_post_reaction_score')->unsigned(false);
			$table->addColumn('first_post_reactions', 'blob')->nullable()->after('first_post_reaction_score');
		});
	}

	public function step42()
	{
		$this->executeUpgradeQuery('UPDATE xf_thread SET first_post_reactions = CONCAT(\'{"1":\', first_post_reaction_score, \'}\') WHERE first_post_reaction_score > 0');
	}

	public function step43()
	{
		if (!$this->schemaManager()->columnExists('xf_user', 'like_count'))
		{
			// column is gone, so assume this has been run
			return;
		}

		// extremely unlikely but prevent the upgrade being blocked
		// in the event of exceeding the signed int max.
		$this->executeUpgradeQuery("
			UPDATE xf_user
			SET like_count = 2147483647
			WHERE like_count > 2147483647
		");

		$this->alterTable('xf_user', function (Alter $table)
		{
			$table->changeColumn('like_count')->renameTo('reaction_score')->unsigned(false);
			$table->dropIndexes('like_count');
			$table->addKey('reaction_score');
		});
	}

	public function step44()
	{
		$this->executeUpgradeQuery('
			INSERT INTO `xf_reaction` 
				(`reaction_id`, `title`, `text_color`, `image_url`, `image_url_2x`, `sprite_mode`, `sprite_params`, `reaction_score`, `display_order`, `active`)
			VALUES
				(1, \'Like\', \'@xf-linkColor\', \'styles/default/xenforo/reactions/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"32\",\"h\":\"32\",\"x\":\"0\",\"y\":\"0\",\"bs\":\"100%\"}\', 1, 100, 1),
				(2, \'Love\', \'#E81C27\', \'styles/default/xenforo/reactions/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"32\",\"h\":\"32\",\"x\":\"0\",\"y\":\"-32\",\"bs\":\"100%\"}\', 1, 200, 1),
				(3, \'Haha\', \'#FDCA47\', \'styles/default/xenforo/reactions/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"32\",\"h\":\"32\",\"x\":\"0\",\"y\":\"-64\",\"bs\":\"100%\"}\', 1, 300, 1),
				(4, \'Wow\', \'#FDCA47\', \'styles/default/xenforo/reactions/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"32\",\"h\":\"32\",\"x\":\"0\",\"y\":\"-96\",\"bs\":\"100%\"}\', 0, 400, 1),
				(5, \'Sad\', \'#FDCA47\', \'styles/default/xenforo/reactions/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"32\",\"h\":\"32\",\"x\":\"0\",\"y\":\"-128\",\"bs\":\"100%\"}\', 0, 500, 1),
				(6, \'Angry\', \'#FF4D4D\', \'styles/default/xenforo/reactions/emojione/sprite_sheet_emojione.png\', \'\', 1, \'{\"w\":\"32\",\"h\":\"32\",\"x\":\"0\",\"y\":\"-160\",\"bs\":\"100%\"}\', 0, 600, 1);
		');

		$db = $this->db();

		$spamUserCriteria = $db->fetchOne('SELECT option_value FROM xf_option WHERE option_id = \'spamUserCriteria\'');
		$spamUserCriteria = json_decode($spamUserCriteria, true);
		if ($spamUserCriteria && isset($spamUserCriteria['like_count']))
		{
			$spamUserCriteria['reaction_score'] = $spamUserCriteria['like_count'];
			unset($spamUserCriteria['like_count']);

			$this->executeUpgradeQuery('UPDATE xf_option SET option_value = ? WHERE option_id = \'spamUserCriteria\'', json_encode($spamUserCriteria));
		}

		$userTitleLadderField = $db->fetchOne('SELECT option_value FROM xf_option WHERE option_id = \'userTitleLadderField\'');
		if ($userTitleLadderField && $userTitleLadderField == 'like_count')
		{
			$userTitleLadderField = 'reaction_score';
			$this->executeUpgradeQuery('UPDATE xf_option SET option_value = ? WHERE option_id = \'userTitleLadderField\'', $userTitleLadderField);
		}

		$messageUserElements = $db->fetchAllColumn('SELECT property_id, property_value FROM xf_style_property WHERE property_name = \'messageUserElements\'');
		foreach ($messageUserElements AS $propertyId => $value)
		{
			$elements = json_decode($value, true);
			if ($elements && isset($elements['like_count']))
			{
				$elements['reaction_score'] = $elements['like_count'];
				unset($elements['like_count']);

				$this->executeUpgradeQuery('UPDATE xf_style_property SET property_value = ? WHERE property_id = ?', [json_encode($elements, $propertyId)]);
			}
		}
	}

	// final misc additions

	public function step45()
	{
		$this->alterTable('xf_phrase_map', function (Alter $table)
		{
			$table->addKey('title');
		});
	}

	public function step46()
	{
		$this->alterTable('xf_template_map', function (Alter $table)
		{
			$table->addKey(['type', 'title']);
		});
	}

	public function step47()
	{
		$this->alterTable('xf_mail_queue', function (Alter $table) {
			$table->addColumn('send_date', 'int')->setDefault(0);
			$table->addColumn('fail_date', 'int')->nullable()->setDefault(null);
			$table->addColumn('fail_count', 'int')->setDefault(0);
			$table->dropIndexes('queue_date');
			$table->addKey(['send_date', 'queue_date']);
		});
	}

	public function step48()
	{
		$this->executeUpgradeQuery("
			REPLACE INTO xf_phrase
				(language_id, title, phrase_text)
			VALUES
				(0, 'trophy_description.5', 'Somebody out there reacted positively to one of your messages. Keep posting like that for more!'),
				(0, 'trophy_description.6', 'Your messages have been positively reacted to 25 times.'),
				(0, 'trophy_description.7', 'Content you have posted has attracted a positive reaction score of 100.'),
				(0, 'trophy_description.8', 'Your content has been positively reacted to 250 times.'),
				(0, 'trophy_description.9', 'Content you have posted has attracted 500 positive reactions.')
		");
	}
}