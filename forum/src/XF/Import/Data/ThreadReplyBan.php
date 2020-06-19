<?php

namespace XF\Import\Data;

class ThreadReplyBan extends AbstractEmulatedData
{
	public function getImportType()
	{
		return 'thread_reply_ban';
	}

	public function getEntityShortName()
	{
		return 'XF:ThreadReplyBan';
	}
}