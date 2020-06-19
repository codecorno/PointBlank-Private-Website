<?php

namespace XF\Pub\View\Misc;

use XF\Mvc\View;

class AcceptTerms extends View
{
	public function renderHtml()
	{
		if (isset($this->params['templateName']))
		{
			$this->params['templateHtml'] = $this->renderTemplate(
				$this->params['templateName'],
				$this->params
			);
		}
	}
}