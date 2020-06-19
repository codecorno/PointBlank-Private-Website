<?php

namespace XF\Admin\View\Smilie;

class Export extends \XF\Mvc\View
{
	public function renderXml()
	{
		/** @var \DOMDocument $document */
		$document = $this->params['xml'];

		$this->response->setDownloadFileName('smilies.xml');

		return $document->saveXml();
	}
}