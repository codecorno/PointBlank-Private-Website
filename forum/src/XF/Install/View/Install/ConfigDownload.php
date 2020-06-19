<?php

namespace XF\Install\View\Install;

class ConfigDownload extends \XF\Mvc\View
{
	public function renderRaw()
	{
		$this->response->header('Content-type', 'application/octet-stream', true);
		$this->response->setDownloadFileName('config.php');

		return $this->params['generated'];
	}
}