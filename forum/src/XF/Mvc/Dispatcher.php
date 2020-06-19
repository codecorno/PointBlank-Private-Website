<?php

namespace XF\Mvc;

use XF\Http;
use XF\Mvc\Renderer\AbstractRenderer;
use XF\Mvc\Reply\AbstractReply;
use XF\PrintableException;

class Dispatcher
{
	/**
	 * @var \XF\App
	 */
	protected $app;

	/**
	 * @var \XF\Http\Request
	 */
	protected $request;

	/**
	 * @var \XF\Mvc\Router
	 */
	protected $router;

	protected $eventPrefix = 'dispatcher';

	public function __construct(\XF\App $app, Http\Request $request = null)
	{
		$this->app = $app;
		$this->request = $request ? $request : $app->request();
	}

	public function run($routePath = null)
	{
		if ($routePath === null)
		{
			$routePath = $this->request->getRoutePath();
		}

		$match = $this->route($routePath);

		$earlyResponse = $this->beforeDispatch($match);
		if ($earlyResponse)
		{
			return $earlyResponse;
		}

		$reply = $this->dispatchLoop($match);

		$responseType = $reply->getResponseType() ? $reply->getResponseType() : $match->getResponseType();
		$response = $this->render($reply, $responseType);

		return $response;
	}

	public function route($routePath)
	{
		$match = $this->getRouter()->routeToController($routePath, $this->request);

		if (!($match instanceof RouteMatch) || !$match->getController())
		{
			$match = $this->app->getErrorRoute('DispatchError', [
				'code' => 'invalid_route',
				'match' => $match
			]);
		}

		return $match;
	}

	protected function beforeDispatch(RouteMatch $match)
	{
		$this->app->fire($this->eventPrefix . '_pre_dispatch', [$this, $match]);

		return $this->app->preDispatch($match);
	}

	public function dispatchLoop(RouteMatch $match)
	{
		$i = 1;
		$attemptErrorReroute = true;
		$originalMatch = $match;
		$reply = null;

		$this->app->fire($this->eventPrefix . '_match', [$this, &$match]);

		do
		{
			$controllerClass = $match->getController();
			$action = $match->getAction();
			$responseType = $match->getResponseType();
			$sectionContext = $match->getSectionContext();
			$params = $match->getParameterBag();
			$controller = null;

			try
			{
				$reply = $this->dispatchFromMatch($match, $controller, $reply);
			}
			catch (\Throwable $e)
			{
				$reply = $this->handleControllerError($e, $attemptErrorReroute, $controller, [
					'responseType' => $responseType,
					'sectionContext' => $sectionContext,
					'action' => $action,
					'params' => $params
				]);
				$attemptErrorReroute = false;
			}
			catch (\Exception $e)
			{
				// this will only be hit in PHP 5.x
				$reply = $this->handleControllerError($e, $attemptErrorReroute, $controller, [
					'responseType' => $responseType,
					'sectionContext' => $sectionContext,
					'action' => $action,
					'params' => $params
				]);
				$attemptErrorReroute = false;
			}

			if (!$reply instanceof Reply\AbstractReply)
			{
				$reply = new Reply\Reroute(
					$this->app->getErrorRoute('DispatchError', [
						'code' => 'no_reply',
						'controller' => $controllerClass,
						'action' => $action
					], $responseType)
				);
				$reply->setSectionContext($sectionContext);
			}

			if (!($reply instanceof Reply\Reroute) && $attemptErrorReroute)
			{
				// if we might be debugging, move this up so that we can display an error instead of the page results.
				// not doing this can hide errors

				try
				{
					\XF::triggerRunOnce(true);
				}
				catch (\Throwable $e)
				{
					$attemptErrorReroute = false;

					$reply = new Reply\Reroute(
						$this->app->getErrorRoute('Exception', ['exception' => $e], $responseType)
					);
					$reply->setResponseType($responseType);
					$reply->setSectionContext($sectionContext);
				}
				catch (\Exception $e)
				{
					// this will only be hit in PHP 5.x
					$attemptErrorReroute = false;

					$reply = new Reply\Reroute(
						$this->app->getErrorRoute('Exception', ['exception' => $e], $responseType)
					);
					$reply->setResponseType($responseType);
					$reply->setSectionContext($sectionContext);
				}
			}

			if ($reply instanceof Reply\Reroute)
			{
				$match = $reply->getMatch();
				if (!$match->getResponseType())
				{
					$match->setResponseType($responseType);
				}
				if (!$match->getSectionContext())
				{
					$match->setSectionContext($sectionContext);
				}
			}
			else
			{
				break;
			}
		}
		while ($i++ < 10);

		if ($reply instanceof Reply\Reroute)
		{
			// rerouted too many times
			$reply = new Reply\Error(
				'An error occurred while the page was being generated. Please try again later.'
			);
			$reply->setResponseType($responseType);
			$reply->setSectionContext($sectionContext);
		}

		$this->app->postDispatch($reply, $match, $originalMatch);

		$this->app->fire($this->eventPrefix . '_post_dispatch', [$this, &$reply, $match, $originalMatch]);

		return $reply;
	}

	protected function handleControllerError($e, $attemptErrorReroute, $controller, array $state = [])
	{
		/** @var \Throwable $e */

		$state = array_replace([
			'responseType' => null,
			'sectionContext' => '',
			'action' => '',
			'params' => null
		], $state);

		if ($attemptErrorReroute)
		{
			\XF::logException($e, true); // rollback as don't know the state

			$reply = new Reply\Reroute(
				$this->app->getErrorRoute('Exception', ['exception' => $e], $state['responseType'])
			);
		}
		else
		{
			$reply = new Reply\Error(
				'An error occurred while the page was being generated. Please try again later.'
			);
		}

		$reply->setResponseType($state['responseType']);
		$reply->setSectionContext($state['sectionContext']);

		if ($controller instanceof \XF\Mvc\Controller)
		{
			$controller->applyReplyChanges($state['action'], $state['params'] ?: new ParameterBag(), $reply);
		}

		return $reply;
	}

	public function dispatchFromMatch(RouteMatch $match, &$controller = null, AbstractReply $previousReply = null)
	{
		return $this->dispatchClass(
			$match->getController(),
			$match->getAction(),
			$match,
			$controller,
			$previousReply
		);
	}

	public function dispatchClass(
		$controllerClass, $action, RouteMatch $match, &$controller = null, AbstractReply $previousReply = null
	)
	{
		$params = $match->getParameterBag();
		if (!$params)
		{
			$params = new ParameterBag();
		}

		$responseType = $match->getResponseType();

		if (!$controllerClass)
		{
			return new Reply\Reroute(
				$this->app->getErrorRoute('DispatchError', [
					'code' => 'no_controller',
					'controller' => $controllerClass,
					'action' => is_string($action) ? $action : null,
					'match' => $match
				], $responseType)
			);
		}

		$controller = $this->app->controller($controllerClass, $this->request);
		if (!$controller)
		{
			return new Reply\Reroute(
				$this->app->getErrorRoute('DispatchError', [
					'code' => 'invalid_controller',
					'controller' => $controllerClass,
					'action' => is_string($action) ? $action : null,
					'match' => $match
				], $responseType)
			);
		}

		$controller->setupFromMatch($match);
		if ($previousReply)
		{
			$controller->setupFromReply($previousReply);
		}

		if ($action instanceof \Closure)
		{
			$action = $action($controller, $responseType, $params);
		}
		else
		{
			$action = preg_replace('#[^a-z0-9]#i', ' ', $action);
			$action = str_replace(' ', '', ucwords($action));
		}

		$method = 'action' . $action;
		if (!is_callable([$controller, $method]))
		{
			return new Reply\Reroute(
				$this->app->getErrorRoute('DispatchError', [
					'code' => 'invalid_action',
					'controller' => $controllerClass,
					'action' => $action,
					'match' => $match
				], $responseType)
			);
		}

		try
		{
			$controller->preDispatch($action, $params);
			$reply = $controller->$method($params);
		}
		catch (PrintableException $e)
		{
			$reply = new Reply\Error($e->getMessages());
		}
		catch (Reply\Exception $e)
		{
			$reply = $e->getReply();
		}

		if (!$reply)
		{
			$reply = new Reply\Reroute(
				$this->app->getErrorRoute('DispatchError', [
					'code' => 'no_reply',
					'controller' => $controllerClass,
					'action' => $action
				], $responseType)
			);
		}

		$controller->postDispatch($action, $params, $reply);

		$reply->setControllerClass($controllerClass);
		$reply->setAction($action);

		return $reply;
	}

	public function render(AbstractReply $reply, $responseType)
	{
		$this->app->fire($this->eventPrefix . '_pre_render', [$this, $reply, $responseType]);

		$this->app->preRender($reply, $responseType);

		$renderer = $this->app->renderer($responseType);
		$this->setupRenderer($renderer, $reply);

		$content = $this->renderReply($renderer, $reply);

		$content = $this->app->renderPage($content, $reply, $renderer);
		$content = $renderer->postFilter($content, $reply);

		$response = $renderer->getResponse();

		$this->app->fire($this->eventPrefix . '_post_render', [$this, &$content, $reply, $renderer, $response]);

		$response->body($content);

		return $response;
	}

	protected function setupRenderer(AbstractRenderer $renderer, AbstractReply $reply)
	{
		$renderer->setReply($reply);

		$renderer->getResponse()->header('Last-Modified', gmdate('D, d M Y H:i:s', \XF::$time) . ' GMT');
		$renderer->setResponseCode($reply->getResponseCode());
		$renderer->getTemplater()->setPageParams($reply->getPageParams());
	}

	protected function renderReply(AbstractRenderer $renderer, AbstractReply $reply)
	{
		if ($reply instanceof Reply\Error)
		{
			return $renderer->renderErrors($reply->getErrors());
		}
		else if ($reply instanceof Reply\Message)
		{
			return $renderer->renderMessage($reply->getMessage());
		}
		else if ($reply instanceof Reply\Redirect)
		{
			$url = $this->request->convertToAbsoluteUri($reply->getUrl());
			return $renderer->renderRedirect($url, $reply->getType(), $reply->getMessage());
		}
		else if ($reply instanceof Reply\View)
		{
			return $this->renderView($renderer, $reply);
		}
		else
		{
			throw new \InvalidArgumentException("Unknown reply type: " . get_class($reply));
		}
	}

	public function renderView(AbstractRenderer $renderer, Reply\View $reply)
	{
		$params = $reply->getParams();

		$template = $reply->getTemplateName();
		if ($template && !strpos($template, ':'))
		{
			$template = $this->app['app.defaultType'] . ':' . $template;
		}

		return $renderer->renderView($reply->getViewClass(), $template, $params);
	}

	/**
	 * @return Http\Request
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * @return Router
	 */
	public function getRouter()
	{
		if (!$this->router)
		{
			$this->router = $this->app->router();
		}

		return $this->router;
	}

	/**
	 * @param Router $router
	 */
	public function setRouter(Router $router)
	{
		$this->router = $router;
	}
}