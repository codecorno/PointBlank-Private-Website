<?php

namespace XF\Mvc;

use XF\App;
use XF\DataRegistry;
use XF\Http\Request;
use XF\Mvc\Reply;

abstract class Controller
{
	protected $app;
	protected $request;

	protected $rootClass;

	protected $responseType = 'html';
	protected $sectionContext = null;
	protected $containerKey = null;
	protected $contentKey = null;
	protected $viewOptions = [];

	public function __construct(App $app, Request $request)
	{
		$this->app = $app;
		$this->request = $request;

		$this->rootClass = $this->app->extension()->resolveExtendedClassToRoot($this);

		$this->init();
	}

	protected function init()
	{
	}

	public function setupFromMatch(RouteMatch $match)
	{
		$this->setResponseType($match->getResponseType());
		$this->setDefaultSectionContext($match->getSectionContext());
	}

	public function setupFromReply(Reply\AbstractReply $reply)
	{
		// this is the inverse of applyReplyChanges, should be updated in sync
		if (!$this->sectionContext && $reply->getSectionContext())
		{
			$this->sectionContext = $reply->getSectionContext();
		}
		if (!$this->responseType && $reply->getResponseType())
		{
			$this->responseType = $reply->getResponseType();
		}
		if (!$this->containerKey && $reply->getContainerKey())
		{
			$this->containerKey = $reply->getContainerKey();
		}
		if (!$this->contentKey && !$reply->getContentKey())
		{
			$this->contentKey = $reply->getContentKey();
		}

		foreach ($reply->getViewOptions() AS $option => $value)
		{
			if (!array_key_exists($option, $this->viewOptions))
			{
				$this->viewOptions[$option] = $value;
			}
		}
	}

	public function responseType()
	{
		return $this->responseType;
	}

	public function setResponseType($responseType)
	{
		$this->responseType = $responseType;
	}

	public function sectionContext()
	{
		return $this->sectionContext;
	}

	public function setSectionContext($sectionContext)
	{
		$this->sectionContext = $sectionContext;
	}

	public function setDefaultSectionContext($sectionContext)
	{
		if (!$this->sectionContext && $sectionContext)
		{
			$this->sectionContext = $sectionContext;
			return true;
		}
		else
		{
			return false;
		}
	}

	public function setContainerKey($containerKey)
	{
		$this->containerKey = $containerKey;
	}

	public function setContentKey($contentKey)
	{
		$this->contentKey = $contentKey;
	}

	public function setViewOption($option, $value)
	{
		$this->viewOptions[$option] = $value;
	}

	public function preDispatch($action, ParameterBag $params)
	{
		$this->checkCsrfIfNeeded($action, $params);
		$this->preDispatchType($action, $params);

		$this->app->fire('controller_pre_dispatch', [$this, $action, $params], $this->rootClass);
	}

	protected function preDispatchType($action, ParameterBag $params)
	{
	}

	public function checkCsrfIfNeeded($action, ParameterBag $params)
	{
		if (strtolower(substr($this->responseType, 0, 2) == 'js'))
		{
			$check = true;
		}
		else
		{
			$check = !($this->request->isGet() || $this->request->isHead());
		}

		if (!$check)
		{
			return;
		}

		$this->assertValidCsrfToken();
	}

	public function assertValidCsrfToken($token = null, $validityPeriod = null)
	{
		if (!$this->validateCsrfToken($token, $error, $validityPeriod))
		{
			if ($error == 'no_cookie')
			{
				$error = \XF::phrase('cookies_required_to_use_this_site');
			}
			else
			{
				$error = \XF::phrase('security_error_occurred');
			}
			throw $this->exception($this->error($error, 400));
		}
	}

	public function validateCsrfToken($token = null, &$error = null, $validityPeriod = null)
	{
		if ($token === null)
		{
			$token = $this->filter('_xfToken', 'str');
			if (!$token)
			{
				$token = $this->request->getServer('HTTP_X_XF_CSRF_TOKEN', '');
			}
		}

		$token = strval($token);
		if (!$token)
		{
			$error = 'missing';
			return false;
		}

		$parts = explode(',', $token);
		if (count($parts) == 2)
		{
			list($tokenTime, $tokenValue) = $parts;

			$cookie = $this->request->getCookie('csrf');
			if (!$cookie)
			{
				$error = 'no_cookie';
				return false;
			}

			/** @var \Closure $csrfValidator */
			$csrfValidator = $this->app['csrf.validator'];

			if ($csrfValidator($cookie, $tokenTime) === $tokenValue)
			{
				if ($validityPeriod === null)
				{
					$validityPeriod = 86400;
				}

				if ($validityPeriod > 0 && ($tokenTime + $validityPeriod) < \XF::$time)
				{
					$error = 'expired';
					return false;
				}

				return true;
			}
			else
			{
				$error = 'invalid';
				return false;
			}
		}
		else
		{
			$error = 'invalid';
			return false;
		}
	}

	public function assertCorrectVersion($action)
	{
		if (\XF::$debugMode || !\XF::config('checkVersion'))
		{
			return;
		}

		if (\XF::$versionId != $this->options()->currentVersionId)
		{
			throw $this->exception($this->message(\XF::phrase('site_currently_being_upgraded'), $this->app->config('serviceUnavailableCode')));
		}
	}

	public function assertPasswordVerified($validLength, $redirect = null, \Closure $wrapper = null)
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return;
		}

		$session = $this->session();
		$confirmDate = $session['passwordConfirm'];

		$cutOff = \XF::$time - $validLength;
		if ($confirmDate && $confirmDate >= $cutOff)
		{
			return;
		}

		$auth = $visitor->Auth;
		if (!$auth)
		{
			return;
		}

		$authClass = $auth->getAuthenticationHandler();
		if (!$authClass || !$authClass->hasPassword())
		{
			return;
		}

		$viewParams = [
			'redirect' => $redirect
		];
		$view = $this->view('XF:Login\PasswordConfirm', 'login_password_confirm', $viewParams);
		if ($wrapper)
		{
			$view = $wrapper($view);
		}
		throw $this->exception($view);
	}

	public function postDispatch($action, ParameterBag $params, Reply\AbstractReply &$reply)
	{
		$this->applyReplyChanges($action, $params, $reply);
		$this->postDispatchType($action, $params, $reply);

		$this->app->fire('controller_post_dispatch', [$this, $action, $params, &$reply], $this->rootClass);
	}

	protected function postDispatchType($action, ParameterBag $params, Reply\AbstractReply &$reply)
	{
	}

	public function applyReplyChanges($action, ParameterBag $params, Reply\AbstractReply &$reply)
	{
		// this is the inverse of setupFromReply, should be updated in sync
		if ($this->sectionContext && !$reply->getSectionContext())
		{
			$reply->setSectionContext($this->sectionContext);
		}
		if ($this->responseType && !$reply->getResponseType())
		{
			$reply->setResponseType($this->responseType);
		}
		if ($this->containerKey && !$reply->getContainerKey())
		{
			$reply->setContainerKey($this->containerKey);
		}
		if ($this->contentKey && !$reply->getContentKey())
		{
			$reply->setContentKey($this->contentKey);
		}

		$existingViewOptions = $reply->getViewOptions();
		foreach ($this->viewOptions AS $option => $value)
		{
			if (!array_key_exists($option, $existingViewOptions))
			{
				$reply->setViewOption($option, $value);
			}
		}
	}

	public function assertPostOnly()
	{
		if (!$this->request->isPost())
		{
			$this->app->response()->header('Allow', 'POST');

			throw $this->exception($this->error(
				\XF::phrase('action_available_via_post_only'), 405
			));
		}
	}

	public function isLoggedIn()
	{
		return (bool)\XF::visitor()->user_id;
	}

	public function getDynamicRedirect($fallbackUrl = null, $useReferrer = true)
	{
		return $this->app->getDynamicRedirect($fallbackUrl, $useReferrer);
	}

	public function getDynamicRedirectIfNot($notUrl, $fallbackUrl = null, $useReferrer = true)
	{
		return $this->app->getDynamicRedirectIfNot($notUrl, $fallbackUrl, $useReferrer);
	}

	public function error($error, $code = 200)
	{
		return new Reply\Error($error, $code);
	}

	public function message($message, $code = 200)
	{
		return new Reply\Message($message, $code);
	}

	public function redirect($url, $message = null, $type = 'temporary')
	{
		if ($message === null)
		{
			$message = \XF::phrase('your_changes_have_been_saved');
		}
		return new Reply\Redirect($url, $type, $message);
	}

	public function redirectPermanently($url, $message = null)
	{
		return $this->redirect($url, $message, 'permanent');
	}

	public function reroute(RouteMatch $match)
	{
		return new Reply\Reroute($match);
	}

	public function rerouteController($controller, $action, $params = [])
	{
		$match = $this->router()->getNewRouteMatch($controller, $action, $params, $this->responseType);

		return $this->reroute($match);
	}

	public function reroutePath($path)
	{
		$match = $this->router()->routeToController($path, $this->request);
		$match->setResponseType($this->responseType);

		return $this->reroute($match);
	}

	public function view($viewClass = '', $templateName = '', array $params = [])
	{
		return new Reply\View($viewClass, $templateName, $params);
	}

	public function exception(Reply\AbstractReply $reply)
	{
		return new Reply\Exception($reply);
	}

	public function errorException($error, $code = 200)
	{
		return $this->exception($this->error($error, $code));
	}

	public function noPermission($message = null)
	{
		return $this->plugin('XF:Error')->actionNoPermission($message);
	}

	public function notFound($message = null)
	{
		return $this->plugin('XF:Error')->actionNotFound($message);
	}

	/**
	 * @param string $identifier
	 * @param mixed $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return Entity\Entity
	 *
	 * @throws Reply\Exception
	 */
	public function assertRecordExists($identifier, $id, $with = null, $phraseKey = null)
	{
		$record = $this->em()->find($identifier, $id, $with);
		if (!$record)
		{
			if (!$phraseKey)
			{
				$phraseKey = 'requested_page_not_found';
			}

			throw $this->exception(
				$this->notFound(\XF::phrase($phraseKey))
			);
		}

		return $record;
	}

	/**
	 * @param string $identifier
	 * @param mixed $id
	 * @param array|string|null $with
	 * @param string|null $phraseKey
	 *
	 * @return Entity\Entity
	 *
	 * @throws Reply\Exception|\LogicException
	 */
	public function assertViewableRecord($identifier, $id, $with = null, $phraseKey = null)
	{
		$record = $this->assertRecordExists($identifier, $id, $with, $phraseKey);

		if (!method_exists($record, 'canView'))
		{
			throw new \LogicException("assertViewableRecord requires the entity of type $identifier to implement canView()");
		}
		if (!$record->canView($error))
		{
			throw $this->exception($this->noPermission($error));
		}

		return $record;
	}

	/**
	 * Asserts that the URL meets the canonical/expected URL for SEO benefits.
	 *
	 * @param string $linkUrl
	 *
	 * @throws Reply\Exception
	 */
	public function assertCanonicalUrl($linkUrl)
	{
		if ($this->responseType != 'html')
		{
			return;
		}

		if (!$this->request->isGet() && !$this->request->isHead())
		{
			return;
		}

		$linkUrl = strval($linkUrl);

		if (strlen($linkUrl) == 0)
		{
			return;
		}

		if ($linkUrl[0] == '.')
		{
			$linkUrl = substr($linkUrl, 1);
		}

		$basePath = $this->request->getBasePath();
		$requestUri = $this->request->getRequestUri();
		$fullBasePath = $this->request->getFullBasePath();

		if (substr($linkUrl, 0, strlen($fullBasePath)) == $fullBasePath)
		{
			$linkUrl = ltrim(substr($linkUrl, strlen($fullBasePath)), '/');
		}
		else if (substr($linkUrl, 0, strlen($basePath)) == $basePath)
		{
			$linkUrl = ltrim(substr($linkUrl, strlen($basePath)), '/');
		}

		if (substr($requestUri, 0, strlen($basePath)) != $basePath)
		{
			return;
		}

		$routeBase = ltrim(substr($requestUri, strlen($basePath)), '/');

		if (preg_match('#^([^?]*\?[^=&]*)(&(.*))?$#U', $routeBase, $match))
		{
			$requestUrlPrefix = $match[1];
			$requestParams = isset($match[3]) ? $match[3] : false;
		}
		else
		{
			$parts = explode('?', $routeBase);
			$requestUrlPrefix = $parts[0];
			$requestParams = isset($parts[1]) ? $parts[1]: false;
		}

		if (preg_match('#^([^?]*\?[^=&]*)(&(.*))?$#U', $linkUrl, $match))
		{
			$linkUrlPrefix = $match[1];
		}
		else
		{
			$parts = explode('?', $linkUrl);
			$linkUrlPrefix = $parts[0];
		}

		if (urldecode($requestUrlPrefix) != urldecode($linkUrlPrefix))
		{
			$redirectUrl = rtrim($fullBasePath, '/') . '/' . $linkUrlPrefix;
			if ($requestParams !== false)
			{
				$paramsSep = (strpos($redirectUrl, '?') === false ? '?' : '&');

				if (strpos($redirectUrl, '#') !== false)
				{
					list ($link, $hash) = explode('#', $redirectUrl, 2);
					$redirectUrl = $link . $paramsSep . $requestParams . '#' . $hash;
				}
				else
				{
					$redirectUrl .= $paramsSep . $requestParams;
				}
			}

			throw $this->exception($this->redirectPermanently($redirectUrl));
		}
	}

	public function assertValidPage($page, $perPage, $total, $linkType, $linkData = null)
	{
		if ($this->responseType != 'html' || !$this->request->isGet())
		{
			return;
		}

		if ($perPage < 1 || $total < 1)
		{
			return;
		}

		$page = max(1, intval($page));
		$maxPage = ceil($total / $perPage);

		if ($page <= $maxPage)
		{
			return; // within the range
		}

		$params = $_GET;
		if ($maxPage <= 1)
		{
			unset($params['page']);
		}
		else
		{
			$params['page'] = $maxPage;
		}

		$redirectUrl = $this->buildLink($linkType, $linkData, $params);

		throw $this->exception(
			$this->redirect($redirectUrl)
		);
	}

	/**
	 * @param string|array $checkIps
	 * @param array $ipList
	 *
	 * @return bool
	 *
	 * @deprecated Call \XF\Util\Ip::checkIpsAgainstBinaryRangeList() directly.
	 */
	public function ipMatch($checkIps, array $ipList)
	{
		return \XF\Util\Ip::checkIpsAgainstBinaryRangeList($checkIps, $ipList);
	}

	/**
	 * Returns an array of IPs for the current client
	 *
	 * @return array
	 *
	 * @deprecated Call getAllIps() on the request object directly.
	 */
	protected function getClientIps()
	{
		return $this->request->getAllIps();
	}

	/**
	 * @param string $name
	 *
	 * @return \XF\ControllerPlugin\AbstractPlugin
	 */
	public function plugin($name)
	{
		if (substr_count($name, ':') == 2)
		{
			$class = \XF::stringToClass($name, '%s\%s\ControllerPlugin\%s', $this->app->container('app.classType'));
		}
		else
		{
			$class = \XF::stringToClass($name, '%s\ControllerPlugin\%s');
		}

		$class = $this->app->extendClass($class);

		return new $class($this);
	}

	/**
	 * @return App
	 */
	public function app()
	{
		return $this->app;
	}

	/**
	 * @return Request
	 */
	public function request()
	{
		return $this->request;
	}

	/**
	 * @param string|array $key
	 * @param string|null $type
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function filter($key, $type = null, $default = null)
	{
		return $this->request->filter($key, $type, $default);
	}

	public function filterArray(array $array, array $filters)
	{
		return $this->app->inputFilterer()->filterArray($array, $filters);
	}

	/**
	 * Filters input from a form (or part of a form) that may be serialized to JSON.
	 *
	 * @param array $filters List of filters, like passed to filter/filterArray
	 * @param string $jsonInputName Name of the input that holds the serialized JSON
	 *
	 * @return array
	 */
	public function filterFormJson(array $filters, $jsonInputName = 'json')
	{
		if ($this->request->exists($jsonInputName))
		{
			$json = $this->filter($jsonInputName, 'json-array');
			return $this->filterArray($json, $filters);
		}
		else
		{
			return $this->filter($filters);
		}
	}

	public function filterPage($page = 0, $inputName = 'page')
	{
		return max(1, intval($page) ?: $this->filter($inputName, 'uint'));
	}

	public function isPost()
	{
		return $this->request->isPost();
	}

	public function formAction($inTransaction = true)
	{
		return $this->app->formAction($inTransaction);
	}

	/**
	 * @param $class
	 * @param array|null $criteria
	 *
	 * @return \XF\Searcher\AbstractSearcher
	 */
	public function searcher($class, array $criteria = null)
	{
		return $this->app->searcher($class, $criteria);
	}

	/**
	 * @param string $class
	 *
	 * @return \XF\Service\AbstractService
	 */
	public function service($class)
	{
		return call_user_func_array([$this->app, 'service'], func_get_args());
	}

	/**
	 * @return \XF\Session\Session
	 */
	public function session()
	{
		return $this->app->session();
	}

	/**
	 * @return Router
	 */
	public function router()
	{
		return $this->app->router();
	}

	/**
	 * @param string $link
	 * @param mixed $data
	 * @param array $parameters
	 *
	 * @return string
	 */
	public function buildLink($link, $data = null, array $parameters = [])
	{
		return $this->app->router()->buildLink($link, $data, $parameters);
	}

	public function buildLinkHash($hash)
	{
		return '#' . $this->app()->getRedirectHash($hash);
	}

	/**
	 * @return DataRegistry
	 */
	public function registry()
	{
		return $this->app->registry();
	}

	/**
	 * @return \ArrayObject
	 */
	public function options()
	{
		return $this->app->options();
	}

	/**
	 * @param bool $force
	 * @param null|string $class
	 *
	 * @return bool
	 */
	public function captchaIsValid($force = false, $class = null)
	{
		if (!$force && !\XF::visitor()->isShownCaptcha())
		{
			return true;
		}

		$captcha = $this->app->captcha($class);
		if (!$captcha)
		{
			return true;
		}
		else
		{
			return $captcha->isValid();
		}
	}

	/**
	 * @param $class
	 *
	 * @return mixed
	 */
	public function data($class)
	{
		return $this->app->data($class);
	}

	/**
	 * @return Entity\Manager
	 */
	public function em()
	{
		return $this->app->em();
	}

	/**
	 * @param string $type
	 *
	 * @return Entity\Finder
	 */
	public function finder($type)
	{
		return $this->app->em()->getFinder($type);
	}

	/**
	 * @param string $identifier
	 *
	 * @return Entity\Repository
	 */
	public function repository($identifier)
	{
		return $this->app->em()->getRepository($identifier);
	}
}