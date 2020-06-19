<?php

namespace XF\Admin\View\Error;

use XF\Util\File;
use XF\Util\Xml;

class Server extends \XF\Mvc\View
{
	public function renderHtml()
	{
		$e = isset($this->params['exception']) ? $this->params['exception'] : null;
		return $this->renderExceptionHtml($e);
	}

	public function renderJson()
	{
		$e = isset($this->params['exception']) ? $this->params['exception'] : null;
		$html = $this->renderExceptionHtml($e, $error);

		return [
			'exception' => $error,
			'errorHtml' => $html
		];
	}

	public function renderXml()
	{
		$e = isset($this->params['exception']) ? $this->params['exception'] : null;
		return $this->renderExceptionXml($e)->saveXML();
	}
}