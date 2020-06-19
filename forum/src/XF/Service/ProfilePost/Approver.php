<?php

namespace XF\Service\ProfilePost;

use XF\Entity\ProfilePost;

class Approver extends \XF\Service\AbstractService
{
	/**
	 * @var ProfilePost
	 */
	protected $profilePost;

	public function __construct(\XF\App $app, ProfilePost $profilePost)
	{
		parent::__construct($app);
		$this->profilePost = $profilePost;
	}

	public function getProfilePost()
	{
		return $this->profilePost;
	}

	public function approve()
	{
		if ($this->profilePost->message_state == 'moderated')
		{
			$this->profilePost->message_state = 'visible';
			$this->profilePost->save();

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
		/** @var \XF\Service\ProfilePost\Notifier $notifier */
		$notifier = $this->service('XF:ProfilePost\Notifier', $this->profilePost);
		$notifier->notify();
	}
}