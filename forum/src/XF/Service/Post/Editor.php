<?php

namespace XF\Service\Post;

use XF\Entity\Post;

class Editor extends \XF\Service\AbstractService
{
	use \XF\Service\ValidateAndSavableTrait;

	/**
	 * @var Post
	 */
	protected $post;

	/**
	 * @var \XF\Service\Post\Preparer
	 */
	protected $postPreparer;

	protected $oldMessage;

	protected $logDelay;
	protected $logEdit = true;
	protected $logHistory = true;

	protected $alert = false;
	protected $alertReason = '';

	protected $performValidations = true;

	/**
	 * @var \XF\Service\Thread\Editor|null
	 */
	protected $threadEditor = null;

	public function __construct(\XF\App $app, Post $post)
	{
		parent::__construct($app);
		$this->setPost($post);
	}

	protected function setPost(Post $post)
	{
		$this->post = $post;
		$this->postPreparer = $this->service('XF:Post\Preparer', $this->post);
	}

	public function getPost()
	{
		return $this->post;
	}

	public function logDelay($logDelay)
	{
		$this->logDelay = $logDelay;
	}

	public function logEdit($logEdit)
	{
		$this->logEdit = $logEdit;
	}

	public function logHistory($logHistory)
	{
		$this->logHistory = $logHistory;
	}

	public function setPerformValidations($perform)
	{
		$this->performValidations = (bool)$perform;
	}

	public function getPerformValidations()
	{
		return $this->performValidations;
	}

	public function setIsAutomated()
	{
		$this->setPerformValidations(false);
	}

	public function setThreadEditor(\XF\Service\Thread\Editor $editor = null)
	{
		$this->threadEditor = $editor;
	}

	public function getThreadEditor()
	{
		return $this->threadEditor;
	}

	public function getPostPreparer()
	{
		return $this->postPreparer;
	}

	protected function setupEditHistory($oldMessage)
	{
		$post = $this->post;

		$post->edit_count++;

		$options = $this->app->options();
		if ($options->editLogDisplay['enabled'] && $this->logEdit)
		{
			$delay = is_null($this->logDelay) ? $options->editLogDisplay['delay'] * 60 : $this->logDelay;
			if ($post->post_date + $delay <= \XF::$time)
			{
				$post->last_edit_date = \XF::$time;
				$post->last_edit_user_id = \XF::visitor()->user_id;
			}
		}

		if ($options->editHistory['enabled'] && $this->logHistory)
		{
			$this->oldMessage = $oldMessage;
		}
	}

	public function setMessage($message, $format = true)
	{
		$setupHistory = !$this->post->isChanged('message');
		$oldMessage = $this->post->message;

		$result = $this->postPreparer->setMessage($message, $format, $this->performValidations);

		if ($setupHistory && $result && $this->post->isChanged('message'))
		{
			$this->setupEditHistory($oldMessage);
		}

		return $result;
	}

	public function setAttachmentHash($hash)
	{
		$this->postPreparer->setAttachmentHash($hash);
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
		if ($this->post->message_state == 'visible' && \XF::visitor()->isSpamCheckRequired())
		{
			$this->postPreparer->checkForSpam();
		}
	}

	protected function finalSetup()
	{

	}

	protected function _validate()
	{
		$this->finalSetup();

		$this->post->preSave();
		$errors = $this->post->getErrors();

		if ($this->threadEditor && !$this->threadEditor->validate($threadErrors))
		{
			$errors = array_merge($errors, $threadErrors);
		}

		return $errors;
	}

	protected function _save()
	{
		$post = $this->post;
		$visitor = \XF::visitor();

		$db = $this->db();
		$db->beginTransaction();

		$post->save(true, false);

		$this->postPreparer->afterUpdate();

		if ($this->oldMessage)
		{
			/** @var \XF\Repository\EditHistory $repo */
			$repo = $this->repository('XF:EditHistory');
			$repo->insertEditHistory('post', $post, $visitor, $this->oldMessage, $this->app->request()->getIp());
		}

		if ($post->message_state == 'visible' && $this->alert && $post->user_id != $visitor->user_id)
		{
			/** @var \XF\Repository\Post $postRepo */
			$postRepo = $this->repository('XF:Post');
			$postRepo->sendModeratorActionAlert($post, 'edit', $this->alertReason);
		}

		if ($this->threadEditor)
		{
			$this->threadEditor->save();
		}

		$db->commit();

		return $post;
	}
}