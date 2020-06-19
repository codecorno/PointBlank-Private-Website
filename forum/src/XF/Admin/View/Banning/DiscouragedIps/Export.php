<?php

namespace XF\Admin\View\Banning\DiscouragedIps;

class Export extends \XF\Mvc\View
{
	public function renderXml()
	{
		/** @var \DOMDocument $document */
		$document = $this->params['xml'];

		$this->response->setDownloadFileName('discouraged_ips.xml');

		return $document->saveXML();
	}
}