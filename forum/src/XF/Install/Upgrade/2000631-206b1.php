<?php

namespace XF\Install\Upgrade;

use XF\Db\Schema\Alter;

class Version2000631 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.0.6 Beta 1';
	}

	public function step1()
	{
		$db = $this->db();

		$privacyUrl = $db->fetchOne('
			SELECT option_value
			FROM xf_option
			WHERE option_id = ?
		', 'privacyPolicyUrl');

		if (is_array(@json_decode($privacyUrl, true)))
		{
			// likely already had the changes when upgrading to XF 1.5.20
			return;
		}

		$newValue = [
			'type' => 'default',
			'custom' => false
		];

		if ($privacyUrl)
		{
			$newValue['type'] = 'custom';
			$newValue['custom'] = $privacyUrl;
		}

		$this->executeUpgradeQuery('
			UPDATE xf_option
			SET option_value = ?
			WHERE option_id = ?
		', [json_encode($newValue), 'privacyPolicyUrl']);
	}

	public function step2()
	{
		$sm = $this->schemaManager();

		if (!$sm->columnExists('xf_user', 'privacy_policy_accepted'))
		{
			$sm->alterTable('xf_user', function(Alter $table)
			{
				$table->addColumn('privacy_policy_accepted', 'int')->setDefault(0);
			});
			$this->executeUpgradeQuery("
				UPDATE xf_user
				SET privacy_policy_accepted = register_date
			");
		}
		if (!$sm->columnExists('xf_user', 'terms_accepted'))
		{
			$sm->alterTable('xf_user', function(Alter $table)
			{
				$table->addColumn('terms_accepted', 'int')->setDefault(0);
			});
			$this->executeUpgradeQuery("
				UPDATE xf_user
				SET terms_accepted = register_date
			");
		}
	}

	public function step3()
	{
		$sm = $this->schemaManager();

		if (!$sm->columnExists('xf_change_log', 'protected'))
		{
			$sm->alterTable('xf_change_log', function(Alter $table)
			{
				$table->addColumn('protected', 'tinyint')->setDefault(0);
			});

			// protect any existing receive_admin_email logs
			$this->executeUpgradeQuery("
				UPDATE xf_change_log
				SET protected = 1
				WHERE content_type = 'user'
				AND field = 'receive_admin_email'
			");
		}
	}

	public function step4()
	{
		$dupePage = $this->db()->fetchRow("
			SELECT *
			FROM xf_help_page 
			WHERE page_id = 'privacy_policy'
			OR page_name = 'privacy-policy'
		");

		if (!$dupePage)
		{
			return;
		}

		$updates = [
			'active' => 0
		];

		if ($dupePage['page_id'] == 'privacy_policy')
		{
			$updates['page_id'] = 'privacy_policy_old';
		}
		if ($dupePage['page_name'] == 'privacy-policy')
		{
			$updates['page_name'] = 'privacy-policy-old';
		}

		$this->db()->update('xf_help_page', $updates, 'page_id = ?', $dupePage['page_id']);
	}

	public function step5()
	{
		$optionValue = $this->db()->fetchOne('SELECT option_value FROM xf_option WHERE option_id = ?', 'registrationSetup');
		$optionValue = json_decode($optionValue, true);
		$optionValue['requireEmailChoice'] = false;

		$this->db()->update('xf_option', [
			'option_value' => json_encode($optionValue)
		], 'option_id = ?', 'registrationSetup');
	}
}