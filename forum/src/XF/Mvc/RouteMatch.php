<?php

namespace XF\Mvc;

use XF\Util\Arr;

class RouteMatch
{
	protected $controller = '';
	protected $action = '';
	protected $params = [];

	protected $responseType = 'html';

	/**
	 * @var string|null
	 */
	protected $sectionContext = null;

	protected $pathRewrite = null;

	public function __construct($controller = '', $action = '', $params = [], $responseType = 'html')
	{
		$this->controller = $controller;
		$this->action = $action;

		if ($params instanceof ParameterBag)
		{
			$params = $params->params();
		}
		$this->params = (array)$params;

		$this->responseType = $responseType;
	}

	/**
	 * @param string $action
	 */
	public function setAction($action)
	{
		$this->action = $action;
	}

	/**
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @param string $controller
	 */
	public function setController($controller)
	{
		$this->controller = $controller;
	}

	/**
	 * @return string
	 */
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * @param string $responseType
	 */
	public function setResponseType($responseType)
	{
		$this->responseType = $responseType;
	}

	/**
	 * @return string
	 */
	public function getResponseType()
	{
		return $this->responseType;
	}

	/**
	 * @param string|null $sectionContext
	 */
	public function setSectionContext($sectionContext)
	{
		$this->sectionContext = $sectionContext;
	}

	/**
	 * @return string|null
	 */
	public function getSectionContext()
	{
		return $this->sectionContext;
	}

	/**
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * @return ParameterBag
	 */
	public function getParameterBag()
	{
		return new ParameterBag($this->params);
	}

	/**
	 * @param array|ParameterBag $params
	 * @param bool $merge
	 */
	public function setParams($params, $merge = true)
	{
		if ($params instanceof ParameterBag)
		{
			$params = $params->params();
		}

		if ($merge)
		{
			$this->params = Arr::mapMerge($this->params, $params);
		}
		else
		{
			$this->params = $params;
		}
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 */
	public function setParam($name, $value)
	{
		$this->params[$name] = $value;
	}

	/**
	 * @param null|string $pathRewrite
	 */
	public function setPathRewrite($pathRewrite)
	{
		$this->pathRewrite = $pathRewrite;
	}

	/**
	 * @return null|string
	 */
	public function getPathRewrite()
	{
		return $this->pathRewrite;
	}
}