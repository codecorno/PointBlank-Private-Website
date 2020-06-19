<?php

namespace XF\NewsFeed;

class Thread extends AbstractHandler
{
	public function getEntityWith()
	{
		$visitor = \XF::visitor();

		return ['User', 'FirstPost', 'Forum', 'Forum.Node.Permissions|' . $visitor->permission_combination_id];
	}

	protected function addAttachmentsToContent($content)
	{
		$firstPosts = [];
		foreach ($content AS $thread)
		{
			$firstPost = $thread->FirstPost;
			if ($firstPost)
			{
				$firstPosts[$firstPost->post_id] = $firstPost;
			}
		}

		/** @var \XF\Repository\Attachment $attachmentRepo */
		$attachmentRepo = \XF::repository('XF:Attachment');
		$attachmentRepo->addAttachmentsToContent($firstPosts, 'post');

		return $content;
	}
}