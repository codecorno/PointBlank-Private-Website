<?php

namespace XF\Api;

use XF\Api\Mvc\RouteMatch;
use XF\Container;
use XF\Http\Response;
use XF\Mvc\Renderer\AbstractRenderer;
use XF\Mvc\Reply\AbstractReply;

class App extends \XF\App
{
	protected $preLoadLocal = [
		'routesAdmin',
		'routesPublic',
		'routesApi',
		'userFieldsInfo',
		'threadFieldsInfo',
		'threadPrefixes'
	];

	public $apiKeyOmitted = false;

	public function initializeExtra()
	{
		$container = $this->container;

		$container['app.classType'] = 'Api';
		$container['app.defaultType'] = 'api';

		$container['dispatcher'] = function (Container $c)
		{
			$class = $this->extendClass('XF\Api\Mvc\Dispatcher');
			return new $class($this);
		};
		$container['router'] = function (Container $c)
		{
			return $c['router.api'];
		};
		$container['session'] = function (Container $c)
		{
			return $c['session.api'];
		};

		$container['renderer.unknown'] = function()
		{
			return function($rendererType)
			{
				if ($rendererType === 'api')
				{
					// only register this in the API app so it can't be used elsewhere
					return 'XF\Api\Mvc\Renderer\Api';
				}

				return 'Html';
			};
		};
	}

	public function setup(array $options = [])
	{
		parent::setup($options);
		$this->assertConfigExists();

		$this->fire('app_api_setup', [$this]);
	}

	public function start($allowShortCircuit = false)
	{
		parent::start($allowShortCircuit);

		if (!$this->config('enableApi'))
		{
			return $this->getApiErrorResponse(\XF::phrase('api_error.api_disabled'), $this->config('serviceUnavailableCode'));
		}

		$this->fire('app_api_start_begin', [$this]);

		$user = $this->validateUserFromApiHeader($error, $code);
		if (!$user)
		{
			return $this->getApiErrorResponse(\XF::phrase($error), $code);
		}
		\XF::setVisitor($user);

		$language = $this->language($user->language_id);
		$language->setTimeZone($user->timezone);
		\XF::setLanguage($language);

		if ($this->request()->filter('api_bypass_permissions', 'bool') && \XF::apiKey()->is_super_user)
		{
			\XF::setApiBypassPermissions(true);
		}

		$this->fire('app_api_start_end', [$this]);

		return null;
	}

	public function preDispatch(\XF\Mvc\RouteMatch $match)
	{
		if ($this->apiKeyOmitted)
		{
			$controller = $this->controller($match->getController(), $this->request());
			if ($controller instanceof \XF\Api\Controller\AbstractController)
			{
				if ($controller->allowUnauthenticatedRequest($match->getAction()))
				{
					// unauthenticated is ok, so continue the request
					return null;
				}
			}

			return $this->getApiErrorResponse(\XF::phrase('api_error.no_api_key_in_request'), 400);
		}

		return null;
	}

	protected function getApiErrorResponse($error, $code = 400)
	{
		$renderer = $this->renderer('api');
		$reply = new \XF\Mvc\Reply\Error($error, $code);

		$renderer->setReply($reply);
		$renderer->setResponseCode($code);
		$content = $renderer->renderErrors($reply->getErrors());
		$content = $renderer->postFilter($content, $reply);

		$response = $renderer->getResponse();
		$response->body($content);

		return $response;
	}

	protected function validateUserFromApiHeader(&$error = '', &$code = null)
	{
		$request = $this->request();

		$result = null;
		$this->fire('app_api_validate_request', [$request, &$result, &$error, &$code]);
		if ($result !== null)
		{
			return $result;
		}

		$apiKeyValue = $request->getApiKey();
		$apiUserId = $request->getApiUser();

		if (!$apiKeyValue)
		{
			// If no API key is presented, then don't immediately quit as we want the option to be able to
			// support unauthenticated controllers/actions. This will get picked up in preDispatch.
			$this->apiKeyOmitted = true;
			return $this->repository('XF:User')->getGuestUser();
		}

		/** @var \XF\Repository\Api $apiRepo */
		$apiRepo = $this->repository('XF:Api');
		$apiKey = $apiRepo->findApiKeyByKey($apiKeyValue);

		if (!$apiKey || $apiKeyValue !== $apiKey->api_key)
		{
			$error = 'api_error.api_key_not_found';
			$code = 401;
			return false;
		}

		if (!$apiKey->active)
		{
			$error = 'api_error.api_key_inactive';
			$code = 403;
			return false;
		}

		/** @var \XF\Repository\User $userRepo */
		$userRepo = $this->repository('XF:User');

		if ($apiKey->is_super_user)
		{
			if ($apiUserId)
			{
				$visitor = $userRepo->getVisitor($apiUserId);

				if ($visitor->user_id != $apiUserId)
				{
					$error = 'api_error.user_id_not_valid';
					$code = 403;
					return false;
				}
			}
			else
			{
				$visitor = $this->repository('XF:User')->getGuestUser();
			}
		}
		else
		{
			if ($apiUserId && $apiUserId !== $apiKey->user_id)
			{
				$error = 'api_error.user_id_not_allowed';
				$code = 403;
				return false;
			}

			$visitor = $userRepo->getVisitor($apiKey->user_id);

			if ($visitor->user_id != $apiKey->user_id)
			{
				$error = 'api_error.user_id_not_valid';
				$code = 403;
				return false;
			}
		}

		// only update this every 15 minutes as it should roughly be close enough and this avoids
		// writes on every page
		if ($apiKey->last_use_date < \XF::$time - 900)
		{
			$apiKey->fastUpdate('last_use_date', \XF::$time);
		}

		\XF::setApiKey($apiKey);

		return $visitor;
	}

	public function complete(Response $response)
	{
		parent::complete($response);

		$this->fire('app_api_complete', [$this, &$response]);
	}

	public function preRender(AbstractReply $reply, $responseType)
	{

	}

	protected function renderPageHtml($content, array $params, AbstractReply $reply, AbstractRenderer $renderer)
	{
		return $content;
	}
}