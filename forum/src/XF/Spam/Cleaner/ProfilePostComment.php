<?php

namespace XF\Spam\Cleaner;

class ProfilePostComment extends AbstractHandler
{
	public function canCleanUp(array $options = [])
	{
		return !empty($options['delete_messages']);
	}

	public function cleanUp(array &$log, &$error = null)
	{
		$app = \XF::app();

		$profilePostCommentsFinder = $app->finder('XF:ProfilePostComment');
		$profilePostComments = $profilePostCommentsFinder
			->where('user_id', $this->user->user_id)
			->fetch();

		if ($profilePostComments->count())
		{
			$profilePostCommentIds = $profilePostComments->pluckNamed('profile_post_comment_id');
			$submitter = $app->container('spam.contentSubmitter');
			$submitter->submitSpam('profile_post_comment', $profilePostCommentIds);

			$deleteType = $app->options()->spamMessageAction == 'delete' ? 'hard' : 'soft';

			$log['profile_post_comment'] = [
				'deleteType' => $deleteType,
				'profilePostCommentIds' => []
			];

			foreach ($profilePostComments AS $profilePostCommentId => $profilePostComment)
			{
				$log['profile_post_comment']['profilePostCommentIds'][] = $profilePostCommentId;

				/** @var \XF\Entity\ProfilePostComment $profilePostComment */
				$profilePostComment->setOption('log_moderator', false);
				if ($deleteType == 'soft')
				{
					$profilePostComment->softDelete();
				}
				else
				{
					$profilePostComment->delete();
				}
			}
		}

		return true;
	}

	public function restore(array $log, &$error = null)
	{
		$profilePostCommentsFinder = \XF::app()->finder('XF:ProfilePostComment');

		if ($log['deleteType'] == 'soft')
		{
			$profilePostComments = $profilePostCommentsFinder->where('profile_post_comment_id', $log['profilePostCommentIds'])->fetch();
			foreach ($profilePostComments AS $profilePostComment)
			{
				/** @var \XF\Entity\ProfilePostComment $profilePostComment */
				$profilePostComment->setOption('log_moderator', false);
				$profilePostComment->message_state = 'visible';
				$profilePostComment->save();
			}
		}

		return true;
	}
}