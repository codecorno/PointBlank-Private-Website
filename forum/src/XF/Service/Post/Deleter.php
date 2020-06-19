<?php

namespace XF\Service\Post;

use XF\Entity\Post;
use XF\Entity\User;

class Deleter extends \XF\Service\AbstractService
{
	/**
	 * @var Post
	 */
	protected $post;

	/**
	 * @var User
	 */
	protected $user;

	protected $alert = false;
	protected $alertReason = '';

	protected $threadDeleted = false;

	public function __construct(\XF\App $app, Post $post)
	{
		parent::__construct($app);
		$this->post = $post;
		$this->setUser(\XF::visitor());
	}

	public function getPost()
	{
		return $this->post;
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
		$deleteThread = $this->isThreadDeleteRequired($type);

		$user = $this->user;

		/** @var \XF\Entity\Thread $thread */
		$thread = $this->post->Thread;

		$result = null;
		$this->threadDeleted = $deleteThread;

		$wasVisible = $this->post->message_state == 'visible';

		if ($type == 'soft')
		{
			if ($deleteThread)
			{
				if ($thread)
				{
					$result = $thread->softDelete($reason, $user);
				}
			}
			else
			{
				$result = $this->post->softDelete($reason, $user);
			}
		}
		else
		{
			if ($deleteThread)
			{
				if ($thread)
				{
					$result = $thread->delete();
				}
			}
			else
			{
				$result = $this->post->delete();
			}
		}

		if ($result && $wasVisible && $this->alert && $this->post->user_id != $user->user_id)
		{
			/** @var \XF\Repository\Post $postRepo */
			$postRepo = $this->repository('XF:Post');
			$postRepo->sendModeratorActionAlert($this->post, 'delete', $this->alertReason);
		}

		return $result;
	}

	public function wasThreadDeleted()
	{
		return $this->threadDeleted;
	}

	protected function isThreadDeleteRequired($type)
	{
		return $this->post->isFirstPost();
	}
}