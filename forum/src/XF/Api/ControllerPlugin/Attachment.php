<?php

namespace XF\Api\ControllerPlugin;

use XF\Entity\ReactionTrait;
use XF\Mvc\Entity\Entity;
use XF\Mvc\FormAction;

class Attachment extends AbstractPlugin
{
	public function uploadFile(
		\XF\Http\Upload $upload, \XF\Attachment\AbstractHandler $handler, array $context, $tempHash
	)
	{
		/** @var \XF\Repository\Attachment $attachmentRepo */
		$attachmentRepo = $this->repository('XF:Attachment');

		/** @var \XF\Attachment\Manipulator $manipulator */
		$class = \XF::extendClass('XF\Attachment\Manipulator');
		$manipulator = new $class($handler, $attachmentRepo, $context, $tempHash);

		if (!$manipulator->canUpload($uploadError))
		{
			throw $this->exception($this->error($uploadError));
		}

		$attachment = $manipulator->insertAttachmentFromUpload($upload, $error);
		if (!$attachment)
		{
			throw $this->exception($this->error($error));
		}

		return $attachment;
	}
}