<?php

namespace XF\Import\Data;

class UserUpgrade extends AbstractEmulatedData
{
	public function getImportType()
	{
		return 'user_upgrade';
	}

	public function getEntityShortName()
	{
		return 'XF:UserUpgrade';
	}
}