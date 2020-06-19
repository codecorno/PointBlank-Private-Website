<?php

namespace XF\Import\Data;

class LinkForum extends AbstractNode
{
	public function getImportType()
	{
		return 'link_forum';
	}

	public function getEntityShortName()
	{
		return 'XF:LinkForum';
	}
}