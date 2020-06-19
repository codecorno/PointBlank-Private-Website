<?php

namespace XF\Http;

class Request
{
	/**
	 * @var \XF\InputFilterer
	 */
	protected $filterer;

	protected $input;
	protected $files;
	protected $cookie;
	protected $server;

	protected $skipLogKeys = ['_xfToken'];

	protected $cookiePrefix = '';

	public static $googleIps = [
		'v4' => [
			'35.190.247.0/24',
			'35.191.0.0/16',
			'64.233.160.0/19',
			'66.102.0.0/20',
			'66.249.80.0/20',
			'72.14.192.0/18',
			'74.125.0.0/16',
			'108.177.8.0/21',
			'108.177.96.0/19',
			'130.211.0.0/22',
			'172.217.0.0/19',
			'172.217.32.0/20',
			'172.217.128.0/19',
			'172.217.160.0/20',
			'172.217.192.0/19',
			'173.194.0.0/16',
			'209.85.128.0/17',
			'216.58.192.0/19',
			'216.239.32.0/19'
		],
		'v6' => [
			'2a00:1450:4000::/36',
			'2c0f:fb50:4000::/36',
			'2001:4860:4000::/36',
			'2404:6800:4000::/36',
			'2607:f8b0:4000::/36',
			'2800:3f0:4000::/36'
		]
	];

	public static $cloudFlareIps = [
		'v4' => [
			'103.21.244.0/22',
			'103.22.200.0/22',
			'103.31.4.0/22',
			'104.16.0.0/12',
			'108.162.192.0/18',
			'131.0.72.0/22',
			'141.101.64.0/18',
			'162.158.0.0/15',
			'172.64.0.0/13',
			'173.245.48.0/20',
			'188.114.96.0/20',
			'190.93.240.0/20',
			'197.234.240.0/22',
			'198.41.128.0/17'
		],
		'v6' => [
			'2a06:98c0::/29',
			'2c0f:f248::/32',
			'2400:cb00::/32',
			'2405:8100::/32',
			'2405:b500::/32',
			'2606:4700::/32',
			'2803:f800::/32'
		]
	];

	protected $remoteIp = null;

	protected $robotName;

	protected $fromSearch;

	protected static $customMethodPhpInput = null;

	public function __construct(\XF\InputFilterer $filterer,
		array $input = null, array $files = null, array $cookie = null, array $server = null
	)
	{
		$this->filterer = $filterer;

		if ($input === null)
		{
			if (self::$customMethodPhpInput === null)
			{
				self::$customMethodPhpInput = $this->convertCustomMethodPhpInput();
			}

			$input = self::$customMethodPhpInput + $_POST + $_GET;
		}
		if ($files === null)
		{
			$files = $_FILES;
		}
		if ($cookie === null)
		{
			$cookie = $_COOKIE;
		}
		if ($server === null)
		{
			$server = $_SERVER;
		}

		$this->input = $input;
		$this->files = $files;
		$this->cookie = $cookie;
		$this->server = $server;
	}

	protected function convertCustomMethodPhpInput()
	{
		if (!empty($_SERVER['REQUEST_METHOD'])
			&& in_array(strtoupper($_SERVER['REQUEST_METHOD']), ['PUT', 'PATCH', 'DELETE'])
			&& !empty($_SERVER['CONTENT_TYPE'])
			&& $_SERVER['CONTENT_TYPE'] === 'application/x-www-form-urlencoded'
		)
		{
			$rawInput = @file_get_contents("php://input");
			if ($rawInput)
			{
				parse_str($rawInput, $extra);
				if (is_array($extra))
				{
					return $extra;
				}
			}
		}

		return [];
	}

	public function setCookiePrefix($prefix)
	{
		$this->cookiePrefix = $prefix;
	}

	public function getCookiePrefix()
	{
		return $this->cookiePrefix;
	}

	public function get($key, $fallback = false)
	{
		$subParts = explode('.', $key);
		$key = array_shift($subParts);

		if (array_key_exists($key, $this->input))
		{
			$value = $this->input[$key];
		}
		else
		{
			return $fallback;
		}

		return $this->getSubValue($value, $subParts, $fallback);
	}

	public function exists($key)
	{
		$subParts = explode('.', $key);
		$key = array_shift($subParts);

		if (array_key_exists($key, $this->input))
		{
			$value = $this->input[$key];
		}
		else
		{
			return false;
		}

		while ($subParts)
		{
			if (!is_array($value))
			{
				return false;
			}

			$key = array_shift($subParts);
			if (array_key_exists($key, $value))
			{
				$value = $value[$key];
			}
			else
			{
				return false;
			}
		}

		return true;
	}

	public function getUser($key, $fallback = false)
	{
		return $this->get($key, $fallback);
	}

	protected function getSubValue($value, array $subParts, $fallback)
	{
		while ($subParts)
		{
			if (!is_array($value))
			{
				return $fallback;
			}

			$key = array_shift($subParts);
			if (array_key_exists($key, $value))
			{
				$value = $value[$key];
			}
			else
			{
				return $fallback;
			}
		}

		return $value;
	}

	public function filter($key, $type = null, $default = null)
	{
		if (is_array($key) && $type === null)
		{
			$output = [];
			foreach ($key AS $name => $value)
			{
				if (is_array($value))
				{
					$array = $this->get($name);
					if (!is_array($array))
					{
						$array = [];
					}
					$output[$name] = $this->filterer->filterArray($array, $value);
				}
				else
				{
					$output[$name] = $this->filter($name, $value);
				}
			}

			return $output;
		}
		else
		{
			$value = $this->get($key, $default);

			if (is_string($type) && $type[0] == '?')
			{
				if ($value === null)
				{
					return null;
				}

				$type = substr($type, 1);
			}

			if (is_array($type))
			{
				if (!is_array($value))
				{
					$value = [];
				}

				return $this->filterer->filterArray($value, $type);
			}
			else
			{
				return $this->filterer->filter($value, $type);
			}
		}
	}

	/**
	 * @param $key string Input key to set - either 'keyName' or 'arrayName.subArrayName.keyName' etc.
	 * @param $value
	 */
	public function set($key, $value)
	{
		$parts = explode('.', $key);

		$var =& $this->input;
		while ($part = array_shift($parts))
		{
			$var =& $var[$part];
		}

		$var = $value;
	}

	public function getInput()
	{
		return $this->input;
	}

	public function getInputForLogs()
	{
		return $this->filterForLog($this->input);
	}

	public function filterForLog(array $data)
	{
		$skip = array_fill_keys($this->skipLogKeys, true);

		$filter = function(array $d) use ($skip, &$filter)
		{
			$output = [];
			foreach ($d AS $k => $v)
			{
				if (isset($skip[$k]) || strpos($k, 'password') !== false)
				{
					$output[$k] = '********';
				}
				else if (is_array($v))
				{
					$output[$k] = $filter($v);
				}
				else
				{
					$output[$k] = $v;
				}
			}

			return $output;
		};

		return $filter($data);
	}

	public function skipKeyForLogging($key)
	{
		$this->skipLogKeys[] = $key;
	}

	/**
	 * @param string $key
	 * @param bool $multiple If true, returns an array of uploads for this key
	 * @param bool $skipErrors If true, uploads with errors will not be returned
	 *
	 * @return Upload|Upload[]
	 */
	public function getFile($key, $multiple = false, $skipErrors = true)
	{
		if (!isset($this->files[$key]['name']))
		{
			return ($multiple ? [] : null);
		}

		if (is_array($this->files[$key]['name']))
		{
			// multiple uploads
			$files = [];
			foreach (array_keys($this->files[$key]['name']) AS $idx)
			{
				$files[$idx] = [
					'name' => $this->files[$key]['name'][$idx],
					'type' => $this->files[$key]['type'][$idx],
					'size' => $this->files[$key]['size'][$idx],
					'tmp_name' => $this->files[$key]['tmp_name'][$idx],
					'error' => $this->files[$key]['error'][$idx],
				];
			}
		}
		else
		{
			// single upload
			$files = [$this->files[$key]];
		}

		$output = [];

		$imageI = 1;
		$imageBase = 'img-' . gmdate('Y-m-d-H-i-s') . '-';

		foreach ($files AS $idx => $file)
		{
			if ($file['error'] == UPLOAD_ERR_NO_FILE || ($skipErrors && $file['error']))
			{
				// didn't upload a file or has errors - just ignore
				continue;
			}

			// this handles files uploaded via JS that don't have a proper filename
			if ($file['name'] == 'blob' && preg_match('#^image/(pjpeg|jpeg|gif|png)$#', $file['type'], $match))
			{
				switch ($match[1])
				{
					case 'jpeg':
					case 'pjpeg':
						$type = 'jpg';
						break;

					default:
						$type = $match[1];
				}

				$file['name'] = $imageBase . $imageI . '.' . $type;
				$imageI++;
			}

			$class = \XF::extendClass('XF\Http\Upload');
			$output[$idx] = new $class($file['tmp_name'], $file['name'], $file['error']);
		}

		if ($multiple)
		{
			return $output;
		}
		else
		{
			return reset($output);
		}
	}

	public function getCookie($key, $fallback = false)
	{
		$cookie = $this->getCookieRaw($this->cookiePrefix . $key, $fallback);
		if (is_array($cookie) && !is_array($fallback))
		{
			$cookie = $fallback;
		}

		return $cookie;
	}

	public function getCookieArray($key, array $fallback = [])
	{
		$cookie = $this->getCookieRaw($this->cookiePrefix . $key, $fallback);
		if (!is_array($cookie))
		{
			$cookie = $fallback;
		}

		return $cookie;
	}

	public function getCookies($prefixFiltered = true)
	{
		if (!$prefixFiltered)
		{
			return $this->cookie;
		}

		$output = [];
		$prefixLength = strlen($this->cookiePrefix);

		foreach ($this->cookie AS $cookie => $value)
		{
			if (substr($cookie, 0, $prefixLength) == $this->cookiePrefix)
			{
				$cookie = substr($cookie, $prefixLength);
				if (is_string($cookie) && strlen($cookie))
				{
					$output[$cookie] = $value;
				}
			}
		}

		return $output;
	}

	public function getCookieRaw($key, $fallback = false)
	{
		if (array_key_exists($key, $this->cookie))
		{
			return $this->cookie[$key];
		}
		else
		{
			return $fallback;
		}
	}

	public function getInputRaw($fallback = '')
	{
		$input = file_get_contents('php://input');
		return ($input ?: $fallback);
	}

	public function getIp($allowProxied = false)
	{
		if ($allowProxied && $ip = $this->getServer('HTTP_CLIENT_IP'))
		{
			list($ip) = explode(',', $ip);
			return $this->getFilteredIp($ip);
		}
		else if ($allowProxied && $ip = $this->getServer('HTTP_X_FORWARDED_FOR'))
		{
			list($ip) = explode(',', $ip);
			return $this->getFilteredIp($ip);
		}

		if ($this->remoteIp === null)
		{
			$ip = $this->getTrustedRealIp($this->getServer('REMOTE_ADDR'));
			$this->remoteIp = $this->getFilteredIp($ip);
		}

		return $this->remoteIp;
	}

	public function getAllIps()
	{
		$proxied = $this->getIp(true);
		$unproxied = $this->getIp(false);

		if ($proxied === $unproxied)
		{
			return $unproxied;
		}

		$ips = preg_split('/,\s*/', $proxied);
		$ips[] = $unproxied;

		return array_unique($ips);
	}

	protected function getTrustedRealIp($ip)
	{
		$via = $this->getServer('HTTP_VIA');
		if ($via && strpos(strtolower($via), 'chrome-compression-proxy'))
		{
			// may have Google Data Saver enabled
			$realIps = $this->getServer('HTTP_X_FORWARDED_FOR');
			if ($realIps)
			{
				$realIps = explode(',', $realIps);
				$realIp = end($realIps);
				$realIp = trim($realIp);

				if ($realIp === $ip || $this->ipMatchesRanges($ip, self::$googleIps))
				{
					// if the IP comes from a known Google IP, then we can trust that they put the client IP
					// in X-Forwarded-For. (They should have appended it to the end.)
					return $realIp;
				}
			}
		}

		$cfIp = $this->getServer('HTTP_CF_CONNECTING_IP');
		if ($cfIp && $cfIp !== $ip)
		{
			if ($this->ipMatchesRanges($ip, self::$cloudFlareIps))
			{
				// connection from known CloudFlare IP, real IP in their header
				return $cfIp;
			}
		}

		return $ip;
	}

	protected function ipMatchesRanges($ip, array $ranges)
	{
		$ip = \XF\Util\Ip::convertIpStringToBinary($ip);
		if ($ip === false)
		{
			return false;
		}

		$type = strlen($ip) == 4 ? 'v4' : 'v6';

		if (empty($ranges[$type]))
		{
			return false;
		}

		foreach ($ranges[$type] AS $range)
		{
			if (is_string($range))
			{
				$range = explode('/', $range);
			}

			$rangeIp = \XF\Util\Ip::convertIpStringToBinary($range[0]);
			$cidr = intval($range[1]);

			if (\XF\Util\Ip::ipMatchesCidrRange($ip, $rangeIp, $cidr))
			{
				return true;
			}
		}

		return false;
	}

	protected function getFilteredIp($ip)
	{
		$ip = trim($ip);

		if (preg_match('#:(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})$#', $ip, $match))
		{
			// embedded IPv4
			$long = ip2long($match[1]);
			if (!$long)
			{
				return $ip;
			}

			return $match[1];
		}

		return $ip;
	}

	public function getUserAgent()
	{
		return $this->getServer('HTTP_USER_AGENT');
	}

	public function getRobotName()
	{
		if ($this->robotName === null)
		{
			$userAgent = $this->getUserAgent();
			if ($userAgent)
			{
				$this->robotName = \XF::app()->data('XF:Robot')->userAgentMatchesRobot($userAgent);
			}
			else
			{
				$this->robotName = '';
			}
		}

		return $this->robotName;
	}

	/**
	 * @return string
	 */
	public function getFromSearch()
	{
		if ($this->fromSearch === null)
		{
			$this->populateFromSearch();
		}

		return $this->fromSearch;
	}

	public function populateFromSearch(Response $persistResponse = null)
	{
		$fromSearch = $this->getCookie('from_search');
		if (!is_string($fromSearch))
		{
			$referrer = $this->getReferrer();
			if ($referrer)
			{
				$fromSearch = \XF::app()->data('XF:Search')->urlMatchesSearchDomain($referrer);
				if ($persistResponse && $fromSearch)
				{
					$persistResponse->setCookie('from_search', $fromSearch, 0, null, false);
				}
			}
			else
			{
				$fromSearch = '';
			}
		}

		$this->fromSearch = $fromSearch;

		return $this->fromSearch;
	}

	public function getReferrer()
	{
		return $this->getServer('HTTP_REFERER');
	}

	public function getServer($key, $fallback = false)
	{
		if (array_key_exists($key, $this->server))
		{
			return $this->server[$key];
		}
		else
		{
			return $fallback;
		}
	}

	public function getRequestMethod()
	{
		return strtolower($this->getServer('REQUEST_METHOD'));
	}

	public function getApiKey()
	{
		return trim($this->getServer('HTTP_XF_API_KEY', ''));
	}

	public function getApiUser()
	{
		return intval($this->getServer('HTTP_XF_API_USER', 0));
	}

	public function isGet()
	{
		return ($this->getRequestMethod() === 'get');
	}

	public function isHead()
	{
		return ($this->getRequestMethod() === 'head');
	}

	public function isPost()
	{
		return ($this->getRequestMethod() === 'post');
	}

	public function isXhr()
	{
		return ($this->getServer('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest');
	}

	public function isSecure()
	{
		return (
			$this->getServer('REQUEST_SCHEME') === 'https'
			|| $this->getServer('HTTP_X_FORWARDED_PROTO') === 'https'
			|| $this->getServer('HTTPS') === 'on'
			|| $this->getServer('SERVER_PORT') == 443
		);
	}

	public function isHostLocal($host = null)
	{
		if ($host === null)
		{
			$host = $this->getHost();
		}

		return (
			$host == 'localhost'
			|| $host == '127.0.0.1'
			|| $host == '[::1]'
			|| preg_match('#\.(dev|localhost|local|test)$#', $host)
			|| strpos($host, '.') === false // no dot, very likely to be on a local named server
		);
	}

	public function getProtocol()
	{
		return $this->isSecure() ? 'https' : 'http';
	}

	public function getBaseUrl()
	{
		$baseUrl = $this->getServer('SCRIPT_NAME', '');
		$basePath = dirname($baseUrl);

		if (strlen($basePath) <= 1)
		{
			// Looks to be at the root, so trust that.
			return $baseUrl;
		}

		$requestUri = $this->getRequestUri();
		if (!strlen($requestUri))
		{
			// no request URI, probably not a normal HTTP request - just return the root
			return '/';
		}

		if (strpos($requestUri, $basePath) === 0)
		{
			// We're not at the root but we match the first part of the request URI, so trust that.
			return $baseUrl;
		}

		// Otherwise, the SCRIPT_NAME is wrong and likely has extra stuff prepended. See if we can find the request
		// URI in the base URL. If so, ignore what comes before it.
		$qsPos = strpos($requestUri, '?');
		if ($qsPos !== false)
		{
			$requestUriNoQs = substr($requestUri, 0, $qsPos);
		}
		else
		{
			$requestUriNoQs = $requestUri;
		}

		$requestPos = strpos($baseUrl, $requestUriNoQs);
		if ($requestPos)
		{
			$realBaseUrl = substr($baseUrl, $requestPos);
			if ($realBaseUrl)
			{
				return $realBaseUrl;
			}
		}

		return $baseUrl;
	}

	public function getBasePath()
	{
		$baseUrl = $this->getBaseUrl();

		if (is_string($baseUrl) && strlen($baseUrl))
		{
			$lastSlash = strrpos($baseUrl, '/');
			if ($lastSlash) // intentionally skipping for false and 0
			{
				return substr($baseUrl, 0, $lastSlash);
			}
		}

		return '/';
	}

	public function getFullBasePath()
	{
		return $this->getHostUrl() . $this->getBasePath();
	}

	public function getExtendedUrl($requestUri = null)
	{
		$baseUrl = $this->getBaseUrl();
		$basePath = $this->getBasePath();

		if ($requestUri === null)
		{
			$requestUri = $this->getRequestUri();
		}

		if (strpos($requestUri, $baseUrl) === 0)
		{
			return strval(substr($requestUri, strlen($baseUrl)));
		}
		else if (strpos($requestUri, $basePath) === 0)
		{
			return strval(substr($requestUri, strlen($basePath)));
		}
		else
		{
			return $requestUri;
		}
	}

	public function getRequestUri()
	{
		if ($this->getServer('IIS_WasUrlRewritten') === '1')
		{
			$unencodedUrl = $this->getServer('UNENCODED_URL', '');
			if ($unencodedUrl !== '')
			{
				return $unencodedUrl;
			}
		}

		return $this->getServer('REQUEST_URI', '');
	}

	public function getFullRequestUri()
	{
		return $this->getHostUrl() . $this->getRequestUri();
	}

	public function getHost()
	{
		$host = $this->getServer('HTTP_HOST');
		if (!$host)
		{
			$host = $this->getServer('SERVER_NAME');
			$port = intval($this->getServer('SERVER_PORT'));
			if ($port && $port != 80 && $port != 443)
			{
				$host .= ":$port";
			}
		}

		return $host;
	}

	public function getHostUrl()
	{
		return $this->getProtocol() . '://' . $this->getHost();
	}

	/**
	 * @return string
	 */
	public function getRoutePath()
	{
		$xfRoute = $this->filter('_xfRoute', 'str');
		if ($xfRoute)
		{
			return $xfRoute;
		}
		$routePath = ltrim($this->getExtendedUrl(), '/');
		return $this->getRoutePathInternal($routePath);
	}

	public function getRoutePathFromExtended($extended)
	{
		$routePath = ltrim($extended, '/');
		return $this->getRoutePathInternal($routePath);
	}

	public function getRoutePathFromUrl($url)
	{
		$url = $this->convertToAbsoluteUri($url);
		$url = str_replace($this->getHostUrl(), '', $url);

		$routePath = ltrim($this->getExtendedUrl($url), '/');

		return $this->getRoutePathInternal($routePath);
	}

	protected function getRoutePathInternal($routePath)
	{
		if (strlen($routePath) == 0)
		{
			return '';
		}

		if ($routePath[0] == '?')
		{
			$routePath = substr($routePath, 1);

			$nextArg = strpos($routePath, '&');
			if ($nextArg !== false)
			{
				$routePath = substr($routePath, 0, $nextArg);
			}

			if (strpos($routePath, '=') !== false)
			{
				return ''; // first bit has a "=" so it's named
			}
		}
		else
		{
			$queryStart = strpos($routePath, '?');
			if ($queryStart !== false)
			{
				$routePath = substr($routePath, 0, $queryStart);
			}
		}

		return strval($routePath);
	}

	public function convertToAbsoluteUri($uri, $fullBasePath = null)
	{
		if (!$fullBasePath)
		{
			$fullBasePath = $this->getFullBasePath();
		}

		return \XF::convertToAbsoluteUrl($uri, $fullBasePath);
	}

	public function getInputFilterer()
	{
		return $this->filterer;
	}

	public function getNewArrayFilterer(array $input = [])
	{
		return $this->filterer->getNewArrayFilterer($input);
	}
}