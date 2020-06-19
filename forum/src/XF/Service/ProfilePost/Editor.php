<?php

namespace XF\Service\ProfilePost;

use XF\Entity\ProfilePost;

class Editor extends \XF\Service\AbstractService
{
	use \XF\Service\ValidateAndSavableTrait;

	/**
	 * @var ProfilePost
	 */
	protected $profilePost;

	/**
	 * @var \XF\Service\ProfilePost\Preparer
	 */
	protected $preparer;

	protected $alert = false;
	protected $alertReason = '';

	public function __construct(\XF\App $app, ProfilePost $profilePost)
	{
		parent::__construct($app);
		$this->setProfilePost($profilePost);
	}

	protected function setProfilePost(ProfilePost $profilePost)
	{
		$this->profilePost = $profilePost;
		$this->preparer = $this->service('XF:ProfilePost\Preparer', $profilePost);
	}

	public function getProfilePost()
	{
		return $this->profilePost;
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
		if ($this->profilePost->message_state == 'visible' && \XF::visitor()->isSpamCheckRequired())
		{
			$this->preparer->checkForSpam();
		}
	}

	protected function finalSetup() {}

	protected function _validate()
	{
		$this->finalSetup();

		$this->profilePost->preSave();
		return $this->profilePost->getErrors();
	}

	protected function _save()
	{
		$db = $this->db();
		$db->beginTransaction();

		$profilePost = $this->profilePost;
		$visitor = \XF::visitor();

		$profilePost->save(true, false);

		$this->preparer->afterUpdate();

		if ($profilePost->message_state == 'visible' && $this->alert && $profilePost->user_id != $visitor->user_id)
		{
			/** @var \XF\Repository\ProfilePost $profilePostRepo */
			$profilePostRepo = $this->repository('XF:ProfilePost');
			$profilePostRepo->sendModeratorActionAlert($profilePost, 'edit', $this->alertReason);
		}

		$db->commit();

		return $profilePost;
	}
}