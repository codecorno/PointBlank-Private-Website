<?php

namespace XF\Widget;

class MembersOnline extends AbstractWidget
{
	protected $defaultOptions = [
		'limit' => 50,
		'staffOnline' => true,
		'staffQuery' => false,
		'followedOnline' => true
	];

	public function render()
	{
		if (!\XF::visitor()->canViewMemberList())
		{
			return '';
		}

		/** @var \XF\Repository\SessionActivity $activityRepo */
		$activityRepo = $this->repository('XF:SessionActivity');

		$viewParams = [
			'online' => $activityRepo->getOnlineStatsBlockData(true, $this->options['limit'], $this->options['staffQuery'])
		];
		return $this->renderer('widget_members_online', $viewParams);
	}

	public function verifyOptions(\XF\Http\Request $request, array &$options, &$error = null)
	{
		$options = $request->filter([
			'limit' => 'uint',
			'staffOnline' => 'bool',
			'staffQuery' => 'bool',
			'followedOnline' => 'bool',
		]);
		return true;
	}
}