<?php

namespace XF\ControllerPlugin;

use XF\Mvc\ParameterBag;

class Error extends AbstractPlugin
{
	public function actionDispatchError(ParameterBag $params)
	{
		$code = $params->get('code', null) ?: 'unknown';
		$controller = $params->get('controller', null) ?: '-';
		$action = $params->get('action', null) ?: '-';

		if (\XF::$debugMode)
		{
			return $this->error(
				\XF::phrase('requested_page_not_found')
					. " (Code: $code, controller: $controller, action: $action)",
				404
			);
		}
		else
		{
			return $this->error(\XF::phrase('requested_page_not_found'), 404);
		}
	}

	public function actionNotFound($message = null)
	{
		if (!$message)
		{
			$message = \XF::phrase('requested_page_not_found');
		}

		return $this->error($message, 404);
	}

	public function actionAddOnUpgrade()
	{
		return $this->error(\XF::phrase('area_temporarily_unavailable_addon_upgrade_try_again'), $this->app->config('serviceUnavailableCode'));
	}

	public function actionNoPermission($message)
	{
		if ($message instanceof \XF\Phrase
			&& preg_match('/_not_found$/', $message->getName())
		)
		{
			// phrase coming from an error that looks like a 404 error so trigger it as such
			return $this->notFound($message);
		}

		if (!\XF::visitor()->user_id && $this->app instanceof \XF\Pub\App)
		{
			return $this->actionRegistrationRequired();
		}
		else
		{
			if (!$message)
			{
				$message = \XF::phrase('do_not_have_permission');
			}

			return $this->error($message, 403);
		}
	}

	public function actionRegistrationRequired()
	{
		$view = $this->view('XF:Error\RegistrationRequired', 'login', [
			'error' => \XF::phrase('login_required'),
			'providers' => \XF::repository('XF:ConnectedAccount')->getUsableProviders(false)
		]);
		$view->setResponseCode(403);

		return $view;
	}

	public function actionBannedIp()
	{
		return $this->error(\XF::phrase('your_ip_address_has_been_banned'), 403);
	}

	public function actionBanned()
	{
		$visitor = \XF::visitor();
		if (!$visitor->Ban)
		{
			return $this->noPermission();
		}
		else
		{
			$banEndDate = $visitor->Ban['end_date'];

			if ($visitor->Ban['triggered'] && !$banEndDate)
			{
				/** @var \XF\Repository\Warning $warningRepo */
				$warningRepo = $this->repository('XF:Warning');

				$minUnbanDate = $warningRepo->getMinimumUnbanDate($visitor->user_id);
				if ($minUnbanDate)
				{
					$banEndDate = $minUnbanDate;
				}
			}

			if ($visitor->Ban['user_reason'])
			{
				$message = \XF::phrase('you_have_been_banned_for_following_reason_x', ['reason' => $visitor->Ban['user_reason']]);
			}
			else
			{
				$message = \XF::phrase('you_have_been_banned');
			}
			if ($banEndDate > time())
			{
				$message .= ' ' . \XF::phrase('your_ban_will_be_lifted_on_x', ['date' => \XF::language()->dateTime($banEndDate)]);
			}

			return $this->error($message, 403);
		}
	}

	public function actionRejected()
	{
		$visitor = \XF::visitor();
		if (!$visitor->Reject)
		{
			return $this->noPermission();
		}
		else
		{
			if ($visitor->Reject['reject_reason'])
			{
				$message = \XF::phrase('your_account_has_been_rejected_for_following_reason_x', ['reason' => $visitor->Reject['reject_reason']]);
			}
			else
			{
				$message = \XF::phrase('your_account_has_been_rejected');
			}

			return $this->error($message, 403);
		}
	}

	public function actionDisabled()
	{
		$visitor = \XF::visitor();
		if ($visitor->user_state != 'disabled')
		{
			return $this->noPermission();
		}
		else
		{
			if ($visitor->canUseContactForm())
			{
				$link = null;

				$contactUrl = $this->options()->contactUrl;
				if ($contactUrl['type'] == 'default')
				{
					$link = $this->buildLink('misc/contact');
				}
				else if ($contactUrl['type'] == 'custom')
				{
					$link = $contactUrl['custom'];
				}

				if ($link)
				{
					$message = \XF::phrase('your_account_has_been_disabled_please_contact_us', [
						'link' => $link
					]);

					return $this->error($message, 403);
				}
			}

			return $this->error(\XF::phrase('your_account_has_been_disabled'), 403);
		}
	}

	public function actionException($exception, $showDetails = null)
	{
		if ($showDetails === null)
		{
			$showDetails = (\XF::$debugMode || \XF::visitor()->is_admin);
		}

		if ($showDetails)
		{
			$reply = $this->view('XF:Error\Server', '', [
				'exception' => $exception
			]);
		}
		else
		{
			$reply = $this->error(\XF::phrase('server_error_occurred'));
		}

		$reply->setResponseCode(500);
		return $reply;
	}
}