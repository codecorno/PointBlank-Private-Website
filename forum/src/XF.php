<?php

/**
 * Basic setup class and facade into app-specific configurations and the DIC.
 */
class XF
{
	/**
	 * Current printable and encoded versions. These are used for visual output
	 * and installation/upgrading.
	 *
	 * @var string
	 * @var integer
	 */
	public static $version = '2.1.4';
	public static $versionId = 2010470; // abbccde = a.b.c d (alpha: 1, beta: 3, RC: 5, stable: 7, PL: 9) e

	const XF_API_URL = '';
	const XF_LICENSE_KEY = '';

	const API_VERSION = 1;

	protected static $memoryLimit = null;

	/**
	 * @var \Composer\Autoload\ClassLoader
	 */
	public static $autoLoader = null;

	public static $debugMode = false;
	public static $developmentMode = false;
	public static $time = 0;

	public static $DS = DIRECTORY_SEPARATOR;

	protected static $rootDirectory = '.';
	protected static $sourceDirectory = '.';

	protected static $app = null;

	/**
	 * @var \XF\Entity\User
	 */
	protected static $visitor = null;

	/**
	 * @var \XF\Language
	 */
	protected static $language = null;

	/**
	 * @var \XF\Entity\ApiKey|null
	 */
	protected static $apiKey = null;

	/**
	 * @var bool
	 */
	protected static $apiBypassPermissions = false;

	/**
	 * Starts the XF framework and standardized the environment.
	 */
	public static function start($rootDirectory)
	{
		self::$time = time();
		self::$rootDirectory = $rootDirectory;
		self::$sourceDirectory = __DIR__;

		self::standardizeEnvironment();
		self::startAutoloader();
		self::startSystem();
	}

	/**
	 * Sets up the PHP environment in the XF-expected way
	 */
	public static function standardizeEnvironment()
	{
		ignore_user_abort(true);

		self::setMemoryLimit(128 * 1024 * 1024);

		error_reporting(E_ALL | E_STRICT & ~8192);
		set_error_handler(['XF', 'handlePhpError']);
		set_exception_handler(['XF', 'handleException']);
		register_shutdown_function(['XF', 'handleFatalError']);

		date_default_timezone_set('UTC');
		setlocale(LC_ALL, 'C');

		// if you really need to load a phar file, you can call stream_wrapper_restore('phar');
		@stream_wrapper_unregister('phar');

		@ini_set('output_buffering', false);

		if (version_compare(PHP_VERSION, '7.1', '>='))
		{
			@ini_set('serialize_precision', -1);
		}

		// see http://bugs.php.net/bug.php?id=36514
		// and http://xenforo.com/community/threads/53637/
		if (!@ini_get('output_handler'))
		{
			$level = ob_get_level();
			while ($level)
			{
				@ob_end_clean();
				$newLevel = ob_get_level();
				if ($newLevel >= $level)
				{
					break;
				}
				$level = $newLevel;
			}
		}
	}

	/**
	 * Handler for set_error_handler to convert notices, warnings, and other errors
	 * into exceptions.
	 *
	 * @param integer $errorType Type of error (one of the E_* constants)
	 * @param string $errorString
	 * @param string $file
	 * @param integer $line
	 *
	 * @throws \ErrorException
	 */
	public static function handlePhpError($errorType, $errorString, $file, $line)
	{
		if ($errorType & error_reporting())
		{
			$errorString = '[' . \XF\Util\Php::convertErrorCodeToString($errorType) . '] '. $errorString;

			$trigger = true;
			if (!self::$debugMode)
			{
				if ($errorType & E_STRICT
					|| $errorType & E_DEPRECATED
					|| $errorType & E_USER_DEPRECATED
				)
				{
					$trigger = false;
				}
				else if ($errorType & E_NOTICE || $errorType & E_USER_NOTICE)
				{
					$trigger = false;
					$e = new \ErrorException($errorString, 0, $errorType, $file, $line);
					self::app()->logException($e);
				}
			}

			if ($trigger)
			{
				throw new \ErrorException($errorString, 0, $errorType, $file, $line);
			}
		}
	}

	/**
	 * Default exception handler.
	 *
	 * @param Exception $e
	 */
	public static function handleException($e)
	{
		$app = self::app();
		$app->logException($e, true); // exiting so rollback
		$app->displayFatalExceptionMessage($e);
	}

	/**
	 * @param \Exception|\Throwable $e
	 * @param bool $rollback
	 * @param string $messagePrefix
	 * @param bool $forceLog
	 */
	public static function logException($e, $rollback = false, $messagePrefix = '', $forceLog = false)
	{
		self::app()->error()->logException($e, $rollback, $messagePrefix, $forceLog);
	}

	public static function logError($message, $forceLog = false)
	{
		self::app()->error()->logError($message, $forceLog);
	}

	/**
	 * Try to log fatal errors so that debugging is easier.
	 */
	public static function handleFatalError()
	{
		$error = @error_get_last();
		if (!$error)
		{
			return;
		}

		if (empty($error['type']) || !($error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR)))
		{
			return;
		}

		try
		{
			self::app()->logException(
				new ErrorException("Fatal Error: " . $error['message'], $error['type'], 1, $error['file'], $error['line']),
				true
			);
		}
		catch (Exception $e) {}
	}

	/**
	 * Sets up XF's autoloader
	 */
	public static function startAutoloader()
	{
		if (self::$autoLoader)
		{
			return;
		}

		/** @var \Composer\Autoload\ClassLoader $autoLoader */
		$autoLoader = require(__DIR__ . '/vendor/autoload.php');
		$autoLoader->register();

		self::$autoLoader = $autoLoader;
	}

	public static function registerComposerAutoloadDir($dir, $prepend = false)
	{
		try
		{
			$composerAutoload = new \XF\ComposerAutoload(self::app(), $dir);
			$composerAutoload->autoloadAll($prepend);
		}
		catch (\Exception $e)
		{
			if (\XF::$debugMode)
			{
				throw $e;
			}
			else
			{
				self::logException($e, true, 'Error registering composer autoload directory: ');
			}
		}
	}

	public static function startSystem()
	{
		register_shutdown_function(['XF', 'triggerRunOnce']);

		require(__DIR__ . '/vendor/dokuwiki/utf8/utf8.php');
	}

	protected static $runOnce = [];

	public static function runOnce($key, \Closure $fn)
	{
		if (isset(self::$runOnce[$key]))
		{
			// if this key already exists, allow a new function with the
			// same key to replace it and move to the end of the queue.
			unset(self::$runOnce[$key]);
		}
		self::$runOnce[$key] = $fn;
	}

	public static function runLater(\Closure $fn)
	{
		self::$runOnce[] = $fn;
	}

	public static function triggerRunOnce($rethrow = false)
	{
		$i = 0;

		do
		{
			foreach (self::$runOnce AS $key => $fn)
			{
				unset(self::$runOnce[$key]);

				try
				{
					$fn();
				}
				catch (\Exception $e)
				{
					self::logException($e, true);
					// can't know if we have an open transaction from before so have to roll it back

					if ($rethrow)
					{
						throw $e;
					}
				}
			}

			$i++;
		}
		while (self::$runOnce && $i < 5);
	}

	public static function getRootDirectory()
	{
		return self::$rootDirectory;
	}

	public static function getSourceDirectory()
	{
		return self::$sourceDirectory;
	}

	public static function getAddOnDirectory()
	{
		return \XF::getSourceDirectory() . self::$DS . 'addons';
	}

	public static function getVendorDirectory()
	{
		return \XF::getSourceDirectory() . self::$DS . 'vendor';
	}

	/**
	 * @param \XF\App $app
	 */
	public static function setApp(\XF\App $app)
	{
		if (self::$app)
		{
			throw new \LogicException(
				'A second app cannot be setup. '
				. 'Tried to set ' . get_class($app) . ' after setting ' . get_class(self::$app)
			);
		}

		self::$app = $app;
	}

	/**
	 * @return \XF\App
	 */
	public static function app()
	{
		if (!self::$app)
		{
			return self::setupApp('\XF\App');
		}

		return self::$app;
	}

	public static function setupApp($appClass, array $setupOptions = [])
	{
		/** @var \XF\App $app */
		$app = new $appClass(new \XF\Container());
		self::setApp($app);
		$app->setup($setupOptions);

		return $app;
	}

	/**
	 * Detects if the request URL matches the API path
	 *
	 * @return bool
	 */
	public static function requestUrlMatchesApi()
	{
		$baseRequest = new \XF\Http\Request(new \XF\InputFilterer());
		return boolval(preg_match('#^api(?:/|$)#i', $baseRequest->getRoutePath()));
	}

	/**
	 * Runs the specified application
	 *
	 * @param string $appClass
	 */
	public static function runApp($appClass)
	{
		$app = self::setupApp($appClass);

		ob_start();

		$response = $app->run();

		$extraOutput = ob_get_clean();
		if (strlen($extraOutput))
		{
			$body = $response->body();
			if (is_string($body))
			{
				if ($response->contentType() == 'text/html')
				{
					if (strpos($body, '<!--XF:EXTRA_OUTPUT-->') !== false)
					{
						$body = str_replace('<!--XF:EXTRA_OUTPUT-->', $extraOutput . '<!--XF:EXTRA_OUTPUT-->', $body);
					}
					else
					{
						$body = preg_replace('#<body[^>]*>#i', "\\0$extraOutput", $body);
					}
					$response->body($body);
				}
				else
				{
					$response->body($extraOutput . $body);
				}
			}
		}

		if (\XF::$debugMode)
		{
			$app = \XF::app();
			$container = $app->container();

			if ($container->isCached('db'))
			{
				$queryCount = \XF::db()->getQueryCount();
			}
			else
			{
				$queryCount = null;
			}

			$debug = [
				'time' => round(microtime(true) - $app->container('time.granular'), 4),
				'queries' => $queryCount,
				'memory' => round(memory_get_peak_usage() / 1024 / 1024, 2)
			];

			$response->header('X-XF-Debug-Stats', json_encode($debug));
		}

		$response->send($app->request());
	}

	/**
	 * @return \XF\Entity\User
	 */
	public static function visitor()
	{
		if (!self::$visitor)
		{
			/** @var \XF\Repository\User $userRepo */
			$userRepo = self::repository('XF:User');
			self::$visitor = $userRepo->getVisitor(0);
		}

		return self::$visitor;
	}

	public static function setVisitor(\XF\Entity\User $user = null)
	{
		self::$visitor = $user;
	}

	/**
	 * Temporarily take an action with the given user considered to be the visitor
	 *
	 * @param \XF\Entity\User $user
	 * @param Closure $action
	 * @return mixed
	 *
	 * @throws Exception
	 */
	public static function asVisitor(\XF\Entity\User $user, \Closure $action)
	{
		$old = self::$visitor;
		self::setVisitor($user);
		try
		{
			return $action();
		}
		finally
		{
			self::setVisitor($old);
		}
	}

	/**
	 * @return \XF\Language
	 */
	public static function language()
	{
		if (!self::$language)
		{
			self::$language = self::app()->language(0);
		}

		return self::$language;
	}

	public static function setLanguage(\XF\Language $language)
	{
		self::$language = $language;
	}

	/**
	 * @return \XF\Entity\ApiKey
	 */
	public static function apiKey()
	{
		if (!self::$apiKey)
		{
			/** @var \XF\Repository\Api $apiRepo */
			$apiRepo = self::repository('XF:Api');
			self::$apiKey = $apiRepo->getFallbackApiKey();
		}

		return self::$apiKey;
	}

	public static function setApiKey(\XF\Entity\ApiKey $key = null)
	{
		self::$apiKey = $key;
	}

	/**
	 * True if the API has been set to bypass permissions for the current request.
	 * This is only possible if a super user key is being used.
	 *
	 * @return bool
	 */
	public static function isApiBypassingPermissions()
	{
		return self::$apiBypassPermissions && self::apiKey()->is_super_user;
	}

	/**
	 * True in most cases, this is just the inverse of isApiBypassingPermissions(), in contexts where
	 * the inverted logic is easier to read.
	 *
	 * @return bool
	 */
	public static function isApiCheckingPermissions()
	{
		return !self::isApiBypassingPermissions();
	}

	public static function setApiBypassPermissions($bypass)
	{
		self::$apiBypassPermissions = $bypass;
	}

	/**
	 * @return bool
	 */
	public static function isPushUsable()
	{
		$options = self::options();

		if (!isset($options->enablePush) || !$options->enablePush)
		{
			return false;
		}

		$request = self::app()->request();

		if ($request->isHostLocal())
		{
			return true;
		}

		if ($request->isSecure())
		{
			return true;
		}

		return false;
	}

	public static function phrasedException($name, array $params = [])
	{
		return new \XF\PrintableException(
			self::phrase($name, $params)->render(),
			$name
		);
	}

	public static function phrase($name, array $params = [], $allowHtml = true)
	{
		return self::language()->phrase($name, $params, true, $allowHtml);
	}

	public static function phraseDeferred($name, array $params = [])
	{
		return self::language()->phrase($name, $params, false);
	}

	public static function string(array $parts = [])
	{
		return new \XF\StringBuilder($parts);
	}

	public static function config($key = null)
	{
		return self::app()->config($key);
	}

	/**
	 * @return \XF\Session\Session
	 */
	public static function session()
	{
		return self::app()->session();
	}

	/**
	 * @return \ArrayObject
	 */
	public static function options()
	{
		return self::app()->options();
	}

	/**
	 * @return \XF\Mail\Mailer
	 */
	public static function mailer()
	{
		return self::app()->mailer();
	}

	/**
	 * @return \XF\Db\AbstractAdapter
	 */
	public static function db()
	{
		return self::app()->db();
	}

	/**
	 * @return \XF\PermissionCache
	 */
	public static function permissionCache()
	{
		return self::app()->permissionCache();
	}

	/**
	 * @return \XF\Mvc\Entity\Manager
	 */
	public static function em()
	{
		return self::app()->em();
	}

	/**
	 * @param string $identifier
	 *
	 * @return \XF\Mvc\Entity\Finder
	 */
	public static function finder($identifier)
	{
		return self::app()->finder($identifier);
	}

	/**
	 * @param string $identifier
	 *
	 * @return \XF\Mvc\Entity\Repository
	 */
	public static function repository($identifier)
	{
		return self::app()->repository($identifier);
	}

	/**
	 * @param string $class
	 *
	 * @return \XF\Service\AbstractService
	 */
	public static function service($class)
	{
		$args = func_get_args();
		return call_user_func_array([self::app(), 'service'], $args);
	}

	/**
	 * @return \XF\DataRegistry
	 */
	public static function registry()
	{
		return self::app()->registry();
	}

	/**
	 * @return \League\Flysystem\MountManager
	 */
	public static function fs()
	{
		return self::app()->fs();
	}

	public static function extension()
	{
		return self::app()->extension();
	}

	/**
	 * Fires a code event for an extension point
	 *
	 * @param string $event
	 * @param array $args
	 * @param null|string $hint
	 *
	 * @return bool
	 */
	public static function fire($event, array $args, $hint = null)
	{
		return self::extension()->fire($event, $args, $hint);
	}

	/**
	 * Gets the callable class name for a dynamically extended class.
	 *
	 * @param string $class
	 * @param null|string $fakeBaseClass
	 * @return string
	 *
	 * @throws Exception
	 */
	public static function extendClass($class, $fakeBaseClass = null)
	{
		return self::app()->extendClass($class, $fakeBaseClass);
	}

	/**
	 * Sets the memory limit. Will not shrink the limit.
	 *
	 * @param integer $limit Limit must be given in integer (byte) format.
	 *
	 * @return bool True if the limit was updated (or already met)
	 */
	public static function setMemoryLimit($limit)
	{
		$existingLimit = self::getMemoryLimit();
		if ($existingLimit < 0)
		{
			return true;
		}

		$limit = intval($limit);
		if ($limit > $existingLimit && $existingLimit)
		{
			if (@ini_set('memory_limit', $limit) === false)
			{
				return false;
			}

			self::$memoryLimit = $limit;
		}

		return true;
	}

	public static function increaseMemoryLimit($amount)
	{
		$amount = intval($amount);
		if ($amount <= 0)
		{
			return false;
		}

		$currentLimit = self::getMemoryLimit();
		if ($currentLimit < 0)
		{
			return true;
		}

		return self::setMemoryLimit($currentLimit + $amount);
	}

	/**
	 * Gets the current memory limit.
	 *
	 * @return int
	 */
	public static function getMemoryLimit()
	{
		if (self::$memoryLimit === null)
		{
			$curLimit = @ini_get('memory_limit');
			if ($curLimit === false)
			{
				// reading failed, so we have to treat it as unlimited - unlikely to be able to change anyway
				$curLimitInt = -1;
			}
			else
			{
				$curLimitInt = intval($curLimit);

				switch (substr($curLimit, -1))
				{
					case 'g':
					case 'G':
						$curLimitInt *= 1024;
					// fall through

					case 'm':
					case 'M':
						$curLimitInt *= 1024;
					// fall through

					case 'k':
					case 'K':
						$curLimitInt *= 1024;
				}
			}

			self::$memoryLimit = $curLimitInt;
		}

		return self::$memoryLimit;
	}

	/**
	 * Attempts to determine the current available amount of memory.
	 * If there is no memory limit
	 *
	 * @return int
	 */
	public static function getAvailableMemory()
	{
		$limit = self::getMemoryLimit();
		if ($limit < 0)
		{
			return PHP_INT_MAX;
		}

		$used = memory_get_usage();
		$available = $limit - $used;

		return ($available < 0 ? 0 : $available);
	}

	/**
	 * Generates a psuedo-random string of the specified length.
	 *
	 * @param integer $length
	 * @param boolean $raw If true, raw binary is returned, otherwise modified base64
	 *
	 * @return string
	 */
	public static function generateRandomString($length, $raw = false)
	{
		if ($raw)
		{
			return \XF\Util\Random::getRandomBytes($length);
		}
		else
		{
			return \XF\Util\Random::getRandomString($length);
		}
	}

	public static function stringToClass($string, $formatter, $defaultInfix = null)
	{
		$parts = explode(':', $string, 3);
		if (count($parts) == 1)
		{
			// already a class
			return $string;
		}

		$prefix = $parts[0];
		if (isset($parts[2]))
		{
			$infix = $parts[1];
			$suffix = $parts[2];
		}
		else
		{
			$infix = $defaultInfix;
			$suffix = $parts[1];
		}

		return $defaultInfix === null
			? sprintf($formatter, $prefix, $suffix)
			: sprintf($formatter, $prefix, $infix, $suffix);
	}

	public static function getCopyrightHtml()
	{
		return '<a href="https://xenforo.com" class="u-concealed" dir="ltr" target="_blank">Forum software by XenForo<sup>&reg;</sup> <span class="copyright">&copy; 2010-2019 XenForo Ltd.</span></a>';
	}

	public static function isPreEscaped($value, $type = 'html')
	{
		if ($value instanceof \XF\PreEscaped && $value->escapeType == $type)
		{
			return true;
		}
		else if ($value instanceof \XF\PreEscapedInterface && $value->getPreEscapeType() == $type)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function escapeString($value, $type = 'html')
	{
		if ($type === false)
		{
			$type = 'raw';
		}
		else if ($type === true)
		{
			$type = 'html';
		}

		if (self::isPreEscaped($value, $type))
		{
			return strval($value);
		}
		else if ($type == 'html' && ($value instanceof \XF\Phrase || $value instanceof \XF\Template\Template))
		{
			return strval($value);
		}

		$value = strval($value);

		switch ($type)
		{
			case 'html':
				return htmlspecialchars($value, ENT_QUOTES, 'utf-8');

			case 'raw':
				return $value;

			case 'js':
				$value = strtr($value, [
					'\\' => '\\\\',
					'"' => '\\"',
					"'" => "\\'",
					"\r" => '\r',
					"\n" => '\n',
					'</' => '<\\/',
				]);
				$value = preg_replace('/-(?=-)/', '-\\', $value);
				return $value;

			case 'json':
				$value = strtr($value, [
					'\\' => '\\\\',
					'"' => '\\"',
					"\t" => '\t',
					"\r" => '\r',
					"\n" => '\n',
					'/' => '\\/',
					'<!' => '\u003C!'
				]);
				return $value;

			case 'htmljs':
				return \XF::escapeString(\XF::escapeString($value, 'html'), 'js');

			case 'datauri':
				$value = strtr($value, [
					"\r" => '%0D',
					"\n" => '%0A',
					'%' => '%25',
					'#' => '%23',
					'(' => '%28',
					')' => '%29',
					'<' => '%3C',
					'>' => '%3E',
					'?' => '%3F',
					'[' => '%5B',
					']' => '%5D',
					'\\' => '%5C',
					'^' => '%5E',
					'`' => '%60',
					'{' => '%7B',
					'}' => '%7D',
					'|' => '%7C'
				]);
				return $value;

			default:
				return htmlspecialchars($value, ENT_QUOTES, 'utf-8');
		}
	}

	public static function cleanString($string, $trim = true)
	{
		return self::app()->inputFilterer()->cleanString($string, $trim);
	}

	public static function cleanArrayStrings(array $input, $trim = true)
	{
		return self::app()->inputFilterer()->cleanArrayStrings($input, $trim);
	}

	public static function dump($var)
	{
		self::app()->debugger()->dump($var);
	}

	public static function dumpSimple($var, $echo = true)
	{
		return self::app()->debugger()->dumpSimple($var, $echo);
	}

	public static function dumpConsole($var, $type = 'log')
	{
		return self::app()->debugger()->dumpConsole($var, $type);
	}

	public static function dumpToFile($var, $logName = null)
	{
		return self::app()->debugger()->dumpToFile($var, $logName);
	}

	public static function canonicalizeUrl($uri)
	{
		return self::convertToAbsoluteUrl($uri, self::options()->boardUrl);
	}

	public static function convertToAbsoluteUrl($uri, $fullBasePath)
	{
		$fullBasePath = rtrim($fullBasePath, '/');
		$baseParts = parse_url($fullBasePath);
		if (!$baseParts)
		{
			return $uri;
		}

		if ($uri == '.')
		{
			$uri = ''; // current directory
		}

		if (empty($baseParts['scheme']))
		{
			$baseParts['scheme'] = 'http';
		}

		if (substr($uri, 0, 2) == '//')
		{
			return $baseParts['scheme'] . ':' . $uri;
		}
		else if (substr($uri, 0, 1) == '/')
		{
			if (empty($baseParts['host']))
			{
				return $uri; // really can't guess
			}

			return $baseParts['scheme'] . '://' . $baseParts['host']
				. (!empty($baseParts['port']) ? ":$baseParts[port]" : '')  . $uri;
		}
		else if (preg_match('#^[a-z0-9-]+://#i', $uri))
		{
			return $uri;
		}
		else
		{
			return $fullBasePath . '/' . $uri;
		}
	}
}
