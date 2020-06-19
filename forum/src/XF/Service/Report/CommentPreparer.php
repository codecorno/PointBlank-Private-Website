<?php

namespace XF\Service\Report;

use XF\Entity\ReportComment;

class CommentPreparer extends \XF\Service\AbstractService
{
	/**
	 * @var ReportComment
	 */
	protected $comment;

	protected $mentionedUsers = [];

	public function __construct(\XF\App $app, ReportComment $comment)
	{
		parent::__construct($app);
		$this->setComment($comment);
	}

	public function setComment(ReportComment $comment)
	{
		$this->comment = $comment;
	}

	public function getComment()
	{
		return $this->comment;
	}

	public function setUser(\XF\Entity\User $user)
	{
		$this->comment->user_id = $user->user_id;
		$this->comment->username = $user->username;
	}

	public function setMessage($message, $format = true)
	{
		$preparer = $this->getMessagePreparer($format);
		$this->comment->message = $preparer->prepare($message);

		$this->mentionedUsers = $preparer->getMentionedUsers();

		return $preparer->pushEntityErrorIfInvalid($this->comment);
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

	/**
	 * @param bool $format
	 *
	 * @return \XF\Service\Message\Preparer
	 */
	protected function getMessagePreparer($format = true)
	{
		/** @var \XF\Service\Message\Preparer $preparer */
		$preparer = $this->service('XF:Message\Preparer', 'report_comment', $this->comment);
		if (!$format)
		{
			$preparer->disableAllFilters();
		}
		$preparer->setConstraint('allowEmpty', true);

		return $preparer;
	}
}