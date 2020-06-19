<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class SpamCleaner extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		if (!\XF::visitor()->canCleanSpam())
		{
			throw $this->exception($this->noPermission());
		}
	}

	protected function filterSpamCleanActions()
	{
		return $this->filter([
			'action_threads' => 'bool',
			'delete_messages' => 'bool',
			'delete_conversations' => 'bool',
			'ban_user' => 'bool',
			'check_ips' => 'bool'
		]);
	}

	public function actionIndex(ParameterBag $params)
	{
		/** @var \XF\Entity\User $user */
		$user = $this->assertRecordExists('XF:User', $params->user_id);
		if (!$user->isPossibleSpammer($error))
		{
			return $this->error($error);
		}

		$this->assertCanonicalUrl($this->buildLink('spam-cleaner', $user));

		$canViewIps = \XF::visitor()->canViewIps();
		$noRedirect = $this->filter('no_redirect', 'bool');

		if ($this->isPost())
		{
			$actions = $this->filterSpamCleanActions();

			$cleaner = $this->app->spam()->cleaner($user);
			$cleaner->cleanUp($actions);

			if (!$cleaner->finalize())
			{
				return $this->error($cleaner->getErrors());
			}

			if ($actions['check_ips'] && $canViewIps)
			{
				$shared = $user->getSharedIpUsers($this->app()->options()->sharedIpsCheckLimit);

				$viewParams = [
					'spammer' => $user,
					'shared' => $shared,
					'noRedirect' => $noRedirect
				];
				return $this->view('XF:SpamCleaner\CheckIps', 'spam_cleaner_check_ips', $viewParams);
			}
			else
			{
				return $this->redirect($this->getDynamicRedirect(), \XF::phrase('spam_deleted'));
			}
		}
		else
		{
			if ($ipId = $this->filter('ip_id', 'uint'))
			{
				$ip = $this->finder('XF:Ip')->where('ip_id', $ipId)->fetchOne();
				if ($ip)
				{
					$contentIp = $ip->ip;
				}
				else
				{
					$contentIp = '';
				}
			}
			else
			{
				$contentIp = '';
			}

			$viewParams = [
				'user' => $user,
				'canViewIps' => $canViewIps,
				'contentIp' => $contentIp,
				'noRedirect' => $noRedirect
			];
			return $this->view('XF:SpamCleaner\Cleaner', 'spam_cleaner', $viewParams);
		}
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('performing_moderation_duties');
	}
}