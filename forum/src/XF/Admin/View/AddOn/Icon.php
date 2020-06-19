<?php

namespace XF\Admin\View\AddOn;

class Icon extends \XF\Mvc\View
{
	public function renderRaw()
	{
		$this->response->setAttachmentFileParams($this->params['icon']);
		return $this->response->responseFile($this->params['icon']);
	}
}