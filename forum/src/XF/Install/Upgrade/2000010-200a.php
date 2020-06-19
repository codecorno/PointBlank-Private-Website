<?php

namespace XF\Install\Upgrade;

use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Version2000010 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.0.0 Alpha';
	}

	// dropping tables we no longer use (without data we need)
	public function step1()
	{
		$sm = $this->schemaManager();

		$dropTables = [
			'xf_admin_search_type',
			'xf_admin_template',
			'xf_admin_template_compiled',
			'xf_admin_template_include',
			'xf_admin_template_modification',
			'xf_admin_template_modification_log',
			'xf_admin_template_phrase',
			'xf_attachment_view', // will be recreated
			'xf_bb_code_parse_cache',
			'xf_content_type',
			'xf_deferred', // will be recreated as xf_job
			'xf_email_template',
			'xf_email_template_compiled',
			'xf_email_template_modification',
			'xf_email_template_modification_log',
			'xf_email_template_phrase',
			'xf_node_type', // will be recreated
			'xf_permission_group',
			'xf_route_prefix',
			'xf_template_compiled',
			'xf_template_include',
			'xf_thread_view', // will be recreated
			'xf_user_news_feed_cache',
			'xf_user_status'
		];
		foreach ($dropTables AS $dropTable)
		{
			$sm->dropTable($dropTable);
		}
	}

	// creating entirely new tables (part 1)
	public function step2()
	{
		$sm = $this->schemaManager();

		$sm->createTable('xf_advertising', function (Create $table)
		{
			$table->addColumn('ad_id', 'int')->autoIncrement();
			$table->addColumn('title', 'varchar', 150);
			$table->addColumn('position_id', 'varbinary', 50);
			$table->addColumn('ad_html', 'text');
			$table->addColumn('display_order', 'int')->setDefault(0);
			$table->addColumn('display_criteria', 'blob');
			$table->addColumn('active', 'tinyint');
		});

		$sm->createTable('xf_advertising_position', function (Create $table)
		{
			$table->addColumn('position_id', 'varbinary', 50);
			$table->addColumn('arguments', 'blob');
			$table->addColumn('active', 'tinyint');
			$table->addColumn('addon_id', 'varbinary', 50);
			$table->addPrimaryKey('position_id');
		});

		$sm->createTable('xf_attachment_view', function (Create $table)
		{
			$table->engine('memory');
			$table->addColumn('attachment_id', 'int');
			$table->addColumn('total', 'int');
			$table->addPrimaryKey('attachment_id');
		});

		$sm->createTable('xf_category', function (Create $table)
		{
			$table->addColumn('node_id', 'int');
			$table->addPrimaryKey('node_id');
		});

		$sm->createTable('xf_class_extension', function (Create $table)
		{
			$table->addColumn('extension_id', 'int')->autoIncrement();
			$table->addColumn('from_class', 'varchar', 100);
			$table->addColumn('to_class', 'varchar', 100);
			$table->addColumn('execute_order', 'int');
			$table->addColumn('active', 'tinyint');
			$table->addColumn('addon_id', 'varbinary', 50);
			$table->addUniqueKey(['from_class', 'to_class'], 'from_class');
		});

		$sm->createTable('xf_connected_account_provider', function (Create $table)
		{
			$table->addColumn('provider_id', 'varbinary', 25);
			$table->addColumn('provider_class', 'varchar', 100);
			$table->addColumn('display_order', 'smallint');
			$table->addColumn('options', 'mediumblob');
			$table->addPrimaryKey('provider_id');
		});

		$sm->createTable('xf_css_cache', function (Create $table)
		{
			$table->addColumn('cache_id', 'int')->autoIncrement();
			$table->addColumn('style_id', 'int');
			$table->addColumn('language_id', 'int');
			$table->addColumn('title', 'varbinary', 150);
			$table->addColumn('modifier_key', 'varbinary', 32);
			$table->addColumn('output', 'mediumblob');
			$table->addColumn('cache_date', 'int');
			$table->addUniqueKey(['style_id', 'language_id', 'title', 'modifier_key'], 'style_language_title_modifier');
		});

		$sm->createTable('xf_file_check', function (Create $table)
		{
			$table->addColumn('check_id', 'int')->autoIncrement();
			$table->addColumn('check_date', 'int');
			$table->addColumn('check_state')->type('enum', ['pending', 'success', 'failure'])->setDefault('pending');
			$table->addColumn('check_hash', 'varbinary', 64)->setDefault('');
			$table->addColumn('total_missing', 'int')->setDefault(0);
			$table->addColumn('total_inconsistent', 'int')->setDefault(0);
			$table->addColumn('total_checked', 'int')->setDefault(0);
		});

		$sm->createTable('xf_find_new', function (Create $table)
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
		});
	}

	// creating entirely new tables (part 2)
	public function step3()
	{
		$sm = $this->schemaManager();

		$sm->createTable('xf_find_new_default', function (Create $table)
		{
			$table->addColumn('find_new_default_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('content_type', 'varbinary', 25);
			$table->addColumn('filters', 'blob');
			$table->addUniqueKey(['user_id', 'content_type'], 'user_content');
		});

		$sm->createTable('xf_forum_field', function (Create $table)
		{
			$table->addColumn('node_id', 'int');
			$table->addColumn('field_id', 'varbinary', 25);
			$table->addPrimaryKey(['node_id', 'field_id']);
			$table->addKey('field_id');
		});

		$sm->createTable('xf_forum_prompt', function(Create $table)
		{
			$table->addColumn('node_id', 'int');
			$table->addColumn('prompt_id', 'int');
			$table->addPrimaryKey(['node_id', 'prompt_id']);
		});

		$sm->createTable('xf_job', function (Create $table)
		{
			$table->addColumn('job_id', 'int')->autoIncrement();
			$table->addColumn('unique_key', 'varbinary', 50)->nullable();
			$table->addColumn('execute_class', 'varchar', 100);
			$table->addColumn('execute_data', 'mediumblob');
			$table->addColumn('manual_execute', 'tinyint');
			$table->addColumn('trigger_date', 'int');
			$table->addUniqueKey('unique_key');
			$table->addKey('trigger_date');
			$table->addKey(['manual_execute', 'trigger_date'], 'manual_execute_date');
		});

		$sm->createTable('xf_member_stat', function (Create $table)
		{
			$table->addColumn('member_stat_id', 'int')->autoIncrement();
			$table->addColumn('member_stat_key', 'varbinary', 50);
			$table->addColumn('criteria', 'blob')->nullable();
			$table->addColumn('callback_class', 'varchar', 100)->setDefault('');
			$table->addColumn('callback_method', 'varchar', 75)->setDefault('');
			$table->addColumn('sort_order', 'varchar', 25)->setDefault('message_count');
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
		});

		$sm->createTable('xf_navigation', function (Create $table)
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
		});

		$sm->createTable('xf_navigation_type', function (Create $table)
		{
			$table->addColumn('navigation_type_id', 'varbinary', 25);
			$table->addColumn('handler_class', 'varchar', 100);
			$table->addColumn('display_order', 'int')->setDefault(0);
			$table->addPrimaryKey('navigation_type_id');
		});

		$sm->createTable('xf_node_type', function (Create $table)
		{
			$table->addColumn('node_type_id', 'varbinary', 25);
			$table->addColumn('entity_identifier', 'varchar', 75);
			$table->addColumn('permission_group_id', 'varchar', 25)->setDefault('');
			$table->addColumn('admin_route', 'varchar', 75);
			$table->addColumn('public_route', 'varchar', 75);
			$table->addPrimaryKey('node_type_id');
		});
	}

	// creating entirely new tables (part 3)
	public function step4()
	{
		$sm = $this->schemaManager();

		$sm->createTable('xf_oembed', function(Create $table)
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
		});

		$sm->createTable('xf_oembed_referrer', function(Create $table)
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
		});

		$sm->createTable('xf_payment_profile', function (Create $table)
		{
			$table->addColumn('payment_profile_id', 'int')->autoIncrement();
			$table->addColumn('provider_id', 'varbinary', 25);
			$table->addColumn('title', 'varchar', 100)->setDefault('');
			$table->addColumn('display_title', 'varchar', 100)->setDefault('');
			$table->addColumn('options', 'blob')->nullable();
			$table->addColumn('active', 'tinyint')->setDefault(1);
		});

		$sm->createTable('xf_payment_provider', function (Create $table)
		{
			$table->addColumn('provider_id', 'varbinary', 25);
			$table->addColumn('provider_class', 'varchar', 100);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('provider_id');
		});

		$sm->createTable('xf_purchasable', function (Create $table)
		{
			$table->addColumn('purchasable_type_id', 'varchar', 50);
			$table->addColumn('purchasable_class', 'varchar', 100);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('purchasable_type_id');
		});

		$sm->createTable('xf_purchase_request', function (Create $table)
		{
			$table->addColumn('purchase_request_id', 'int')->autoIncrement();
			$table->addColumn('request_key', 'varbinary', 32);
			$table->addColumn('user_id', 'int')->setDefault(0);
			$table->addColumn('provider_id', 'varbinary', 25);
			$table->addColumn('payment_profile_id', 'int');
			$table->addColumn('purchasable_type_id', 'varchar', 50);
			$table->addColumn('cost_amount')->type('decimal', '10,2');
			$table->addColumn('cost_currency', 'varchar', 3);
			$table->addColumn('extra_data', 'blob')->nullable();
			$table->addColumn('provider_metadata', 'varbinary', 100)->nullable();
			$table->addUniqueKey('request_key');
			$table->addKey(['provider_id', 'provider_metadata'], 'provider_id_metadata');
		});

		$sm->createTable('xf_route', function (Create $table)
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
		});

		$this->db()->emptyTable('xf_session');
		$this->db()->emptyTable('xf_session_activity');
		$this->db()->emptyTable('xf_session_admin');

		$sm->createTable('xf_session_install', function (Create $table)
		{
			$table->engine('MyISAM');

			$table->addColumn('session_id', 'varbinary', 32);
			$table->addColumn('session_data', 'mediumblob');
			$table->addColumn('expiry_date', 'int');
			$table->addPrimaryKey('session_id');
			$table->addKey('expiry_date');
		});
	}

	// creating entirely new tables (part 4)
	public function step5()
	{
		$sm = $this->schemaManager();

		$sm->createTable('xf_thread_field', function (Create $table)
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
			$table->addColumn('user_editable', 'enum')->values(['yes','once','never'])->setDefault('yes');
			$table->addColumn('display_template', 'text');
			$table->addColumn('moderator_editable', 'tinyint', 3)->setDefault(0);
			$table->addPrimaryKey('field_id');
			$table->addKey(['display_group', 'display_order'], 'display_group_order');
		});

		$sm->createTable('xf_thread_field_value', function (Create $table)
		{
			$table->addColumn('thread_id', 'int');
			$table->addColumn('field_id', 'varbinary', 25);
			$table->addColumn('field_value', 'mediumtext');
			$table->addPrimaryKey(['thread_id', 'field_id']);
			$table->addKey('field_id');
		});

		$sm->createTable('xf_thread_prompt', function(Create $table)
		{
			$table->addColumn('prompt_id', 'int')->autoIncrement();
			$table->addColumn('prompt_group_id', 'int');
			$table->addColumn('display_order', 'int');
			$table->addColumn('materialized_order', 'int')->comment('Internally-set order, based on prompt_group.display_order, prompt.display_order');
			$table->addKey('materialized_order');
		});

		$sm->createTable('xf_thread_prompt_group', function(Create $table)
		{
			$table->addColumn('prompt_group_id', 'int')->autoIncrement();
			$table->addColumn('display_order', 'int');
		});

		$sm->createTable('xf_thread_view', function (Create $table)
		{
			$table->engine('memory');
			$table->addColumn('thread_id', 'int');
			$table->addColumn('total', 'int');
			$table->addPrimaryKey('thread_id');
		});

		$sm->createTable('xf_upgrade_job', function (Create $table)
		{
			$table->addColumn('unique_key', 'varbinary', 50)->primaryKey();
			$table->addColumn('execute_class', 'varchar', 100);
			$table->addColumn('execute_data', 'mediumblob');
			$table->addColumn('immediate', 'tinyint');
			$table->addKey('immediate');
		});

		$sm->createTable('xf_user_reject', function (Create $table)
		{
			$table->addColumn('user_id', 'int');
			$table->addColumn('reject_date', 'int');
			$table->addColumn('reject_user_id', 'int')->setDefault(0);
			$table->addColumn('reject_reason', 'varchar', 200)->setDefault('');
			$table->addPrimaryKey('user_id');
			$table->addKey('reject_date');
		});

		$sm->createTable('xf_user_remember', function (Create $table)
		{
			$table->addColumn('remember_id', 'int')->autoIncrement();
			$table->addColumn('user_id', 'int');
			$table->addColumn('remember_key', 'varbinary', 32);
			$table->addColumn('start_date', 'int');
			$table->addColumn('expiry_date', 'int');
			$table->addUniqueKey(['user_id', 'remember_key']);
			$table->addKey('expiry_date');
		});

		$sm->createTable('xf_widget', function (Create $table)
		{
			$table->addColumn('widget_id', 'int')->autoIncrement();
			$table->addColumn('widget_key', 'varbinary', 50);
			$table->addColumn('definition_id', 'varbinary', 25);
			$table->addColumn('positions', 'blob');
			$table->addColumn('options', 'blob');
			$table->addUniqueKey('widget_key');
		});

		$sm->createTable('xf_widget_definition', function (Create $table)
		{
			$table->addColumn('definition_id', 'varbinary', 25);
			$table->addColumn('definition_class', 'varchar', 100);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
			$table->addPrimaryKey('definition_id');
			$table->addUniqueKey('definition_class');
		});

		$sm->createTable('xf_widget_position', function (Create $table)
		{
			$table->addColumn('position_id', 'varbinary', 50);
			$table->addColumn('active', 'tinyint');
			$table->addColumn('addon_id', 'varbinary', 50);
			$table->addPrimaryKey('position_id');
		});
	}

	// updating style property tables and maintaining specific data
	public function step6()
	{
		$db = $this->db();
		$sm = $this->schemaManager();

		// maintain values that we want to import and save them for a job later
		$map = [
			'contentBackground' => 'paletteNeutral1',
			'mutedTextColor' => 'paletteNeutral2',
			'contentText' => 'paletteNeutral3',

			'primaryLightest' => 'paletteColor1',
			'primaryLighter' => 'paletteColor2',
			'primaryLight' => 'paletteColor3',
			'primaryMedium' => 'paletteColor4',
			'primaryDark' => 'paletteColor5',

			'secondaryLightest' => 'paletteAccent1',
			'secondaryMedium' => 'paletteAccent2',
			'secondaryDarker' => 'paletteAccent3',

			'headerLogoPath' => 'publicLogoUrl',
			'ogLogoPath' => 'publicMetadataLogoUrl',
		];
		$oldValues = $db->fetchAll("
			SELECT property.style_id, definition.property_name, property.property_value
			FROM xf_style_property AS property
			INNER JOIN xf_style_property_definition AS definition ON 
				(definition.property_definition_id = property.property_definition_id)
			WHERE property.style_id > 0
				AND definition.property_name IN (
					'contentBackground', 'mutedTextColor', 'contentText',
					'primaryLightest', 'primaryLighter', 'primaryLight', 'primaryMedium', 'primaryDark',
					'secondaryLightest', 'secondaryMedium', 'secondaryDarker',
					'headerLogoPath', 'ogLogoPath'
				)
		");

		$newValues = [];
		foreach ($oldValues AS $value)
		{
			if (!isset($map[$value['property_name']]))
			{
				continue;
			}

			$validProperty = true;

			switch ($value['property_name'])
			{
				case 'headerLogoPath':
				case 'ogLogoPath':
					break;

				default:
					if (!\XF\Util\Color::isValidColor($value['property_value']))
					{
						$validProperty = false;
					}
			}

			if (!$validProperty)
			{
				continue;
			}

			$newProperty = $map[$value['property_name']];

			$newValues[$value['style_id']][$newProperty] = $value['property_value'];
		}

		foreach ($newValues AS $styleId => &$customProperties)
		{
			if (isset($customProperties['paletteNeutral1'], $customProperties['paletteNeutral3']))
			{
				$n1 = \XF\Util\Color::colorToRgb($customProperties['paletteNeutral1']);
				$n3 = \XF\Util\Color::colorToRgb($customProperties['paletteNeutral3']);

				if ($n1 && $n3)
				{
					$n1Lum = \XF\Util\Color::getRelativeLuminance($n1);
					$n3Lum = \XF\Util\Color::getRelativeLuminance($n3);
					if ($n1Lum < $n3Lum)
					{
						$customProperties['styleType'] = 'dark';
					}
				}
			}
		}

		if ($newValues)
		{
			$this->insertUpgradeJob(
				'upgradeStyleProperty200', 'XF:Upgrade\StyleProperty200', ['properties' => $newValues]
			);
		}

		// now we can change to the new tables

		$sm->dropTable('xf_style_property');
		$sm->dropTable('xf_style_property_definition');
		$sm->dropTable('xf_style_property_group');

		$sm->createTable('xf_style_property', function (Create $table)
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
		});

		$sm->createTable('xf_style_property_group', function (Create $table)
		{
			$table->addColumn('property_group_id', 'int')->autoIncrement();
			$table->addColumn('group_name', 'varbinary', 50);
			$table->addColumn('style_id', 'int');
			$table->addColumn('title', 'varchar', 100);
			$table->addColumn('description', 'varchar', 255)->setDefault('');
			$table->addColumn('display_order', 'int')->setDefault(0);
			$table->addColumn('addon_id', 'varbinary', 50);
			$table->addUniqueKey(['group_name', 'style_id']);
		});

		$sm->createTable('xf_style_property_map', function (Create $table)
		{
			$table->addColumn('property_map_id', 'int')->autoIncrement();
			$table->addColumn('style_id', 'int');
			$table->addColumn('property_name', 'varbinary', 50);
			$table->addColumn('property_id', 'int');
			$table->addColumn('parent_property_id', 'int')->nullable();
			$table->addUniqueKey(['style_id', 'property_name'], 'style_id');
			$table->addKey('parent_property_id');
		});
	}

	// inserting default data into new tables
	public function step7()
	{
		$this->executeUpgradeQuery("
			REPLACE INTO xf_category
				(node_id)
			SELECT node_id
			FROM xf_node
			WHERE node_type_id = 'Category'
		");

		$this->executeUpgradeQuery("
			REPLACE INTO `xf_navigation_type`
				(`navigation_type_id`, `handler_class`, `display_order`)
			VALUES
				('basic', 'XF:BasicType', 10),
				('callback', 'XF:CallbackType', 1000),
				('node', 'XF:NodeType', 100)
		");

		$this->executeUpgradeQuery("
			REPLACE INTO xf_node_type
				(node_type_id, entity_identifier, permission_group_id, admin_route, public_route)
			VALUES
				('Category', 'XF:Category', 'category', 'categories', 'categories'),
				('Forum', 'XF:Forum', 'forum', 'forums', 'forums'),
				('LinkForum', 'XF:LinkForum', 'linkForum', 'link-forums', 'link-forums'),
				('Page', 'XF:Page', 'page', 'pages', 'pages')
		");

		$this->executeUpgradeQuery("
			REPLACE INTO xf_payment_provider
				(provider_id, provider_class, addon_id)
			VALUES
				('braintree', 'XF:Braintree', 'XF'),
				('paypal', 'XF:PayPal', 'XF'),
				('stripe', 'XF:Stripe', 'XF'),
				('twocheckout', 'XF:TwoCheckout', 'XF')
		");

		$this->executeUpgradeQuery("
			REPLACE INTO xf_purchasable
				(purchasable_type_id, purchasable_class, addon_id)
			VALUES
				('user_upgrade', 'XF:UserUpgrade', 'XF')
		");

		$widgetInserts = [
			"('member_wrapper_find_member', 'find_member', '[]', '{\"member_wrapper_sidenav\":\"10\"}')",
			"('member_wrapper_newest_members', 'newest_members', '[]', '{\"member_wrapper_sidenav\":\"20\"}')",

			"('online_list_online_statistics', 'online_statistics', '[]', '{\"online_list_sidebar\":\"10\"}')",

			"('whats_new_new_posts', 'new_posts', '{\"limit\": 10, \"style\": \"full\"}', '{\"whats_new_overview\":\"10\"}')",
			"('whats_new_new_profile_posts', 'new_profile_posts', '{\"style\": \"full\"}', '{\"whats_new_overview\":\"10000\"}')",

			"('forum_overview_members_online', 'members_online', '[]', '{\"forum_list_sidebar\":\"10\",\"forum_new_posts_sidebar\":\"10\"}')",
			"('forum_overview_forum_statistics', 'forum_statistics', '[]', '{\"forum_list_sidebar\":\"40\",\"forum_new_posts_sidebar\":\"1000\"}')",
			"('forum_overview_share_page', 'share_page', '[]', '{\"forum_list_sidebar\":\"1010\"}')"
		];

		$widgetPhrases = [
			"(0, 'widget.member_wrapper_find_member', '', 0, '')",
			"(0, 'widget.member_wrapper_newest_members', '', 0, '')",
			"(0, 'widget.online_list_online_statistics', '', 0, '')",
			"(0, 'widget.whats_new_new_posts', '', 0, '')",
			"(0, 'widget.whats_new_new_profile_posts', '', 0, '')",
			"(0, 'widget.forum_overview_members_online', '', 0, '')",
			"(0, 'widget.forum_overview_forum_statistics', '', 0, '')",
			"(0, 'widget.forum_overview_share_page', '', 0, '')"
		];

		$widgetOptions = $this->db()->fetchPairs("
			SELECT option_id, option_value
			FROM xf_option
			WHERE option_id IN ('forumListNewProfilePosts', 'forumListNewPosts')
		");
		if (!empty($widgetOptions['forumListNewPosts']))
		{
			$widgetInserts[] = "('forum_overview_new_posts', 'new_posts', '[]', '{\"forum_list_sidebar\":\"20\"}')";
			$widgetPhrases[] = "(0, 'widget.forum_overview_new_posts', '', 0, '')";
		}
		if (!empty($widgetOptions['forumListNewProfilePosts']))
		{
			$widgetInserts[] = "('forum_overview_new_profile_posts', 'new_profile_posts', '[]', '{\"forum_list_sidebar\":\"90\",\"forum_new_posts_sidebar\":\"900\"}')";
			$widgetPhrases[] = "(0, 'widget.forum_overview_new_profile_posts', '', 0, '')";
		}

		$this->executeUpgradeQuery("
			REPLACE INTO `xf_widget` 
				(`widget_key`, `definition_id`, `options`, `positions`)
			VALUES
				" . implode(",\n", $widgetInserts)
		);

		$this->executeUpgradeQuery("
			REPLACE INTO xf_phrase
				(language_id, title, phrase_text, global_cache, addon_id)
			VALUES
				" . implode(",\n", $widgetPhrases)
		);
	}

	// basic/general alters (part 1) and removing data which isn't relevant
	public function step8()
	{
		$sm = $this->schemaManager();

		$sm->renameTable('xf_moderation_queue', 'xf_approval_queue');
		$this->db()->query('
			INSERT IGNORE INTO xf_approval_queue (
				SELECT \'user\', user_id, register_date
				FROM xf_user
				WHERE user_state = \'moderated\'
			)
		');

		$this->db()->emptyTable('xf_admin_navigation');
		$sm->alterTable('xf_admin_navigation', function (Alter $table)
		{
			$table->addColumn('icon', 'varchar', 50)->setDefault('')->after('link');
		});

		$sm->alterTable('xf_admin_permission_entry', function (Alter $table)
		{
			$table->changeColumn('user_id')->length(10)->unsigned();
		});

		$sm->alterTable('xf_ban_email', function (Alter $table)
		{
			$table->addColumn('create_user_id', 'int')->setDefault(0);
			$table->addColumn('create_date', 'int')->setDefault(0);
			$table->addColumn('reason', 'varchar', 255)->setDefault('');
			$table->addColumn('last_triggered_date', 'int')->setDefault(0);
			$table->addKey('create_date');
		});

		$this->db()->emptyTable('xf_code_event');
		$this->db()->emptyTable('xf_code_event_listener');
		$sm->alterTable('xf_code_event_listener', function (Alter $table)
		{
			$table->addUniqueKey([
				'addon_id', 'event_id', 'callback_class', 'callback_method'
			], 'addon_id_event_id_class_method');
		});

		$sm->alterTable('xf_content_spam_cache', function (Alter $table)
		{
			$table->changeColumn('insert_date')->length(10)->unsigned();
		});
	}

	// basic/general alters (part 2) and removing data which isn't relevant
	public function step9()
	{
		$sm = $this->schemaManager();

		$this->db()->emptyTable('xf_content_type_field');
		$sm->alterTable('xf_content_type_field', function (Alter $table)
		{
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
		});

		$this->db()->emptyTable('xf_cron_entry');
		$this->db()->emptyTable('xf_error_log');

		$sm->alterTable('xf_forum', function (Alter $table)
		{
			$table->addColumn('last_thread_prefix_id', 'int')->setDefault(0)->after('last_thread_title');
			$table->addColumn('field_cache', 'mediumblob')->comment('Serialized data from xf_thread_field')->after('find_new');
			$table->addColumn('prompt_cache', 'mediumblob')->comment('JSON data from xf_forum_prompt');
		});

		$sm->alterTable('xf_image_proxy', function (Alter $table)
		{
			$table->changeColumn('image_id')->length(10);
		});

		$sm->alterTable('xf_image_proxy_referrer', function (Alter $table)
		{
			$table->changeColumn('first_date')->length(10)->unsigned()->nullable(false);
			$table->changeColumn('hits')->length(10)->unsigned()->nullable(false);
			$table->changeColumn('last_date')->length(10)->unsigned()->nullable(false);
		});

		$sm->alterTable('xf_ip_match', function (Alter $table)
		{
			$table->addColumn('create_user_id', 'int')->setDefault(0);
			$table->addColumn('create_date', 'int')->setDefault(0);
			$table->addColumn('reason', 'varchar', 255)->setDefault('');
			$table->addColumn('last_triggered_date', 'int')->setDefault(0);
			$table->addKey('create_date');
		});
	}

	// basic/general alters (part 3)
	public function step10()
	{
		$sm = $this->schemaManager();

		$sm->alterTable('xf_link_proxy_referrer', function (Alter $table)
		{
			$table->changeColumn('first_date')->length(10)->unsigned()->nullable(false);
			$table->changeColumn('hits')->length(10)->unsigned()->nullable(false);
			$table->changeColumn('last_date')->length(10)->unsigned()->nullable(false);
		});

		// these are stored as objects so we can't send the old data any longer
		$this->db()->emptyTable('xf_mail_queue');

		$sm->alterTable('xf_moderator', function (Alter $table)
		{
			$table->dropColumns('moderator_permissions');
		});

		$sm->alterTable('xf_moderator_content', function (Alter $table)
		{
			$table->dropColumns('moderator_permissions');
		});

		$sm->alterTable('xf_node', function (Alter $table)
		{
			$table->addColumn('navigation_id', 'varbinary', 50)->setDefault('');
			$table->addColumn('effective_navigation_id', 'varbinary', 50)->setDefault('');
		});

		$sm->alterTable('xf_page', function (Alter $table)
		{
			$table->addColumn('advanced_mode', 'tinyint')->setDefault(0);
		});

		$sm->alterTable('xf_permission', function (Alter $table)
		{
			$table->dropColumns(['default_value', 'default_value_int']);
		});

		$sm->alterTable('xf_permission_interface_group', function (Alter $table)
		{
			$table->addColumn('is_moderator', 'tinyint')->setDefault(0)->after('display_order');
		});
	}

	// basic/general alters (part 4)
	public function step11()
	{
		$sm = $this->schemaManager();

		$sm->alterTable('xf_route_filter', function (Alter $table)
		{
			$table->dropColumns('route_type');
		});

		$this->db()->emptyTable('xf_search');
		$sm->alterTable('xf_search', function (Alter $table)
		{
			$table->changeColumn('search_grouping')->length(3)->unsigned();
		});

		$sm->alterTable('xf_style', function(Alter $table)
		{
			$table->addColumn('designer_mode', 'varbinary', 50)->nullable()->setDefault(null);
			$table->addUniqueKey('designer_mode');
		});

		$sm->alterTable('xf_tfa_attempt', function (Alter $table)
		{
			$table->changeColumn('attempt_id')->length(10)->unsigned();
		});

		$sm->alterTable('xf_user_group_promotion', function (Alter $table)
		{
			$table->changeColumn('active')->length(3)->unsigned();
		});
	}

	// next set of steps are all potentially large alters
	public function step12()
	{
		$this->schemaManager()->alterTable('xf_conversation_message', function (Alter $table)
		{
			$table->addColumn('likes', 'int')->setDefault(0);
			$table->addColumn('like_users', 'blob');
			$table->addColumn('embed_metadata', 'blob')->nullable();
		});
	}

	public function step13()
	{
		$this->schemaManager()->alterTable('xf_deletion_log', function (Alter $table)
		{
			$table->changeColumn('content_id')->length(10)->unsigned();
			$table->changeColumn('delete_date')->length(10)->unsigned();
			$table->changeColumn('delete_user_id')->length(10)->unsigned();
		});
	}

	public function step14()
	{
		$this->schemaManager()->alterTable('xf_feed_log', function (Alter $table)
		{
			$table->changeColumn('unique_id', 'varbinary');
		});
	}

	public function step15()
	{
		$this->schemaManager()->alterTable('xf_liked_content', function (Alter $table)
		{
			$table->addColumn('is_counted', 'tinyint')->setDefault(1);
		});
	}

	public function step16()
	{
		$this->schemaManager()->alterTable('xf_post', function (Alter $table)
		{
			$table->addColumn('embed_metadata', 'blob')->nullable();
		});
	}

	public function step17()
	{
		$sm = $this->schemaManager();

		$sm->alterTable('xf_tag', function (Alter $table)
		{
			$table->changeColumn('permanent')->length(3)->unsigned();
		});

		$sm->alterTable('xf_tag_content', function (Alter $table)
		{
			$table->changeColumn('content_id')->length(10)->unsigned();
		});

		$this->db()->emptyTable('xf_tag_result_cache');
		$sm->alterTable('xf_tag_result_cache', function (Alter $table)
		{
			$table->changeColumn('result_cache_id')->length(10)->unsigned();
		});
	}

	public function step18()
	{
		$this->schemaManager()->alterTable('xf_thread', function (Alter $table)
		{
			$table->addColumn('custom_fields', 'mediumblob');
		});
	}

	public function step19()
	{
		// let this silently fail, but this won't work with utf8mb4 without this modification
		try
		{
			$this->schemaManager()->alterTable('xengallery_exif', function (Alter $table)
			{
				$table->changeColumn('exif_name', 'varbinary');
			});
		}
		catch (\Exception $e) {}
	}

	public function step20()
	{
		$this->schemaManager()->alterTable('xf_user_option', function (Alter $table)
		{
			$table->dropColumns(['enable_flash_uploader', 'enable_rte']);
			$table->renameColumn('default_watch_state', 'creation_watch_state');
			$table->addColumn('interaction_watch_state', 'enum')
				->values(['', 'watch_no_email', 'watch_email'])
				->after('creation_watch_state')
				->setDefault('');
		});
	}

	// add-on system changes
	public function step21()
	{
		$this->schemaManager()->alterTable('xf_addon', function (Alter $table)
		{
			$table->dropColumns([
				'url',
				'install_callback_class',
				'install_callback_method',
				'uninstall_callback_class',
				'uninstall_callback_method'
			]);
			$table->addColumn('json_hash', 'varbinary', 64)->setDefault('')->after('version_id');
			$table->addColumn('is_legacy', 'tinyint')->setDefault(0);
			$table->addColumn('is_processing', 'tinyint')->setDefault(0);
		});

		$this->executeUpgradeQuery("
			UPDATE xf_addon
			SET 
				active = 0,
				is_legacy = 1
		");

		$this->executeUpgradeQuery("
			INSERT INTO xf_addon
				(
					addon_id,
					title,
					version_string,
					version_id,
					active
				)
			VALUES
				('XF', 'XenForo', '2.0.0 Alpha', 2000010, 1)
		");

		// other tables have (or will have) their data emptied
		$tables = [
			'xf_admin_permission',
			'xf_bb_code',
			'xf_option',
			'xf_option_group',
			'xf_permission',
			'xf_permission_interface_group',
			'xf_phrase',
		];
		foreach ($tables AS $table)
		{
			$this->executeUpgradeQuery("
				UPDATE $table
				SET addon_id = 'XF'
				WHERE addon_id = 'XenForo'
			");
		}
	}

	// more template system changes
	public function step22()
	{
		$db = $this->db();
		$sm = $this->schemaManager();

		$db->emptyTable('xf_template_phrase');
		$sm->alterTable('xf_template_phrase', function (Alter $table)
		{
			$table->renameColumn('template_map_id', 'template_id');
		});

		// We need to wipe out all non-custom templates. Custom templates may be attached to content so we need to keep them.
		$this->executeUpgradeQuery("
			DELETE FROM xf_template
			WHERE style_id > 0 OR addon_id <> ''
		");

		$sm->alterTable('xf_template', function (Alter $table)
		{
			$table->changeColumn('title')->length(100);
			$table->addColumn('type', 'varbinary', 20)->after('template_id');
			$table->dropColumns('disable_modifications');
			$table->dropIndexes('title_style_id');
			$table->addUniqueKey(['type', 'title', 'style_id']);
		});

		$this->executeUpgradeQuery("
			UPDATE xf_template SET type = 'public'
		");

		$db->emptyTable('xf_template_history');
		$sm->alterTable('xf_template_history', function (Alter $table)
		{
			$table->changeColumn('template_history_id')->length(10);
			$table->changeColumn('title')->length(100);
			$table->changeColumn('style_id')->length(10);
			$table->changeColumn('edit_date')->length(10);
			$table->changeColumn('log_date')->length(10);
			$table->addColumn('type', 'varbinary', 20)->after('template_history_id');
			$table->dropIndexes('style_id_title');
			$table->addKey(['type', 'title', 'style_id']);
		});

		$db->emptyTable('xf_template_map');
		$sm->alterTable('xf_template_map', function (Alter $table)
		{
			$table->changeColumn('title')->length(100);
			$table->addColumn('type', 'varbinary', 20)->after('style_id');
			$table->dropIndexes('style_id_title');
			$table->addUniqueKey(['style_id', 'type', 'title']);
		});

		$db->emptyTable('xf_template_modification');
		$sm->alterTable('xf_template_modification', function (Alter $table)
		{
			$table->changeColumn('template')->length(100);
			$table->addColumn('type', 'varbinary', 20)->after('addon_id');
		});

		$db->emptyTable('xf_template_modification_log');
	}

	// phrase system changes
	public function step23()
	{
		$sm = $this->schemaManager();

		$this->db()->emptyTable('xf_phrase_map');
		$sm->alterTable('xf_phrase_map', function (Alter $table)
		{
			$table->addColumn('phrase_group', 'varbinary', 50)->nullable();
			$table->addKey(['phrase_group', 'language_id'], 'group_language');
		});

		$this->db()->emptyTable('xf_phrase_compiled');

		$this->executeUpgradeQuery("
			DELETE FROM xf_phrase
			WHERE language_id = 0 AND addon_id = 'XF'
		");
	}

	// further phrase changes
	public function step24()
	{
		$map = [
			'admin_navigation_*' => 'admin_navigation.*',
			'admin_permission_*' => 'admin_permission.*',
			'custom_bb_code_*_title' => 'custom_bb_code_title.*',
			'custom_bb_code_*_desc' => 'custom_bb_code_desc.*',
			'custom_bb_code_*_example' => 'custom_bb_code_example.*',
			'custom_bb_code_*_output' => 'custom_bb_code_output.*',
			'cron_entry_*' => 'cron_entry.*',
			'help_page_*_desc' => 'help_page_desc.*',
			'help_page_*_title' => 'help_page_title.*',
			'option_group_*_description' => 'option_group_description.*', // make sure this is done before option groups
			'option_group_*' => 'option_group.*',
			'option_*_explain' => 'option_explain.*', // do this before regular options
			'option_([a-zA-Z0-9]+)' => 'option.*',
			'permission_interface_*' => 'permission_interface.*',
			'permission_([a-zA-Z0-9_]+_[a-zA-Z0-9_]+)' => 'permission.*',
			'smilie_category_*_title' => 'smilie_category_title.*',
			'thread_prefix_group_*' => 'thread_prefix_group.*',
			'thread_prefix_*' => 'thread_prefix.*',
			'trophy_*_description' => 'trophy_description.*',
			'trophy_*_title' => 'trophy_title.*',
			'user_field_*_choice_*' => 'user_field_choice.$1_$2',
			'user_field_*_desc' => 'user_field_desc.*',
			'user_field_*' => 'user_field_title.*',
			'warning_definition_*_conversation_title' => 'warning_conv_title.*',
			'warning_definition_*_conversation_text' => 'warning_conv_text.*',
			'warning_definition_*_title' => 'warning_title.*',
		];

		$db = $this->db();

		foreach ($map AS $from => $to)
		{
			$mySqlRegex = '^' . str_replace('*', '[a-zA-Z0-9_]+', $from) . '$';
			$phpRegex = '/^' . str_replace('*', '([a-zA-Z0-9_]+)', $from) . '$/';
			$replace = str_replace('*', '$1', $to);

			$results = $db->fetchAll("
				SELECT phrase_id, title
				FROM xf_phrase
				WHERE title RLIKE ?
			", $mySqlRegex);
			foreach ($results AS $result)
			{
				$newTitle = preg_replace($phpRegex, $replace, $result['title']);
				$db->update(
					'xf_phrase',
					['title' => $newTitle, 'global_cache' => 0],
					'phrase_id = ?',
					$result['phrase_id']
				);
			}
		}
	}

	// update option array format (now JSON)
	public function step25()
	{
		$db = $this->db();

		$arrayTypeOptions = $db->fetchAllKeyed('
			SELECT option_id, option_value, default_value
			FROM xf_option
			WHERE data_type = \'array\'
		', 'option_id');

		$db->beginTransaction();

		foreach ($arrayTypeOptions AS $optionId => $option)
		{
			$value = @unserialize($option['option_value']) ?: [];
			$defaultValue = @unserialize($option['default_value']) ?: [];

			if ($optionId == 'imageLibrary')
			{
				// currently an array type but converting to string
				if (isset($value['class']))
				{
					$db->update('xf_option', [
						'option_value' => $value['class'],
						'default_value' => 'gd',
						'data_type' => 'string',
						'sub_options' => ''
					], 'option_id = ?', $optionId);
				}
			}
			else
			{
				$db->update('xf_option', [
					'option_value' => json_encode($value),
					'default_value' => json_encode($defaultValue)
				], 'option_id = ?', $optionId);
			}
		}

		$db->commit();

		$this->schemaManager()->alterTable('xf_option', function (Alter $table)
		{
			$table->dropColumns('can_backup');
			$table->changeColumn('edit_format')->addValues('username');
		});
	}

	// super admin updates
	public function step26()
	{
		$db = $this->db();

		$this->schemaManager()->alterTable('xf_admin', function (Alter $table)
		{
			$table->addColumn('is_super_admin', 'tinyint')->setDefault(0);
		});

		$superAdmins = preg_split(
			'#\s*,\s*#', \XF::config('superAdmins'), -1, PREG_SPLIT_NO_EMPTY
		);
		if (!$superAdmins)
		{
			$admins = $db->fetchAllKeyed('SELECT * FROM xf_admin', 'user_id');
			if (sizeof($admins) === 1)
			{
				$admin = reset($admins);
				$superAdmins[] = $admin['user_id'];
			}
			else
			{
				$mostPermissiveAdmins = [];

				foreach ($admins AS $userId => $admin)
				{
					$permissionCache = @unserialize($admin['permission_cache']) ?: [];
					$permissionCount = count($permissionCache);

					$mostPermissiveAdmins[$userId] = $permissionCount;
				}

				arsort($mostPermissiveAdmins);
				$superAdmins = array_keys($mostPermissiveAdmins, max($mostPermissiveAdmins));
			}
		}

		$superAdmins = array_map('intval', $superAdmins);
		foreach ($superAdmins AS $userId)
		{
			$db->update('xf_admin', ['is_super_admin' => 1], 'user_id = ?', $userId);
		}
	}

	// apply new moderator permissions for what used to be an admin thing
	public function step27()
	{
		$db = $this->db();

		$admins = $db->fetchAllKeyed("
			SELECT ad.user_id, ad.permission_cache, ad.is_super_admin
			FROM xf_admin AS ad
			INNER JOIN xf_user AS user ON
				(ad.user_id = user.user_id)
			WHERE user.is_moderator = 1
		", 'user_id');

		$userPermUserIds = [];
		$banPermUserIds = [];
		foreach ($admins AS $userId => $admin)
		{
			$permCache = @unserialize($admin['permission_cache']);
			if ($admin['is_super_admin'] || !empty($permCache['user']))
			{
				$userPermUserIds[] = $userId;
			}
			if ($admin['is_super_admin'] || !empty($permCache['ban']))
			{
				$banPermUserIds[] = $userId;
			}
		}

		$this->applyPermissionToUsers($userPermUserIds, 'general', 'approveRejectUser');
		$this->applyPermissionToUsers($banPermUserIds, 'general', 'banUser');
	}

	protected function applyPermissionToUsers($userIds, $permGroupId, $permId)
	{
		if (!is_array($userIds))
		{
			$userIds = [$userIds];
		}

		$inserts = [];

		foreach ($userIds AS $userId)
		{
			$inserts[] = [
				'user_group_id' => 0,
				'user_id' => $userId,
				'permission_group_id' => $permGroupId,
				'permission_id' => $permId,
				'permission_value' => 'allow',
				'permission_value_int' => 0
			];
		}

		if ($inserts)
		{
			$this->db()->insertBulk('xf_permission_entry', $inserts, false, 'permission_value = VALUES(permission_value)');
		}
	}

	// account for user changes
	public function step28($position, array $stepData)
	{
		$perPage = 1000;
		$db = $this->db();

		$skipGender = isset($stepData['skipGender']) ? $stepData['skipGender'] : false;
		$skipOccupation = isset($stepData['skipOccupation']) ? $stepData['skipOccupation'] : false;

		if (!isset($stepData['max']))
		{
			$stepData['max'] = $db->fetchOne('SELECT MAX(user_id) FROM xf_user');

			$skipGender = (bool)$db->fetchOne('SELECT field_id FROM xf_user_field WHERE field_id = ?', 'gender');
			if (!$skipGender)
			{
				$db->insert('xf_user_field', [
					'field_id' => 'gender',
					'display_group' => 'personal',
					'display_order' => 10,
					'field_type' => 'radio',
					'field_choices' => serialize(['male' => 'Male', 'female' => 'Female']),
					'show_registration' => 0,
					'display_template' => ''
				]);

				$this->executeUpgradeQuery("
					REPLACE INTO xf_phrase
						(language_id, title, phrase_text)
					VALUES
						(0, 'user_field_title.gender', 'Gender'),
						(0, 'user_field_desc.gender', ''),
						(0, 'user_field_choice.gender_male', 'Male'),
						(0, 'user_field_choice.gender_female', 'Female')
				");
			}

			$skipOccupation = (bool)$db->fetchOne('SELECT field_id FROM xf_user_field WHERE field_id = ?', 'occupation');
			if (!$skipOccupation)
			{
				$db->insert('xf_user_field', [
					'field_id' => 'occupation',
					'display_group' => 'personal',
					'display_order' => 20,
					'field_choices' => '',
					'show_registration' => 0,
					'display_template' => ''
				]);

				$this->executeUpgradeQuery("
					REPLACE INTO xf_phrase
						(language_id, title, phrase_text)
					VALUES
						(0, 'user_field_title.occupation', 'Occupation'),
						(0, 'user_field_desc.occupation', '')
				");
			}
		}

		if ($skipGender && $skipOccupation)
		{
			return true;
		}

		$userIds = $db->fetchAllColumn($db->limit('
			SELECT user_id
			FROM xf_user
			WHERE user_id > ?
			ORDER BY user_id
		', $perPage), $position);

		if (!$userIds)
		{
			return true;
		}

		$db->beginTransaction();

		$select = ['user.user_id', 'profile.custom_fields'];
		$where = [];
		if (!$skipGender)
		{
			$select[] = 'user.gender';
			$where[] = 'user.gender <> \'\'';
		}
		if (!$skipOccupation)
		{
			$select[] = 'profile.occupation';
			$where[] = 'profile.occupation <> \'\'';
		}

		$users = $db->fetchAllKeyed('
			SELECT ' . implode(', ', $select) . '
			FROM xf_user AS user
			INNER JOIN xf_user_profile AS profile ON
				(user.user_id = profile.user_id)
			WHERE user.user_id IN(' . $db->quote($userIds) . ')
				AND (' . implode(' OR ', $where) . ')
			ORDER BY user.user_id
		', 'user_id');

		$next = 0;
		foreach ($userIds AS $userId)
		{
			$next = $userId;

			if (!isset($users[$userId]))
			{
				continue;
			}

			$user = $users[$userId];

			$inserts = [];
			if (!$skipGender && $user['gender'])
			{
				$inserts['gender'] = [
					'user_id' => $userId,
					'field_id' => 'gender',
					'field_value' => $user['gender']
				];
			}
			if (!$skipOccupation && $user['occupation'])
			{
				$inserts['occupation'] = [
					'user_id' => $userId,
					'field_id' => 'occupation',
					'field_value' => $user['occupation']
				];
			}
			if ($inserts)
			{
				try
				{
					$db->insertBulk('xf_user_field_value', $inserts);
				}
				catch (\XF\Db\Exception $e)
				{
					continue;
				}
				$customFields = @unserialize($user['custom_fields']) ?: [];
				foreach ($inserts AS $fieldId => $fieldValues)
				{
					$customFields[$fieldId] = $fieldValues['field_value'];
				}
				$db->update('xf_user_profile', ['custom_fields' => serialize($customFields)], 'user_id = ?', $userId);
			}
		}

		$db->commit();

		$stepData['skipGender'] = $skipGender;
		$stepData['skipOccupation'] = $skipOccupation;

		return [
			$next,
			"$next / $stepData[max]",
			$stepData
		];
	}

	public function step29()
	{
		$sm = $this->schemaManager();

		$sm->alterTable('xf_user_profile', function (Alter $table)
		{
			$table->renameColumn('homepage', 'website');
			$table->renameColumn('external_auth', 'connected_accounts');
			$table->dropColumns(['status', 'status_date', 'status_profile_post_id', 'occupation', 'csrf_token']);
		});
	}

	public function step30()
	{
		$this->schemaManager()->alterTable('xf_user', function (Alter $table)
		{
			$table->dropColumns('gender');
			$table->changeColumn('user_state')->addValues(['rejected', 'disabled']);
			$table->addColumn('secret_key', 'varbinary', 32);
			$table->addColumn('avatar_highdpi', 'tinyint', 3)
				->after('avatar_height')
				->setDefault(0);
		});
	}

	public function step31()
	{
		$this->executeUpgradeQuery("
			UPDATE xf_user
			INNER JOIN xf_user_authenticate ON xf_user.user_id = xf_user_authenticate.user_id
			SET xf_user.secret_key = SUBSTR(xf_user_authenticate.remember_key, 1, 32)
		");
	}

	public function step32()
	{
		$this->schemaManager()->alterTable('xf_user_authenticate', function (Alter $table)
		{
			$table->dropColumns('remember_key');
		});
	}

	// update the watch state configuration
	public function step33()
	{
		$this->executeUpgradeQuery("
			UPDATE xf_user_option
			SET interaction_watch_state = creation_watch_state
			WHERE creation_watch_state IN ('watch_no_email', 'watch_email')
		");

		$db = $this->db();

		$registrationDefaults = $db->fetchOne('
			SELECT option_value
			FROM xf_option
			WHERE option_id = \'registrationDefaults\'
		');

		$registrationDefaults = json_decode($registrationDefaults, true);
		$registrationDefaults['creation_watch_state'] = $registrationDefaults['default_watch_state'];
		$registrationDefaults['interaction_watch_state'] = $registrationDefaults['default_watch_state'];
		unset($registrationDefaults['default_watch_state']);

		if (!isset($registrationDefaults['requireLocation']))
		{
			$registrationDefaults['requireLocation'] = false;
		}

		$this->executeUpgradeQuery('
			UPDATE xf_option
			SET option_value = ?
			WHERE option_id = \'registrationDefaults\'
		', json_encode($registrationDefaults));
	}

	// update profile post comment caches
	public function step34($position, array $stepData)
	{
		$perPage = 250;
		$db = $this->db();

		if (!isset($stepData['max']))
		{
			$stepData['max'] = $db->fetchOne('SELECT MAX(profile_post_id) FROM xf_profile_post WHERE latest_comment_ids <> \'\'');
		}

		$profilePostIds = $db->fetchAllColumn($db->limit(
			'
				SELECT profile_post_id
				FROM xf_profile_post
				WHERE profile_post_id > ?
					AND latest_comment_ids <> \'\'
				ORDER BY profile_post_id
			', $perPage
		), $position);
		if (!$profilePostIds)
		{
			return true;
		}

		$db->beginTransaction();

		$profilePosts = $db->fetchAllKeyed('
			SELECT *
			FROM xf_profile_post
			WHERE profile_post_id IN(' . $db->quote($profilePostIds) . ')
		', 'profile_post_id');

		foreach ($profilePosts AS $profilePostId => $profilePost)
		{
			$latestCommentIds = @unserialize($profilePost['latest_comment_ids']);
			if (is_array($latestCommentIds))
			{
				continue; // already in new format
			}

			$data = $db->fetchAllKeyed($db->limit('
				SELECT profile_post_comment_id, message_state, user_id
				FROM xf_profile_post_comment
				WHERE profile_post_id = ?
				ORDER BY comment_date DESC
			', 20), 'profile_post_comment_id', $profilePostId);

			$cache = [];
			$visibleCount = 0;

			foreach ($data AS $id => $row)
			{
				$cache[$id] = [$row['message_state'], $row['user_id']];
				if ($row['message_state'] == 'visible')
				{
					$visibleCount++;
				}

				if ($visibleCount === 3)
				{
					break;
				}
			}
			$cache = array_reverse($cache, true); // need last comments, but in oldest first order

			$db->update('xf_profile_post', ['latest_comment_ids' => serialize($cache)], 'profile_post_id = ?', $profilePostId);
		}

		$db->commit();

		$nextPosition = end($profilePostIds);

		return [
			$nextPosition,
			"$nextPosition / $stepData[max]",
			$stepData
		];
	}

	// update the user field structure
	public function step35()
	{
		$sm = $this->schemaManager();
		$db = $this->db();

		$db->beginTransaction();

		$sm->alterTable('xf_user_field', function (Alter $table)
		{
			$table->changeColumn('field_type')->resetDefinition()->type('varbinary', 25)->setDefault('textbox');
			$table->changeColumn('match_type')->resetDefinition()->type('varbinary', 25)->setDefault('none');
			$table->changeColumn('viewable_message')->length(3)->unsigned();
			$table->changeColumn('viewable_profile')->length(3)->unsigned();
			$table->addColumn('match_params', 'blob')->after('match_type');
		});

		foreach ($db->fetchAllKeyed("SELECT * FROM xf_user_field", 'field_id') AS $fieldId => $field)
		{
			if (!isset($field['match_regex']))
			{
				// column removed already, this has been run
				continue;
			}

			$update = [];
			$matchParams = [];

			switch ($field['match_type'])
			{
				case 'regex':
					if ($field['match_regex'])
					{
						$matchParams['regex'] = $field['match_regex'];
					}
					break;

				case 'callback':
					if ($field['match_callback_class'] && $field['match_callback_method'])
					{
						$matchParams['callback_class'] = $field['match_callback_class'];
						$matchParams['callback_method'] = $field['match_callback_method'];
					}
					break;
			}

			if (!empty($matchParams))
			{
				$update['match_params'] = json_encode($matchParams);
			}

			if ($field['field_choices'] && $fieldChoices = @unserialize($field['field_choices']))
			{
				$update['field_choices'] = json_encode($fieldChoices);
			}

			if (!empty($update))
			{
				$db->update('xf_user_field', $update, 'field_id = ?', $fieldId);
			}
		}

		$db->update('xf_user_field', ['match_type' => 'validator', 'match_params' => json_encode(['validator' => 'Facebook'])], 'field_id = ?', 'facebook');
		$db->update('xf_user_field', ['match_type' => 'validator', 'match_params' => json_encode(['validator' => 'Twitter'])], 'field_id = ?', 'twitter');

		$sm->alterTable('xf_user_field', function(Alter $table)
		{
			$table->dropColumns(['match_regex', 'match_callback_class', 'match_callback_method']);
		});
	}

	// update user upgrade structures
	public function step36()
	{
		$sm = $this->schemaManager();

		$this->executeUpgradeQuery("
			UPDATE xf_user_upgrade
			SET cost_currency = UPPER(cost_currency)
		");

		$sm->alterTable('xf_user_upgrade_log', function (Alter $table)
		{
			$table->renameTo('xf_payment_provider_log');
			$table->renameColumn('user_upgrade_log_id', 'provider_log_id');
			$table->changeColumn('user_upgrade_record_id')
				->resetDefinition()
				->renameTo('purchase_request_key')
				->type('varbinary', 32)
				->nullable();
			$table->renameColumn('processor', 'provider_id')->type('varbinary');
			$table->changeColumn('transaction_id')->length(100)->nullable();
			$table->changeColumn('subscriber_id')->length(100)->nullable()->setDefault(null); // this had a default previously so need to override
			$table->renameColumn('transaction_type', 'log_type');
			$table->renameColumn('message', 'log_message');
			$table->renameColumn('transaction_details', 'log_details');
		});

		$sm->alterTable('xf_user_upgrade', function (Alter $table)
		{
			$table->addColumn('payment_profile_ids', 'varbinary', 255)->setDefault('');
		});

		$sm->alterTable('xf_user_upgrade_active', function (Alter $table)
		{
			$table->addColumn('purchase_request_key', 'varbinary', 32)->nullable()->after('user_id');
		});

		$sm->alterTable('xf_user_upgrade_expired', function (Alter $table)
		{
			$table->addColumn('purchase_request_key', 'varbinary', 32)->nullable()->after('user_id');
		});

		$db = $this->db();

		$payPalPrimary = $db->fetchOne("SELECT option_value FROM xf_option WHERE option_id = 'payPalPrimaryAccount'");
		$payPalAlternate = $db->fetchOne("SELECT option_value FROM xf_option WHERE option_id = 'payPalAlternateAccounts'");

		if ($payPalPrimary || ($payPalPrimary && $payPalAlternate))
		{
			$options = [
				'primary_account' => $payPalPrimary,
				'alternate_accounts' => $payPalAlternate,
				'legacy' => true
			];

			$this->executeUpgradeQuery("
				INSERT INTO xf_payment_profile
					(provider_id, title, options)
				VALUES
					('paypal', 'PayPal', ?)
			", json_encode($options));

			$profileId = $db->lastInsertId();

			// this is a comma list, so 1 ID is just the number
			$this->executeUpgradeQuery("
				UPDATE xf_user_upgrade
				SET payment_profile_ids = ?
			", $profileId);
		}
	}

	// update language structurs
	public function step37()
	{
		$this->schemaManager()->alterTable('xf_language', function (Alter $table)
		{
			$table->addColumn('currency_format', 'varchar', 30)->after('time_format');
			$table->addColumn('week_start', 'tinyint')->setDefault(0)->comment('Week start day. 0 = Sunday, 6 = Saturday');
			$table->addColumn('label_separator', 'varchar', 15)->setDefault(':');
			$table->addColumn('comma_separator', 'varchar', 15)->setDefault(', ');
			$table->addColumn('ellipsis', 'varchar', 15)->setDefault('...');
			$table->addColumn('parenthesis_open', 'varchar', 15)->setDefault('(');
			$table->addColumn('parenthesis_close', 'varchar', 15)->setDefault(')');
		});

		$utf8Ellipsis = chr(0xE2) . chr(0x80) . chr(0xA6); // http://www.fileformat.info/info/unicode/char/2026/index.htm

		$this->executeUpgradeQuery("
			UPDATE xf_language
			SET currency_format = '{symbol}{value}',
				ellipsis = ?
		", $utf8Ellipsis);
	}

	// making the user change log a more generic system
	public function step38()
	{
		$this->schemaManager()->alterTable('xf_user_change_log', function (Alter $table)
		{
			$table->renameTo('xf_change_log');
			$table->dropIndexes('user_id');
			$table->changeColumn('log_id')->length(10);
			$table->changeColumn('edit_user_id')->length(10);
			$table->addColumn('content_type', 'varbinary', 25)->after('log_id');
			$table->renameColumn('user_id', 'content_id')->length(10);
			$table->addKey(['content_type', 'content_id', 'edit_date'], 'content_type_content_id_date');
			$table->addKey(['content_type', 'edit_date'], 'content_type_date');
		});

		$this->executeUpgradeQuery("
			UPDATE xf_change_log SET content_type = 'user' WHERE content_type = ''
		");
		$this->executeUpgradeQuery("
			UPDATE xf_option SET option_id = 'changeLogLength' WHERE option_id = 'userChangeLogLength'
		");
		$this->executeUpgradeQuery("
			DELETE FROM xf_option_group_relation
			WHERE option_id = 'userChangeLogLength'
		");
	}

	// notice structure changes
	public function step39()
	{
		$this->schemaManager()->alterTable('xf_notice', function (Alter $table)
		{
			$table->dropColumns('wrap');
			$table->changeColumn('notice_type', 'varchar', 25);
			$table->changeColumn('display_style', 'varchar', 25);
		});

		$this->executeUpgradeQuery("
			UPDATE xf_notice
			SET display_style = 'primary'
			WHERE notice_type = 'block'
		");
		$this->executeUpgradeQuery("
			UPDATE xf_notice
			SET display_style = 'accent'
			WHERE notice_type = 'secondary'
		");
	}

	// custom BB code changes
	public function step40()
	{
		$db = $this->db();

		$bbCodes = [];
		try
		{
			$bbCodes = $db->fetchAll('
				SELECT bb_code_id, example, addon_id
				FROM xf_bb_code
			');
		}
		catch (\XF\Db\Exception $e) {}

		foreach ($bbCodes AS $bbCode)
		{
			$exampleTitle = 'custom_bb_code_example.' . $bbCode['bb_code_id'];
			$this->executeUpgradeQuery("
				REPLACE INTO xf_phrase
					(language_id, title, phrase_text, global_cache, addon_id, version_id, version_string)
				VALUES
					(0, ?, ?, 0, ?, 0, '')
			", [$exampleTitle, $bbCode['example'], $bbCode['addon_id']]);

			$outputTitle = 'custom_bb_code_output.' . $bbCode['bb_code_id'];
			$this->executeUpgradeQuery("
				REPLACE INTO xf_phrase
					(language_id, title, phrase_text, global_cache, addon_id, version_id, version_string)
				VALUES
					(0, ?, '', 0, ?, 0, '')
			", [$outputTitle,  $bbCode['addon_id']]);
		}

		$this->schemaManager()->alterTable('xf_bb_code', function (Alter $table)
		{
			$table->dropColumns(['example', 'editor_icon_url', 'sprite_mode', 'sprite_params']);
			$table->addColumn('editor_icon_type', 'varchar', 25)->setDefault('');
			$table->addColumn('editor_icon_value', 'varchar', 150)->setDefault('');
		});
	}

	// BB code media site changes
	public function step41()
	{
		$db = $this->db();

		// remove non add-on associated sites that match ours so we can replace them
		$defaultMediaSites = [
			'dailymotion',
			'facebook',
			'liveleak',
			'metacafe',
			'vimeo',
			'youtube'
		];
		$this->executeUpgradeQuery("
			DELETE FROM xf_bb_code_media_site
			WHERE media_site_id IN(" . $db->quote($defaultMediaSites) . ")
			AND addon_id = ''
		");

		$customMediaSites = $db->fetchAllKeyed('
			SELECT site.*, addon.addon_id
			FROM xf_bb_code_media_site AS site
			LEFT JOIN xf_addon AS addon ON
				(site.addon_id = addon.addon_id)
		', 'media_site_id');

		foreach ($customMediaSites AS $mediaSiteId => $mediaSite)
		{
			$bind = [
				'_media_site_embed_' . $mediaSiteId,
				isset($mediaSite['embed_html']) ? $mediaSite['embed_html'] : '',
				isset($mediaSite['addon_id']) ? $mediaSite['addon_id'] : ''
			];

			$this->executeUpgradeQuery("
				REPLACE INTO xf_template
					(title, style_id, type, template, template_parsed, addon_id)
				VALUES
					(?, 0, 'public', ?, '', ?)
			", $bind);
		}

		$this->schemaManager()->alterTable('xf_bb_code_media_site', function (Alter $table)
		{
			$table->addColumn('active', 'tinyint')->setDefault(1)->after('supported');
			$table->addColumn('oembed_enabled', 'tinyint', 3)->setDefault(0);
			$table->addColumn('oembed_api_endpoint', 'varbinary', 250)->setDefault('');
			$table->addColumn('oembed_url_scheme', 'varbinary', 250)->setDefault('');
			$table->addColumn('oembed_retain_scripts', 'tinyint', 3)->setDefault(0);
			$table->dropColumns('embed_html');
		});
	}

	// help page changes
	public function step42()
	{
		$this->schemaManager()->alterTable('xf_help_page', function (Alter $table)
		{
			$table->changeColumn('page_id')->resetDefinition()->type('varbinary', 50)->primaryKey();
			$table->addColumn('advanced_mode', 'tinyint')->setDefault(0);
			$table->addColumn('active', 'tinyint')->setDefault(1);
			$table->addColumn('addon_id', 'varbinary', 50)->setDefault('');
		});
	}

	// connected account changes
	public function step43()
	{
		$sm = $this->schemaManager();

		$sm->renameTable('xf_user_external_auth', 'xf_user_connected_account');

		$this->executeUpgradeQuery("
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
		");

		$db = $this->db();

		$providers = [
			'facebook' => [
				'facebookAppId' => 'app_id',
				'facebookAppSecret' => 'app_secret',
			],
			'twitter' => [
				'twitterAppKey' => 'consumer_key',
				'twitterAppSecret' => 'consumer_secret',
			],
			'google' => [
				'googleClientId' => 'client_id',
				'googleClientSecret' => 'client_secret',
			]
		];

		$updates = [];

		foreach ($providers AS $providerId => $optionIds)
		{
			foreach ($optionIds AS $oldOptionId => $newOption)
			{
				$value = $db->fetchOne('SELECT option_value FROM xf_option WHERE option_id = ?', $oldOptionId);
				$updates[$providerId][$newOption] = $value;
			}

			$db->update('xf_connected_account_provider', ['options' => json_encode($updates[$providerId])], 'provider_id = ?', $providerId);
		}
	}

	// label and banner changes
	public function step44()
	{
		$db = $this->db();

		$groups = $db->fetchAllKeyed("
			SELECT user_group_id, banner_css_class, username_css
			FROM xf_user_group
			WHERE banner_css_class <> '' OR username_css <> ''
		", 'user_group_id');

		$db->beginTransaction();

		foreach ($groups AS $id => $group)
		{
			$updates = [];

			if ($group['banner_css_class'])
			{
				$class = $group['banner_css_class'];

				$newClass = preg_replace_callback('#(^|\s)banner([A-Z][a-zA-Z0-9_-]*)#', function ($match)
				{
					$variant = strtolower($match[2][0]) . substr($match[2], 1);
					if ($variant == 'secondary')
					{
						$variant = 'accent';
					}
					return $match[1] . 'userBanner--' . $variant;
				}, $class);
				if ($newClass != $class)
				{
					$updates['banner_css_class'] = $newClass;
				}
			}

			if ($group['username_css'])
			{
				$parser = new \Less_Parser();
				try
				{
					$parser->parse('.example { ' . $group['username_css'] . '}')->getCss();
				}
				catch (\Exception $e)
				{
					// invalid CSS, need to comment it out
					$updates['username_css'] = '/* ' . str_replace('*/', '* /', $group['username_css']) . ' */';
				}
			}

			if ($updates)
			{
				$db->update('xf_user_group',
					$updates,
					'user_group_id = ?', $id
				);
			}
		}

		$db->commit();

		$prefixes = $db->fetchPairs("
			SELECT prefix_id, css_class
			FROM xf_thread_prefix
		");

		$db->beginTransaction();

		foreach ($prefixes AS $id => $class)
		{
			if ($class === '')
			{
				$newClass = 'label label--hidden';
			}
			else
			{
				$newClass = preg_replace_callback('#prefix\s+prefix([A-Z][a-zA-Z0-9_-]*)#', function ($match)
				{
					$variant = strtolower($match[1][0]) . substr($match[1], 1);
					if ($variant == 'secondary')
					{
						$variant = 'accent';
					}
					return 'label label--' . $variant;
				}, $class);
			}
			if ($newClass != $class)
			{
				$db->update('xf_thread_prefix',
					['css_class' => $newClass],
					'prefix_id = ?', $id
				);
			}
		}

		$db->commit();
	}

	// update forum prefix cache format
	public function step45()
	{
		$db = $this->db();

		$associations = $db->fetchAll("
			SELECT fp.*, p.*
			FROM xf_forum_prefix AS fp
			INNER JOIN xf_thread_prefix as p ON
				(fp.prefix_id = p.prefix_id)
			ORDER BY p.materialized_order
		");

		$cache = [];
		foreach ($associations AS $association)
		{
			$cache[$association['node_id']][$association['prefix_id']] = $association['prefix_id'];
		}

		$db->beginTransaction();

		foreach ($cache AS $nodeId => $prefixes)
		{
			$db->update(
				'xf_forum',
				['prefix_cache' => serialize($prefixes)],
				'node_id = ?',
				$nodeId
			);
		}

		$db->commit();
	}

	// remove invalid criteria
	public function step46()
	{
		$db = $this->db();

		$notices = $db->fetchAll("
			SELECT *
			FROM xf_notice
			WHERE user_criteria LIKE '%\"from\_search\"%'
				OR user_criteria LIKE '%\"style\"%'
		");
		foreach ($notices AS $notice)
		{
			$userCriteria = @unserialize($notice['user_criteria']);
			$pageCriteria = @unserialize($notice['page_criteria']);

			if (!is_array($userCriteria) || !is_array($pageCriteria))
			{
				continue;
			}

			foreach ($userCriteria AS $k => $criterion)
			{
				if (!empty($criterion['rule'])
					&& ($criterion['rule'] == 'from_search' || $criterion['rule'] == 'style')
				)
				{
					$pageCriteria[] = $criterion;
					unset($userCriteria[$k]);
				}
			}

			$db->update('xf_notice', [
				'user_criteria' => serialize($userCriteria),
				'page_criteria' => serialize($pageCriteria)
			], 'notice_id = ?', $notice['notice_id']);
		}

		$removeFromTables = [
			'xf_trophy' => 'trophy_id',
			'xf_user_group_promotion' => 'promotion_id'
		];
		foreach ($removeFromTables AS $table => $primaryKey)
		{
			$removeList = $db->fetchAll("
				SELECT *
				FROM `{$table}`
				WHERE user_criteria LIKE '%\"from\_search\"%'
					OR user_criteria LIKE '%\"style\"%'
			");
			foreach ($removeList AS $remove)
			{
				$userCriteria = @unserialize($remove['user_criteria']);

				if (!is_array($userCriteria))
				{
					continue;
				}

				foreach ($userCriteria AS $k => $criterion)
				{
					if (!empty($criterion['rule'])
						&& ($criterion['rule'] == 'from_search' || $criterion['rule'] == 'style')
					)
					{
						unset($userCriteria[$k]);
					}
				}

				$db->update($table, [
					'user_criteria' => serialize($userCriteria)
				], "{$primaryKey} = ?", $remove[$primaryKey]);
			}
		}
	}

	// update alerts and tag/mention changes
	public function step47()
	{
		$this->executeUpgradeQuery("
			UPDATE xf_user_alert_optout
			SET alert = 'post_mention'
			WHERE alert = 'post_tag'
		");

		$this->executeUpgradeQuery("
			UPDATE xf_user_alert_optout
			SET alert = 'profile_post_mention'
			WHERE alert = 'profile_post_tag'
		");

		$this->executeUpgradeQuery("
			UPDATE xf_user_alert
			SET action = 'mention'
			WHERE action = 'tag'
		");

		$this->executeUpgradeQuery("
			UPDATE xf_permission_entry
			SET permission_id = 'maxMentionedUsers'
			WHERE permission_id = 'maxTaggedUsers'
		");

		$this->executeUpgradeQuery("
			UPDATE xf_option
			SET option_id = 'userMentionKeepAt'
			WHERE option_id = 'userTagKeepAt'
		");

		$this->executeUpgradeQuery("
			DELETE FROM xf_option_group_relation
			WHERE option_id = 'userTagKeepAt'
		");

		$this->executeUpgradeQuery("
			UPDATE xf_user_alert
			SET action = 'insert'
			WHERE action = 'insert_attachment'
				AND content_type = 'post'
		");
	}

	// update smilies
	public function step48()
	{
		$this->schemaManager()->alterTable('xf_smilie', function (Alter $table)
		{
			$table->addColumn('image_url_2x')->type('varchar', 200)->setDefault('')->after('image_url');
			$table->changeColumn('sprite_params')->type('blob');
		});

		// [ x => [ y => newY ] ]
		$coordMap = [
			0 => [
				0 => 0, // smile
				21 => -110, // rolleyes
				42 => -220, // redface
				63 => -330, // giggle
				84 => -440, // notworthy
				105 => -550, // thumbsup
				126 => -660 // speechless
			],
			20 => [
				0 => -22, // biggrin
				21 => -132, // tongue
				42 => -242, // eek
				63 => -352, // sleep
				84 => -462, // poop
				105 => -572, // thumbsdown
				126 => -682 // sick
			],
			40 => [
				0 => -44, // cool
				21 => -154, // confused
				42 => -264, // frown
				63 => -374, // ninja
				84 => -484, // cry
				105 => -594, // barefoot
				126 => -704 // sneaky
			],
			60 => [
				0 => -66, //mad
				21 => -176, // wink
				42 => -286, //roflmao
				63 => -396, // laugh
				84 => -506, // inlove
				105 => -616, // alien
				126 => -726 // x3
			],
			80 => [
				0 => -88, // geek
				21 => -198, // coffee
				42 => -308, // O_o
				63 => -418, // cautious
				84 => -528, // unsure
				105 => -638, // devilish
				126 => -748 // whistle
			]
		];

		$defaultSprite = 'styles/default/xenforo/xenforo-smilies-sprite.png';

		$db = $this->db();

		$possibleDefaultSmilies = $db->fetchAllKeyed('
			SELECT *
			FROM xf_smilie
			WHERE image_url = ?
				AND sprite_mode = 1
		', 'smilie_id', $defaultSprite);

		$updates = [];
		foreach ($possibleDefaultSmilies AS $smilieId => $smilie)
		{
			$spriteParams = @unserialize($smilie['sprite_params']);
			if (!$spriteParams)
			{
				continue;
			}

			$x = abs($spriteParams['x']);
			$y = abs($spriteParams['y']);

			if (!isset($coordMap[$x][$y]))
			{
				continue; // Smilie coords don't appear in map above so skip
			}
			$newY = $coordMap[$x][$y];

			$updates[$smilieId] = [
				'w' => 22,
				'h' => 22,
				'x' => 0,
				'y' => $newY,
				'bs' => '100%'
			];
		}

		if ($updates)
		{
			$db->beginTransaction();

			foreach ($updates AS $smilieId => $spriteParams)
			{
				$db->update('xf_smilie', [
					'image_url' => 'styles/default/xenforo/smilies/emojione/sprite_sheet_emojione.png',
					'sprite_params' => serialize($spriteParams)
				], 'smilie_id = ?', $smilieId);
			}

			$db->commit();
		}
	}

	// update addon_id length
	public function step49($position, array $stepData)
	{
		$startTime = microtime(true);

		$sm = $this->schemaManager();

		if (!isset($stepData['tables']))
		{
			$stepData['tables'] = [
				'xf_addon',
				'xf_admin_navigation',
				'xf_admin_permission',
				'xf_bb_code',
				'xf_bb_code_media_site',
				'xf_code_event',
				'xf_code_event_listener',
				'xf_cron_entry',
				'xf_option',
				'xf_option_group',
				'xf_permission',
				'xf_permission_interface_group',
				'xf_phrase',
				'xf_template',
				'xf_template_modification'
			];
			$stepData['max'] = count($stepData['tables']);
		}

		if (empty($stepData['tables']))
		{
			return true;
		}

		$next = $position;

		foreach ($stepData['tables'] AS $key => $table)
		{
			$sm->alterTable($table, function(Alter $table)
			{
				$table->changeColumn('addon_id')->length(50);
			});

			$next++;
			unset($stepData['tables'][$key]);

			if (microtime(true) - $startTime >= 5)
			{
				break;
			}
		}

		return [
			$next,
			"$next / $stepData[max]",
			$stepData
		];
	}

	// update callback lengths
	public function step50($position, array $stepData)
	{
		$startTime = microtime(true);

		$sm = $this->schemaManager();

		if (!isset($stepData['tables']))
		{
			$stepData['tables'] = [
				'xf_bb_code' => [
					'class' => 'callback_class',
					'method' => 'callback_method'
				],
				'xf_bb_code_media_site' => [
					'class' => ['match_callback_class', 'embed_html_callback_class'],
					'method' => ['match_callback_method', 'embed_html_callback_method']
				],
				'xf_code_event_listener' => [
					'class' => 'callback_class',
					'method' => 'callback_method'
				],
				'xf_cron_entry' => [
					'class' => 'cron_class',
					'method' => 'cron_method'
				],
				'xf_help_page' => [
					'class' => 'callback_class',
					'method' => 'callback_method'
				],
				'xf_option' => [
					'class' => 'validation_class',
					'method' => 'validation_method'
				],
				'xf_page' => [
					'class' => 'callback_class',
					'method' => 'callback_method'
				],
				'xf_session_activity' => [
					'class' => 'controller_name',
					'method' => 'controller_action'
				],
				'xf_tfa_provider' => [
					'class' => 'provider_class',
					'method' => null
				],
				'xf_user_authenticate' => [
					'class' => 'scheme_class',
					'method' => null
				]
			];
			$stepData['max'] = count($stepData['tables']);
		}

		if (empty($stepData['tables']))
		{
			return true;
		}

		$next = $position;

		foreach ($stepData['tables'] AS $table => $types)
		{
			$sm->alterTable($table, function(Alter $table) use ($types)
			{
				foreach ($types AS $type => $columns)
				{
					if ($columns === null)
					{
						continue;
					}
					if (!is_array($columns))
					{
						$columns = [$columns];
					}
					foreach ($columns AS $column)
					{
						$table->changeColumn($column)->type('varchar')->length($type == 'class' ? 100 : 75);
					}
				}
			});

			$next++;
			unset($stepData['tables'][$table]);

			if (microtime(true) - $startTime >= 5)
			{
				break;
			}
		}

		return [
			$next,
			"$next / $stepData[max]",
			$stepData
		];
	}

	// general permission updates
	public function step51()
	{
		$this->executeUpgradeQuery("
			REPLACE INTO xf_permission_entry
				(user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
			SELECT DISTINCT user_group_id, user_id, 'forum', 'inlineMod', 'allow', 0
			FROM xf_permission_entry
			WHERE permission_group_id = 'forum'
				AND permission_id IN ('deleteAnyThread', 'undelete', 'approveUnapprove',
					'lockUnlockThread', 'stickUnstickThread', 'manageAnyThread')
		");
		$this->executeUpgradeQuery("
			REPLACE INTO xf_permission_entry_content
				(content_type, content_id, user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
			SELECT DISTINCT content_type, content_id, user_group_id, user_id, 'forum', 'inlineMod', 'content_allow', 0
			FROM xf_permission_entry_content
			WHERE permission_group_id = 'forum'
				AND permission_id IN ('deleteAnyThread', 'undelete', 'approveUnapprove',
					'lockUnlockThread', 'stickUnstickThread', 'manageAnyThread')
		");
		$this->executeUpgradeQuery("
			REPLACE INTO xf_permission_entry
				(user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
			SELECT DISTINCT user_group_id, user_id, 'profilePost', 'inlineMod', 'allow', 0
			FROM xf_permission_entry
			WHERE permission_group_id = 'profilePost'
				AND permission_id IN ('deleteAny', 'undelete', 'approveUnapprove')
		");

		$db = $this->db();

		// rename followModerationRules permission
		$this->executeUpgradeQuery("
			UPDATE xf_permission_entry
			SET permission_id = 'submitWithoutApproval'
			WHERE permission_group_id = 'general' AND permission_id = 'followModerationRules'
		");

		// conversation permission renames
		$tablesToUpdate = [
			'xf_permission',
			'xf_permission_entry',
			'xf_permission_entry_content'
		];
		$renames = [
			'editOwnPost' => 'editOwnMessage',
			'editOwnPostTimeLimit' => 'editOwnMessageTimeLimit',
			'editAnyPost' => 'editAnyMessage'
		];

		foreach ($tablesToUpdate AS $table)
		{
			foreach ($renames AS $old => $new)
			{
				$db->update($table, [
					'permission_id' => $new
				], 'permission_id = ? AND permission_group_id = ?', [$old, 'conversation']);
			}
		}

		$this->applyGlobalPermission('conversation', 'like', 'forum', 'like');
		$this->applyGlobalPermission('general', 'useContactForm', 'general', 'view');

		$this->executeUpgradeQuery("
			DELETE FROM xf_permission_entry
			WHERE permission_group_id = 'avatar' AND permission_id = 'maxFileSize'
		");
	}

	// general option changes
	public function step52()
	{
		$db = $this->db();

		// update sitemap submit to allow for urls
		$pingEnabled = (bool)$db->fetchOne('SELECT option_value FROM xf_option WHERE option_id = \'sitemapAutoSubmit\'');
		if ($pingEnabled)
		{
			$urls = [
				'http://www.google.com/webmasters/tools/ping?sitemap={$url}',
				'http://www.bing.com/ping?sitemap={$url}'
			];

			$newValue = [
				'enabled' => true,
				'urls' => implode("\n", $urls)
			];
		}
		else
		{
			$newValue = ['enabled' => false, 'urls' => ''];
		}

		$db->update('xf_option', ['option_value' => json_encode($newValue)], 'option_id = \'sitemapAutoSubmit\'');

		// ensure trophy stat isn't enabled when trophies are disabled
		$enableTrophies = $db->fetchOne("SELECT option_value FROM xf_option WHERE option_id = 'enableTrophies'");
		if (!$enableTrophies)
		{
			// The active flag being 0 here will persist through to the import and keep this stat disabled if
			// the trophies option is already disabled.
			$db->insert('xf_member_stat', [
				'member_stat_key' => 'most_points',
				'addon_id' => 'XF',
				'active' => 0
			]);
		}

		// NoCaptcha is now the default ReCaptcha option
		$this->executeUpgradeQuery("
			UPDATE xf_option
			SET option_value = 'ReCaptcha'
			WHERE option_value = 'NoCaptcha'
				AND option_id = 'captcha'
		");

		// we now store user ids in the registration welcome field
		$optionValue = $db->fetchOne('SELECT option_value FROM xf_option WHERE option_id = \'registrationWelcome\'');
		$optionValue = json_decode($optionValue, true);

		if ($optionValue && $optionValue['messageParticipants'])
		{
			$users = $db->fetchAllKeyed('SELECT * FROM xf_user WHERE username IN(' . $db->quote($optionValue['messageParticipants']) . ')', 'user_id');
			if (!$users)
			{
				return;
			}
			$optionValue['messageParticipants'] = array_keys($users);
			$db->query('UPDATE xf_option SET option_value = ? WHERE option_id = \'registrationWelcome\'', json_encode($optionValue));
		}
	}

	// final assorted actions
	public function step53()
	{
		// custom TFA providers will no longer work, so we need to remove them
		$this->db()->emptyTable('xf_tfa_provider');
		$this->executeUpgradeQuery("
			INSERT INTO `xf_tfa_provider`
				(`provider_id`, `provider_class`, `priority`, `active`)
			VALUES
				('backup', 'XF:Backup', 1000, 1),
				('email', 'XF:Email', 900, 1),
				('totp', 'XF:Totp', 10, 1)
		");

		$this->executeUpgradeQuery("
			UPDATE xf_admin_permission_entry
			SET admin_permission_id = 'tags'
			WHERE admin_permission_id = 'tag'
		");

		$this->executeUpgradeQuery("
			REPLACE INTO xf_admin_permission_entry
				(user_id, admin_permission_id)
			SELECT user_id, 'payment'
			FROM xf_admin_permission_entry
			WHERE admin_permission_id = 'userUpgrade'
		");

		$this->executeUpgradeQuery("
			REPLACE INTO xf_admin_permission_entry
				(user_id, admin_permission_id)
			SELECT user_id, 'advertising'
			FROM xf_admin_permission_entry
			WHERE admin_permission_id = 'style'
		");

		$this->executeUpgradeQuery("
			REPLACE INTO xf_admin_permission_entry
				(user_id, admin_permission_id)
			SELECT user_id, 'widget'
			FROM xf_admin_permission_entry
			WHERE admin_permission_id = 'style'
		");

		$this->executeUpgradeQuery("
			REPLACE INTO xf_admin_permission_entry
				(user_id, admin_permission_id)
			SELECT user_id, 'navigation'
			FROM xf_admin_permission_entry
			WHERE admin_permission_id = 'style'
		");

		$registryClear = [
			'adminSearchTypes',
			'adminStyleModifiedDate',
			'adminStyleProperties',
			'bbCode',
			'bbCodeMedia',
			'boardTotals',
			'codeEventListeners',
			'defaultStyleProperties',
			'deferredRun',
			'disabledAddOns',
			'importSession',
			'moderationCounts',
			'nodeTypes',
			'notices',
			'simpleCache',
			'reportCounts',
			'routeFiltersIn',
			'routeFiltersOut',
			'routesAdmin',
			'routesPublic',
			'smilies',
			'threadPrefixes',
			'userBanners',
			'userFieldsInfo'
		];

		$this->executeUpgradeQuery("
			DELETE FROM xf_data_registry
			WHERE data_key IN (" . $this->db()->quote($registryClear) . ")
		");

		$this->insertPostUpgradeJob('upgradePostEmbedMetadataRebuild', 'XF:PostEmbedMetadata', ['types' => 'attachments']);
		$this->insertPostUpgradeJob('upgradeConversationEmbedMetadataRebuild', 'XF:ConversationEmbedMetadata', ['types' => 'attachments']);
		$this->insertPostUpgradeJob('upgradePostLikeIsCountedRebuild', 'XF:LikeIsCounted', ['type' => 'post']);
		$this->insertPostUpgradeJob('upgradeProfilePostLikeIsCountedRebuild', 'XF:LikeIsCounted', ['type' => 'profile_post']);
		$this->insertPostUpgradeJob('upgradeProfilePostCommentLikeIsCountedRebuild', 'XF:LikeIsCounted', ['type' => 'profile_post_comment']);
		$this->insertUpgradeJob('upgradeUserAvatarRebuild200', 'XF:Upgrade\\UserAvatar200', []);
	}
}