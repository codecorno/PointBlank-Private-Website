<?php

namespace XF\Mvc\Renderer;

class Rss extends Xml
{
	protected function initialize()
	{
		parent::initialize();
		$this->response->contentType('application/rss+xml');
	}

	public function getResponseType()
	{
		return 'rss';
	}
}