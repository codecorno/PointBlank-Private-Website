<?php

namespace XF\Widget;

class NewestMembers extends AbstractWidget
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
			->isValidUser()
			->order('register_date', 'DESC')
			->limit($this->options['limit']);

		$viewParams = [
			'users' => $userFinder->fetch()
		];
		return $this->renderer('widget_newest_members', $viewParams);
	}

	public function verifyOptions(\XF\Http\Request $request, array &$options, &$error = null)
	{
		$options = $request->filter([
			'limit' => 'uint'
		]);
		if ($options['limit'] < 1)
		{
			$options['limit'] = 1;
		}

		return true;
	}
}