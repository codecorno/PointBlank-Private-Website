<?php

namespace XF\Pub\Controller;

class WhatsNewPost extends AbstractWhatsNewFindType
{
	protected function getContentType()
	{
		// This returns threads, so we attach to the thread content type. However, as it's really individual posts
		// that generally bump things up, we refer to this in the interface as "new posts".
		return 'thread';
	}
}