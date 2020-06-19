<?php

namespace XF\Service\ProfilePostComment;

use XF\Entity\ProfilePostComment;

class Approver extends \XF\Service\AbstractService
{
	/**
	 * @var ProfilePostComment
	 */
	protected $comment;

	protected $notifyRunTime = 3;

	public function __construct(\XF\App $app, ProfilePostComment $comment)
	{
		parent::__construct($app);
		$this->comment = $comment;
	}

	public function getComment()
	{
		return $this->comment;
	}

	public function setNotifyRunTime($time)
	{
		$this->notifyRunTime = $time;
	}

	public function approve()
	{
		if ($this->comment->message_state == 'moderated')
		{
			$this->comment->message_state = 'visible';
			$this->comment->save();

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
		if ($this->comment->isLastComment())
		{
			/** @var \XF\Service\ProfilePostComment\Notifier $notifier */
			$notifier = $this->service('XF:ProfilePostComment\Notifier', $this->comment);
			$notifier->notify();
		}
	}
}