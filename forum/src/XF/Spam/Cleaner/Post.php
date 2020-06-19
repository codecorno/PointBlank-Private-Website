<?php

namespace XF\Spam\Cleaner;

class Post extends AbstractHandler
{
	public function canCleanUp(array $options = [])
	{
		return !empty($options['delete_messages']);
	}

	public function cleanUp(array &$log, &$error = null)
	{
		$app = \XF::app();

		$postsFinder = $app->finder('XF:Post');
		$posts = $postsFinder
			->with('Thread', true)
			->where('user_id', $this->user->user_id)
			->isNotFirstPost()
			->fetch();

		if ($posts->count())
		{
			$postIds = $posts->pluckNamed('post_id');
			$submitter = $app->container('spam.contentSubmitter');
			$submitter->submitSpam('post', $postIds);

			$deleteType = $app->options()->spamMessageAction == 'delete' ? 'hard' : 'soft';

			$log['post'] = [
				'deleteType' => $deleteType,
				'postIds' => []
			];

			foreach ($posts AS $postId => $post)
			{
				$log['post']['postIds'][] = $postId;

				/** @var \XF\Entity\Post $post */
				$post->setOption('log_moderator', false);
				if ($deleteType == 'soft')
				{
					$post->softDelete();
				}
				else
				{
					$post->delete();
				}
			}
		}

		return true;
	}

	public function restore(array $log, &$error = null)
	{
		$postsFinder = \XF::app()->finder('XF:Post');

		if ($log['deleteType'] == 'soft')
		{
			$posts = $postsFinder->where('post_id', $log['postIds'])->fetch();
			foreach ($posts AS $post)
			{
				/** @var \XF\Entity\Post $post */
				$post->setOption('log_moderator', false);
				$post->message_state = 'visible';
				$post->save();
			}
		}

		return true;
	}
}