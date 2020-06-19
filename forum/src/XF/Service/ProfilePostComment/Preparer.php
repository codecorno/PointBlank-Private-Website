<?php

namespace XF\Service\ProfilePostComment;

use XF\Entity\ProfilePostComment;

class Preparer extends \XF\Service\AbstractService
{
	/**
	 * @var ProfilePostComment
	 */
	protected $comment;

	protected $logIp = true;

	protected $mentionedUsers = [];

	public function __construct(\XF\App $app, ProfilePostComment $comment)
	{
		parent::__construct($app);
		$this->setComment($comment);
	}

	protected function setComment(ProfilePostComment $comment)
	{
		$this->comment = $comment;
	}

	public function getComment()
	{
		return $this->comment;
	}

	public function logIp($logIp)
	{
		$this->logIp = $logIp;
	}

	public function getMentionedUsers($limitPermissions = true)
	{
		if ($limitPermissions && $this->comment)
		{
			/** @var \XF\Entity\User $user */
			$user = $this->comment->User ?: $this->repository('XF:User')->getGuestUser();
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
		$this->comment->message = $preparer->prepare($message);
		$this->comment->embed_metadata = $preparer->getEmbedMetadata();

		$this->mentionedUsers = $preparer->getMentionedUsers();

		return $preparer->pushEntityErrorIfInvalid($this->comment);
	}

	/**
	 * @param bool $format
	 *
	 * @return \XF\Service\Message\Preparer
	 */
	protected function getMessagePreparer($format = true)
	{
		/** @var \XF\Service\Message\Preparer $preparer */
		$preparer = $this->service('XF:Message\Preparer', 'profile_post_comment', $this->comment);
		$preparer->enableFilter('structuredText');
		if (!$format)
		{
			$preparer->disableAllFilters();
		}

		return $preparer;
	}

	public function checkForSpam()
	{
		$comment = $this->comment;

		/** @var \XF\Entity\User $user */
		$user = $comment->User ?: $this->repository('XF:User')->getGuestUser($comment->username);
		$message = $comment->message;

		$checker = $this->app->spam()->contentChecker();
		$checker->check($user, $message, [
			'content_type' => 'profile_post_comment'
		]);

		$decision = $checker->getFinalDecision();
		switch ($decision)
		{
			case 'moderated':
				$comment->message_state = 'moderated';
				break;

			case 'denied':
				$checker->logSpamTrigger('profile_post_comment', null);
				$comment->error(\XF::phrase('your_content_cannot_be_submitted_try_later'));
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
		$checker->logSpamTrigger('profile_post_comment', $this->comment->profile_post_comment_id);
	}

	public function afterUpdate()
	{
		$checker = $this->app->spam()->contentChecker();
		$checker->logSpamTrigger('profile_post_comment', $this->comment->profile_post_comment_id);

		// TODO: edit history?
	}

	protected function writeIpLog($ip)
	{
		$comment = $this->comment;
		if (!$comment->user_id)
		{
			return;
		}

		/** @var \XF\Repository\IP $ipRepo */
		$ipRepo = $this->repository('XF:Ip');
		$ipEnt = $ipRepo->logIp($comment->user_id, $ip, 'profile_post_comment', $comment->profile_post_comment_id);
		if ($ipEnt)
		{
			$comment->fastUpdate('ip_id', $ipEnt->ip_id);
		}
	}
}