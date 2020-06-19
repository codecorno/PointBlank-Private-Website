<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\AbstractReply;

class Attachment extends AbstractController
{
	public function actionIndex(ParameterBag $params)
	{
		/** @var \XF\Entity\Attachment $attachment */
		$attachment = $this->em()->find('XF:Attachment', $params->attachment_id);
		if (!$attachment)
		{
			throw $this->exception($this->notFound());
		}

		if ($attachment->temp_hash)
		{
			$hash = $this->filter('hash', 'str');
			if ($attachment->temp_hash !== $hash)
			{
				return $this->noPermission();
			}
		}
		else
		{
			if (!$attachment->canView($error))
			{
				return $this->noPermission($error);
			}
		}

		if (!$this->filter('no_canonical', 'bool'))
		{
			$this->assertCanonicalUrl($this->buildLink('attachments', $attachment));
		}

		/** @var \XF\ControllerPlugin\Attachment $attachPlugin */
		$attachPlugin = $this->plugin('XF:Attachment');

		return $attachPlugin->displayAttachment($attachment);
	}

	public function actionUpload()
	{
		$type = $this->filter('type', 'str');
		$handler = $this->getAttachmentRepo()->getAttachmentHandler($type);
		if (!$handler)
		{
			return $this->noPermission();
		}

		$context = $this->filter('context', 'array-str');
		if (!$handler->canManageAttachments($context, $error))
		{
			return $this->noPermission($error);
		}

		$hash = $this->filter('hash', 'str');
		if (!$hash)
		{
			return $this->noPermission();
		}

		/** @var \XF\Attachment\Manipulator $manipulator */
		$class = \XF::extendClass('XF\Attachment\Manipulator');
		$manipulator = new $class($handler, $this->getAttachmentRepo(), $context, $hash);

		if ($this->isPost())
		{
			$json = [];

			$delete = $this->filter('delete', 'uint');
			if ($delete)
			{
				$manipulator->deleteAttachment($delete);
				$json['delete'] = $delete;
			}

			$uploadError = null;
			if ($manipulator->canUpload($uploadError))
			{
				$upload = $this->request->getFile('upload', false, false);
				if ($upload)
				{
					$attachment = $manipulator->insertAttachmentFromUpload($upload, $error);
					if (!$attachment)
					{
						return $this->error($error);
					}

					$json['attachment'] = [
						'attachment_id' => $attachment->attachment_id,
						'filename' => $attachment->filename,
						'file_size' => $attachment->file_size,
						'thumbnail_url' => $attachment->thumbnail_url,
						'is_video' => $attachment->is_video,
						'video_url' => $attachment->video_url,
						'link' => $attachment->is_video
							? $attachment->video_url
							: $this->buildLink('attachments', $attachment, [
								'hash' => $attachment->temp_hash
							])
					];
					$json['link'] = $json['attachment']['link'];

					$json = $handler->prepareAttachmentJson($attachment, $context, $json);
				}
			}
			else if ($uploadError)
			{
				return $this->error($uploadError);
			}

			$reply = $this->redirect($this->buildLink('attachments/upload', null, [
				'type' => $type,
				'context' => $context,
				'hash' => $hash
			]));
			$reply->setJsonParams($json);

			return $reply;
		}
		else
		{
			$uploadError = null;
			$canUpload = $manipulator->canUpload($uploadError);

			$viewParams = [
				'handler' => $handler,
				'constraints' => $manipulator->getConstraints(),

				'canUpload' => $canUpload,
				'uploadError' => $uploadError,
				'existing' => $manipulator->getExistingAttachments(),
				'new' => $manipulator->getNewAttachments(),

				'hash' => $hash,
				'type' => $type,
				'context' => $context
			];
			return $this->view('XF:Attachment\Upload', 'attachment_upload', $viewParams);
		}
	}

	/**
	 * @return \XF\Repository\Attachment
	 */
	protected function getAttachmentRepo()
	{
		return $this->repository('XF:Attachment');
	}

	public function updateSessionActivity($action, ParameterBag $params, AbstractReply &$reply) {}
}