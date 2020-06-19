<?php

namespace XF\Install\Upgrade;

class Version2000370 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '2.0.3';
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

	public function step2()
	{
		if (!$this->db()->fetchOne('SELECT COUNT(*) FROM xf_user_title_ladder'))
		{
			// if all user title ladder records are deleted the cache isn't rebuilt
			// so if we detect there are no ladder records, rebuild the cache
			\XF::repository('XF:UserTitleLadder')->rebuildLadderCache();
		}
	}
}