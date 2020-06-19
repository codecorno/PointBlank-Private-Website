<?php

namespace XF\Api\Mvc;

use XF\Http\Request;
use XF\Mvc\ParameterBag;

class Router extends \XF\Mvc\Router
{
	protected $defaultAction = '';

	public function routePreProcessApiPrefix(\XF\Mvc\Router $router, $path, RouteMatch $match, Request $request = null)
	{
		if (preg_match('#^api(?:/|$)(.*)$#i', $path, $matches))
		{
			$path = $matches[1];
			$match->setPathRewrite($path);
		}
		return $match;
	}

	public function routePreProcessApiVersion(\XF\Mvc\Router $router, $path, RouteMatch $match, Request $request = null)
	{
		if (preg_match('#^v(\d+)(?:/|$)(.*)$#i', $path, $matches))
		{
			$version = intval($matches[1]);
			$path = $matches[2];

			$match->setVersion($version);
			$match->setPathRewrite($path);
		}
		return $match;
	}

	public function routePreProcessApiResponseType(\XF\Mvc\Router $router, $path, RouteMatch $match, Request $request = null)
	{
		$match->setResponseType('api');
		return $match;
	}

	public function routeToController($path, Request $request = null)
	{
		$match = parent::routeToController($path, $request);

		// By convention, the API will not allow "prefix/1/act/ion" to match the action as "act/ion" to prevent
		// inconsistent URLs. If this format is needed, define an explicit match for "prefix/1/act/" as needed (with
		// an action of "ion").
		$action = $match->getAction();
		if (strpos($action, '/') !== false)
		{
			$match->setController('');
			$match->setAction('');
		}
		else
		{
			// Prepend the request method to the action. This is done here instead of in dispatching as doing it there
			// makes it difficult to reroute to controllers (such as for errors).

			if ($request)
			{
				$requestMethod = preg_replace('#[^a-z]#', '', strtolower($request->getRequestMethod()));
				if (!$requestMethod)
				{
					$requestMethod = 'get';
				}
			}
			else
			{
				$requestMethod = 'get';
			}

			$match->setAction($requestMethod . $match->getAction());
			if ($match instanceof \XF\Api\Mvc\RouteMatch)
			{
				$match->setRequestMethod($requestMethod);
			}
		}

		return $match;
	}

	protected function manipulateLinkPathInternal($prefix, &$path, &$data, array &$parameters)
	{
		// This applies the API route convention that a "-" path identifies automatic switching to routes that take
		// a param without us having to explicitly call "xyz/-" when building a link.
		// This also assumes that the route is define properly, making the parameter part of it required in the matching
		// process.

		$prefixRoutes = $this->routes[$prefix];
		if (isset($prefixRoutes['-']) && $data && (!strlen($path) || $path[0] != '-'))
		{
			$path = '-/' . $path;
		}
	}

	/**
	 * @param string $controller
	 * @param string $action
	 * @param array|ParameterBag $params
	 * @param string $responseType
	 *
	 * @return RouteMatch
	 */
	public function getNewRouteMatch($controller = '', $action = '', $params = [], $responseType = 'html')
	{
		return new RouteMatch($controller, $action, $params, $responseType);
	}
}