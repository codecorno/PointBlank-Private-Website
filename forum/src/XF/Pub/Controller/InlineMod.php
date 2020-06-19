<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\FormAction;
use XF\Mvc\Reply\View;

class InlineMod extends AbstractController
{
	public function actionIndex()
	{
		if ($this->isPost())
		{
			return $this->rerouteController('XF:InlineMod', 'perform');
		}
		else
		{
			return $this->rerouteController('XF:InlineMod', 'showActions');
		}
	}

	public function actionShowActions()
	{
		$type = $this->filter('type', 'str');

		$handler = $this->getInlineModHandler($type);
		if (!$handler)
		{
			return $this->noPermission();
		}

		$ids = $handler->getCookieIds($this->request);
		$entities = $handler->getEntities($ids);

		$actions = $handler->getActions();
		$available = [];
		if ($entities->count())
		{
			foreach ($actions AS $actionId => $action)
			{
				if ($action->canApply($entities))
				{
					$available[$actionId] = $action->getTitle();
				}
			}
		}

		$viewParams = [
			'type' => $type,
			'title' => $handler->getSelectedTypeTitle(),
			'actions' => $available,
			'total' => count($entities)
		];
		return $this->view('XF:InlineMod\ShowActions', 'inline_mod_actions', $viewParams);
	}

	public function actionPerform()
	{
		$this->assertPostOnly();

		$type = $this->filter('type', 'str');

		$handler = $this->getInlineModHandler($type);
		if (!$handler)
		{
			return $this->noPermission();
		}

		$action = $this->filter('action', 'str');
		$actionHandler = $handler->getAction($action);
		if (!$actionHandler)
		{
			return $this->noPermission();
		}

		$redirect = $this->getDynamicRedirect();

		$confirmed = $this->filter('confirmed', 'bool');
		if ($confirmed)
		{
			if (!$this->request->exists('ids'))
			{
				return $this->error('Developer: No ids param submitted.');
			}

			$ids = $this->filter('ids', 'array-uint');
			$ids = array_unique($ids);
		}
		else
		{
			$ids = $handler->getCookieIds($this->request);
		}

		$entities = $handler->getEntities($ids);

		if (!$entities->count())
		{
			return $this->redirect($redirect);
		}

		if ($confirmed)
		{
			$options = $actionHandler->getFormOptions($entities, $this->request);
		}
		else
		{
			$options = [];
		}

		if (!$actionHandler->canApply($entities, $options, $error))
		{
			return $this->noPermission($error);
		}

		if (!$confirmed)
		{
			$reply = $actionHandler->renderForm($entities, $this);
			if ($reply)
			{
				if (!($reply instanceof \XF\Mvc\Reply\AbstractReply))
				{
					throw new \LogicException("Renderer for inline mod action $action must return a controller reply");
				}

				return $reply;
			}
		}

		// either we're confirmed or we don't have a form to render
		$actionHandler->apply($entities, $options);

		$reply = $this->redirect($actionHandler->getReturnUrl() ?: $redirect);
		$actionHandler->postApply($entities, $reply, $this->app->response());

		return $reply;
	}

	/**
	 * @param string $type
	 *
	 * @return null|\XF\InlineMod\AbstractHandler
	 */
	protected function getInlineModHandler($type)
	{
		return $this->plugin('XF:InlineMod')->getInlineModHandler($type);
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('performing_moderation_duties');
	}
}