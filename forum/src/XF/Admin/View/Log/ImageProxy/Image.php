<?php

namespace XF\Admin\View\Log\ImageProxy;

class Image extends \XF\Mvc\View
{
	public function renderRaw()
	{
		/** @var \XF\Entity\ImageProxy $image */
		$image = $this->params['image'];

		$proxyController = \XF::app()->proxy()->controller();
		$proxyController->applyImageResponseHeaders($this->response, $image, null);

		if ($image->isPlaceholder())
		{
			return $this->response->responseFile($image->getPlaceholderPath());
		}
		else
		{
			$resource = \XF::fs()->readStream($image->getAbstractedImagePath());
			return $this->response->responseStream($resource, $image->file_size);
		}
	}
}