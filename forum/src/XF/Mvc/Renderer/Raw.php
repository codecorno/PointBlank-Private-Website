<?php

namespace XF\Mvc\Renderer;

use XF\Mvc\Reply\AbstractReply;

class Raw extends AbstractRenderer
{
	protected function initialize()
	{
	}

	public function getResponseType()
	{
		return 'raw';
	}

	public function renderRedirect($url, $type, $message = '')
	{
		$this->setResponseCode($type == 'permanent' ? 301 : 303);
		$this->response->header('Location', $url);
	}

	public function renderMessage($message)
	{
		return '';
	}

	public function renderErrors(array $errors)
	{
		return '';
	}

	public function renderView($viewName, $templateName, array $params = [])
	{
		if (isset($params['innerContent']))
		{
			return $params['innerContent'];
		}

		$output = $this->renderViewObject($viewName, $templateName, $params);
		if ($output === null)
		{
			$output = '';
		}
		return $output;
	}
}