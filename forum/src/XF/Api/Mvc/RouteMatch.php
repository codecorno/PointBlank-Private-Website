<?php

namespace XF\Api\Mvc;

class RouteMatch extends \XF\Mvc\RouteMatch
{
	protected $version;

	protected $requestMethod;

	/**
	 * @param mixed $version
	 */
	public function setVersion($version)
	{
		$this->version = $version;
	}

	/**
	 * @return int
	 */
	public function getVersion()
	{
		return intval($this->version);
	}

	public function setRequestMethod($method)
	{
		$this->requestMethod = $method;
	}

	public function getRequestMethod()
	{
		return $this->requestMethod;
	}
}