<?php

namespace XF\Import\Data;

class UserUpgradeActive extends AbstractEmulatedData
{
	public function getImportType()
	{
		return 'user_upgrade_active';
	}

	public function getEntityShortName()
	{
		return 'XF:UserUpgradeActive';
	}
}