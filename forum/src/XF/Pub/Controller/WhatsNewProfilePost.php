<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class WhatsNewProfilePost extends AbstractWhatsNewFindType
{
	protected function getContentType()
	{
		return 'profile_post';
	}
}