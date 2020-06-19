<?php

namespace XF\Admin\View\User;

class Export extends \XF\Mvc\View
{
	public function renderXml()
	{
		/** @var \DOMDocument $document */
		$document = $this->params['xml'];

		$this->response->setDownloadFileName('user.xml');

		return $document->saveXML();
	}
}