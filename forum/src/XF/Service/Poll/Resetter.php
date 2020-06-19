<?php

namespace XF\Service\Poll;

use XF\Mvc\Entity\Entity;
use XF\Entity\Poll;

class Resetter extends \XF\Service\AbstractService
{
	/** @var Poll */
	protected $poll;

	public function __construct(\XF\App $app, Poll $poll)
	{
		parent::__construct($app);
		$this->poll = $poll;
	}

	public function getPoll()
	{
		return $this->poll;
	}

	public function reset()
	{
		$poll = $this->poll;
		$content = $poll->Content;
		$contentType = $poll->content_type;

		$this->repository('XF:Poll')->resetPollVotes($poll);

		$this->app->logger()->logModeratorAction($contentType, $content, 'poll_reset');
	}
}