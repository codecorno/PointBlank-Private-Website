<?php

namespace XF\ApprovalQueue;

use XF\Mvc\Entity\Entity;

class Thread extends AbstractHandler
{
	protected function canActionContent(Entity $content, &$error = null)
	{
		/** @var $content \XF\Entity\Thread */
		return $content->canApproveUnapprove($error);
	}

	public function getEntityWith()
	{
		$visitor = \XF::visitor();

		return ['Forum', 'Forum.Node.Permissions|' . $visitor->permission_combination_id, 'FirstPost', 'User'];
	}

	public function actionApprove(\XF\Entity\Thread $thread)
	{
		/** @var \XF\Service\Thread\Approver $approver */
		$approver = \XF::service('XF:Thread\Approver', $thread);
		$approver->setNotifyRunTime(1); // may be a lot happening
		$approver->approve();
	}

	public function actionDelete(\XF\Entity\Thread $thread)
	{
		$this->quickUpdate($thread, 'discussion_state', 'deleted');
	}

	public function actionSpamClean(\XF\Entity\Thread $thread)
	{
		if (!$thread->User)
		{
			return;
		}

		$this->_spamCleanInternal($thread->User);
	}
}