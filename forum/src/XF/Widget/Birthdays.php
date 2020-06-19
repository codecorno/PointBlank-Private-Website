<?php

namespace XF\Widget;

class Birthdays extends AbstractWidget
{
	protected $defaultOptions = [
		'limit' => 12
	];

	public function render()
	{
		if (!\XF::visitor()->canViewMemberList())
		{
			return '';
		}

		$userFinder = $this->finder('XF:User')
			->isBirthday()
			->isRecentlyActive(365)
			->isValidUser()
			->order('username');

		if ($this->options['limit'])
		{
			$userFinder->limit($this->options['limit']);
		}

		$viewParams = [
			'users' => $userFinder->fetch()
		];
		return $this->renderer('widget_birthdays', $viewParams);
	}

	public function verifyOptions(\XF\Http\Request $request, array &$options, &$error = null)
	{
		$options = $request->filter([
			'limit' => 'uint'
		]);
		return true;
	}
}