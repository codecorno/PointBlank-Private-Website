<?php

namespace XF\Admin\View\Language;

class Export extends \XF\Mvc\View
{
	public function renderXml()
	{
		$this->response->setDownloadFileName($this->params['filename']);

		/** @var \DOMDocument $document */
		$document = $this->params['xml'];
		return $document->saveXml();
	}
}