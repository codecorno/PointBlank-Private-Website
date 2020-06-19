<?php

namespace XF\Service\ProfilePost;

use XF\Entity\ProfilePost;

class Preparer extends \XF\Service\AbstractService
{
	/**
	 * @var ProfilePost
	 */
	protected $profilePost;

	protected $logIp = true;

	protected $mentionedUsers = [];

	public function __construct(\XF\App $app, ProfilePost $profilePost)
	{
		parent::__construct($app);
		$this->setProfilePost($profilePost);
	}

	protected function setProfilePost(ProfilePost $profilePost)
	{
		$this->profilePost = $profilePost;
	}

	public function getProfilePost()
	{
		return $this->profilePost;
	}

	public function logIp($logIp)
	{
		$this->logIp = $logIp;
	}

	public function getMentionedUsers($limitPermissions = true)
	{
		if ($limitPermissions && $this->profilePost)
		{
			/** @var \XF\Entity\User $user */
			$user = $this->profilePost->User ?: $this->repository('XF:User')->getGuestUser();
			return $user->getAllowedUserMentions($this->mentionedUsers);
		}
		else
		{
			return $this->mentionedUsers;
		}
	}

	public function getMentionedUserIds($limitPermissions = true)
	{
		return array_keys($this->getMentionedUsers($limitPermissions));
	}

	public function setMessage($message, $format = true)
	{
		$preparer = $this->getMessagePreparer($format);
		$preparer->setConstraint('maxLength', $this->app->options()->profilePostMaxLength);
		$this->profilePost->message = $preparer->prepare($message);
		$this->profilePost->embed_metadata = $preparer->getEmbedMetadata();

		$this->mentionedUsers = $preparer->getMentionedUsers();

		return $preparer->pushEntityErrorIfInvalid($this->profilePost);
	}

	/**
	 * @param bool $format
	 *
	 * @return \XF\Service\Message\Preparer
	 */
	protected function getMessagePreparer($format = true)
	{
		/** @var \XF\Service\Message\Preparer $preparer */
		$preparer = $this->service('XF:Message\Preparer', 'profile_post', $this->profilePost);
		$preparer->enableFilter('structuredText');
		if (!$format)
		{
			$preparer->disableAllFilters();
		}

		return $preparer;
	}

	public function checkForSpam()
	{
		$profilePost = $this->profilePost;

		/** @var \XF\Entity\User $user */
		$user = $profilePost->User ?: $this->repository('XF:User')->getGuestUser($profilePost->username);
		$message = $profilePost->message;

		$checker = $this->app->spam()->contentChecker();
		$checker->check($user, $message, [
			'content_type' => 'profile_post'
		]);

		$decision = $checker->getFinalDecision();
		switch ($decision)
		{
			case 'moderated':
				$profilePost->message_state = 'moderated';
				break;

			case 'denied':
				$checker->logSpamTrigger('profile_post', null);
				$profilePost->error(\XF::phrase('your_content_cannot_be_submitted_try_later'));
				break;
		}
	}

	public function afterInsert()
	{
		if ($this->logIp)
		{
			$ip = ($this->logIp === true ? $this->app->request()->getIp() : $this->logIp);
			$this->writeIpLog($ip);
		}

		$checker = $this->app->spam()->contentChecker();
		$checker->logSpamTrigger('profile_post', $this->profilePost->profile_post_id);
	}

	public function afterUpdate()
	{
		$checker = $this->app->spam()->contentChecker();
		$checker->logSpamTrigger('profile_post', $this->profilePost->profile_post_id);

		// TODO: edit history?
	}

	protected function writeIpLog($ip)
	{
		$profilePost = $this->profilePost;
		if (!$profilePost->user_id)
		{
			return;
		}

		/** @var \XF\Repository\IP $ipRepo */
		$ipRepo = $this->repository('XF:Ip');
		$ipEnt = $ipRepo->logIp($profilePost->user_id, $ip, 'profile_post', $profilePost->profile_post_id);
		if ($ipEnt)
		{
			$profilePost->fastUpdate('ip_id', $ipEnt->ip_id);
		}
	}
}