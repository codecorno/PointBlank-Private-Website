<?php

namespace XF\Install\Upgrade;

class Version1020470 extends AbstractUpgrade
{
	public function getVersionName()
	{
		return '1.2.4';
	}

	public function step1()
	{
		$this->executeUpgradeQuery("
			DELETE log
			FROM xf_admin_template_modification_log AS log
			LEFT JOIN xf_admin_template AS template ON (log.template_id = template.template_id)
			WHERE template.template_id IS NULL
		");

		$this->executeUpgradeQuery("
			DELETE log
			FROM xf_email_template_modification_log AS log
			LEFT JOIN xf_email_template AS template ON (log.template_id = template.template_id)
			WHERE template.template_id IS NULL
		");

		$this->executeUpgradeQuery("
			DELETE log
			FROM xf_template_modification_log AS log
			LEFT JOIN xf_template AS template ON (log.template_id = template.template_id)
			WHERE template.template_id IS NULL
		");
	}
}