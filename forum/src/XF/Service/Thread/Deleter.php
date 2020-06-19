<?php

namespace XF\Service\Thread;

use XF\Entity\Thread;

class Deleter extends \XF\Service\AbstractService
{
	/**
	 * @var Thread
	 */
	protected $thread;

	protected $user;

	protected $alert = false;
	protected $alertReason = '';

	public function __construct(\XF\App $app, Thread $thread)
	{
		parent::__construct($app);
		$this->thread = $thread;
	}

	public function getThread()
	{
		return $this->thread;
	}

	public function setUser(\XF\Entity\User $user = null)
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
		switch ($type)
		{
			case 'soft':
			case 'hard':
				break;

			default:
				throw new \InvalidArgumentException("Unexpected delete type '$type'. Should be soft or hard.");
		}

		$user = $this->user ?: \XF::visitor();

		$wasVisible = $this->thread->discussion_state == 'visible';

		if ($type == 'soft')
		{
			$result = $this->thread->softDelete($reason, $user);
		}
		else
		{
			$result = $this->thread->delete();
		}

		if ($result
			&& $wasVisible
			&& $this->alert
			&& $this->thread->user_id != $user->user_id
			&& $this->thread->discussion_state != 'redirect'
		)
		{
			/** @var \XF\Repository\Thread $threadRepo */
			$threadRepo = $this->repository('XF:Thread');
			$threadRepo->sendModeratorActionAlert($this->thread, 'delete', $this->alertReason);
		}

		return $result;
	}
}