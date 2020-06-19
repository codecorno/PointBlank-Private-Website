<?php

namespace XF\Install\Upgrade;

class Version2000053 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.0.0 Release Candidate 3';
	}

	public function step1()
	{
		$this->db()->update('xf_user_authenticate', [
			'scheme_class' => 'XF:IpsForums3x'
		], 'scheme_class = ?', 'XF:IPBoard');

		$this->db()->update('xf_user_authenticate', [
			'scheme_class' => 'XF:IpsForums4x'
		], 'scheme_class = ?', 'XF:IPBoard40x');
	}
}