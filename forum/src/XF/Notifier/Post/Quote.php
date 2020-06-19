<?php

namespace XF\Notifier\Post;

use XF\Notifier\AbstractNotifier;

class Quote extends AbstractNotifier
{
	/**
	 * @var \XF\Entity\Post
	 */
	protected $post;

	public function __construct(\XF\App $app, \XF\Entity\Post $post)
	{
		parent::__construct($app);

		$this->post = $post;
	}

	public function canNotify(\XF\Entity\User $user)
	{
		return ($user->user_id != $this->post->user_id);
	}

	public function sendAlert(\XF\Entity\User $user)
	{
		$post = $this->post;

		return $this->basicAlert($user, $post->user_id, $post->username, 'post', $post->post_id, 'quote');
	}
}