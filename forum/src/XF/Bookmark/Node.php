<?php

namespace XF\Bookmark;

use XF\Mvc\Entity\Entity;

class Node extends AbstractHandler
{
	public function getContentTitle(Entity $content)
	{
		return $content->Data->title;
	}

	public function getContentRoute(Entity $content)
	{
		/** @var \XF\Entity\Node $content */
		return $content->getRoute('public');
	}

	public function getCustomIconTemplateName()
	{
		return 'public:bookmark_item_custom_icon_node';
	}
}