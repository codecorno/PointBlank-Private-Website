<?php

namespace XF\Admin\Controller;

use XF\Entity\UserBan;
use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class Banning extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('ban');

		if (preg_match('/^users/i', $action))
		{
			$this->setSectionContext('bannedUsers');
		}
		else if (preg_match('/^emails/i', $action))
		{
			$this->setSectionContext('bannedEmails');
		}
		else if (preg_match('/^ips/i', $action))
		{
			$this->setSectionContext('bannedIps');
		}
		else if (preg_match('/^discouragedips/i', $action))
		{
			$this->setSectionContext('discouragedIps');
		}
	}

	public function actionUsers(ParameterBag $params)
	{
		if ($params->user_id)
		{
			return $this->rerouteController(__CLASS__, 'usersEdit', $params);
		}

		$page = $this->filterPage();
		$perPage = 20;

		$banningRepo = $this->getBanningRepo();
		$banFinder = $banningRepo->findUserBansForList()->limitByPage($page, $perPage);
		$total = $banFinder->total();

		$viewParams = [
			'userBans' => $banFinder->fetch(),

			'page' => $page,
			'perPage' => $perPage,
			'total' => $total
		];
		return $this->view('XF:Banning\User\Listing', 'ban_user_list', $viewParams);
	}

	public function userBanAddEdit(UserBan $userBan, $addName = '')
	{
		$viewParams = [
			'userBan' => $userBan,
			'addName' => $addName
		];
		return $this->view('XF:Feed\Edit', 'ban_user_edit', $viewParams);
	}

	public function actionUsersEdit(ParameterBag $params)
	{
		$userBan = $this->assertUserBanExists($params['user_id']);
		return $this->userBanAddEdit($userBan);
	}

	public function actionUsersAdd(ParameterBag $params)
	{
		if ($params['user_id'])
		{
			$user = $this->assertRecordExists('XF:User', $params['user_id']);
			$addName = $user->username;
		}
		else
		{
			$addName = '';
		}

		$userBan = $this->em()->create('XF:UserBan');
		return $this->userBanAddEdit($userBan, $addName);
	}

	protected function userBanSaveProcess(UserBan $userBan)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'username' => 'str',
			'ban_length' => 'str',
			'end_date' => 'datetime',
			'user_reason' => 'str'
		]);

		$user = $userBan->User;
		if (!$user)
		{
			$user = $this->finder('XF:User')->where('username', $input['username'])->fetchOne();
			if (!$user)
			{
				throw $this->exception($this->error(\XF::phrase('requested_user_not_found')));
			}
		}

		$form->apply(function(FormAction $form) use ($input, $user)
		{
			if ($input['ban_length'] == 'permanent')
			{
				$input['end_date'] = 0;
			}

			$banningRepo = $this->getBanningRepo();
			if (!$banningRepo->banUser($user, $input['end_date'], $input['user_reason'], $error))
			{
				$form->logError($error);
			}
		});

		return $form;
	}

	public function actionUsersSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['user_id'])
		{
			$userBan = $this->assertUserBanExists($params['user_id']);
		}
		else
		{
			$userBan = $this->em()->create('XF:UserBan');
		}

		$this->userBanSaveProcess($userBan)->run();

		return $this->redirect($this->buildLink('banning/users'));
	}

	public function actionUsersLift(ParameterBag $params)
	{
		$userBan = $this->assertUserBanExists($params->user_id);

		if ($this->isPost())
		{
			$userBan->delete();
			return $this->redirect($this->buildLink('banning/users'));
		}
		else
		{
			$viewParams = [
				'userBan' => $userBan
			];
			return $this->view('XF:Banning\User\Lift', 'ban_user_lift', $viewParams);
		}
	}

	public function actionEmails()
	{
		$page = $this->filterPage();
		$perPage = 20;

		$order = $this->filter('order', 'str', 'create_date');
		$direction = $this->filter('direction', 'str', 'desc');

		$orderFields = [
			[$order, $direction]
		];
		if ($order !== 'banned_email')
		{
			// If not already set, add this as a secondary sort because
			// majority of fields may be blank (especially legacy data)
			$orderFields[] = ['banned_email', 'asc'];
		}

		$emailBanFinder = $this->getBanningRepo()->findEmailBans()
			->with('User')
			->order($orderFields)
			->limitByPage($page, $perPage);
		$total = $emailBanFinder->total();

		$this->assertValidPage($page, $perPage, $total, 'banning/emails');

		$viewParams = [
			'emailBans' => $emailBanFinder->fetch(),
			'page' => $page,
			'perPage' => $perPage,
			'total' => $total,
			'order' => $order,
			'direction' => $direction,
			'newEmail' => $this->em()->create('XF:BanEmail')
		];
		return $this->view('XF:Banning\Email\Listing', 'ban_email_list', $viewParams);
	}

	public function actionEmailsAdd()
	{
		$this->assertPostOnly();

		$this->getBanningRepo()->banEmail(
			$this->filter('email', 'str'),
			$this->filter('reason', 'str')
		);
		return $this->redirect($this->buildLink('banning/emails'));
	}

	public function actionEmailsDelete()
	{
		$this->assertPostOnly();

		$deletes = $this->filter('delete', 'array-str');

		$emailBans = $this->em()->findByIds('XF:BanEmail', $deletes);
		foreach ($emailBans AS $emailBan)
		{
			$emailBan->delete();
		}

		return $this->redirect($this->buildLink('banning/emails'));
	}

	public function actionEmailsExport()
	{
		$bannedEmails = $this->getBanningRepo()->findEmailBans();
		return $this->plugin('XF:Xml')->actionExport($bannedEmails, 'XF:Banning\Emails\Export');
	}

	public function actionEmailsImport()
	{
		return $this->plugin('XF:Xml')->actionImport('banning/emails', 'banned_emails', 'XF:Banning\Emails\Import');
	}

	public function actionIps()
	{
		$page = $this->filterPage();
		$perPage = 20;

		$order = $this->filter('order', 'str', 'create_date');
		$direction = $this->filter('direction', 'str', 'desc');

		$orderFields = [
			[$order, $direction]
		];
		if ($order !== 'start_range')
		{
			// If not already set, add this as a secondary sort because
			// majority of fields may be blank (especially legacy data)
			$orderFields[] = ['start_range', 'asc'];
		}

		$ipBanFinder = $this->getBanningRepo()->findIpBans()
			->with('User')
			->order($orderFields)
			->limitByPage($page, $perPage);
		$total = $ipBanFinder->total();

		$this->assertValidPage($page, $perPage, $total, 'banning/ips');

		$viewParams = [
			'ipBans' => $ipBanFinder->fetch(),
			'ip' => $this->filter('ip', 'str'),
			'page' => $page,
			'perPage' => $perPage,
			'total' => $total,
			'order' => $order,
			'direction' => $direction,
			'newIp' => $this->em()->create('XF:IpMatch')
		];
		return $this->view('XF:Banning\Ip\Listing', 'ban_ip_list', $viewParams);
	}

	public function actionIpsAdd()
	{
		$ip = $this->filter('ip', 'str');

		if ($this->isPost())
		{
			$this->getBanningRepo()->banIp(
				$this->filter('ip', 'str'),
				$this->filter('reason', 'str')
			);
			return $this->redirect($this->buildLink('banning/ips'));
		}
		else
		{
			$ipEntity = $this->em()->create('XF:IpMatch');
			$ipEntity->set('ip', $ip);

			$viewParams = [
				'ip' => $ipEntity
			];
			return $this->view('XF:Banning\Ip\Add', 'ban_ip', $viewParams);
		}
	}

	public function actionIpsDelete()
	{
		$this->assertPostOnly();

		$deletes = $this->filter('delete', 'array-str');

		$ipBans = $this->getBanningRepo()->findIpBans()->where('ip', $deletes);
		foreach ($ipBans->fetch() AS $ipBan)
		{
			$ipBan->delete();
		}

		return $this->redirect($this->buildLink('banning/ips'));
	}

	public function actionIpsExport()
	{
		$bannedIps = $this->getBanningRepo()->findIpBans();
		return $this->plugin('XF:Xml')->actionExport($bannedIps, 'XF:Banning\Ips\Export');
	}

	public function actionIpsImport()
	{
		return $this->plugin('XF:Xml')->actionImport('banning/ips', 'banned_ips', 'XF:Banning\Ips\Import');
	}

	public function actionDiscouragedIps()
	{
		$page = $this->filterPage();
		$perPage = 20;

		$order = $this->filter('order', 'str', 'create_date');
		$direction = $this->filter('direction', 'str', 'desc');

		$orderFields = [
			[$order, $direction]
		];
		if ($order !== 'start_range')
		{
			// If not already set, add this as a secondary sort because
			// majority of fields may be blank (especially legacy data)
			$orderFields[] = ['start_range', 'asc'];
		}

		$discouragedFinder = $this->getBanningRepo()->findDiscouragedIps()
			->with('User')
			->order($orderFields)
			->limitByPage($page, $perPage);

		$total = $discouragedFinder->total();

		$this->assertValidPage($page, $perPage, $total, 'banning/discouraged-ips');

		$viewParams = [
			'discouragedIps' => $discouragedFinder->fetch(),
			'ip' => $this->filter('ip', 'str'),
			'order' => $order,
			'direction' => $direction,
			'newIp' => $this->em()->create('XF:IpMatch')
		];
		return $this->view('XF:Banning\DiscouragedIp\Listing', 'discouraged_ip_list', $viewParams);
	}

	public function actionDiscouragedIpsAdd()
	{
		$ip = $this->filter('ip', 'str');

		if ($this->isPost())
		{
			$this->getBanningRepo()->discourageIp(
				$this->filter('ip', 'str'),
				$this->filter('reason', 'str')
			);
			return $this->redirect($this->buildLink('banning/discouraged-ips'));
		}
		else
		{
			$viewParams = [
				'ip' => $ip
			];
			return $this->view('XF:Banning\DiscouragedIp\Add', 'discourage_ip', $viewParams);
		}
	}

	public function actionDiscouragedIpsDelete()
	{
		$this->assertPostOnly();

		$deletes = $this->filter('delete', 'array-str');

		$discouragedIps = $this->getBanningRepo()->findDiscouragedIps()->where('ip', $deletes);
		foreach ($discouragedIps->fetch() AS $discouragedIp)
		{
			$discouragedIp->delete();
		}

		return $this->redirect($this->buildLink('banning/discouraged-ips'));
	}

	public function actionDiscouragedIpsExport()
	{
		$discouragedIps = $this->getBanningRepo()->findDiscouragedIps();
		return $this->plugin('XF:Xml')->actionExport($discouragedIps, 'XF:Banning\DiscouragedIps\Export');
	}

	public function actionDiscouragedIpsImport()
	{
		return $this->plugin('XF:Xml')->actionImport('banning/discouraged-ips', 'discouraged_ips', 'XF:Banning\DiscouragedIps\Import');
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return UserBan
	 */
	protected function assertUserBanExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:UserBan', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Banning
	 */
	protected function getBanningRepo()
	{
		return $this->repository('XF:Banning');
	}
}