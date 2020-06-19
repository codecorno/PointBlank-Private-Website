<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\AbstractReply;

class AccountConfirmation extends AbstractController
{
	public function actionEmail(ParameterBag $params)
	{
		/** @var \XF\Entity\User $user */
		$user = $this->assertRecordExists('XF:User', $params->user_id);

		/** @var \XF\Service\User\EmailConfirmation $emailConfirmation */
		$emailConfirmation = $this->service('XF:User\EmailConfirmation', $user);

		if (!$emailConfirmation->canTriggerConfirmation())
		{
			return $this->redirect($this->buildLink('index'));
		}

		$confirmationKey = $this->filter('c', 'str');
		if (!$emailConfirmation->matchesKey($confirmationKey))
		{
			return $this->error(\XF::phrase('your_email_could_not_be_confirmed_use_resend'));
		}

		$emailConfirmation->emailConfirmed();

		return $this->view('XF:Register\Confirm', 'register_confirm');
	}

	public function actionResend()
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return $this->redirect($this->buildLink('index'), '');
		}

		return $this->plugin('XF:EmailConfirmation')->actionResend(
			$visitor,
			$this->buildLink('account-confirmation/resend'),
			['checkCaptcha' => true]
		);
	}

	public function updateSessionActivity($action, ParameterBag $params, AbstractReply &$reply) {}

	public function assertViewingPermissions($action) {}
	public function assertCorrectVersion($action) {}
	public function assertBoardActive($action) {}
	public function assertTfaRequirement($action) {}
	public function assertPolicyAcceptance($action) {}
}