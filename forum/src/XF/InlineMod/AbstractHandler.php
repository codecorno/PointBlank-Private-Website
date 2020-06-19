<?php

namespace XF\InlineMod;

use XF\App;
use XF\Http\Request;
use XF\HTTP\Response;
use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;

abstract class AbstractHandler
{
	/**
	 * @var string
	 */
	protected $contentType;

	/**
	 * @var App
	 */
	protected $app;

	protected $actions;

	protected $baseCookie = 'inlinemod';

	public function __construct($contentType, App $app)
	{
		$this->contentType = $contentType;
		$this->app = $app;

		$actions = $this->getPossibleActions();
		$app->fire('inline_mod_actions', [$this, $app, &$actions], $contentType);
		$this->actions = $actions;
	}

	/**
	 * @return AbstractAction[]
	 */
	public function getActions()
	{
		return $this->actions;
	}

	/**
	 * @return AbstractAction[]
	 */
	abstract public function getPossibleActions();

	public function getEntityWith()
	{
		return [];
	}

	/**
	 * @param string $action
	 *
	 * @return AbstractAction|null
	 */
	public function getAction($action)
	{
		return isset($this->actions[$action]) ? $this->actions[$action] : null;
	}

	public function canViewContent(Entity $entity, &$error = null)
	{
		if (method_exists($entity, 'canView'))
		{
			return $entity->canView($error);
		}

		throw new \LogicException("Could not determine content viewability; please override");
	}

	public function getCookieName()
	{
		return $this->baseCookie . '_' . $this->contentType;
	}

	public function getCookieIds(Request $request)
	{
		$cookie = $request->getCookie($this->getCookieName());
		if ($cookie)
		{
			$ids = explode(',', $cookie);
			$ids = array_map('intval', $ids);
			$ids = array_unique($ids);
			return $ids;
		}
		else
		{
			return [];
		}
	}

	public function clearCookie(Response $response)
	{
		$response->setCookie($this->getCookieName(), false);
	}

	public function updateCookieIds(Response $response, array $ids)
	{
		$ids = array_map('intval', $ids);
		$ids = array_unique($ids);

		if (!$ids)
		{
			$this->clearCookie($response);
		}
		else
		{
			$response->setCookie($this->getCookieName(), implode(',', $ids), 0, null, false);
		}
	}

	public function getActionHandler($class)
	{
		$class = \XF::stringToClass($class, '%s\InlineMod\%s');
		$class = $this->app->extendClass($class);
		return new $class($this);
	}

	public function getSimpleActionHandler($title, $canApply, \Closure $apply)
	{
		return new SimpleAction($this, $title, $canApply, $apply);
	}

	/**
	 * @param array $ids
	 *
	 * @return \XF\Mvc\Entity\ArrayCollection
	 */
	public function getEntities(array $ids)
	{
		return $this->app->findByContentType($this->contentType, $ids, $this->getEntityWith());
	}


	public function getContentType()
	{
		return $this->contentType;
	}

	public function getSelectedTypeTitle()
	{
		return $this->app->getContentTypePhrase($this->contentType, true);
	}

	/**
	 * @return App
	 */
	public function app()
	{
		return $this->app;
	}
}