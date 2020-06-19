<?php

namespace XF\Admin\View\Banning\Ips;

class Export extends \XF\Mvc\View
{
	public function renderXml()
	{
		/** @var \DOMDocument $document */
		$document = $this->params['xml'];

		$this->response->setDownloadFileName('banned_ips.xml');

		return $document->saveXML();
	}
}