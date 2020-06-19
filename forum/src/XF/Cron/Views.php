<?php

namespace XF\Cron;

class Views
{
	public static function runViewUpdate()
	{
		$app = \XF::app();

		/** @var \XF\Repository\Thread $threadRepo */
		$threadRepo = $app->repository('XF:Thread');
		$threadRepo->batchUpdateThreadViews();

		/** @var \XF\Repository\Attachment $attachmentRepo */
		$attachmentRepo = $app->repository('XF:Attachment');
		$attachmentRepo->batchUpdateAttachmentViews();
	}
}