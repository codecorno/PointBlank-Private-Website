<?php

namespace XF\Legacy;

use XF\Mvc\ParameterBag;

trait ControllerTrait
{
	/**
	 * @var \XF\Legacy\Input
	 */
	protected $_input;

	protected function init()
	{
		$this->_input = new \XF\Legacy\Input($this->request);
	}

	public function preDispatch($action, ParameterBag $params)
	{
		foreach ($params->params() AS $key => $value)
		{
			$this->request->set($key, $value);
		}

		parent::preDispatch($action, $params);
	}

	public function getModelFromCache($class)
	{
		return Model::create($class);
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function responseView($viewName = '', $templateName = '', array $params = [], array $containerParams = [])
	{
		return $this->view($viewName, $templateName, $params);
	}

	public function responseReroute($controllerName, $action, array $containerParams = [])
	{
		return $this->reroute(new RouteMatch($controllerName, $action));
	}

	public function responseReroutePath($path, array $containerParams = [])
	{
		return $this->reroutePath($path);
	}

	public function responseRedirect($redirectType, $redirectTarget, $redirectMessage = null, array $redirectParams = [])
	{
		return $this->redirect($redirectTarget, $redirectMessage);
	}

	public function responseError($error, $responseCode = 200, array $containerParams = [])
	{
		return $this->error($error, $responseCode);
	}

	public function responseMessage($message, array $containerParams = [])
	{
		return $this->message($message, 200);
	}

	public function responseException($controllerResponse, $responseCode = null)
	{
		return $this->exception($controllerResponse);
	}

	public function responseNoPermission()
	{
		return $this->noPermission();
	}

	public function getNoPermissionResponseException()
	{
		return $this->exception($this->noPermission());
	}

	public function getErrorOrNoPermissionResponseException($errorPhraseKey, $stringToPhrase = true)
	{
		$responseCode = 403;

		if ($errorPhraseKey && (is_string($errorPhraseKey) || is_array($errorPhraseKey)) && $stringToPhrase)
		{
			$error = \XF::phrase($errorPhraseKey);
			if (preg_match('/^requested_.*_not_found$/i', $error->getPhraseName()))
			{
				$responseCode = 404;
			}
		}
		else
		{
			$error = $errorPhraseKey;
		}

		if ($errorPhraseKey)
		{
			return $this->exception($this->error($error, $responseCode));
		}
		else
		{
			return $this->getNoPermissionResponseException();
		}
	}

	public function responseFlooding($floodSeconds)
	{
		return $this->error(\XF::phrase('must_wait_x_seconds_before_performing_this_action', ['count' => $floodSeconds]));
	}

	public function canonicalizeRequestUrl($url)
	{
		$this->assertCanonicalUrl($url);
	}

	public function isConfirmedPost()
	{
		return $this->request->isPost();
	}

	/**
	 * Helper to assert that this action is available over POST only. Throws
	 * an exception if the request is not via POST.
	 */
	protected function _assertPostOnly()
	{
		if (!$this->request->isPost())
		{
			throw $this->exception(
				$this->error(\XF::phrase('action_available_via_post_only'), 405)
			);
		}
	}

	public function getLastHash($value)
	{
		return '';
	}
}