<?php

namespace XF\Service\Thread;

use XF\Entity\Thread;

class Approver extends \XF\Service\AbstractService
{
	/**
	 * @var Thread
	 */
	protected $thread;

	protected $notifyRunTime = 3;

	public function __construct(\XF\App $app, Thread $thread)
	{
		parent::__construct($app);
		$this->thread = $thread;
	}

	public function getThread()
	{
		return $this->thread;
	}

	public function setNotifyRunTime($time)
	{
		$this->notifyRunTime = $time;
	}

	public function approve()
	{
		if ($this->thread->discussion_state == 'moderated')
		{
			$this->thread->discussion_state = 'visible';
			$this->thread->save();

			$this->onApprove();
			return true;
		}
		else
		{
			return false;
		}
	}

	protected function onApprove()
	{
		$post = $this->thread->FirstPost;

		if ($post)
		{
			/** @var \XF\Service\Post\Notifier $notifier */
			$notifier = $this->service('XF:Post\Notifier', $post, 'thread');
			$notifier->notifyAndEnqueue($this->notifyRunTime);
		}
	}
}