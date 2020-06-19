<?php

namespace XF\Service\ProfilePostComment;

use XF\Entity\ProfilePostComment;
use XF\Entity\User;

class Deleter extends \XF\Service\AbstractService
{
	/**
	 * @var ProfilePostComment
	 */
	protected $comment;

	/**
	 * @var User
	 */
	protected $user;

	protected $alert = false;
	protected $alertReason = '';

	public function __construct(\XF\App $app, ProfilePostComment $content)
	{
		parent::__construct($app);
		$this->setComment($content);
		$this->setUser(\XF::visitor());
	}

	protected function setComment(ProfilePostComment $content)
	{
		$this->comment = $content;
	}

	public function getComment()
	{
		return $this->comment;
	}

	protected function setUser(\XF\Entity\User $user)
	{
		$this->user = $user;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function setSendAlert($alert, $reason = null)
	{
		$this->alert = (bool)$alert;
		if ($reason !== null)
		{
			$this->alertReason = $reason;
		}
	}

	public function delete($type, $reason = '')
	{
		$user = $this->user;

		$comment = $this->comment;
		$wasVisible = ($comment->message_state == 'visible');

		if ($type == 'soft')
		{
			$result = $comment->softDelete($reason, $user);
		}
		else
		{
			$result = $comment->delete();
		}

		if ($result && $wasVisible && $this->alert && $comment->user_id != $user->user_id)
		{
			/** @var \XF\Repository\ProfilePost $profilePostRepo */
			$profilePostRepo = $this->repository('XF:ProfilePost');
			$profilePostRepo->sendCommentModeratorActionAlert($comment, 'delete', $this->alertReason);
		}

		return $result;
	}
}