<?php

namespace XF\Import\Data;

class Admin extends AbstractEntityData
{
	public function getImportType()
	{
		return 'admin';
	}

	public function getEntityShortName()
	{
		return 'XF:Admin';
	}
}