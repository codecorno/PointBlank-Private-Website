<?php

namespace XF\ApprovalQueue;

use XF\Mvc\Entity\Entity;

class ProfilePost extends AbstractHandler
{
	protected function canActionContent(Entity $content, &$error = null)
	{
		/** @var $content \XF\Entity\ProfilePost */
		return $content->canApproveUnapprove($error);
	}

	public function actionApprove(\XF\Entity\ProfilePost $profilePost)
	{
		/** @var \XF\Service\ProfilePost\Approver $approver */
		$approver = \XF::service('XF:ProfilePost\Approver', $profilePost);
		$approver->approve();
	}

	public function actionDelete(\XF\Entity\ProfilePost $profilePost)
	{
		$this->quickUpdate($profilePost, 'message_state', 'deleted');
	}

	public function actionSpamClean(\XF\Entity\ProfilePost $profilePost)
	{
		if (!$profilePost->User)
		{
			return;
		}

		$this->_spamCleanInternal($profilePost->User);
	}

	public function getEntityWith()
	{
		return ['ProfileUser', 'ProfileUser.Privacy'];
	}
}