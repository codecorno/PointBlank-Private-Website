<?php

namespace XF\Install\Upgrade;

use XF\Db\Schema\Alter;
use XF\Util\Arr;

class Version2000270 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.0.2';
	}

	public function step1()
	{
		$this->schemaManager()->alterTable('xf_payment_provider_log', function(Alter $table)
		{
			$table->addKey('purchase_request_key');
		});
	}

	public function step2()
	{
		// if a paypal payment profile exists and was used before the upgrade to 2.0 then
		// we need to ensure it is still marked as legacy as this could have been lost
		// due to that value not being maintained on edit.

		$db = $this->db();

		$paymentProfiles = $db->fetchAll('
			SELECT *
			FROM xf_payment_profile
			WHERE provider_id = \'paypal\'
		');

		if (!$paymentProfiles)
		{
			return;
		}

		$haveLegacy = false;
		foreach ($paymentProfiles AS $paymentProfile)
		{
			$options = @json_decode($paymentProfile['options'], true);
			if ($options && !empty($options['legacy']))
			{
				$haveLegacy = true;
				break;
			}
		}
		if ($haveLegacy)
		{
			return;
		}

		$xf2upgradeDate = $db->fetchOne('
			SELECT completion_date
			FROM xf_upgrade_log
			WHERE version_id = 2000070
			AND log_type = \'upgrade\'
		');

		if (!$xf2upgradeDate)
		{
			return;
		}

		$paymentLog = $db->fetchRow('
			SELECT *
			FROM xf_payment_provider_log
			WHERE provider_id = \'paypal\'
			AND log_type = \'payment\'
			AND log_date < ?
			ORDER BY log_date DESC
			LIMIT 1
		', $xf2upgradeDate);

		if (!$paymentLog)
		{
			return;
		}

		$logDetails = @unserialize($paymentLog['log_details']);

		if (!isset($logDetails['receiver_email']) || !isset($logDetails['business']))
		{
			return;
		}

		$receiverEmail = $logDetails['receiver_email'];
		$business = $logDetails['business'];

		// we know we have no legacy entries by this point
		foreach ($paymentProfiles AS $paymentProfile)
		{
			$options = @json_decode($paymentProfile['options'], true);
			if (!$options)
			{
				continue;
			}

			$matched = false;

			try
			{
				$accounts = Arr::stringToArray($options['alternate_accounts'], '#\r?\n#');
				$accounts[] = $options['primary_account'];

				foreach ($accounts AS $account)
				{
					$account = trim(strtolower($account));
					if ($account && ($business == $account || $receiverEmail == $account))
					{
						// account matches business/receiver email and this profile is not marked as legacy so update

						$options['legacy'] = true;
						$db->query('
							UPDATE xf_payment_profile
							SET options = ?
							WHERE payment_profile_id = ?
						', [json_encode($options), $paymentProfile['payment_profile_id']]);

						// there should only be a single legacy profile so we can finish.
						$matched = true;
						break;
					}
				}
			}
			catch (\Exception $e)
			{
				// swallow errors so as to not block the upgrade but log and we can deal with tis if we have to
				\XF::logException($e, false, '2.0.2 upgrade error:', true);
			}

			if ($matched)
			{
				break;
			}
		}
	}

	public function step3()
	{
		// adding this index prevents a regular full table scan on the conversation_master table
		$this->schemaManager()->alterTable('xf_conversation_master', function(Alter $table)
		{
			$table->addKey('start_date');
		});
	}

	public function step4()
	{
		// Prior to 2.0.2, the XF 1 to XF 2 upgrade ignored thread prefixes where prefix_css = ''
		// but what it should have done is updated them to prefix_css = 'label label--hidden'
		// so this fixes that.

		if ($this->db()->update('xf_thread_prefix', ['css_class' => 'label label--hidden'], 'css_class = ?', ''))
		{
			/** @var \XF\Repository\ThreadPrefix $threadPrefixRepo */
			$threadPrefixRepo = $this->app->repository('XF:ThreadPrefix');

			$threadPrefixRepo->rebuildPrefixCache();
		}
	}
}