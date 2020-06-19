<?php

namespace XF\Spam\Cleaner;

class ProfilePost extends AbstractHandler
{
	public function canCleanUp(array $options = [])
	{
		return !empty($options['delete_messages']);
	}

	public function cleanUp(array &$log, &$error = null)
	{
		$app = \XF::app();

		$profilePostsFinder = $app->finder('XF:ProfilePost');
		$profilePosts = $profilePostsFinder
			->where('user_id', $this->user->user_id)
			->fetch();

		if ($profilePosts->count())
		{
			$profilePostIds = $profilePosts->pluckNamed('profile_post_id');
			$submitter = $app->container('spam.contentSubmitter');
			$submitter->submitSpam('profile_post', $profilePostIds);

			$deleteType = $app->options()->spamMessageAction == 'delete' ? 'hard' : 'soft';

			$log['profile_post'] = [
				'deleteType' => $deleteType,
				'profilePostIds' => []
			];

			foreach ($profilePosts AS $profilePostId => $profilePost)
			{
				$log['profile_post']['profilePostIds'][] = $profilePostId;

				/** @var \XF\Entity\ProfilePost $profilePost */
				$profilePost->setOption('log_moderator', false);
				if ($deleteType == 'soft')
				{
					$profilePost->softDelete();
				}
				else
				{
					$profilePost->delete();
				}
			}
		}

		return true;
	}

	public function restore(array $log, &$error = null)
	{
		$profilePostsFinder = \XF::app()->finder('XF:ProfilePost');

		if ($log['deleteType'] == 'soft')
		{
			$profilePosts = $profilePostsFinder->where('profile_post_id', $log['profilePostIds'])->fetch();
			foreach ($profilePosts AS $profilePost)
			{
				/** @var \XF\Entity\ProfilePost $profilePost */
				$profilePost->setOption('log_moderator', false);
				$profilePost->message_state = 'visible';
				$profilePost->save();
			}
		}

		return true;
	}
}