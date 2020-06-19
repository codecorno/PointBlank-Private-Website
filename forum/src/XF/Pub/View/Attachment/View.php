<?php

namespace XF\Pub\View\Attachment;

class View extends \XF\Mvc\View
{
	public function renderRaw()
	{
		/** @var \XF\Entity\Attachment $attachment */
		$attachment = $this->params['attachment'];

		if (!empty($this->params['return304']))
		{
			$this->response
				->httpCode(304)
				->removeHeader('last-modified');

			return '';
		}

		$this->response
			->setAttachmentFileParams($attachment->filename, $attachment->extension)
			->header('ETag', '"' . $attachment->attach_date . '"');

		$resource = \XF::fs()->readStream($attachment->Data->getAbstractedDataPath());
		return $this->response->responseStream($resource, $attachment->file_size);
	}
}