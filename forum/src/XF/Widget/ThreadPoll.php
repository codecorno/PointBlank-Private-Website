<?php

namespace XF\Widget;

class ThreadPoll extends AbstractPollWidget
{
	public function getPollFromRoutePath($routePath, &$error = null)
	{
		$thread = $this->repository('XF:Thread')->getThreadFromUrl($routePath, 'public', $error);
		if (!$thread)
		{
			return false;
		}

		if (!$thread->Poll)
		{
			$error = \XF::phrase('specified_thread_does_not_have_poll_attached_to_it');
			return false;
		}

		return $thread->Poll;
	}

	public function getDefaultTitle()
	{
		/** @var \XF\Entity\Thread $content */
		$content = $this->getContent();
		if ($content && $content->canView() && $content->Poll)
		{
			return $content->Poll->question;
		}
		else
		{
			return parent::getDefaultTitle();
		}
	}

	public function render()
	{
		/** @var \XF\Entity\Thread $content */
		$content = $this->getContent();
		if ($content && $content->canView() && $content->Poll)
		{
			$viewParams = [
				'content' => $content,
				'poll' => $content->Poll
			];
			return $this->renderer('widget_thread_poll', $viewParams);
		}

		return '';
	}

	public function getEntityWith()
	{
		return [
			'Poll',
			'Forum',
			'Forum.Node',
			'Forum.Node.Permissions|' . \XF::visitor()->permission_combination_id
		];
	}
}