<?php

namespace XF\Install\Upgrade;

class Version2001070 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.0.10';
	}

	public function step1()
	{
		$this->executeUpgradeQuery("
			UPDATE xf_job
			SET manual_execute = 0
			WHERE unique_key = 'xfCollectStats'
		");
	}

	public function step2()
	{
		$this->renameAuthSchemeClass(
			['XenForo_Authentication_Core', 'XenForo_Authentication_Default'], 'XF:Core'
		);
	}

	public function step3()
	{
		$this->renameAuthSchemeClass(
			'XenForo_Authentication_Core12', 'XF:Core12'
		);
	}

	public function step4()
	{
		$this->renameAuthSchemeClass(
			'XenForo_Authentication_IPBoard', 'XF:IpsForums3x'
		);
	}

	public function step5()
	{
		$this->renameAuthSchemeClass(
			'XenForo_Authentication_IPBoard40x', 'XF:IpsForums4x'
		);
	}

	public function step6()
	{
		$this->renameAuthSchemeClass(
			'XenForo_Authentication_MyBb', 'XF:MyBb'
		);
	}

	public function step7()
	{
		$this->renameAuthSchemeClass(
			'XenForo_Authentication_NoPassword', 'XF:NoPassword'
		);
	}

	public function step8()
	{
		$this->renameAuthSchemeClass(
			'XenForo_Authentication_PhpBb3', 'XF:PhpBb3'
		);
	}

	public function step9()
	{
		$this->renameAuthSchemeClass(
			'XenForo_Authentication_SMF', 'XF:SMF'
		);
	}

	public function step10()
	{
		$this->renameAuthSchemeClass(
			'XenForo_Authentication_vBulletin', 'XF:vBulletin'
		);
	}

	protected function renameAuthSchemeClass($old, $new)
	{
		if (!is_array($old))
		{
			$old = [$old];
		}
		$old = $this->db()->quote($old);

		$this->executeUpgradeQuery("
			UPDATE xf_user_authenticate
			SET scheme_class = ?
			WHERE scheme_class IN($old)
		", $new);
	}
}