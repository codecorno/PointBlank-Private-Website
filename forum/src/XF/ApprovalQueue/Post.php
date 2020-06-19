<?php

namespace XF\ApprovalQueue;

use XF\Mvc\Entity\Entity;

class Post extends AbstractHandler
{
	protected function canActionContent(Entity $content, &$error = null)
	{
		/** @var $content \XF\Entity\Post */
		return $content->canApproveUnapprove($error);
	}

	public function getEntityWith()
	{
		$visitor = \XF::visitor();

		return ['Thread', 'Thread.Forum', 'Thread.Forum.Node.Permissions|' . $visitor->permission_combination_id, 'User'];
	}

	public function actionApprove(\XF\Entity\Post $post)
	{
		/** @var \XF\Service\Post\Approver $approver */
		$approver = \XF::service('XF:Post\Approver', $post);
		$approver->setNotifyRunTime(1); // may be a lot happening
		$approver->approve();
	}

	public function actionDelete(\XF\Entity\Post $post)
	{
		$this->quickUpdate($post, 'message_state', 'deleted');
	}

	public function actionSpamClean(\XF\Entity\Post $post)
	{
		if (!$post->User)
		{
			return;
		}

		$this->_spamCleanInternal($post->User);
	}
}