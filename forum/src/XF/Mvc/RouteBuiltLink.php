<?php

namespace XF\Mvc;

class RouteBuiltLink
{
	protected $link;
	protected $allowPather;

	public function __construct($link, $allowPather = true)
	{
		$this->link = $link;
		$this->allowPather = $allowPather;
	}

	public function getLink()
	{
		return $this->link;
	}

	public function setLink($link)
	{
		$this->link = $link;
	}

	public function getFinalLink(Router $router, $modifier, $queryString)
	{
		if ($this->link instanceof \Closure)
		{
			$link = $this->link;
			return $link($router, $modifier, $queryString);
		}

		if (strlen($queryString))
		{
			$replace = [
				'{qs}' => $queryString,
				'{?qs}' => "?$queryString",
				'{&qs}' => "&$queryString"
			];
		}
		else
		{
			$replace = [
				'{qs}' => '',
				'{?qs}' => '',
				'{&qs}' => ''
			];
		}

		$url = strtr($this->link, $replace);

		if ($this->allowPather)
		{
			$url = $router->applyPather($url, $modifier);
		}

		return $url;
	}
}