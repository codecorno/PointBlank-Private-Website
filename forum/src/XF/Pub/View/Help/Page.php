<?php

namespace XF\Pub\View\Help;

class Page extends \XF\Mvc\View
{
	public function renderHtml()
	{
		$this->params['templateHtml'] = $this->renderTemplate(
			$this->params['templateName'],
			$this->params
		);
	}
}