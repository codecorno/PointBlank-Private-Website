<?php

namespace XF\Mvc;

use XF\Http\Request;

class Router
{
	protected $linkFormatter = null;
	protected $routes = [];
	protected $routePreProcessors = [];
	protected $routeFiltersIn = [];
	protected $routeFiltersOut = [];
	protected $routeFiltersOutRegex = [];

	protected $indexRoute = 'index';
	protected $defaultAction = 'index';
	protected $includeTitleInUrls = true;
	protected $romanizeUrls = false;

	/**
	 * @var \Closure|null
	 */
	protected $pather = null;

	protected $stringCache = [];

	public function __construct($linkFormatter = null, array $routes = [])
	{
		$this->linkFormatter = $linkFormatter;
		$this->routes = $routes;
	}

	public function setLinkFormatter($linkFormatter)
	{
		$this->linkFormatter = $linkFormatter;
	}

	public function getLinkFormatter()
	{
		return $this->linkFormatter;
	}

	public function setPather(\Closure $pather = null)
	{
		$this->pather = $pather;
	}

	public function getPather()
	{
		return $this->pather;
	}

	public function setRouteFilters(array $routeFiltersIn, array $routeFiltersOut)
	{
		$this->routeFiltersIn = $routeFiltersIn;
		$this->routeFiltersOut = $routeFiltersOut;
	}

	public function getRouteFiltersIn()
	{
		return $this->routeFiltersIn;
	}

	public function getRouteFiltersOut()
	{
		return $this->routeFiltersOut;
	}

	public function setIndexRoute($indexRoute)
	{
		$this->indexRoute = $indexRoute;
	}

	public function getIndexRoute()
	{
		return $this->indexRoute;
	}

	public function setIncludeTitleInUrls($includeTitleInUrls)
	{
		$this->includeTitleInUrls = $includeTitleInUrls;
	}

	public function getIncludeTitlesInUrls()
	{
		return $this->includeTitleInUrls;
	}

	public function setRomanizeUrls($romanizeUrls)
	{
		$this->romanizeUrls = $romanizeUrls;
	}

	public function getRomanizeUrls()
	{
		return $this->romanizeUrls;
	}

	public function addRoute($prefix, $subSection, $route)
	{
		$this->routes[$prefix][$subSection] = $route;
	}

	public function getRoutes()
	{
		return $this->routes;
	}

	public function overrideRoute($prefix, $subSection, $controller, $action)
	{
		if (!isset($this->routes[$prefix][$subSection]))
		{
			return false;
		}

		$this->routes[$prefix][$subSection]['controller'] = $controller;
		$this->routes[$prefix][$subSection]['force_action'] = $action;

		return true;
	}

	public function addRoutePreProcessor($name, $preProcessor, $beginning = false)
	{
		if ($beginning)
		{
			$this->routePreProcessors = [$name => $preProcessor] + $this->routePreProcessors;
		}
		else
		{
			$this->routePreProcessors[$name] = $preProcessor;
		}
	}

	public function getRoutePreProcessors()
	{
		return $this->routePreProcessors;
	}

	public function routePreProcessRouteFilter(Router $router, $path, RouteMatch $match, Request $request = null)
	{
		if (!$this->routeFiltersIn)
		{
			return $match;
		}

		foreach ($this->routeFiltersIn AS $filter)
		{
			list($from, $to) = $this->routeFilterToRegex(
				urldecode($filter['replace_route']), urldecode($filter['find_route'])
			);

			$newRoutePath = preg_replace($from, $to, $path);
			if ($newRoutePath != $path)
			{
				$match->setPathRewrite($newRoutePath);
				return $match;
			}
		}

		return $match;
	}

	public function routePreProcessExtension(Router $router, $path, RouteMatch $match, Request $request = null)
	{
		$lastDot = strrpos($path, '.');
		if ($lastDot === false || $lastDot == 0)
		{
			return false;
		}

		$suffix = substr($path, $lastDot + 1);
		if (!preg_match('/^[a-zA-Z0-9_-]+$/', $suffix) || !preg_match('/[a-z]/i', $suffix))
		{
			return false;
		}

		$match->setResponseType($suffix);
		$match->setPathRewrite(substr($path, 0, $lastDot));

		return $match;
	}

	public function routePreProcessResponseType(Router $router, $path, RouteMatch $match, Request $request = null)
	{
		if (!$request)
		{
			return false;
		}

		$responseType = $request->filter('_xfResponseType', 'str');
		if (!$responseType)
		{
			return false;
		}

		$match->setResponseType($responseType);
		return $match;
	}

	public function routeToController($path, Request $request = null)
	{
		$match = $this->getNewRouteMatch();
		$path = urldecode($path);

		if (strlen($path) && strpos($path, '/') === false)
		{
			$path .= '/';
		}

		foreach ($this->routePreProcessors AS $preProcessor)
		{
			if (!is_callable($preProcessor))
			{
				continue;
			}

			/** @var RouteMatch $newMatch */
			$newMatch = call_user_func($preProcessor, $this, $path, $match, $request);
			if ($newMatch)
			{
				if ($newMatch->getPathRewrite() !== null)
				{
					$path = $newMatch->getPathRewrite();
					$newMatch->setPathRewrite(null);
				}
				$match = $newMatch;
			}
		}

		if ($path === '')
		{
			$path = 'index';
		}

		$parts = explode('/', $path, 2);
		$prefix = $parts[0];
		$suffix = isset($parts[1]) ? $parts[1] : '';

		if (!isset($this->routes[$prefix]))
		{
			// return this to maintain the response type
			return $match;
		}

		$possibleRoutes = $this->routes[$prefix];
		$matched = false;

		foreach ($possibleRoutes AS $route)
		{
			$newMatch = $this->suffixMatchesRoute($suffix, $route, $match, $request);
			if ($newMatch)
			{
				$match = $newMatch;
				$matched = true;
				break;
			}
		}

		if (!$matched && isset($possibleRoutes['']))
		{
			$route = $possibleRoutes[''];

			$match->setController($route['controller']);
			if (!empty($route['force_action']))
			{
				$match->setAction($route['force_action']);
			}
			else
			{
				$match->setAction(strlen($suffix) ? $suffix : $this->defaultAction);
			}

			if (isset($route['context']))
			{
				$match->setSectionContext($route['context']);
			}
		}

		return $match;
	}

	protected function suffixMatchesRoute($suffix, array $route, RouteMatch $match, Request $request = null)
	{
		// TODO: callback based processing

		$matchRegex = $this->generateMatchRegexInner($route['format'], '#');
		if (!preg_match('#^' . $matchRegex . '#i', $suffix, $textMatch))
		{
			return false;
		}

		$matchText = $textMatch[0];
		$trail = substr($suffix, strlen($matchText));

		$action = isset($textMatch['_action']) ? $textMatch['_action'] : '';
		$params = [];

		unset($textMatch['_action']);

		foreach ($textMatch AS $key => $value)
		{
			if (is_string($key) && strlen($value))
			{
				$params[$key] = $value;
			}
		}

		$action .= rtrim(strval($trail), '/');
		if (!empty($route['action_prefix']))
		{
			$action = $route['action_prefix'] . $action;
		}

		if (!strlen($action))
		{
			$action = $this->defaultAction;
		}

		if (!empty($route['force_action']))
		{
			$action = $route['force_action'];
		}

		$match->setController($route['controller']);
		$match->setAction($action);
		$match->setParams($params);
		if (isset($route['context']))
		{
			$match->setSectionContext($route['context']);
		}

		return $match;
	}

	public function generateMatchRegexInner($format, $wrapper = '#')
	{
		$matchRegex = str_replace($wrapper, '\\' . $wrapper, $format);

		$matchRegex = preg_replace_callback(
			'#:(\+)?int(?:_p)?<([a-zA-Z0-9_]+)(?:,[a-zA-Z0-9_]+)?>/?#',
			function ($match)
			{
				$mainMatch = '(?:(?:[^/]*\.)?(?P<' . $match[2] . '>[0-9]+)(?:/|$))';
				return $match[1] ? $mainMatch : "{$mainMatch}?";
			},
			$matchRegex
		);

		$matchRegex = preg_replace_callback(
			'#:(\+)?str(?:_p)?<([a-zA-Z0-9_]+)>/?#',
			function ($match)
			{
				$mainMatch = '(?:(?P<' . $match[2] . '>[a-zA-Z0-9_-]+)/)';
				return $match[1] ? $mainMatch : "{$mainMatch}?";
			},
			$matchRegex
		);

		$matchRegex = preg_replace_callback(
			'#:(\+)?str_int<([a-zA-Z0-9_]+),([a-zA-Z0-9_]+)(?:,[a-zA-Z0-9_]+)?>/?#',
			function ($match)
			{
				$mainMatch = '(?:(?:(?:(?:[^/]*\.)?(?P<' . $match[3] . '>[0-9]+))|-|(?P<' . $match[2] . '>[a-zA-Z0-9_-]+))(?:/|$))';
				return $match[1] ? $mainMatch : "{$mainMatch}?";
			},
			$matchRegex
		);

		$matchRegex = preg_replace(
			'#:page<([a-zA-Z0-9_]+)>/?#',
			'(?:page-(?P<$1>[0-9]+)(?:/|$))?',
			$matchRegex
		);
		$matchRegex = preg_replace(
			'#:page/?#',
			'(?:page-(?P<page>[0-9]+)(?:/|$))?',
			$matchRegex
		);

		$matchRegex = str_replace(
			':action',
			'(?P<_action>[^/]*)',
			$matchRegex
		);

		$matchRegex = preg_replace_callback(
			'#:(\+)?any<([a-zA-Z0-9_]+)>/?#',
			function ($match)
			{
				if ($match[1])
				{
					return '(?P<' . $match[2] . '>.+)';
				}
				else
				{
					return '(?P<' . $match[2] . '>.*)';
				}
			},
			$matchRegex
		);

		return $matchRegex;
	}

	public function buildLink($link, $data = null, array $parameters = [])
	{
		if (is_array($link))
		{
			$tempLink = $link;
			$link = $tempLink[0];
			if (!$parameters)
			{
				$parameters = $tempLink[1];
			}
		}

		$parts = explode(':', $link);
		if (isset($parts[1]))
		{
			$modifier = $parts[0];
			$link = $parts[1];
		}
		else
		{
			$modifier = null;
		}

		return $this->buildFinalUrl(
			$modifier,
			$this->buildLinkPath($link, $data, $parameters),
			$parameters
		);
	}

	public function buildLinkPath($link, $data = null, array &$parameters = [])
	{
		if (!$link || $link == 'index')
		{
			return '';
		}

		$parts = explode('/', $link, 2);
		$prefix = $parts[0];
		if (!isset($this->routes[$prefix]))
		{
			return $link;
		}

		$this->manipulateLinkPathInternal($prefix, $parts[1], $data, $parameters);

		$sections = isset($parts[1]) ? explode('/', $parts[1]) : [''];
		$action = '';
		$prefixRoutes = $this->routes[$prefix];

		for ($totalSections = count($sections), $i = $totalSections; $i > 0; $i--)
		{
			$possibleSection = implode('/', array_slice($sections, 0, $i));

			if (isset($prefixRoutes[$possibleSection]))
			{
				return $this->buildRouteUrl(
					$prefix, $prefixRoutes[$possibleSection], $action, $data, $parameters
				);
			}

			if ($i == $totalSections)
			{
				$action = $sections[$i - 1];
			}
			else
			{
				$action = $sections[$i - 1] . '/' . $action;
			}
		}

		if (isset($prefixRoutes['']))
		{
			return $this->buildRouteUrl(
				$prefix, $prefixRoutes[''], $action, $data, $parameters
			);
		}

		return $link;
	}

	protected function manipulateLinkPathInternal($prefix, &$path, &$data, array &$parameters)
	{

	}

	public function prepareStringForUrl($string, $romanizeOverride = null)
	{
		$string = strval($string);
		$romanize = $romanizeOverride === null ? $this->romanizeUrls : (bool)$romanizeOverride;
		$cacheKey = $string . ($romanize ? '|r' : '');

		if (isset($this->stringCache[$cacheKey]))
		{
			return $this->stringCache[$cacheKey];
		}

		if ($romanize)
		{
			$string = utf8_romanize(utf8_deaccent($string));

			$originalString = $string;

			// Attempt to transliterate remaining UTF-8 characters to their ASCII equivalents
			$string = @iconv('UTF-8', 'ASCII//TRANSLIT', $string);
			if (!$string)
			{
				// iconv failed so forget about it
				$string = $originalString;
			}
		}

		$string = strtr(
			$string,
			'`!"$%^&*()-+={}[]<>;:@#~,./?|' . "\r\n\t\\",
			'                             ' . '    '
		);
		$string = strtr($string, ['"' => '', "'" => '']);

		if ($romanize)
		{
			$string = preg_replace('/[^a-zA-Z0-9_ -]/', '', $string);
		}

		$string = preg_replace('/[ ]+/', '-', trim($string));
		$string = strtr($string, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
		$string = urlencode($string);

		$this->stringCache[$cacheKey] = $string;

		return $string;
	}

	protected function buildRouteUrl($prefix, array $route, $action, $data = null, array &$parameters = [])
	{
		if (!empty($route['build_callback']))
		{
			$output = call_user_func_array(
				[$route['build_callback'][0], $route['build_callback'][1]],
				[&$prefix, &$route, &$action, &$data, &$parameters, $this]
			);
			if (is_string($output) || $output instanceof RouteBuiltLink)
			{
				return $output;
			}
		}

		$url = $route['format'];

		$url = preg_replace_callback(
			'#:(?:\+)?int(_p)?<([a-zA-Z0-9_]+)(?:,([a-zA-Z0-9_]+))?>(/?)#',
			function($match) use ($data, &$parameters)
			{
				$inParams = !empty($match[1]);
				$idKey = $match[2];
				$stringKey = $match[3];
				$trailingSlash = $match[4];

				$search = $inParams ? $parameters : $data;

				if ($search && isset($search[$idKey]))
				{
					$idValue = intval($search[$idKey]);

					if ($inParams)
					{
						unset($parameters[$idKey]);
					}

					if ($stringKey && isset($search[$stringKey]))
					{
						$string = strval($search[$stringKey]);

						if ($inParams)
						{
							unset($parameters[$stringKey]);
						}

						if ($this->includeTitleInUrls)
						{
							$string = $this->prepareStringForUrl($string);
							if (strlen($string))
							{
								return $string . "." . $idValue . $trailingSlash;
							}
						}
					}

					return $idValue . $trailingSlash;
				}

				return '';
			},
			$url
		);
		$url = preg_replace_callback(
			'#:(?:\+)?str(_p)?<([a-zA-Z0-9_]+)>(/?)#',
			function($match) use ($data, &$parameters)
			{
				$inParams = !empty($match[1]);
				$stringKey = $match[2];
				$trailingSlash = $match[3];

				$search = $inParams ? $parameters : $data;

				if ($search && isset($search[$stringKey]))
				{
					$key = strval($search[$stringKey]);

					if ($inParams)
					{
						unset($parameters[$stringKey]);
					}

					if (strlen($key))
					{
						return $key . $trailingSlash;
					}
				}

				return '';
			},
			$url
		);
		$url = preg_replace_callback(
			'#:(?:\+)?str_int<([a-zA-Z0-9_]+),([a-zA-Z0-9_]+)(?:,([a-zA-Z0-9_]+))?>(/?)#',
			function($match) use ($data, $action)
			{
				$stringKey = $match[1];
				$intKey = $match[2];
				$intStringKey = $match[3];
				$trailingSlash = $match[4];

				if ($data === '-')
				{
					return '-' . $trailingSlash;
				}

				if ($data && isset($data[$stringKey]))
				{
					$key = strval($data[$stringKey]);
					if (strlen($key))
					{
						return $key . $trailingSlash;
					}
				}

				if ($data && isset($data[$intKey]))
				{
					$idValue = intval($data[$intKey]);
					if ($intStringKey && isset($data[$intStringKey]) && $this->includeTitleInUrls)
					{
						$string = strval($data[$intStringKey]);
						$string = $this->prepareStringForUrl($string);
						if (strlen($string))
						{
							return $string . "." . $idValue . $trailingSlash;
						}
					}

					return $idValue . $trailingSlash;
				}

				return strlen($action) ? '-' . $trailingSlash : '';
			},
			$url
		);
		$url = preg_replace_callback(
			'#:page(<([a-zA-Z0-9_]+)>)?(/?)#',
			function($match) use ($data, &$parameters)
			{
				$pageKey = !empty($match[2]) ? $match[2] : 'page';
				$trailingSlash = $match[3];

				if (isset($parameters[$pageKey]))
				{
					$page = $parameters[$pageKey];
					unset($parameters[$pageKey]);
					if ($page === '%page%')
					{
						return "page-%page%$trailingSlash";
					}
					else
					{
						$page = intval($page);
						if ($page > 1)
						{
							return "page-$page$trailingSlash";
						}
					}
				}

				return '';
			},
			$url
		);
		$url = preg_replace_callback(
			'#:action#',
			function($match) use (&$action)
			{
				$thisAction = $action;
				$action = '';
				return $thisAction;
			},
			$url
		);
		$url = preg_replace_callback(
			'#:(?:\+)?any<([a-zA-Z0-9_]+)>(/?)#',
			function($match) use ($data, &$parameters)
			{
				$stringKey = $match[1];
				$trailingSlash = $match[2];

				if ($data && isset($data[$stringKey]))
				{
					$key = strval($data[$stringKey]);

					if (strlen($key))
					{
						return $key . $trailingSlash;
					}
				}

				return '';
			},
			$url
		);

		$url = str_replace('?', '', $url);
		if ($url && $action)
		{
			if (substr($url, -1) != '/')
			{
				$url .= '/';
			}
			$url .= $action;
		}
		else if ($action)
		{
			$url = $action;
		}

		$routeUrl = $prefix . '/' . $url;
		if ($this->indexRoute && $routeUrl === $this->indexRoute)
		{
			$routeUrl = '';
		}
		else
		{
			$routeUrl = $this->applyRouteFilterToUrl($prefix, $routeUrl);
		}

		return $routeUrl;
	}

	public function applyRouteFilterToUrl($prefix, $routeUrl)
	{
		$filters = $this->routeFiltersOut;

		if (isset($filters[$prefix]))
		{
			if (!isset($this->routeFiltersOutRegex[$prefix]))
			{
				$regexes = [];

				foreach ($filters[$prefix] AS $filter)
				{
					list($from, $to) = $this->routeFilterToRegex(
						$filter['find_route'], $filter['replace_route']
					);

					$regexes[] = ['from' => $from, 'to' => $to];
				}

				$this->routeFiltersOutRegex[$prefix] = $regexes;
			}

			foreach ($this->routeFiltersOutRegex[$prefix] AS $filter)
			{
				$newLink = preg_replace($filter['from'], $filter['to'], $routeUrl);
				if ($newLink != $routeUrl)
				{
					$routeUrl = $newLink;
					break;
				}
			}
		}

		return $routeUrl;
	}

	public function routeFilterToRegex($from, $to)
	{
		$to = strtr($to, ['\\' => '\\\\', '$' => '\\$']);

		$findReplacements = [];
		$replacementChr = chr(26);

		$varMatches = preg_match_all('/\{([a-z0-9_]+)(:([^}]+))?\}/i', $from, $matches, PREG_SET_ORDER);
		foreach ($matches AS $i => $match)
		{
			$placeholder = $replacementChr . $i . $replacementChr;

			if (!empty($match[3]))
			{
				switch ($match[3])
				{
					case 'digit': $replace = '(\d+)'; break;
					case 'string': $replace = '([^/.]+)'; break;
					default: $replace = '([^/]*)';
				}
			}
			else
			{
				$replace = '([^/]*)';
			}

			$findReplacements[$placeholder] = $replace;

			$from = str_replace($match[0], $placeholder, $from);
			$to = str_replace($match[0], '$' . ($i + 1), $to);
		}

		if (substr($from, -1) == '/' && substr($to, -1) == '/')
		{
			// both end in slashes, make the last slash optional
			$matchId = $varMatches;
			$placeholder = $replacementChr . $matchId . $replacementChr;
			$findReplacements[$placeholder] = '(/|$)';
			$from = substr($from, 0, -1) . $placeholder;
			$to = substr($to, 0, -1) . '$' . ($matchId + 1);
		}

		$from = preg_quote($from, '#');

		foreach ($findReplacements AS $findPlaceholder => $findReplacement)
		{
			$from = str_replace($findPlaceholder, $findReplacement, $from);
		}

		return ['#^' . $from . '#', $to];
	}

	public function buildFinalUrl($modifier, $routeUrl, array $parameters = [])
	{
		$queryString = $parameters ? $this->buildQueryString($parameters) : '';

		if ($routeUrl instanceof RouteBuiltLink)
		{
			$url = $routeUrl->getFinalLink($this, $modifier, $queryString);
		}
		else
		{
			$url = call_user_func($this->linkFormatter, $routeUrl, $queryString);
			$url = $this->applyPather($url, $modifier);
		}

		return $url;
	}

	public function applyPather($url, $modifier = '')
	{
		if ($this->pather)
		{
			$pather = $this->pather;
			$url = $pather($url, $modifier);
		}

		if ($url === '')
		{
			$url = '.';
		}

		return $url;
	}

	public function buildQueryString(array $elements, $prefix = '')
	{
		$output = [];

		foreach ($elements AS $name => $value)
		{
			if (is_array($value))
			{
				if (!$value)
				{
					continue;
				}

				$encodedName = ($prefix ? $prefix . '[' . urlencode($name) . ']' : urlencode($name));
				$childOutput = $this->buildQueryString($value, $encodedName);
				if ($childOutput !== '')
				{
					$output[] = $childOutput;
				}
			}
			else
			{
				if ($value === null || $value === false || $value === '')
				{
					continue;
				}

				$value = strval($value);

				if ($prefix)
				{
					// part of an array
					$output[] = $prefix . '[' . urlencode($name) . ']=' . urlencode($value);
				}
				else
				{
					$output[] = urlencode($name) . '=' . urlencode($value);
				}
			}
		}

		return implode('&', $output);
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