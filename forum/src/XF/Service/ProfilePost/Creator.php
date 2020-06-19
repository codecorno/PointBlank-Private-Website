<?php

namespace XF\Service\ProfilePost;

use XF\Entity\User;
use XF\Entity\UserProfile;
use XF\Entity\ProfilePost;

class Creator extends \XF\Service\AbstractService
{
	use \XF\Service\ValidateAndSavableTrait;

	/**
	 * @var UserProfile
	 */
	protected $userProfile;

	/**
	 * @var ProfilePost
	 */
	protected $profilePost;

	/**
	 * @var User
	 */
	protected $user;

	/**
	 * @var \XF\Service\ProfilePost\Preparer
	 */
	protected $preparer;

	public function __construct(\XF\App $app, UserProfile $userProfile)
	{
		parent::__construct($app);
		$this->setUserProfile($userProfile);
		$this->setUser(\XF::visitor());
		$this->setDefaults();
	}

	protected function setUserProfile(UserProfile $userProfile)
	{
		$this->userProfile = $userProfile;
		$this->profilePost = $userProfile->getNewProfilePost();
		$this->preparer = $this->service('XF:ProfilePost\Preparer', $this->profilePost);
	}

	public function getUserProfile()
	{
		return $this->userProfile;
	}

	public function getProfilePost()
	{
		return $this->profilePost;
	}

	public function getProfilePostPreparer()
	{
		return $this->preparer;
	}

	public function logIp($logIp)
	{
		$this->preparer->logIp($logIp);
	}

	protected function setUser(\XF\Entity\User $user)
	{
		$this->user = $user;
	}

	protected function setDefaults()
	{
		$this->profilePost->message_state = $this->profilePost->getNewContentState();
		$this->profilePost->user_id = $this->user->user_id;
		$this->profilePost->username = $this->user->username;
	}

	public function setContent($message, $format = true)
	{
		return $this->preparer->setMessage($message, $format);
	}

	public function checkForSpam()
	{
		if ($this->profilePost->message_state == 'visible' && $this->user->isSpamCheckRequired())
		{
			$this->preparer->checkForSpam();
		}
	}

	protected function finalSetup()
	{
		$this->profilePost->post_date = time();
	}

	protected function _validate()
	{
		$this->finalSetup();

		$this->profilePost->preSave();
		return $this->profilePost->getErrors();
	}

	protected function _save()
	{
		$profilePost = $this->profilePost;
		$profilePost->save();

		$this->preparer->afterInsert();

		return $profilePost;
	}

	public function sendNotifications()
	{
		if ($this->profilePost->isVisible())
		{
			/** @var \XF\Service\ProfilePost\Notifier $notifier */
			$notifier = $this->service('XF:ProfilePost\Notifier', $this->profilePost);
			$notifier->setNotifyMentioned($this->preparer->getMentionedUserIds());
			$notifier->notify();
		}
	}
}