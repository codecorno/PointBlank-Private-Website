<?php

namespace XF\Admin\View\User;

use XF\Mvc\View;

class Find extends View
{
	public function renderJson()
	{
		$results = [];
		foreach ($this->params['users'] AS $user)
		{
			$avatarArgs = [$user, 'xxs', false, ['href' => '']];

			$results[] = [
				'id' => $user->username,
				'iconHtml' => $this->renderer->getTemplater()->func('avatar', $avatarArgs),
				'text' => $user->username,
				'q' => $this->params['q']
			];
		}

		return [
			'results' => $results,
			'q' => $this->params['q']
		];
	}
}