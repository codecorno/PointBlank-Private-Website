<?php

namespace XF\Admin\View\BbCode;

class Export extends \XF\Mvc\View
{
	public function renderXml()
	{
		/** @var \DOMDocument $document */
		$document = $this->params['xml'];

		$this->response->setDownloadFileName('bb_codes.xml');

		return $document->saveXML();
	}
}