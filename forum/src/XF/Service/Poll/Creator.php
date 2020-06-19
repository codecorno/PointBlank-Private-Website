<?php

namespace XF\Service\Poll;

use XF\Mvc\Entity\Entity;
use XF\Entity\Poll;

class Creator extends \XF\Service\AbstractService
{
	use \XF\Service\ValidateAndSavableTrait;

	/** @var Entity */
	protected $content;

	/** @var Poll */
	protected $poll;

	/** @var \XF\Poll\ResponseEditor */
	protected $responseEditor;

	/** @var \XF\Poll\AbstractHandler */
	protected $handler;

	protected $maxResponses;

	public function __construct(\XF\App $app, $contentType, Entity $content)
	{
		parent::__construct($app);
		$this->setContent($contentType, $content);

		$this->maxResponses = $this->app->options()->pollMaximumResponses;
	}

	protected function setContent($contentType, Entity $content)
	{
		$this->content = $content;

		/** @var \XF\Entity\Poll $poll */
		$poll = $this->em()->create('XF:Poll');
		$poll->content_type = $contentType;

		// might be created before the content has been made
		$id = $content->getEntityId();
		if (!$id)
		{
			$id = $poll->em()->getDeferredValue(function() use ($content)
			{
				return $content->getEntityId();
			}, 'save');
		}

		$poll->content_id = $id;

		$this->handler = $this->repository('XF:Poll')->getPollHandler($contentType, true);
		$this->poll = $poll;
		$this->responseEditor = $poll->getResponseEditor();
	}

	public function getContent()
	{
		return $this->content;
	}

	public function getPoll()
	{
		return $this->poll;
	}

	public function setQuestion($question)
	{
		$this->poll->question = $question;
	}

	public function addResponses(array $responses)
	{
		$this->responseEditor->addResponses($responses);
	}

	public function setMaxResponses($max)
	{
		$this->maxResponses = $max;
	}

	public function getMaxResponses()
	{
		return $this->maxResponses;
	}

	public function setMaxVotes($type, $value = null)
	{
		$this->poll->setMaxVotes($type, $value);
	}

	public function setCloseDateRelative($value, $unit)
	{
		$this->poll->setCloseDateRelative($value, $unit);
	}

	public function setOptions(array $options)
	{
		$this->poll->bulkSet($options);
	}

	protected function _validate()
	{
		$this->poll->preSave();

		$errors = $this->poll->getErrors();

		$responseError = $this->responseEditor->getResponseCountErrorMessage($this->maxResponses);
		if ($responseError)
		{
			$errors['responses'] = $responseError;
		}

		return $errors;
	}

	protected function _save()
	{
		$poll = $this->poll;
		$content = $poll->Content;
		$contentType = $poll->content_type;

		$poll->save();

		if (isset($content->User) && $content->User->user_id != \XF::visitor()->user_id)
		{
			$this->app->logger()->logModeratorAction($contentType, $content, 'poll_create');
		}

		$this->responseEditor->saveChanges();

		return $poll;
	}
}