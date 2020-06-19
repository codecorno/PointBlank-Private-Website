<?php

namespace XF;

use XF\CustomField\DefinitionSet;
use XF\Http;
use XF\Mvc\Dispatcher;
use XF\Mvc\Renderer\AbstractRenderer;
use XF\Mvc\Reply\AbstractReply;
use XF\Mvc\Reply\Redirect;
use XF\Mvc\RouteMatch;
use XF\Mvc\Router;
use XF\Template\Compiler;
use XF\Template\Templater;
use XF\Util\File;

class App implements \ArrayAccess
{
	protected $container;

	protected $preLoadShared = [
		'addOns',
		'addOnsComposer',
		'autoJobRun',
		'bbCodeMedia',
		'classExtensions',
		'codeEventListeners',
		'connectedAccountCount',
		'contentTypes',
		'displayStyles',
		'helpPageCount',
		'languages',
		'masterStyleProperties',
		'nodeTypes',
		'options',
		'reactions',
		'reportCounts',
		'simpleCache',
		'smilies',
		'unapprovedCounts',
		'userBanners',
		'userTitleLadder',
		'userUpgradeCount',
		'widgetCache',
		'widgetDefinition',
		'widgetPosition'
	];
	protected $preLoadLocal = [];

	public function __construct(Container $container)
	{
		$this->container = $container;

		$this->initialize();
	}

	protected function initialize()
	{
		$time = !empty($_SERVER["REQUEST_TIME_FLOAT"]) ? $_SERVER["REQUEST_TIME_FLOAT"] : microtime(true);

		$container = $this->container;

		$container['time'] = intval($time);
		$container['time.granular'] = $time;

		$container['app.classType'] = '';
		$container['app.defaultType'] = '';

		$container['config.default'] = [
			'db' => [
				'adapterClass' => 'XF\Db\Mysqli\Adapter'
			],
			'fullUnicode' => false,
			'cache' => [
				'enabled' => false,
				'sessions' => false,
				'namespace' => 'xf',
				'provider' => 'Void',
				'config' => [],
				'context' => []
			],
			'pageCache' => [
				'enabled' => false,
				'lifetime' => 300,
				'recordSessionActivity' => true,
				'routeMatches' => [],
				'onSetup' => null
			],
			'debug' => false,
			'development' => [
				'enabled' => false,
				'defaultAddOn' => '',
				'skipAddOns' => null, // this has to be something other than an array to allow people to change it
				'throwJobErrors' => false,
				'fullJs' => false,
				'fullEditorJs' => false // internal use only, the necessary files are not distributed
			],
			'designer' => [
				'enabled' => false,
				'basePath' => 'src' . \XF::$DS . 'styles'
			],
			'cookie' => [
				'prefix' => 'xf_',
				'path' => '/',
				'domain' => ''
			],
			'http' => [
				'sslVerify' => null,
				'proxy' => null
			],
			'globalSalt' => '356cb8b4cda32876e98fb4a5b5a050a6',
			'superAdmins' => '', // keep this for upgrade purposes
			'internalDataPath' => 'internal_data',
			'codeCachePath' => '%s/code_cache',
			'tempDataPath' => '%s/temp',
			'fsAdapters' => [],
			'externalDataPath' => 'data',
			'externalDataUrl' => 'data',
			'javaScriptUrl' => 'js',
			'chmodWritableValue' => 0,
			'jobMaxRunTime' => 8,
			'enableMail' => true,
			'enableMailQueue' => true,
			'enableListeners' => true,
			'enableTemplateModificationCallbacks' => true,
			'enableClickjackingProtection' => true,
			'enableReverseTabnabbingProtection' => true,
			'enableGzip' => true,
			'enableContentLength' => true,
			'enableTfa' => true,
			'enableLivePayments' => true,
			'enableApi' => true,
			'enableAddOnArchiveInstaller' => false,
			'enableOneClickUpgrade' => true,
			'maxImageResizePixelCount' => 20000000,
			'adminLogLength' => 60, // number of days to keep admin log entries
			'adminColorHueShift' => 0,
			'checkVersion' => true,
			'passwordIterations' => 10,
			'auth' => null,
			'proxyUrlFormat' => 'proxy.php?{type}={url}&hash={hash}',
			'sendmailPath' => null,
			'serviceUnavailableCode' => 503,
			'container' => [],
			'exists' => false
		];

		$container['config.file'] = \XF::getSourceDirectory() . '/config.php';
		$container['config.legacyFile'] = \XF::getRootDirectory() . '/library/config.php';
		$container['config'] = function (Container $c)
		{
			$default = $c['config.default'];
			$file = $c['config.file'];
			$legacyFile = $c['config.legacyFile'];

			if (file_exists($file))
			{
				$config = [];
				require($file);

				$config = array_replace_recursive($default, $config);
				$config['exists'] = true;
				$config['legacyExists'] = null;
			}
			else
			{
				if (file_exists($legacyFile))
				{
					$config = [];
					require($legacyFile);

					$config = array_replace_recursive($default, $config);
					$config['legacyExists'] = true;
				}
				else
				{
					$config['legacyExists'] = false;
					$config = $default;
				}
			}

			// if there's a specific session cache specified, force this to be enabled
			if (!empty($config['cache']['context']['sessions']))
			{
				$config['cache']['sessions'] = true;
			}

			foreach ($config['container'] AS $key => $value)
			{
				$c[$key] = $value;
			}

			return $config;
		};

		$container['jQueryVersion'] = '3.3.1';

		$container['jsVersion'] = function (Container $c)
		{
			return substr(md5(\XF::$versionId . $c['options']->jsLastUpdate), 0, 8);
		};

		$container['avatarSizeMap'] = [
			'o' => 384,
			'h' => 384,
			'l' => 192,
			'm' => 96,
			's' => 48
		];

		$container['request'] = function (Container $c)
		{
			$request = new Http\Request($c['inputFilterer']);
			$request->setCookiePrefix($c['config']['cookie']['prefix']);
			return $request;
		};
		$container['request.paths'] = function (Container $c)
		{
			/** @var Http\Request $request */
			$request = $c['request'];
			$options = $c['options'];
			$fullPath = $request->getFullBasePath();
			$canonical = isset($options->boardUrl) ? $options->boardUrl : $fullPath;

			return [
				'full' => rtrim($fullPath, '/') . '/',
				'base' => rtrim($request->getBasePath(), '/') . '/',
				'canonical' => rtrim($canonical, '/') . '/',
				'nopath' => '',
			];
		};
		$container['request.pather'] = function (Container $c)
		{
			$paths = $c['request.paths'];

			return function($url, $modifier = 'base') use ($paths)
			{
				if (preg_match('#^(/|[a-z]+:)#i', $url))
				{
					return $url;
				}

				if (isset($paths[$modifier]))
				{
					$url = $paths[$modifier] . $url;
				}
				else
				{
					$url = $paths['base'] . $url;
				}

				return $url;
			};
		};

		$container['inlineImageTypes'] = function (Container $c)
		{
			return [
				'gif' => 'image/gif',
				'jpg' => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'jpe' => 'image/jpeg',
				'png' => 'image/png'
			];
		};

		$container['inlineVideoTypes'] = function (Container $c)
		{
			return [
				'm4v' => 'video/mp4',
				'mov' => 'video/quicktime',
				'mp4' => 'video/mp4',
				'mp4v' => 'video/mp4',
				'mpeg' => 'video/mpeg',
				'mpg' => 'video/mpeg',
				'ogv' => 'video/ogg',
				'webm' => 'video/webm'
			];
		};

		$container['response'] = function (Container $c)
		{
			/** @var \XF\Http\Request $request */
			$request = $c['request'];

			$cookie = $c['config']['cookie'];
			$cookie['secure'] = $request->isSecure() ? true : false;

			$response = new Http\Response();
			$response->setCookieConfig($cookie);

			$config = $c['config'];
			if ($config['enableClickjackingProtection'])
			{
				$response->header('X-Frame-Options', 'SAMEORIGIN');
			}
			if (!$config['enableGzip'])
			{
				$response->compressIfAble(false);
			}
			if (!$config['enableContentLength'])
			{
				$response->includeContentLength(false);
			}

			return $response;
		};

		$container['inputFilterer'] = function(Container $c)
		{
			$class = $this->extendClass('XF\InputFilterer');
			return new $class($c['config']['fullUnicode']);
		};

		$container['dispatcher'] = function ()
		{
			$class = $this->extendClass('XF\Mvc\Dispatcher');
			return new $class($this);
		};

		$container['router'] = function (Container $c)
		{
			return $c['router.public'];
		};

		$container['router.public'] = function (Container $c)
		{
			$class = $this->extendClass('XF\Mvc\Router');

			/** @var \XF\Mvc\Router $r */
			$r = new $class($c['router.public.formatter'], $c['router.public.routes']);
			$r->addRoutePreProcessor('filter', [$r, 'routePreProcessRouteFilter']);
			$r->addRoutePreProcessor('extension', [$r, 'routePreProcessExtension']);
			$r->addRoutePreProcessor('type', [$r, 'routePreProcessResponseType']);

			$routeFilters = $c['routeFilters'];
			$r->setRouteFilters($routeFilters['in'], $routeFilters['out']);

			$r->setPather($c['request.pather']);
			$r->setIndexRoute($c['options']->indexRoute ?: 'forums/');
			$r->setIncludeTitleInUrls($c['options']->includeTitleInUrls);
			$r->setRomanizeUrls($c['options']->romanizeUrls);

			$this->fire('router_public_setup', [$c, &$r]);

			return $r;
		};

		$container['router.public.formatter'] = function ($c)
		{
			if ($c['options']->useFriendlyUrls)
			{
				return function($route, $queryString)
				{
					return $route . (strlen($queryString) ? '?' . $queryString : '');
				};
			}
			else
			{
				return function($route, $queryString)
				{
					$suffix = $route . (strlen($queryString) ? (strlen($route) ? '&' : '') . $queryString : '');
					return strlen($suffix) ? 'index.php?' . $suffix : 'index.php';
				};
			}
		};
		$container['router.public.routes'] = $this->fromRegistry('routesPublic',
			function(Container $c) { return $c['em']->getRepository('XF:Route')->rebuildRouteCache('public'); }
		);

		$container['router.install'] = function (Container $c)
		{
			$class = $this->extendClass('XF\Mvc\Router');

			/** @var \XF\Mvc\Router $r */
			$r = new $class($c['router.install.formatter'], $c['router.install.routes']);
			$r->addRoutePreProcessor('extension', [$r, 'routePreProcessExtension']);
			$r->addRoutePreProcessor('type', [$r, 'routePreProcessResponseType']);

			$r->setPather($c['request.pather']);

			return $r;
		};
		$container['router.install.formatter'] = $container->wrap(function($route, $queryString)
		{
			$suffix = $route . (strlen($queryString) ? '&' . $queryString : '');
			return strlen($suffix) ? 'index.php?' . $suffix : 'index.php';
		});
		$container['router.install.routes'] = function (Container $c)
		{
			return [
				'install' => [
					'' => [
						'controller' => 'XF:Install',
						'format' => ''
					]
				],
				'upgrade' => [
					'' => [
						'controller' => 'XF:Upgrade',
						'format' => ''
					]
				],
				'index' => [
					'' => [
						'controller' => 'XF:Index',
						'format' => ''
					]
				]
			];
		};

		$container['router.admin'] = function (Container $c)
		{
			$class = $this->extendClass('XF\Mvc\Router');

			/** @var \XF\Mvc\Router $r */
			$r = new $class($c['router.admin.formatter'], $c['router.admin.routes']);
			$r->addRoutePreProcessor('extension', [$r, 'routePreProcessExtension']);
			$r->addRoutePreProcessor('type', [$r, 'routePreProcessResponseType']);

			$r->setPather($c['request.pather']);
			$r->setRomanizeUrls($c['options']->romanizeUrls);

			return $r;
		};
		$container['router.admin.formatter'] = $container->wrap(function($route, $queryString)
		{
			$suffix = $route . (strlen($queryString) ? '&' . $queryString : '');
			return strlen($suffix) ? 'admin.php?' . $suffix : 'admin.php';
		});
		$container['router.admin.routes'] = $this->fromRegistry('routesAdmin',
			function(Container $c) { return $c['em']->getRepository('XF:Route')->rebuildRouteCache('admin'); }
		);

		$container['router.api'] = function (Container $c)
		{
			$class = $this->extendClass('XF\Api\Mvc\Router');

			/** @var \XF\Api\Mvc\Router $r */
			$r = new $class($c['router.api.formatter'], $c['router.api.routes']);
			$r->addRoutePreProcessor('apiPrefix', [$r, 'routePreProcessApiPrefix']);
			$r->addRoutePreProcessor('apiVersion', [$r, 'routePreProcessApiVersion']);
			$r->addRoutePreProcessor('apiType', [$r, 'routePreProcessApiResponseType']);

			$r->setPather($c['request.pather']);

			return $r;
		};
		$container['router.api.formatter'] = function ($c)
		{
			// Note: always enforcing friendly URLs for the API for consistency.
			//if ($c['options']->useFriendlyUrls)
			if (true)
			{
				return function($route, $queryString)
				{
					return 'api/' . $route . (strlen($queryString) ? '?' . $queryString : '');
				};
			}
			else
			{
				return function($route, $queryString)
				{
					$suffix = $route . (strlen($queryString) ? (strlen($route) ? '&' : '') . $queryString : '');
					return 'index.php?api/' . $suffix;
				};
			}
		};
		$container['router.api.routes'] = $this->fromRegistry('routesApi',
			function(Container $c) { return $c['em']->getRepository('XF:Route')->rebuildRouteCache('api'); }
		);

		$container['logger'] = function($c)
		{
			$class = $this->extendClass('XF\Logger');
			return new $class($this);
		};

		$container['debugger'] = function($c)
		{
			$class = $this->extendClass('XF\Debugger');
			return new $class($this);
		};

		$container['contactUrl'] = function ($c)
		{
			$options = $c['options'];
			$router = $c['router.public'];

			if (!isset($options->contactUrl['type']))
			{
				return '';
			}

			switch ($options->contactUrl['type'])
			{
				case 'default': $url = $router->buildLink('misc/contact/'); break;
				case 'custom': $url = $options->contactUrl['custom']; break;
				default: $url = '';
			}
			return $url;
		};

		$container['privacyPolicyUrl'] = function ($c)
		{
			$options = $c['options'];
			$router = $c['router.public'];

			if (!isset($options->privacyPolicyUrl['type']))
			{
				return '';
			}

			switch ($options->privacyPolicyUrl['type'])
			{
				case 'default':
					return $router->buildLink('help/privacy-policy/'); break;
				case 'custom':
					return $options->privacyPolicyUrl['custom']; break;
				default:
					return '';
			}
		};

		$container['tosUrl'] = function ($c)
		{
			$options = $c['options'];
			$router = $c['router.public'];

			if (!isset($options->tosUrl['type']))
			{
				return '';
			}

			switch ($options->tosUrl['type'])
			{
				case 'default': $url = $router->buildLink('help/terms/'); break;
				case 'custom': $url = $options->tosUrl['custom']; break;
				default: $url = '';
			}
			return $url;
		};

		$container['homePageUrl'] = function(Container $c)
		{
			$options = $c['options'];
			$router = $c['router.public'];

			$homePageUrl = $options->homePageUrl;

			$this->extension()->fire('home_page_url', [&$homePageUrl, $router]);

			if ($homePageUrl)
			{
				/** @var \Closure $pather */
				$pather = $c['request.pather'];
				$homePageUrl = $pather($homePageUrl, 'full');
			}

			return $homePageUrl;
		};

		$container['navigation.compiler'] = function(Container $c)
		{
			return new \XF\Navigation\Compiler($c['templateCompiler']);
		};
		$container['navigation.file'] = 'navigation_cache.php'; // will be written to code-cache/codeCachePath

		$container['navigation.admin'] = function($c)
		{
			$class = \XF::extendClass('XF\AdminNavigation');
			return new $class($c['navigation.adminEntries']);
		};
		$container['navigation.adminEntries'] = $this->fromRegistry('adminNavigation',
			function(Container $c) { return $c['em']->getRepository('XF:AdminNavigation')->rebuildNavigationCache(); }
		);

		$container['db'] = function ($c)
		{
			$config = $c['config'];

			$dbConfig = $config['db'];
			$adapterClass = $dbConfig['adapterClass'];
			unset($dbConfig['adapterClass']);

			/** @var \XF\Db\AbstractAdapter $db */
			$db = new $adapterClass($dbConfig, $config['fullUnicode']);
			if (\XF::$debugMode)
			{
				$db->logQueries(true);
			}

			return $db;
		};

		$container->factory('cache', function($context, array $params, Container $c)
		{
			$cacheConfig = $c['config']['cache'];
			if (!$cacheConfig['enabled'])
			{
				return null;
			}

			$namespace = $cacheConfig['namespace'];

			if ($context)
			{
				if (empty($cacheConfig['context'][$context]))
				{
					return null;
				}

				$cacheConfig = $cacheConfig['context'][$context];
				if (!is_array($cacheConfig) || empty($cacheConfig['provider']))
				{
					return null;
				}

				if (!isset($cacheConfig['config']))
				{
					$cacheConfig['config'] = [];
				}
				if (isset($cacheConfig['namespace']))
				{
					$namespace = $cacheConfig['namespace'];
				}
			}

			/** @var CacheFactory $factory */
			$factory = $c['cache.factory'];
			$factory->setNamespace($namespace);
			return $factory->create($cacheConfig['provider'], $cacheConfig['config']);
		});

		$container['cache'] = function(Container $c)
		{
			return $c->create('cache', '');
		};
		$container['cache.factory'] = function($c)
		{
			// this cannot be dynamically extended because it's used by the registry
			// different types of cache providers can be configured via code in config.php
			return new CacheFactory();
		};

		$container['permission.cache'] = function ($c)
		{
			return new PermissionCache($c['db']);
		};
		$container['permission.builder'] = function ($c)
		{
			return new \XF\Permission\Builder(
				$c['db'], $c['em'], $this->getContentTypeField('permission_handler_class')
			);
		};

		$container['registry'] = function ($c)
		{
			return new DataRegistry($c['db'], $this->cache('registry'));
		};

		$container['simpleCache'] = function ($c)
		{
			$class = $this->extendClass('XF\SimpleCache');
			return new $class($c['simpleCache.data']);
		};
		$container['simpleCache.data'] = $this->fromRegistry('simpleCache', function() {
			$this->registry()->set('simpleCache', []);
			return [];
		});

		$container['options'] = $this->fromRegistry('options',
			function(Container $c) { return $c['em']->getRepository('XF:Option')->rebuildOptionCache(); },
			function(array $options)
			{
				return new \ArrayObject($options, \ArrayObject::ARRAY_AS_PROPS);
			}
		);

		$container['codeEventListeners'] = $this->fromRegistry('codeEventListeners',
			function(Container $c) { return $c['em']->getRepository('XF:CodeEventListener')->rebuildListenerCache(); }
		);

		$container['contentTypes'] = $this->fromRegistry('contentTypes',
			function(Container $c) { return $c['em']->getRepository('XF:ContentTypeField')->rebuildContentTypeCache(); }
		);

		$container['customFields.threads'] = $this->fromRegistry('threadFieldsInfo',
			function(Container $c) { return $c['em']->getRepository('XF:ThreadField')->rebuildFieldCache(); },
			function(array $threadFieldsInfo)
			{
				return new DefinitionSet($threadFieldsInfo);
			}
		);

		$container['customFields.users'] = $this->fromRegistry('userFieldsInfo',
			function(Container $c) { return $c['em']->getRepository('XF:UserField')->rebuildFieldCache(); },
			function(array $userFieldsInfo)
			{
				$definitionSet = new DefinitionSet($userFieldsInfo);
				$definitionSet->addFilter('registration', function(array $field)
				{
					return (!empty($field['show_registration']) || !empty($field['required']));
				});
				$definitionSet->addFilter('profile', function(array $field)
				{
					return !empty($field['viewable_profile']);
				});
				$definitionSet->addFilter('message', function(array $field)
				{
					return !empty($field['viewable_message']);
				});
				return $definitionSet;
			}
		);

		$container['displayStyles'] = $this->fromRegistry('displayStyles',
			function(Container $c) { return $c['em']->getRepository('XF:UserGroup')->rebuildDisplayStyleCache(); }
		);

		$container['reportCounts'] = $this->fromRegistry('reportCounts',
			function(Container $c) { return $c['em']->getRepository('XF:Report')->rebuildReportCounts(); }
		);

		$container['unapprovedCounts'] = $this->fromRegistry('unapprovedCounts',
			function(Container $c) { return $c['em']->getRepository('XF:ApprovalQueue')->rebuildUnapprovedCounts(); }
		);

		$container['nodeTypes'] = $this->fromRegistry('nodeTypes',
			function(Container $c) { return $c['em']->getRepository('XF:NodeType')->rebuildNodeTypeCache(); }
		);

		$container['notices'] = $this->fromRegistry('notices',
			function(Container $c) { return $c['em']->getRepository('XF:Notice')->rebuildNoticeCache(); }
		);
		$container['notices.lastReset'] = $this->fromRegistry('noticesLastReset',
			function(Container $c) { return $c['em']->getRepository('XF:Notice')->rebuildNoticeLastResetCache(); }
		);

		$container->factory('criteria', function($class, array $params, Container $c)
		{
			$class = \XF::stringToClass($class, '\%s\Criteria\%s');
			$class = $this->extendClass($class);

			array_unshift($params, $this);

			return $c->createObject($class, $params);
		}, false);

		$container['routeFilters'] = $this->fromRegistry('routeFilters',
			function(Container $c) { return $c['em']->getRepository('XF:RouteFilter')->rebuildRouteFilterCache(); }
		);

		$container['reactionDefault'] = function(Container $c)
		{
			return $c['reactions'][1];
		};

		$container['reactionColors'] = function(Container $c)
		{
			$output = [];
			foreach ($c['reactions'] AS $reactionId => $reaction)
			{
				$output[$reactionId] = $reaction['text_color'];
			}
			return $output;
		};

		$container['reactionSprites'] = $this->fromRegistry('reactionSprites',
			function(Container $c) { return $c['em']->getRepository('XF:Reaction')->rebuildReactionSpriteCache(); }
		);

		$container['reactions'] = $this->fromRegistry('reactions',
			function(Container $c) { return $c['em']->getRepository('XF:Reaction')->rebuildReactionCache(); }
		);

		$container['smilieSprites'] = $this->fromRegistry('smilieSprites',
			function(Container $c) { return $c['em']->getRepository('XF:Smilie')->rebuildSmilieSpriteCache(); }
		);

		$container['smilies'] = $this->fromRegistry('smilies',
			function(Container $c) { return $c['em']->getRepository('XF:Smilie')->rebuildSmilieCache(); }
		);

		$container['prefixes.thread'] = $this->fromRegistry('threadPrefixes',
			function(Container $c) { return $c['em']->getRepository('XF:ThreadPrefix')->rebuildPrefixCache(); }
		);

		$container['userBanners'] = $this->fromRegistry('userBanners',
			function(Container $c) { return $c['em']->getRepository('XF:UserGroup')->rebuildUserBannerCache(); }
		);

		$container['userTitleLadder'] = $this->fromRegistry('userTitleLadder',
			function(Container $c) { return $c['em']->getRepository('XF:UserTitleLadder')->rebuildLadderCache(); }
		);

		$container['forumStatistics'] = $this->fromRegistry('forumStatistics',
			function(Container $c) { return $c['em']->getRepository('XF:Counters')->rebuildForumStatisticsCache(); }
		);

		$container['connectedAccountCount'] = $this->fromRegistry('connectedAccountCount',
			function(Container $c) { return $c['em']->getRepository('XF:ConnectedAccount')->rebuildProviderCount(); }
		);

		$container['helpPageCount'] = $this->fromRegistry('helpPageCount',
			function(Container $c) { return $c['em']->getRepository('XF:HelpPage')->rebuildHelpPageCount(); }
		);

		$container['userUpgradeCount'] = $this->fromRegistry('userUpgradeCount',
			function(Container $c) { return $c['em']->getRepository('XF:UserUpgrade')->rebuildUpgradeCount(); }
		);

		$container['session'] = function ()
		{
			throw new \LogicException('The session key must be overridden.');
		};
		$container['session.public'] = function (Container $c)
		{
			$class = $this->extendClass('XF\Session\Session');

			/** @var \XF\Session\Session $session */
			$session = new $class($c['session.public.storage'], [
				'cookie' => 'session'
			]);
			return $session->start($c['request']);
		};
		$container['session.public.storage'] = function (Container $c)
		{
			$storage = null;
			$cache = $c['cache'];

			$this->fire('session_public_storage_setup', [$c, $cache, &$storage]);
			if ($storage)
			{
				if (!($storage instanceof \XF\Session\StorageInterface))
				{
					throw new \LogicException('Storage must be instance of XF\Session\StorageInterface. Received ' . get_class($storage));
				}
				return $storage;
			}

			if ($c['config']['cache']['sessions'] && $cache = $this->cache('sessions'))
			{
				return new \XF\Session\CacheStorage($cache, 'session_');
			}
			else
			{
				return new \XF\Session\DbStorage($c['db'], 'xf_session');
			}
		};

		$container['session.admin'] = function (Container $c)
		{
			$session = new \XF\Session\Session($c['session.admin.storage'], [
				'cookie' => 'session_admin'
			]);
			return $session->start($c['request']);
		};
		$container['session.admin.storage'] = function (Container $c)
		{
			return new \XF\Session\DbStorage($c['db'], 'xf_session_admin');
		};

		$container['session.install'] = function (Container $c)
		{
			$session = new \XF\Session\Session($c['session.install.storage'], [
				'cookie' => 'session_install'
			]);
			return $session->start($c['request']);
		};
		$container['session.install.storage'] = function (Container $c)
		{
			/** @var \XF\Db\SchemaManager $sm */
			$sm = $c['db']->getSchemaManager();

			try
			{
				if (!(bool)$sm->getTableStatus('xf_session_install'))
				{
					$mySql = new \XF\Install\Data\MySql();
					$tables = $mySql->getTables();
					$sm->createTable('xf_session_install', $tables['xf_session_install']);
				}
			}
			catch (\Exception $e) {}

			return new \XF\Session\DbStorage($c['db'], 'xf_session_install');
		};

		$container['session.api'] = function (Container $c)
		{
			$session = new \XF\Session\Session(new \XF\Session\NullStorage());
			return $session->start($c['request']);
		};

		$container['csrf.token'] = function(Container $c)
		{
			/** @var Http\Request $request */
			$request = $c['request'];

			$token = $request->getCookie('csrf');
			if (!$token)
			{
				$token = \XF::generateRandomString(16);
				$this->updateCsrfCookie($token);
			}

			/** @var \Closure $validator */
			$validator = $c['csrf.validator'];
			return \XF::$time . ',' . $validator($token, \XF::$time);
		};
		$container['csrf.validator'] = $container->wrap(function($value, $time)
		{
			return hash_hmac('md5', $value . $time, $this->config('globalSalt'));
		});

		$container['error'] = function ()
		{
			return new Error($this);
		};

		$container['em'] = function (Container $c)
		{
			return new Mvc\Entity\Manager($c['db'], $c['em.valueFormatter'], $c['extension']);
		};
		$container['em.valueFormatter'] = function (Container $c)
		{
			return new Mvc\Entity\ValueFormatter();
		};

		$container['mailer'] = function (Container $c)
		{
			/** @var \ArrayObject $options */
			$options = $c['options'];

			$mailerClass = $this->extendClass('XF\Mail\Mailer');

			/** @var \XF\Mail\Mailer $mailer */
			$mailer = new $mailerClass($c['mailer.templater'], $c['mailer.transport'], $c['mailer.styler'], $c['mailer.queue']);

			$mailer->setDefaultFrom(
				$options->defaultEmailAddress,
				$options->emailSenderName ?: $options->boardTitle
			);
			$mailer->setDefaultReturnPath(
				$options->bounceEmailAddress ?: $options->defaultEmailAddress,
				$options->enableVerp
			);

			$mailClass = $this->extendClass($mailer->getMailClass());
			$mailer->setMailClass($mailClass);

			$this->fire('mailer_setup', [$c, &$mailer]);

			return $mailer;
		};
		$container['mailer.transport'] = function(Container $c)
		{
			$config = $c['config'];
			if (!$config['enableMail'])
			{
				return \Swift_NullTransport::newInstance();
			}

			$transport = null;
			$this->fire('mailer_transport_setup', [$c, &$transport]);
			if ($transport)
			{
				return $transport;
			}

			/** @var \ArrayObject $options */
			$options = $c['options'];

			$mailerClass = $this->extendClass('XF\Mail\Mailer');

			if (is_array($options->emailTransport))
			{
				$method = $options->emailTransport['emailTransport'];
				$config = $options->emailTransport;
			}
			else
			{
				$method = 'sendmail';
				$config = [];
			}

			return $mailerClass::getTransportFromOption($method, $config);
		};
		$container['mailer.styler'] = function($c)
		{
			$rendererClass = $this->extendClass('XF\CssRenderer');
			$stylerClass = $this->extendClass('XF\Mail\Styler');

			return new $stylerClass(
				new $rendererClass($this, $c['mailer.templater'], $this->cache('css')),
				new \Pelago\Emogrifier()
			);
		};
		$container['mailer.queue'] = function(Container $c)
		{
			$config = $c['config'];
			if (!$config['enableMailQueue'])
			{
				return null;
			}

			$queueClass = $this->extendClass('XF\Mail\Queue');
			return new $queueClass($c['db']);
		};
		$container['mailer.templater'] = function (Container $c)
		{
			if ($c['app.classType'] != 'Pub')
			{
				// preload this in bulk as we will load them individually below
				$this->registry()->get(['routesPublic', 'routeFilters', 'styles']);
			}

			$templater = $this->setupTemplaterObject($c, '\XF\Mail\Templater');
			$templater->setStyle($c->create('style', $c['options']->defaultEmailStyleId));

			return $templater;
		};

		$container['fs'] = function(Container $c)
		{
			$mountsClass = $this->extendClass('XF\FsMounts');

			return $mountsClass::loadDefaultMounts($c['config']);
		};

		$container['spam'] = function($c)
		{
			$class = $this->extendClass('XF\SubContainer\Spam');
			return new $class($c, $this);
		};

		$container['http'] = function($c)
		{
			$class = $this->extendClass('XF\SubContainer\Http');
			return new $class($c, $this);
		};

		$container['oAuth'] = function($c)
		{
			$class = $this->extendClass('XF\SubContainer\OAuth');
			return new $class($c, $this);
		};

		$container['proxy'] = function($c)
		{
			$class = $this->extendClass('XF\SubContainer\Proxy');
			return new $class($c, $this);
		};

		$container['oembed'] = function($c)
		{
			$class = $this->extendClass('XF\SubContainer\Oembed');
			return new $class($c, $this);
		};

		$container['bounce'] = function($c)
		{
			$class = $this->extendClass('XF\SubContainer\Bounce');
			return new $class($c, $this);
		};

		$container['unsubscribe'] = function($c)
		{
			$class = $this->extendClass('XF\SubContainer\Unsubscribe');
			return new $class($c, $this);
		};

		$container['widget'] = function($c)
		{
			$class = $this->extendClass('XF\SubContainer\Widget');
			return new $class($c, $this);
		};

		$container['import'] = function($c)
		{
			$class = $this->extendClass('XF\SubContainer\Import');
			return new $class($c, $this);
		};

		$container->set('sitemap.builder', function(Container $c)
		{
			$class = 'XF\Sitemap\Builder';
			$class = $this->extendClass($class);

			$user = $c['em']->getRepository('XF:User')->getGuestUser();
			$types = $c['em']->getRepository('XF:SitemapLog')->getSitemapContentTypes();

			return new $class($this, $user, $types);
		}, false);

		$container->set('sitemap.renderer', function(Container $c)
		{
			$sitemapRepo = $this->repository('XF:SitemapLog');

			return new \XF\Sitemap\Renderer($this, $sitemapRepo->getActiveSitemap());
		}, false);

		$container['imageManager'] = function($c)
		{
			$manager = new \XF\Image\Manager($c['imageManager.defaultDriver'], $c['imageManager.extraDrivers']);
			$manager->setMaxResizePixels($c['config']['maxImageResizePixelCount']);

			return $manager;
		};

		$container['imageManager.defaultDriver'] = function($c)
		{
			return $c['options']->imageLibrary;
		};
		$container['imageManager.extraDrivers'] = [];

		$container['adminSearcher'] = function($c)
		{
			$class = $this->extendClass('XF\AdminSearch\Searcher');

			return new $class(
				$this,
				$this->getContentTypeField('admin_search_class')
			);
		};

		$container['search'] = function($c)
		{
			$class = $this->extendClass('XF\Search\Search');

			return new $class($c['search.source'], $this->getContentTypeField('search_handler_class'));
		};
		$container['search.source'] = function($c)
		{
			$source = null;
			$this->fire('search_source_setup', [$c, &$source]);
			if ($source)
			{
				if (!($source instanceof \XF\Search\Source\AbstractSource))
				{
					throw new \LogicException('Search source must be instance of XF\Search\Source\AbstractSource. Received ' . get_class($source));
				}
				return $source;
			}

			$mySqlFtClass = $this->extendClass('XF\Search\Source\MySqlFt');

			return new $mySqlFtClass($c['db'], $c['options']->searchMinWordLength);
		};

		$container->factory('stats.grouper', function($grouping, array $params, Container $c)
		{
			$groupings = $c['stats.groupings'];
			if (!isset($groupings[$grouping]))
			{
				throw new \InvalidArgumentException("Unknown grouping '$grouping'");
			}

			$grouperClass = \XF::stringToClass($groupings[$grouping], '%s\Stats\Grouper\%s');
			$grouperClass = \XF::extendClass($grouperClass);

			$language = isset($params[0]) ? $params[0] : \XF::language();
			return new $grouperClass($language);
		});
		$container['stats.groupings'] = [
			'daily' => 'XF:Daily',
			'weekly' => 'XF:Weekly',
			'monthly' => 'XF:Monthly'
		];

		$container->factory('language', function($id, array $params, Container $c)
		{
			$id = intval($id);

			$cache = $c['language.cache'];
			if (!$id || !isset($cache[$id]))
			{
				$id = $c['options']->defaultLanguageId;
			}

			if (isset($cache[$id]))
			{
				$groupPath = File::getCodeCachePath() . '/phrase_groups';

				$class = $this->extendClass('XF\Language');
				return new $class($id, $cache[$id], $c['db'], $groupPath);
			}
			else
			{
				return $c['language.fallback'];
			}
		});

		$container['language.fallback'] = function(Container $c)
		{
			$groupPath = File::getCodeCachePath() . '/phrase_groups';

			$class = $this->extendClass('XF\Language');
			return new $class(0, [], $c['db'], $groupPath);
		};
		$container['language.cache'] = $this->fromRegistry('languages',
			function(Container $c) { return $c['em']->getRepository('XF:Language')->rebuildLanguageCache(); }
		);
		$container['language.all'] = function(Container $c)
		{
			$output = [];
			foreach (array_keys($c['language.cache']) AS $languageId)
			{
				$output[$languageId] = $c->create('language', $languageId);
			}

			return $output;
		};

		$container->factory('style', function($id, array $params, Container $c)
		{
			$id = intval($id);

			$cache = $c['style.cache'];
			if (!$id || !isset($cache[$id]))
			{
				$id = $c['options']->defaultStyleId;
			}

			if (isset($cache[$id]))
			{
				$style = $cache[$id];
				$masterStyleProperties = $c['style.masterStyleProperties'];
				if (is_array($style['properties']))
				{
					$style['properties'] += $masterStyleProperties;
				}
				else
				{
					$style['properties'] = $masterStyleProperties;
				}

				$class = $this->extendClass('XF\Style');
				return new $class($id, $style);
			}
			else
			{
				return $c['style.fallback'];
			}
		});

		$container['style.fallback'] = function(Container $c)
		{
			$lastModified = $c['style.masterModifiedDate'];
			$masterStyleProperties = $c['style.masterStyleProperties'];
			$class = $this->extendClass('XF\Style');
			return new $class(0, $masterStyleProperties, $lastModified);
		};
		$container['style.masterModifiedDate'] = $this->fromRegistry('masterStyleModifiedDate',
			function(Container $c) { return \XF::$time; }
		);
		$container['style.masterStyleProperties'] = $this->fromRegistry('masterStyleProperties',
			function(Container $c) { return []; }
		);
		$container['style.cache'] = $this->fromRegistry('styles',
			function(Container $c) { return $c['em']->getRepository('XF:Style')->rebuildStyleCache(); }
		);
		$container['style.all'] = function(Container $c)
		{
			$output = [];
			foreach (array_keys($c['style.cache']) AS $styleId)
			{
				$output[$styleId] = $c->create('style', $styleId);
			}

			return $output;
		};

		$container['defaultNavigationId'] = function(Container $c)
		{
			return '_default';
		};

		$container['uploadMaxFilesize'] = function(Container $c)
		{
			return \XF\Util\Php::getUploadMaxFilesize();
		};

		// We do not recommend trying to extend this class or the tags/functions it defines. Doing so may interfere
		// with the upgrade process. If you must do so, it should not be part of an add-on. It should be done
		// unconditionally via config.php. However, do so at your own peril as the worst case would potentially be
		// an un-upgradeable installation.
		$container['templateCompiler'] = function (Container $c)
		{
			return new Template\Compiler();
		};

		$container['templater'] = function (Container $c)
		{
			return $this->setupTemplaterObject($c, '\XF\Template\Templater');
		};
		// the default config is in the templater
		$container['templater.config.filters'] = [];
		$container['templater.config.functions'] = [];
		$container['templater.config.tests'] = [];

		$container['cssWriter'] = function($c)
		{
			$rendererClass = $this->extendClass('XF\CssRenderer');
			$renderer = new $rendererClass($this, $c['templater'], $this->cache('css'));

			$class = $this->extendClass('XF\CssWriter');

			/** @var \XF\CssWriter $writer */
			$writer = new $class($this, $renderer);
			$writer->setValidator($c['css.validator']);

			return $writer;
		};
		$container['css.validator'] = $container->wrap(function(array $templates)
		{
			return hash_hmac('sha1', implode(',', $templates), $this->config('globalSalt'));
		});

		$container['addon.manager'] = function($c)
		{
			$class = $this->extendClass('XF\AddOn\Manager');
			return new $class(\XF::getAddOnDirectory());
		};
		$container['addon.dataManager'] = function($c)
		{
			$class = $this->extendClass('XF\AddOn\DataManager');
			return new $class($c['em']);
		};
		$container['addon.cache'] = $this->fromRegistry('addOns',
			function(Container $c) { return $c['addon.dataManager']->rebuildActiveAddOnCache(); }
		);
		$container['addon.composer'] = $this->fromRegistry('addOnsComposer',
			function(Container $c)
			{
				$c['addon.dataManager']->rebuildActiveAddOnCache();
				return $this->container['registry']['addOnsComposer'];
			}
		);

		$container['bannedEmails'] = $this->fromRegistry('bannedEmails',
			function(Container $c) { return $c['em']->getRepository('XF:Banning')->rebuildBannedEmailCache(); }
		);

		$container['bannedIps'] = $this->fromRegistry('bannedIps',
			function(Container $c) { return $c['em']->getRepository('XF:Banning')->rebuildBannedIpCache(); }
		);

		$container['discouragedIps'] = $this->fromRegistry('discouragedIps',
			function(Container $c) { return $c['em']->getRepository('XF:Banning')->rebuildDiscouragedIpCache(); }
		);

		$container['string.formatter'] = function(Container $c)
		{
			$class = $this->extendClass('XF\Str\Formatter');
			/** @var \XF\Str\Formatter $formatter */
			$formatter = new $class();

			$options = $c['options'];
			$formatter->setCensorRules($options->censorWords ?: [], $options->censorCharacter);
			$formatter->addSmilies($c['smilies']);
			$formatter->setSmilieHtmlPather($c['request.pather']);
			$formatter->setProxyHandler(function($type, $url, array $options = [])
			{
				return $this->proxy()->generateExtended($type, $url, $options);
			});

			$this->fire('string_formatter_setup', [$c, &$formatter]);

			return $formatter;
		};

		$container['bbCode'] = function($c)
		{
			$class = $this->extendClass('XF\SubContainer\BbCode');
			return new $class($c, $this);
		};

		$container['apiDocs'] = function($c)
		{
			$class = $this->extendClass('XF\SubContainer\ApiDocs');
			return new $class($c, $this);
		};

		$container['job.manager'] = function($c)
		{
			$class = $this->extendClass('XF\Job\Manager');
			return new $class($this, $c['job.manual.allow'], $c['job.manual.force']);
		};
		$container['job.runTime'] = $this->fromRegistry('autoJobRun',
			function(Container $c) { return $c['job.manager']->updateNextRunTime(); }
		);
		$container['job.manual.allow'] = false;
		$container['job.manual.force'] = false;

		$container['development.output'] = function(Container $c)
		{
			$config = $c['config'];

			$skip = $config['development']['skipAddOns'];
			if (!is_array($skip))
			{
				$skip = ['XF', 'XF*'];
			}

			$class = $this->extendClass('XF\DevelopmentOutput');
			return new $class(
				$config['development']['enabled'],
				\XF::getAddOnDirectory(),
				$skip
			);
		};

		$container['development.jsResponse'] = function(Container $c)
		{
			$class = $this->extendClass('XF\DevJsResponse');
			return new $class($this);
		};

		$container['designer.output'] = function(Container $c)
		{
			$config = $c['config'];

			$class = $this->extendClass('XF\DesignerOutput');
			return new $class(
				$config['designer']['enabled'],
				$config['designer']['basePath']
			);
		};

		$container['extension'] = function(Container $c)
		{
			$config = $c['config'];
			if (!$config['enableListeners'])
			{
				// disable
				return new \XF\Extension();
			}

			try
			{
				$listeners = $c['extension.listeners'];
				$classExtensions = $c['extension.classExtensions'];
			}
			catch (\XF\Db\Exception $e)
			{
				$listeners = [];
				$classExtensions = [];
			}

			return new \XF\Extension($listeners, $classExtensions);
		};
		// note: these don't trigger normal rebuilds to prevent the possibility of an infinite loop
		$container['extension.listeners'] = $this->fromRegistry('codeEventListeners',
			function(Container $c) { $c['registry']->set('codeEventListeners', []); return []; }
		);
		$container['extension.classExtensions'] = $this->fromRegistry('classExtensions',
			function(Container $c) { $c['registry']->set('classExtensions', []); return []; }
		);

		$container->factory('controller', function($class, array $params, Container $c)
		{
			$class = \XF::stringToClass($class, '%s\%s\Controller\%s', $c['app.classType']);
			$class = $this->extendClass($class);

			$passParams = [
				$this,
				isset($params['request']) ? $params['request'] : $c['request']
			];
			return $c->createObject($class, $passParams, true);
		}, false);

		$container->factory('renderer', function($type, array $params, Container $c)
		{
			$type = strtolower($type);
			switch ($type)
			{
				case 'html': $class = 'Html'; break;
				case 'json': $class = 'Json'; break;
				case 'xml': $class = 'Xml'; break;
				case 'raw': $class = 'Raw'; break;
				case 'rss': $class = 'Rss'; break;
				default:
					$unknownCallback = $c['renderer.unknown'];
					$class = $unknownCallback($type);
			}

			$params = [
				$c->getInvokableFactory('view'),
				$c['response'],
				$c['templater']
			];
			if (strpos($class, '\\') === false)
			{
				$class = 'XF\Mvc\Renderer\\' . $class;
			}
			$class = $this->extendClass($class);

			return $c->createObject($class, $params);
		}, false);

		$container['renderer.unknown'] = function()
		{
			return function($rendererType)
			{
				return 'Html';
			};
		};

		$container['view.defaultClass'] = 'XF\Mvc\View';

		$container->factory('view', function($class, array $params, Container $c)
		{
			$class = \XF::stringToClass($class, '%s\%s\View\%s', $c['app.classType']);
			$class = $this->extendClass($class, $c['view.defaultClass']);

			if (!$class || !class_exists($class))
			{
				$class = $c['view.defaultClass'];
			}

			return $c->createObject($class, $params);
		}, false);

		$container->factory('job', function($class, array $params, Container $c)
		{
			$class = \XF::stringToClass($class, '\%s\Job\%s');
			$class = $this->extendClass($class);

			array_unshift($params, $this);

			return $c->createObject($class, $params, true);
		}, false);

		$container->factory('searcher', function($class, array $params, Container $c)
		{
			$class = \XF::stringToClass($class, '\%s\Searcher\%s');
			$class = $this->extendClass($class);

			array_unshift($params, $c['em']);

			return $c->createObject($class, $params);
		}, false);

		$container->factory('auth', function($class, array $params, Container $c)
		{
			$class = \XF::stringToClass($class, '\%s\Authentication\%s');
			$class = $this->extendClass($class);

			return $c->createObject($class, $params);
		}, false);

		$container['auth.default'] = 'XF:Core12';

		$container->factory('service', function($class, array $params, Container $c)
		{
			$class = \XF::stringToClass($class, '\%s\Service\%s');
			$class = $this->extendClass($class);

			array_unshift($params, $this);

			return $c->createObject($class, $params);
		}, false);

		$container->factory('validator', function($class, array $params, Container $c)
		{
			if (strpos($class, ':') === false && strpos($class, '\\') === false)
			{
				$class = "XF:$class";
			}

			$class = \XF::stringToClass($class, '\%s\Validator\%s');
			$class = $this->extendClass($class);

			array_unshift($params, $this);

			return $c->createObject($class, $params);
		}, false);

		$container->factory('data', function($class, array $params, Container $c)
		{
			$class = \XF::stringToClass($class, '\%s\Data\%s');
			$class = $this->extendClass($class);

			array_unshift($params, $this);

			return $c->createObject($class, $params);
		}, true);

		$container->factory('captcha', function($class, array $params, Container $c)
		{
			if (strpos($class, ':') === false && strpos($class, '\\') === false)
			{
				$class = "XF:$class";
			}
			$class = \XF::stringToClass($class, '\%s\Captcha\%s');
			if (!class_exists($class))
			{
				$this->error()->logError('CAPTCHA class ' . htmlspecialchars($class) . ' does not exist. Falling back to default provider \\' . htmlspecialchars($c['captcha.default']) . '.');

				$class = $c['captcha.default'];
			}
			$class = $this->extendClass($class);

			array_unshift($params, $this);

			return $c->createObject($class, $params);
		}, true);

		$container['captcha.default'] = 'XF\Captcha\ReCaptcha';

		$container->factory('notifier', function($class, array $params, Container $c)
		{
			$class = \XF::stringToClass($class, '\%s\Notifier\%s');
			$class = $this->extendClass($class);

			array_unshift($params, $this);

			return $c->createObject($class, $params);
		}, false);

		if (function_exists('xdebug_disable'))
		{
			xdebug_disable(); // use PHP's own stack trace in case of errors
		}

		$this->initializeExtra();
	}

	/**
	 * @param               $key
	 * @param \Closure      $rebuildFunction
	 * @param \Closure|null $decoratorFunction
	 *
	 * @return \Closure
	 * @throws \XF\Db\Exception
	 */
	public function fromRegistry($key, \Closure $rebuildFunction, \Closure $decoratorFunction = null)
	{
		return function(Container $c) use ($key, $rebuildFunction, $decoratorFunction)
		{
			$data = $this->container['registry'][$key];

			if ($data === null)
			{
				$data = $rebuildFunction($c, $key);
			}

			return $decoratorFunction ? $decoratorFunction($data, $c, $key) : $data;
		};
	}

	/**
	 * @param Container $c
	 * @param string    $class
	 *
	 * @return Templater
	 * @throws \Exception
	 */
	public function setupTemplaterObject(Container $c, $class)
	{
		$config = $c['config'];

		$class = $this->extendClass($class);

		/** @var \XF\Template\Templater $templater */
		$templater = new $class(
			$this,
			\XF::language(),
			File::getCodeCachePath() . '/templates'
		);
		$templater->addDefaultHandlers();
		$templater->addFilters($c['templater.config.filters']);
		$templater->addFunctions($c['templater.config.functions']);
		$templater->addTests($c['templater.config.tests']);
		if ($config['development']['enabled'])
		{
			$templater->addTemplateWatcher($c['development.output']->getHandler('XF:Template'));
		}
		if ($config['designer']['enabled'])
		{
			$templater->addTemplateWatcher($c['designer.output']->getHandler('XF:Template'));
		}

		$templater->setCssValidator($c['css.validator']);

		$options = $c['options'];

		$templater->setJquerySource($c['jQueryVersion'], $options->jQuerySource);
		$templater->setJsVersion($c['jsVersion']);
		$templater->setJsBaseUrl($config['javaScriptUrl']);

		$templater->setDynamicDefaultAvatars($options->dynamicAvatarEnable);

		$templater->setMediaSites($c['bbCode.media']);

		$templater->setUserTitleLadder($c['userTitleLadder'], $options->userTitleLadderField);
		$templater->setUserBanners($c['userBanners'], $options->userBanners ?: []);
		$templater->setGroupStyles($c['displayStyles']);

		$templater->setWidgetPositions($c['widget.widgetPosition'] ?: []);

		$this->fire('templater_setup', [$c, &$templater]);

		return $templater;
	}

	public function initializeExtra()
	{
	}

	public function setup(array $options = [])
	{
		$config = $this->container('config');

		if (!$config['exists'])
		{
			if ($config['legacyExists'])
			{
				echo 'The site is currently being upgraded. Please check back later.';
				exit;
			}
			else if (\XF\Util\File::installLockExists())
			{
				echo "Couldn't load src/config.php file.";
				exit;
			}
			else
			{
				header('Location: install/index.php');
				exit;
			}
		}

		$this->checkDebugMode();
		$this->checkDbWriteForced();

		$preLoadExtra = isset($options['preLoad']) ? $options['preLoad'] : [];
		$this->preLoadData($preLoadExtra);

		$this->setupAddOnComposerAutoload();

		$this->fire('app_setup', [$this]);
	}

	protected function preLoadData(array $typeSpecific = [])
	{
		try
		{
			$keys = array_merge($this->preLoadShared, $this->preLoadLocal, $typeSpecific);
			$this->registry()->get($keys);
		}
		catch (\Exception $e) {}
	}

	public function start($allowShortCircuit = false)
	{
		return null;
	}

	protected function getVisitorFromSession(\XF\Session\Session $session, array $extraWith = [])
	{
		$userRepo = $this->repository('XF:User');
		$sessionUserId = $session->userId;
		$user = $userRepo->getVisitor($sessionUserId, $extraWith);

		if ($user->user_id && $user->user_id == $sessionUserId)
		{
			$userPasswordDate = $user->Profile ? $user->Profile->password_date : 0;
			if ($session->passwordDate != $userPasswordDate)
			{
				$session->logoutUser();
				$user = $userRepo->getVisitor(0);
			}
		}

		return $user;
	}

	public function preDispatch(RouteMatch $match)
	{

	}

	public function postDispatch(AbstractReply $reply, RouteMatch $finalMatch, RouteMatch $originalMatch)
	{

	}

	public function preRender(AbstractReply $reply, $responseType)
	{
		$this->templater()->addDefaultParam('xf', $this->getGlobalTemplateData($reply));
	}

	public function getCustomFields($type, $group = null, array $onlyInclude = null, array $additionalFilters = [])
	{
		/** @var \XF\CustomField\DefinitionSet $definitionSet */
		$definitionSet = $this->container["customFields.$type"];

		if (!$definitionSet)
		{
			return null;
		}

		if ($group !== null)
		{
			$definitionSet = $definitionSet->filterGroup($group);
		}

		if (is_array($onlyInclude))
		{
			$definitionSet = $definitionSet->filterOnly($onlyInclude);
		}

		if ($additionalFilters)
		{
			$definitionSet = $definitionSet->filter($additionalFilters);
		}

		return $definitionSet;
	}

	public function getCustomFieldsForEdit(
		$type, \XF\CustomField\Set $set, $editMode = 'user',
		$group = null, array $onlyInclude = null, array $additionalFilters = []
	)
	{
		$definitionSet = $this->getCustomFields($type, $group, $onlyInclude, $additionalFilters);
		if (!$definitionSet)
		{
			return null;
		}

		return $definitionSet->filterEditable($set, $editMode);
	}

	public function getGlobalTemplateData(AbstractReply $reply = null)
	{
		$request = $this->request();

		$jobRunTime = $this['job.runTime'];
		$language = \XF::language();
		$config = $this->config();

		$cookieConfig = $config['cookie'];
		$cookieConfig['secure'] = $request->isSecure() ? true : false;

		$data = [
			'versionId' => \XF::$versionId,
			'version' => \XF::$version,
			'app' => $this,
			'request' => $request,
			'uri' => $request->getRequestUri(),
			'fullUri' => $request->getFullRequestUri(),
			'time' => \XF::$time,
			'timeDetails' => $language->getDayStartTimestamps(),
			'debug' => \XF::$debugMode,
			'development' => \XF::$developmentMode,
			'designer' => $config['designer']['enabled'],
			'visitor' => \XF::visitor(),
			'session' => $this->session(),
			'cookie' => $cookieConfig,
			'enableRtnProtect' => $config['enableReverseTabnabbingProtection'],
			'language' => $language,
			'style' => $this->templater()->getStyle(),
			'isRtl' => $language->isRtl(),
			'options' => $this->options(),
			'reactions' => $this->get('reactions'),
			'reactionsActive' => array_filter($this->get('reactions'), function(array $reaction)
			{
				return ($reaction['active'] === true);
			}),
			'addOns' => $this->container['addon.cache'],
			'runJobs' => ($jobRunTime && $jobRunTime <= \XF::$time),
			'simpleCache' => $this->simpleCache(),
			'livePayments' => $config['enableLivePayments'],
			'fullJs' => $config['development']['fullJs'],
			'contactUrl' => $this->container['contactUrl'],
			'privacyPolicyUrl' => $this->container['privacyPolicyUrl'],
			'tosUrl' => $this->container['tosUrl'],
			'homePageUrl' => $this->container['homePageUrl'],
			'helpPageCount' => $this->container['helpPageCount'],
			'uploadMaxFilesize' => $this->container['uploadMaxFilesize'],
			'allowedVideoExtensions' => array_keys($this->container['inlineVideoTypes'])
		];

		if ($reply)
		{
			$replyData = [
				'controller' => $reply->getControllerClass(),
				'action' => $reply->getAction(),
				'section' => $reply->getSectionContext(),
				'containerKey' => $reply->getContainerKey(),
				'contentKey' => $reply->getContentKey()
			];

			if ($reply instanceof \XF\Mvc\Reply\View)
			{
				$replyData['view'] = $reply->getViewClass();
				$replyData['template'] = $reply->getTemplateName();
			}
			else if ($reply instanceof \XF\Mvc\Reply\Error || $reply->getResponseCode() >= 400)
			{
				$replyData['template'] = 'error';
			}
			else if ($reply instanceof \XF\Mvc\Reply\Message)
			{
				$replyData['template'] = 'message_page';
			}

			$data['reply'] = $replyData;
		}

		$this->fire('templater_global_data', [$this, &$data, $reply]);

		return $data;
	}

	protected $updateCsrfCookie = false;

	public function updateCsrfCookie($newValue)
	{
		$this->updateCsrfCookie = $newValue;
	}

	public function complete(Http\Response $response)
	{
		if (!$response->headerExists('Expires'))
		{
			$response->header('Expires', 'Thu, 19 Nov 1981 08:52:00 GMT');
		}
		if (!$response->headerExists('Cache-control'))
		{
			$response->header('Cache-control', 'private, no-cache, max-age=0');
		}

		if ($this->updateCsrfCookie)
		{
			$response->setCookie('csrf', $this->updateCsrfCookie, 0, null, false);
		}

		if ($this->container->isCached('db'))
		{
			$db = $this->db();
			if ($db instanceof \XF\Db\ReplicationAdapterInterface && $db->isForcedToWriteServerExplicit())
			{
				$forceTime = $db->getForceToWriteServerLength();
				if ($forceTime > 0)
				{
					$response->setCookie('dbWriteForced', time());
				}
			}
		}

		$this->fire('app_complete', [$this, &$response]);
	}

	public function finalOutputFilter(Http\Response $response)
	{
		if (\XF::$debugMode && $this->request()->get('_debug'))
		{
			$response->contentType('text/html', 'utf-8');
			$response->body($this->debugger()->getDebugPageHtml());
		}

		$this->fire('app_final_output', [$this, &$response]);

		return $response;
	}

	public function getErrorRoute($action, array $params = [], $responseType = 'html')
	{
		return $this->router()->getNewRouteMatch('XF:Error', $action, $params, $responseType);
	}

	public function renderPage($content, AbstractReply $reply, AbstractRenderer $renderer)
	{
		if ($reply instanceof Redirect)
		{
			return $content;
		}

		if ($renderer instanceof Mvc\Renderer\Html)
		{
			$content = strval($content);
			$pageParams = $renderer->getTemplater()->pageParams;
			return $this->renderPageHtml($content, $pageParams, $reply, $renderer);
		}
		else
		{
			return $content;
		}
	}

	protected function renderPageHtml($content, array $params, AbstractReply $reply, AbstractRenderer $renderer)
	{
		return $content;
	}

	/**
	 * @param string $hash
	 *
	 * Generates the hash used for redirects, such as foo/bar/#redirect-anchor
	 *
	 * @return string
	 */
	public function getRedirectHash($hash)
	{
		return '__' . $hash;
	}

	public function getDynamicRedirect($fallbackUrl = null, $useReferrer = true)
	{
		$request = $this->request();

		$redirect = $request->filter('_xfRedirect', 'str');
		if (!$redirect && $useReferrer)
		{
			$redirect = $request->getServer('HTTP_X_AJAX_REFERER');
			if (!$redirect)
			{
				$redirect = $request->getServer('HTTP_REFERER');
			}
		}

		if ($redirect && preg_match('/./su', $redirect))
		{
			if (strpos($redirect, "\n") === false && strpos($redirect, "\r") === false)
			{
				$fullBasePath = $request->getFullBasePath();

				$fullRedirect = $request->convertToAbsoluteUri($redirect);
				$redirectParts = @parse_url($fullRedirect);
				if ($redirectParts && !empty($redirectParts['host']))
				{
					$pageParts = @parse_url($fullBasePath);

					if ($pageParts && !empty($pageParts['host']) && $pageParts['host'] == $redirectParts['host'])
					{
						return $fullRedirect;
					}
				}
			}
		}

		if ($fallbackUrl === null)
		{
			$fallbackUrl = $this->router()->buildLink('index');
		}
		return $fallbackUrl;
	}

	public function getDynamicRedirectIfNot($notUrl, $fallbackUrl = null, $useReferrer = true)
	{
		$request = $this->request();

		$redirect = $this->getDynamicRedirect($fallbackUrl, $useReferrer);
		$notUrl = $request->convertToAbsoluteUri($notUrl);

		if (strpos($redirect, $notUrl) === 0)
		{
			// the URL we can't redirect to is at the start
			if ($fallbackUrl === false)
			{
				$fallbackUrl = $this->router()->buildLink('index');
			}

			return $request->convertToAbsoluteUri($fallbackUrl);
		}
		else
		{
			return $redirect;
		}
	}

	public function applyExternalDataUrl($externalPath, $canonical = false)
	{
		$externalDataUrl = $this->config('externalDataUrl');
		if ($externalDataUrl instanceof \Closure)
		{
			$url = $externalDataUrl($externalPath, $canonical);
		}
		else
		{
			$url = "$externalDataUrl/$externalPath";
		}

		/** @var \Closure $pather */
		$pather = $this->container('request.pather');

		return $pather($url, ($canonical ? 'canonical' : 'base'));
	}

	public function assertConfigExists()
	{
		if (!$this->container['config']['exists'])
		{
			echo 'Config.php does not exist.';
			exit;
		}
	}

	public function checkDebugMode()
	{
		$config = $this->container['config'];
		if ($config['development']['enabled'])
		{
			$config['debug'] = true;
			\XF::$developmentMode = true;
		}

		if ($config['debug'])
		{
			\XF::$debugMode = true;
			@ini_set('display_errors', true);
		}
	}

	public function checkDbWriteForced()
	{
		// reading this cookie directly as we do this before the DB is initialized and therefore
		// we haven't actually fetched things from the registry
		$cookieName = $this->container['config']['cookie']['prefix'] . 'dbWriteForced';
		if (isset($_COOKIE[$cookieName]) && is_scalar($_COOKIE[$cookieName]))
		{
			$writeForced = intval($_COOKIE[$cookieName]);
		}
		else
		{
			$writeForced = 0;
		}

		if ($writeForced)
		{
			$db = $this->db();
			if ($db instanceof \XF\Db\ReplicationAdapterInterface)
			{
				$forceTime = $db->getForceToWriteServerLength();
				if ($forceTime > 0 && $writeForced + $forceTime >= time())
				{
					$db->forceToWriteServer('implicit');
				}
			}
		}
	}

	public function setupAddOnComposerAutoload()
	{
		if (!$this->config('enableListeners'))
		{
			return;
		}

		/** @var \XF\AddOn\Manager $addOnManager */
		$addOnManager = $this->container['addon.manager'];
		$addOns = $this->container['addon.composer'];

		foreach ($addOns AS $addOnId => $composerPath)
		{
			if ($addOnId == 'XF' || !$composerPath)
			{
				continue;
			}

			$addOnPath = $addOnManager->getAddOnPath($addOnId);
			\XF::registerComposerAutoloadDir($addOnPath . \XF::$DS . $composerPath);
		}
	}

	public function run()
	{
		$response = $this->start(true);
		if (!($response instanceof Http\Response))
		{
			$dispatcher = $this->dispatcher();
			$response = $dispatcher->run();
		}

		$this->complete($response);
		$response = $this->finalOutputFilter($response);

		return $response;
	}

	public function logException($e, $rollback = false, $messagePrefix = '')
	{
		$this->error()->logException($e, $rollback, $messagePrefix);
	}

	public function displayFatalExceptionMessage($e)
	{
		$this->error()->displayFatalExceptionMessage($e);
	}

	public function get($key)
	{
		return $this->container->offsetGet($key);
	}

	public function create($type, $key, array $params = [])
	{
		return $this->container->create($type, $key, $params);
	}

	/**
	 * @param mixed $key
	 *
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->container->offsetGet($key);
	}

	/**
	 * @param mixed $key
	 * @param mixed $value
	 */
	public function offsetSet($key, $value)
	{
		$this->container->offsetSet($key, $value);
	}

	/**
	 * @param mixed $key
	 *
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return $this->container->offsetExists($key);
	}

	/**
	 * @param mixed $key
	 */
	public function offsetUnset($key)
	{
		$this->container->offsetUnset($key);
	}

	/**
	 * @param mixed $key
	 *
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->container->offsetGet($key);
	}

	/**
	 * @param mixed $key
	 * @param mixed $value
	 */
	public function __set($key, $value)
	{
		$this->container->offsetSet($key, $value);
	}

	/**
	 * @param string|null
	 *
	 * @return mixed
	 */
	public function config($key = null)
	{
		$config = $this->container['config'];

		if ($key)
		{
			return isset($config[$key]) ? $config[$key] : null;
		}
		else
		{
			return $config;
		}
	}

	/**
	 * @return \XF\Http\Request
	 */
	public function request()
	{
		return $this->container['request'];
	}

	/**
	 * @return \XF\InputFilterer
	 */
	public function inputFilterer()
	{
		return $this->container['inputFilterer'];
	}

	/**
	 * @return \XF\Http\Response
	 */
	public function response()
	{
		return $this->container['response'];
	}

	/**
	 * @return Dispatcher
	 */
	public function dispatcher()
	{
		return $this->container['dispatcher'];
	}

	/**
	 * @param string|null $type
	 *
	 * @return \XF\Mvc\Router
	 */
	public function router($type = null)
	{
		return $type ? $this->container['router.' . $type] : $this->container['router'];
	}

	/**
	 * @return \XF\Db\AbstractAdapter
	 */
	public function db()
	{
		return $this->container['db'];
	}

	/**
	 * @param string $context Context of cache to load from. Empty represents the global cache.
	 * @param bool $fallbackToGlobal If true and no specific cache is available, fallback to the global cache
	 *
	 * @return \Doctrine\Common\Cache\CacheProvider|null
	 */
	public function cache($context = '', $fallbackToGlobal = true)
	{
		$cache = $this->container->create('cache', $context);
		if (!$cache && $fallbackToGlobal && strlen($context))
		{
			$cache = $this->container->create('cache', '');
		}

		return $cache;
	}

	/**
	 * @return PermissionCache
	 */
	public function permissionCache()
	{
		return $this->container['permission.cache'];
	}

	/**
	 * @return \XF\Permission\Builder
	 */
	public function permissionBuilder()
	{
		return $this->container['permission.builder'];
	}

	/**
	 * @return \XF\DataRegistry
	 */
	public function registry()
	{
		return $this->container['registry'];
	}

	/**
	 * @return \XF\SimpleCache
	 */
	public function simpleCache()
	{
		return $this->container['simpleCache'];
	}

	/**
	 * @return \ArrayObject
	 */
	public function options()
	{
		return $this->container['options'];
	}

	/**
	 * @return \XF\Mail\Mailer
	 */
	public function mailer()
	{
		return $this->container['mailer'];
	}

	/**
	 * @return \XF\Mail\Queue
	 */
	public function mailQueue()
	{
		return $this->container['mailer.queue'];
	}

	/**
	 * @return \League\Flysystem\MountManager
	 */
	public function fs()
	{
		return $this->container['fs'];
	}

	public function getContentTypeField($field)
	{
		$output = [];
		foreach ($this->container['contentTypes'] AS $type => $fields)
		{
			if (isset($fields[$field]))
			{
				$output[$type] = $fields[$field];
			}
		}

		return $output;
	}

	public function getContentTypeFieldValue($type, $field)
	{
		$types = $this->container['contentTypes'];
		return isset($types[$type][$field]) ? $types[$type][$field] : null;
	}

	public function getContentTypePhraseName($type, $plural = false)
	{
		$types = $this->container['contentTypes'];
		if (!isset($types[$type]))
		{
			return '';
		}
		$fields = $types[$type];

		if ($plural)
		{
			if (isset($fields['phrase_plural']))
			{
				return $fields['phrase_plural'];
			}
			else if (isset($fields['phrase']))
			{
				return $fields['phrase'] . 's';
			}
			else
			{
				return "{$type}s";
			}
		}
		else
		{
			return isset($fields['phrase']) ? $fields['phrase'] : $type;
		}
	}

	public function getContentTypePhrase($type, $plural = false)
	{
		return \XF::phrase($this->getContentTypePhraseName($type, $plural));
	}

	public function getContentTypePhrases($plural = false, $withField = null)
	{
		$output = [];
		foreach ($this->container['contentTypes'] AS $type => $fields)
		{
			if (!$withField || isset($fields[$withField]))
			{
				if ($plural)
				{
					if (isset($fields['phrase_plural']))
					{
						$phrase = $fields['phrase_plural'];
					}
					else if (isset($fields['phrase']))
					{
						$phrase = $fields['phrase'] . 's';
					}
					else
					{
						$phrase = "{$type}s";
					}
				}
				else
				{
					$phrase = isset($fields['phrase']) ? $fields['phrase'] : $type;
				}
				$output[$type] = \XF::phrase($phrase);
			}
		}

		return $output;
	}

	/**
	 * @return \XF\Session\Session
	 */
	public function session()
	{
		return $this->container['session'];
	}

	/**
	 * @return Error
	 */
	public function error()
	{
		return $this->container['error'];
	}

	/**
	 * @return \XF\Mvc\Entity\Manager
	 */
	public function em()
	{
		return $this->container['em'];
	}

	public function find($entity, $id, $with = [])
	{
		return $this->em()->find($entity, $id, $with);
	}

	/**
	 * @param string $contentType
	 * @param int|array $contentId
	 * @param string|array $with
	 *
	 * @return null|Mvc\Entity\ArrayCollection|Mvc\Entity\Entity
	 */
	public function findByContentType($contentType, $contentId, $with = [])
	{
		$entity = $this->getContentTypeEntity($contentType);

		if (is_array($contentId))
		{
			return $this->em()->findByIds($entity, $contentId, $with);
		}
		else
		{
			return $this->em()->find($entity, $contentId, $with);
		}
	}

	/**
	 * @param string $contentType
	 * @param bool $throw
	 *
	 * @return string|null
	 */
	public function getContentTypeEntity($contentType, $throw = true)
	{
		$entity = $this->getContentTypeFieldValue($contentType, 'entity');
		if (!$entity && $throw)
		{
			throw new \LogicException("Content type $contentType must define an 'entity' value");
		}

		return $entity;
	}

	/**
	 * @param string $identifier
	 *
	 * @return Mvc\Entity\Finder
	 */
	public function finder($identifier)
	{
		return $this->em()->getFinder($identifier);
	}

	/**
	 * @param string $identifier
	 *
	 * @return Mvc\Entity\Repository
	 */
	public function repository($identifier)
	{
		return $this->em()->getRepository($identifier);
	}

	/**
	 * @param integer $id
	 *
	 * @return \XF\Language
	 */
	public function language($id = 0)
	{
		return $this->container->create('language', $id);
	}

	/**
	 * @param integer $id
	 *
	 * @return \XF\Style
	 */
	public function style($id = 0)
	{
		return $this->container->create('style', $id);
	}

	/**
	 * @param string $class
	 * @param Http\Request $request
	 *
	 * @return \XF\Mvc\Controller
	 */
	public function controller($class, Http\Request $request)
	{
		return $this->container->create('controller', $class, [
			'request' => $request
		]);
	}

	/**
	 * @return \XF\Job\Manager
	 */
	public function jobManager()
	{
		return $this->container['job.manager'];
	}

	/**
	 * @return \XF\Extension
	 */
	public function extension()
	{
		return $this->container['extension'];
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
	public function fire($event, array $args, $hint = null)
	{
		return $this->extension()->fire($event, $args, $hint);
	}

	/**
	 * Gets the callable class name for a dynamically extended class.
	 *
	 * @param string $class
	 * @param null|string $fakeBaseClass
	 * @return string
	 *
	 * @throws \Exception
	 */
	public function extendClass($class, $fakeBaseClass = null)
	{
		return $this->extension()->extendClass($class, $fakeBaseClass);
	}

	/**
	 * @return \XF\Search\Search
	 */
	public function search()
	{
		return $this->container['search'];
	}

	/**
	 * @return \XF\Str\Formatter
	 */
	public function stringFormatter()
	{
		return $this->container['string.formatter'];
	}

	/**
	 * @return \XF\SubContainer\BbCode
	 */
	public function bbCode()
	{
		return $this->container['bbCode'];
	}

	/**
	 * @return \XF\SubContainer\ApiDocs
	 */
	public function apiDocs()
	{
		return $this->container['apiDocs'];
	}

	/**
	 * @return \XF\Image\Manager
	 */
	public function imageManager()
	{
		return $this->container['imageManager'];
	}

	/**
	 * @return \XF\Logger
	 */
	public function logger()
	{
		return $this->container['logger'];
	}

	/**
	 * @return \XF\Debugger
	 */
	public function debugger()
	{
		return $this->container['debugger'];
	}

	/**
	 * @param string $class
	 * @param integer $jobId,
	 * @param array $params
	 *
	 * @return \XF\Job\AbstractJob
	 */
	public function job($class, $jobId, array $params = [])
	{
		$arguments = [$jobId, $params];

		return $this->container->create('job', $class, $arguments);
	}

	/**
	 * @param string $class
	 * @param array|null $criteria
	 *
	 * @return \XF\Searcher\AbstractSearcher
	 */
	public function searcher($class, array $criteria = null)
	{
		return $this->container->create('searcher', $class, [$criteria]);
	}

	/**
	 * @param string $class
	 * @param array $data
	 *
	 * @return \XF\Authentication\AbstractAuth
	 */
	public function auth($class, array $data = [])
	{
		if (!$class)
		{
			$class = $this->container['auth.default'];
		}

		return $this->container->create('auth', $class, [$data]);
	}

	/**
	 * @param $class
	 * @param array ...$arguments
	 *
	 * @return mixed
	 */
	public function service($class, ...$arguments)
	{
		return $this->container->create('service', $class, $arguments);
	}

	/**
	 * @param string $class
	 *
	 * @return mixed Data container object
	 */
	public function data($class)
	{
		return $this->container->create('data', $class);
	}

	/**
	 * @param string $type
	 *
	 * @return \XF\Validator\AbstractValidator
	 */
	public function validator($type)
	{
		return $this->container->create('validator', $type);
	}

	/**
	 * @return \XF\Captcha\AbstractCaptcha|false
	 */
	public function captcha($class = null)
	{
		if ($class === null)
		{
			$class = $this->options()->captcha;
			if (!$class)
			{
				return false;
			}
		}
		$arguments = func_get_args();
		unset($arguments[0]);
		return $this->container->create('captcha', $class, $arguments);
	}

	/**
	 * @param $class
	 * @param $criteria
	 *
	 * @return \XF\Criteria\AbstractCriteria
	 */
	public function criteria($class, array $criteria)
	{
		$arguments = func_get_args();
		unset($arguments[0]);
		return $this->container->create('criteria', $class, $arguments);
	}

	/**
	 * @param string $class
	 *
	 * @return \XF\Notifier\AbstractNotifier
	 */
	public function notifier($class)
	{
		$arguments = func_get_args();
		unset($arguments[0]);

		return $this->container->create('notifier', $class, $arguments);
	}

	/**
	 * @return \XF\SubContainer\Spam
	 */
	public function spam()
	{
		return $this->container['spam'];
	}

	/**
	 * @return \XF\SubContainer\Http
	 */
	public function http()
	{
		return $this->container['http'];
	}

	/**
	 * @return \XF\SubContainer\OAuth
	 */
	public function oAuth()
	{
		return $this->container['oAuth'];
	}

	/**
	 * @return \XF\SubContainer\Proxy
	 */
	public function proxy()
	{
		return $this->container['proxy'];
	}

	/**
	 * @return \XF\SubContainer\Oembed
	 */
	public function oembed()
	{
		return $this->container['oembed'];
	}

	/**
	 * @return \XF\SubContainer\Bounce
	 */
	public function bounce()
	{
		return $this->container['bounce'];
	}

	/**
	 * @return \XF\SubContainer\Unsubscribe
	 */
	public function unsubscribe()
	{
		return $this->container['unsubscribe'];
	}

	/**
	 * @return \XF\SubContainer\Widget
	 */
	public function widget()
	{
		return $this->container['widget'];
	}

	/**
	 * @return \XF\SubContainer\Import
	 */
	public function import()
	{
		return $this->container['import'];
	}

		/**
	 * @return \XF\Sitemap\Builder
	 */
	public function sitemapBuilder()
	{
		return $this->container['sitemap.builder'];
	}

	/**
	 * @param string $type
	 * @param mixed $value
	 * @param null $errorKey Returned error type identifier
	 * @param array $options
	 *
	 * @return bool
	 */
	public function isValid($type, $value, &$errorKey = null, array $options = [])
	{
		$validator = $this->validator($type);
		$validator->setOptions($options);
		return $validator->isValid($value, $errorKey);
	}

	/**
	 * @param bool $inTransaction
	 *
	 * @return Mvc\FormAction
	 */
	public function formAction($inTransaction = true)
	{
		$formAction = new Mvc\FormAction();
		if ($inTransaction)
		{
			$formAction->applyInTransaction($this->db());
		}
		return $formAction;
	}

	/**
	 * @param string $type
	 *
	 * @return \XF\Mvc\Renderer\AbstractRenderer
	 */
	public function renderer($type)
	{
		return $this->container->create('renderer', $type);
	}

	/**
	 * @return Templater
	 */
	public function templater()
	{
		return $this->container['templater'];
	}

	/**
	 * @return Compiler
	 */
	public function templateCompiler()
	{
		return $this->container['templateCompiler'];
	}

	/**
	 * @return CssWriter
	 */
	public function cssWriter()
	{
		return $this->container['cssWriter'];
	}

	/**
	 * @return DevJsResponse
	 */
	public function developmentJsResponse()
	{
		return $this->container['development.jsResponse'];
	}

	/**
	 * @return \XF\DevelopmentOutput
	 */
	public function developmentOutput()
	{
		return $this->container['development.output'];
	}

	/**
	 * @return \XF\DesignerOutput
	 */
	public function designerOutput()
	{
		return $this->container['designer.output'];
	}

	/**
	 * @return AddOn\Manager
	 */
	public function addOnManager()
	{
		return $this->container('addon.manager');
	}

	/**
	 * @return AddOn\DataManager
	 */
	public function addOnDataManager()
	{
		return $this->container('addon.dataManager');
	}

	/**
	 * @param string|null $key
	 *
	 * @return \XF\Container|mixed
	 */
	public function container($key = null)
	{
		return $key === null ? $this->container : $this->container[$key];
	}

	public function __sleep()
	{
		throw new \LogicException('Instances of ' . __CLASS__ . ' cannot be serialized or unserialized');
	}

	public function __wakeup()
	{
		throw new \LogicException('Instances of ' . __CLASS__ . ' cannot be serialized or unserialized');
	}
}