<?php

namespace XF\Mvc\Renderer;

use XF\HTTP\Response;
use XF\Mvc\Reply\AbstractReply;
use XF\Mvc\View;
use XF\Template\Templater;

abstract class AbstractRenderer
{
	/**
	 * @var callable
	 */
	protected $viewFactory;

	/**
	 * @var Response
	 */
	protected $response;

	/**
	 * @var AbstractReply|null
	 */
	protected $reply;

	/**
	 * @var Templater
	 */
	protected $templater;

	public function __construct(\Closure $viewFactory, Response $response, Templater $templater)
	{
		$this->viewFactory = $viewFactory;
		$this->response = $response;
		$this->templater = $templater;

		$this->addDefaultExtraHeaders($response);
		$this->initialize();
	}

	protected function addDefaultExtraHeaders(Response $response)
	{
		$response->header('X-Content-Type-Options', 'nosniff');

		if (\XF::app()->config('enableClickjackingProtection'))
		{
			$response->header('X-Frame-Options', 'SAMEORIGIN');
		}
	}

	abstract protected function initialize();
	abstract public function getResponseType();
	abstract public function renderRedirect($url, $type, $message = '');
	abstract public function renderMessage($message);
	abstract public function renderErrors(array $errors);
	abstract public function renderView($viewName, $templateName, array $params = []);

	public function setReply(AbstractReply $reply)
	{
		$this->reply = $reply;
	}

	public function renderUnrepresentable()
	{
		return $this->renderErrors(['This page is not representable in the requested type.']);
	}

	/**
	 * @param string $viewName
	 * @param string $templateName
	 * @param array $params
	 *
	 * @return View
	 */
	public function createViewObject($viewName, $templateName, array $params = [])
	{
		$f = $this->viewFactory;
		return $f($viewName, [
			$this, $this->response, $templateName, $params
		]);
	}

	public function renderViewObject($viewName, &$templateName, array &$params = [], $responseType = null)
	{
		$responseType = $responseType ?: $this->getResponseType();
		$view = $this->createViewObject($viewName, $templateName, $params);
		$method = 'render' . ucfirst($responseType);
		if ($view && method_exists($view, $method))
		{
			$result = $view->$method();
		}
		else
		{
			$result = null;
		}

		if ($view)
		{
			$templateName = $view->getTemplateName();
			$params = $view->getParams();
		}

		return $result;
	}

	/**
	 * @param string $templateName
	 * @param array $params
	 *
	 * @return \XF\Template\Template
	 */
	public function getTemplate($templateName, array $params = [])
	{
		return $this->templater->getTemplate($templateName, $params);
	}

	/**
	 * @return Templater
	 */
	public function getTemplater()
	{
		return $this->templater;
	}

	public function postFilter($content, AbstractReply $reply)
	{
		return $content;
	}

	public function setResponseCode($code = 200)
	{
		$this->response->httpCode($code);
	}

	/**
	 * @return Response
	 */
	public function getResponse()
	{
		return $this->response;
	}
}