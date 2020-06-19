<?php

namespace XF\Import\Data;

class Feed extends AbstractEmulatedData
{
	public function getImportType()
	{
		return 'feed';
	}

	public function getEntityShortName()
	{
		return 'XF:Feed';
	}
}