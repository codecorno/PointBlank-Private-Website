<?php

namespace XF\ControllerPlugin;

use XF\Mvc\Entity\Entity;
use XF\Mvc\FormAction;

class Thread extends AbstractPlugin
{
	public function getPostLink(\XF\Entity\Post $post)
	{
		$thread = $post->Thread;
		if (!$thread)
		{
			throw new \LogicException("Post has no thread");
		}

		$page = floor($post->position / $this->options()->messagesPerPage) + 1;

		return $this->buildLink('threads', $thread, ['page' => $page]) . '#post-' . $post->post_id;
	}
}