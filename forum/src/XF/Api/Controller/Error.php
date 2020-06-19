<?php

namespace XF\Api\Controller;

use XF\Mvc\ParameterBag;
use XF\Util\File;

class Error extends AbstractController
{
	public function actionDispatchError(ParameterBag $params)
	{
		$errorParams = ['reason' => 'invalid_route'];

		if (
			$params->code == 'invalid_action'
			&& $params->action
			&& preg_match('#^(get|post|delete|put|patch)(.*)$#i', $params->action, $match)
		)
		{
			$errorParams['reason'] = 'invalid_action';

			$controller = $this->app->controller($params->controller, $this->request);
			if ($controller)
			{
				$availableMethods = [];
				foreach (['get', 'post', 'delete', 'put', 'patch'] AS $method)
				{
					if (method_exists($controller, 'action' . $method . $match[2]))
					{
						$availableMethods[] = strtoupper($method);
					}
				}

				if ($availableMethods)
				{
					$errorParams['reason'] = 'invalid_method';
					$errorParams['available_methods'] = $availableMethods;
				}
			}
		}

		if (\XF::$debugMode)
		{
			$errorParams['debug_reason'] = $params->code;
			$errorParams['debug_controller'] = $params->controller;
			$errorParams['debug_action'] = $params->action;
		}

		return $this->apiError(\XF::phrase('api_error.endpoint_not_found'), 'endpoint_not_found', $errorParams, 404);
	}

	public function actionException(ParameterBag $params)
	{
		$error = $this->error(\XF::phrase('api_error.server_error_occurred'), 500);

		if (\XF::$debugMode
			&& $params->exception
			&& ($params->exception instanceof \Exception || $params->exception instanceof \Throwable)
		)
		{
			$error->setJsonParam('exception', $this->getExceptionDetails($params->exception));
		}

		return $error;
	}

	protected function getExceptionDetails($e)
	{
		/** @var \Throwable $e */

		$details = [
			'type' => get_class($e),
			'message' => $e->getMessage(),
			'file' => File::stripRootPathPrefix($e->getFile()),
			'line' => $e->getLine()
		];
		$trace = [];

		foreach ($e->getTrace() AS $traceEntry)
		{
			$function = (isset($traceEntry['class']) ? $traceEntry['class'] . $traceEntry['type'] : '') . $traceEntry['function'];
			if (isset($traceEntry['file']) && isset($traceEntry['line']))
			{
				$trace[] = "$function() - "
					. File::stripRootPathPrefix($traceEntry['file'])
					. ' on line ' . $traceEntry['line'];
			}
			else
			{
				$trace[] = "$function()";
			}
		}

		$details['trace'] = $trace;

		return $details;
	}

	public function actionAddOnUpgrade(ParameterBag $params)
	{
		return $this->plugin('XF:Error')->actionAddOnUpgrade($params);
	}

	public function assertIpNotBanned() {}
	public function assertNotBanned() {}
	public function assertViewingPermissions($action) {}
	public function assertCorrectVersion($action) {}
	public function assertBoardActive($action) {}
	public function assertTfaRequirement($action) {}
}