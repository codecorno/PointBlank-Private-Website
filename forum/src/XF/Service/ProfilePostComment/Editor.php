<?php

namespace XF\Service\ProfilePostComment;

use XF\Entity\ProfilePostComment;

class Editor extends \XF\Service\AbstractService
{
	use \XF\Service\ValidateAndSavableTrait;

	/**
	 * @var ProfilePostComment
	 */
	protected $comment;

	/**
	 * @var \XF\Service\ProfilePostComment\Preparer
	 */
	protected $preparer;

	protected $alert = false;
	protected $alertReason = '';

	public function __construct(\XF\App $app, ProfilePostComment $comment)
	{
		parent::__construct($app);
		$this->setComment($comment);
	}

	protected function setComment(ProfilePostComment $comment)
	{
		$this->comment = $comment;
		$this->preparer = $this->service('XF:ProfilePostComment\Preparer', $this->comment);
	}

	public function getComment()
	{
		return $this->comment;
	}

	public function getPreparer()
	{
		return $this->preparer;
	}

	public function setMessage($message, $format = true)
	{
		return $this->preparer->setMessage($message, $format);
	}

	public function setSendAlert($alert, $reason = null)
	{
		$this->alert = (bool)$alert;
		if ($reason !== null)
		{
			$this->alertReason = $reason;
		}
	}

	public function checkForSpam()
	{
		if ($this->comment->message_state == 'visible' && \XF::visitor()->isSpamCheckRequired())
		{
			$this->preparer->checkForSpam();
		}
	}

	protected function finalSetup() {}

	protected function _validate()
	{
		$this->finalSetup();

		$this->comment->preSave();
		return $this->comment->getErrors();
	}

	protected function _save()
	{
		$db = $this->db();
		$db->beginTransaction();

		$comment = $this->comment;
		$visitor = \XF::visitor();

		$comment->save(true, false);

		$this->preparer->afterUpdate();

		if ($comment->message_state == 'visible' && $this->alert && $comment->user_id != $visitor->user_id)
		{
			/** @var \XF\Repository\ProfilePost $profilePostRepo */
			$profilePostRepo = $this->repository('XF:ProfilePost');
			$profilePostRepo->sendCommentModeratorActionAlert($comment, 'edit', $this->alertReason);
		}

		$db->commit();

		return $comment;
	}
}