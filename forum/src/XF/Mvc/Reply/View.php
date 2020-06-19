<?php

namespace XF\Mvc\Reply;

use XF\Util\Arr;

class View extends AbstractReply
{
	protected $viewClass = '';
	protected $templateName = '';
	protected $params = [];

	public function __construct($viewClass, $templateName, array $params = [])
	{
		$this->setViewClass($viewClass);
		$this->setTemplateName($templateName);
		$this->setParams($params, false);
	}

	public function getViewClass()
	{
		return $this->viewClass;
	}

	public function setViewClass($string)
	{
		$this->viewClass = $string;
	}

	public function getTemplateName()
	{
		return $this->templateName;
	}

	public function setTemplateName($string)
	{
		$this->templateName = $string;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function getParam($key)
	{
		return isset($this->params[$key]) ? $this->params[$key] : null;
	}

	public function setParams(array $params, $merge = true)
	{
		if ($merge)
		{
			$this->params = Arr::mapMerge($this->params, $params);
		}
		else
		{
			$this->params = $params;
		}
	}

	public function setParam($name, $value)
	{
		$this->params[$name] = $value;
	}
}