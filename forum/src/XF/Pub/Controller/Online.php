<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\RouteMatch;

class Online extends AbstractController
{
	public function actionIndex()
	{
		if (!\XF::visitor()->canViewMemberList())
		{
			return $this->noPermission();
		}

		$page = $this->filterPage();
		$perPage = $this->options()->membersPerPage;

		/** @var \XF\Repository\SessionActivity $activityRepo */
		$activityRepo = $this->repository('XF:SessionActivity');

		$typeLimit = $this->filter('type', 'str');
		if (!$activityRepo->isTypeRestrictionValid($typeLimit))
		{
			$typeLimit = '';
		}

		/** @var \XF\Finder\SessionActivity $finder */
		$finder = $activityRepo->findForOnlineList($typeLimit);

		$linkParams = [];
		if ($typeLimit)
		{
			$linkParams['type'] = $typeLimit;
		}

		$total = $finder->total();
		$this->assertValidPage($page, $perPage, $total, 'online', $linkParams);
		$this->assertCanonicalUrl($this->buildLink('online'));

		$activities = $finder->limitByPage($page, $perPage)->fetch();
		$activityRepo->applyActivityDetails($activities);

		$viewParams = [
			'activities' => $activities,

			'typeLimit' => $typeLimit,

			'page' => $page,
			'perPage' => $perPage,
			'total' => $total,
			'linkParams' => $linkParams
		];
		return $this->view('XF:Online\Listing', 'online_list', $viewParams);
	}

	public function actionUserIp()
	{
		if (!\XF::visitor()->canViewIps())
		{
			return $this->noPermission();
		}

		$user = $this->em()->find('XF:User', $this->filter('user_id', 'uint'), ['Activity']);
		if (!$user || !$user->Activity)
		{
			return $this->error(\XF::phrase('no_ip_information_available'));
		}

		$viewParams = [
			'user' => $user,
			'activity' => $user->Activity
		];
		return $this->view('XF:Online\UserIp', 'online_user_ip', $viewParams);
	}

	public function actionGuestIp()
	{
		if (!\XF::visitor()->canViewIps())
		{
			return $this->noPermission();
		}

		$ip = preg_replace('#[^0-9a-f]#', '', $this->filter('ip', 'str'));

		try
		{
			$ip = \XF\Util\Ip::convertHexToBin($ip);
		}
		catch (\Exception $e)
		{
			$ip = false;
		}

		if (!$ip)
		{
			// likely given an invalid IP
			return $this->error(\XF::phrase('unexpected_error_occurred'));
		}

		$viewParams = [
			'ip' => $ip,
		];

		return $this->view('XF:Online\GuestIp', 'online_guest_ip', $viewParams);
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('viewing_list_of_online_members');
	}
}