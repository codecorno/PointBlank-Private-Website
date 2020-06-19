<?php

namespace XF\Service\Thread;

use XF\Entity\Thread;
use XF\Entity\ThreadReplyBan;
use XF\Entity\User;

class ReplyBan extends \XF\Service\AbstractService
{
	use \XF\Service\ValidateAndSavableTrait;

	/**
	 * @var Thread
	 */
	protected $thread;

	/**
	 * @var ThreadReplyBan
	 */
	protected $replyBan;

	/**
	 * @var User
	 */
	protected $user;

	protected $alert = false;

	public function __construct(\XF\App $app, Thread $thread, User $user)
	{
		parent::__construct($app);

		$this->thread = $thread;
		$this->user = $user;

		$replyBan = $this->em()->findOne('XF:ThreadReplyBan', [
			'thread_id' => $thread->thread_id,
			'user_id' => $user->user_id
		]);
		if (!$replyBan)
		{
			$replyBan = $this->em()->create('XF:ThreadReplyBan');
			$replyBan->thread_id = $thread->thread_id;
			$replyBan->user_id = $user->user_id;
		}

		$replyBan->ban_user_id = \XF::visitor()->user_id;

		$this->replyBan = $replyBan;
	}

	public function getThread()
	{
		return $this->thread;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function setExpiryDate($unit, $value = null)
	{
		if (is_int($unit))
		{
			$value = $unit;
			$expiryDate = $value;
		}
		else
		{
			if (!$value)
			{
				$value = 1;
			}
			$expiryDate = min(
				pow(2,32) - 1, strtotime("+$value $unit")
			);
		}
		$this->replyBan->expiry_date = $expiryDate;
	}

	public function setSendAlert($alert)
	{
		$this->alert = (bool)$alert;
	}

	public function setReason($reason = null)
	{
		if ($reason !== null)
		{
			$this->replyBan->reason = $reason;
		}
	}

	protected function finalSetup()
	{
	}

	protected function _validate()
	{
		$this->finalSetup();

		$this->replyBan->preSave();
		$errors = $this->replyBan->getErrors();

		if ($this->user->is_staff)
		{
			$errors['is_staff'] = \XF::phrase('staff_members_cannot_be_reply_banned');
		}

		return $errors;
	}

	protected function _save()
	{
		$replyBan = $this->replyBan;
		$replyBan->save();

		$this->app->logger()->logModeratorAction('thread', $this->thread, 'reply_ban', [
			'name' => $replyBan->User->username,
			'reason' => $replyBan->reason
		]);

		$this->sendAlert();

		return $replyBan;
	}

	protected function sendAlert()
	{
		$thread = $this->thread;
		$replyBan = $this->replyBan;

		if ($thread->discussion_state == 'visible' && $this->alert)
		{
			$extra = [
				'reason' => $replyBan->reason,
				'expiry' => $replyBan->expiry_date
			];

			/** @var \XF\Repository\UserAlert $alertRepo */
			$alertRepo = $this->repository('XF:UserAlert');
			$alertRepo->alert(
				$replyBan->User,
				0, '',
				'thread', $thread->thread_id,
				'reply_ban', $extra
			);
		}
	}
}