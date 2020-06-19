<?php

namespace XF\Service\ProfilePost;

use XF\Entity\ProfilePost;
use XF\Entity\User;

class Deleter extends \XF\Service\AbstractService
{
	/**
	 * @var ProfilePost
	 */
	protected $profilePost;

	/**
	 * @var User
	 */
	protected $user;

	protected $alert = false;
	protected $alertReason = '';

	public function __construct(\XF\App $app, ProfilePost $profilePost)
	{
		parent::__construct($app);
		$this->setProfilePost($profilePost);
		$this->setUser(\XF::visitor());
	}

	protected function setProfilePost(ProfilePost $profilePost)
	{
		$this->profilePost = $profilePost;
	}

	public function getProfilePost()
	{
		return $this->profilePost;
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

		$profilePost = $this->profilePost;
		$wasVisible = ($profilePost->message_state == 'visible');

		if ($type == 'soft')
		{
			$result = $profilePost->softDelete($reason, $user);
		}
		else
		{
			$result = $profilePost->delete();
		}

		if ($result && $wasVisible && $this->alert && $profilePost->user_id != $user->user_id)
		{
			/** @var \XF\Repository\ProfilePost $profilePostRepo */
			$profilePostRepo = $this->repository('XF:ProfilePost');
			$profilePostRepo->sendModeratorActionAlert($profilePost, 'delete', $this->alertReason);
		}

		return $result;
	}
}