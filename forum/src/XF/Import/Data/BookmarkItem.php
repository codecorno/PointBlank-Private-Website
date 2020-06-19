<?php

namespace XF\Import\Data;

class BookmarkItem extends AbstractEmulatedData
{
	public function getImportType()
	{
		return 'bookmark';
	}

	protected function getEntityShortName()
	{
		return 'XF:BookmarkItem';
	}
}