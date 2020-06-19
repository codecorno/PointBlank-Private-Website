<?php

namespace XF\Mvc\Reply;

use XF\Util\Arr;

abstract class AbstractReply
{
	protected $responseCode = 200;
	protected $responseType = null;
	protected $sectionContext = null;
	protected $controllerClass = null;
	protected $action = null;
	protected $pageParams = [];
	protected $jsonParams = [];
	protected $viewOptions = [];
	protected $containerKey;
	protected $contentKey;

	public function getResponseCode()
	{
		return $this->responseCode;
	}

	public function setResponseCode($code)
	{
		$code = intval($code);
		if (!$code)
		{
			throw new \InvalidArgumentException("Invalid response code");
		}

		$this->responseCode = $code;
	}

	public function getResponseType()
	{
		return $this->responseType;
	}

	public function setResponseType($type)
	{
		$this->responseType = strval($type);
	}

	public function getSectionContext()
	{
		return $this->sectionContext;
	}

	public function setSectionContext($context)
	{
		$this->sectionContext = strval($context);
	}

	public function getControllerClass()
	{
		return $this->controllerClass;
	}

	public function setControllerClass($class)
	{
		$this->controllerClass = $class;
	}

	public function getAction()
	{
		return $this->action;
	}

	public function setAction($action)
	{
		$this->action = $action;
	}

	public function getPageParams()
	{
		return $this->pageParams;
	}

	public function setPageParams(array $params, $merge = true)
	{
		if ($merge)
		{
			$this->pageParams = Arr::mapMerge($this->pageParams, $params);
		}
		else
		{
			$this->pageParams = $params;
		}
	}

	public function setPageParam($name, $value)
	{
		$this->pageParams[$name] = $value;
	}

	public function getJsonParams()
	{
		return $this->jsonParams;
	}

	public function setJsonParams(array $params, $merge = true)
	{
		if ($merge)
		{
			$this->jsonParams = Arr::mapMerge($this->jsonParams, $params);
		}
		else
		{
			$this->jsonParams = $params;
		}
	}

	public function setJsonParam($name, $value)
	{
		$this->jsonParams[$name] = $value;
	}

	public function getViewOptions()
	{
		return $this->viewOptions;
	}

	public function getViewOption($name)
	{
		return isset($this->viewOptions[$name]) ? $this->viewOptions[$name] : null;
	}

	public function setViewOptions(array $params, $merge = true)
	{
		if ($merge)
		{
			$this->viewOptions = Arr::mapMerge($this->viewOptions, $params);
		}
		else
		{
			$this->viewOptions = $params;
		}
	}

	public function setViewOption($name, $value)
	{
		$this->viewOptions[$name] = $value;
	}

	public function setContainerKey($containerKey)
	{
		$this->containerKey = $containerKey;
	}

	public function getContainerKey()
	{
		return $this->containerKey;
	}

	public function setContentKey($contentKey)
	{
		$this->contentKey = $contentKey;
	}

	public function getContentKey()
	{
		return $this->contentKey;
	}
}