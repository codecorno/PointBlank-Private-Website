<?php

namespace XF\Pub\View\Account;

use XF\Util\File;
use XF\Util\Xml;

class AvatarUpdate extends \XF\Mvc\View
{
	public function renderJson()
	{
		$visitor = \XF::visitor();

		$templater = $this->renderer->getTemplater();

		$avatars = [];
		$avatarCodes = array_keys(\XF::app()->container('avatarSizeMap'));
		foreach ($avatarCodes AS $code)
		{
			$avatars[$code] = $templater->func('avatar', [$visitor, $code]);
		}

		return [
			'userId' => $visitor->user_id,
			'gravatar' => $visitor->gravatar,
			'gravatarUrl' => $visitor->getGravatarUrl('m'),
			'avatars' => $avatars,
			'defaultAvatars' => ($visitor->getAvatarUrl('s') === null),
			'cropX' => $visitor->Profile->avatar_crop_x,
			'cropY' => $visitor->Profile->avatar_crop_y
		];
	}
}