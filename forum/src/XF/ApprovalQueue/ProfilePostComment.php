<?php

namespace XF\ApprovalQueue;

use XF\Mvc\Entity\Entity;

class ProfilePostComment extends AbstractHandler
{
	protected function canActionContent(Entity $content, &$error = null)
	{
		/** @var $content \XF\Entity\ProfilePostComment */
		return $content->canApproveUnapprove($error);
	}

	public function actionApprove(\XF\Entity\ProfilePostComment $comment)
	{
		/** @var \XF\Service\ProfilePostComment\Approver $approver */
		$approver = \XF::service('XF:ProfilePostComment\Approver', $comment);
		$approver->approve();
	}

	public function actionDelete(\XF\Entity\ProfilePostComment $comment)
	{
		$this->quickUpdate($comment, 'message_state', 'deleted');
	}

	public function actionSpamClean(\XF\Entity\ProfilePostComment $comment)
	{
		if (!$comment->User)
		{
			return;
		}

		$this->_spamCleanInternal($comment->User);
	}
}