<?php

namespace XF\Api\Mvc;

use XF\Mvc\Renderer\AbstractRenderer;
use XF\Mvc\Reply\AbstractReply;

class Dispatcher extends \XF\Mvc\Dispatcher
{
	public function dispatchFromMatch(\XF\Mvc\RouteMatch $match, &$controller = null, AbstractReply $previousReply = null)
	{
		// sanity check here just in case
		if ($match->getResponseType() == 'html')
		{
			$match->setResponseType('api');
		}

		$action = $match->getAction();
		$apiVersion = ($match instanceof \XF\Api\Mvc\RouteMatch ? $match->getVersion() : null);

		$actionCallback = function(\XF\Mvc\Controller $controller) use ($action, $apiVersion)
		{
			return $this->getApplicableActionName($controller, $action, $apiVersion);
		};

		return $this->dispatchClass(
			$match->getController(),
			$actionCallback,
			$match,
			$controller,
			$previousReply
		);
	}

	protected function getApplicableActionName(\XF\Mvc\Controller $controller, $action, $requestedApiVersion)
	{
		$action = preg_replace('#[^a-z0-9]#i', ' ', $action);
		$action = str_replace(' ', '', ucwords($action));

		$apiVersion = \XF::API_VERSION;

		if ($requestedApiVersion > 0 && $requestedApiVersion < $apiVersion)
		{
			do
			{
				$testMethod = $action . '_v' . $requestedApiVersion;
				if (method_exists($controller, $testMethod))
				{
					return $testMethod;
				}

				$requestedApiVersion++;
			}
			while ($requestedApiVersion < $apiVersion);
		}

		return $action;
	}

	protected function setupRenderer(AbstractRenderer $renderer, AbstractReply $reply)
	{
		$renderer->setReply($reply);

		$renderer->setResponseCode($reply->getResponseCode());
	}

	protected function renderReply(AbstractRenderer $renderer, AbstractReply $reply)
	{
		if ($reply instanceof \XF\Api\Mvc\Reply\ApiResult)
		{
			if ($renderer instanceof \XF\Api\Mvc\Renderer\Api)
			{
				return $renderer->renderApiResult($reply->getApiResult());
			}
			else
			{
				return $renderer->renderErrors(['API results are only renderable by the API renderer.']);
			}
		}

		return parent::renderReply($renderer, $reply);
	}
}