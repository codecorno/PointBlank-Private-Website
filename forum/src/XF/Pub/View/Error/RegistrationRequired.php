<?php

namespace XF\Pub\View\Error;

use XF\Util\File;
use XF\Util\Xml;

class RegistrationRequired extends \XF\Mvc\View
{
	public function renderJson()
	{
		$html = $this->renderTemplate($this->templateName, $this->params);

		return [
			'status' => 'error',
			'errors' => [$this->params['error']],
			'errorHtml' => $this->renderer->getHtmlOutputStructure($html)
		];
	}
}