<?php

namespace XF\Api\Mvc\Renderer;

use XF\Api\Result\ResultInterface;
use XF\Http\Response;
use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Renderer\Json;
use XF\Mvc\Reply\AbstractReply;

class Api extends Json
{
	protected function addDefaultExtraHeaders(Response $response)
	{
		// as we need an API key, this isn't relevant
		$this->response->removeHeader('X-Frame-Options');
	}

	public function getResponseType()
	{
		return 'api';
	}

	public function renderErrors(array $errors)
	{
		if ($this->response->httpCode() < 400)
		{
			$this->response->httpCode(400);
		}

		$errorOutput = [];
		foreach ($errors AS $error)
		{
			$errorOutput[] = [
				'code' => $this->getCodeFromError($error),
				'message' => $error,
				'params' => $this->getParamsFromError($error)
			];
		}

		return [
			'errors' => $errorOutput
		];
	}

	protected function getCodeFromError($error)
	{
		if ($error instanceof \XF\Api\ErrorMessage)
		{
			return $error->getCode();
		}

		if ($error instanceof \XF\Phrase)
		{
			$errorCode = $error->getName();

			$errorParts = explode('.', $errorCode, 2);
			if ($errorParts[0] == 'api_error' && isset($errorParts[1]))
			{
				$errorCode = $errorParts[1];
			}
			return $errorCode;
		}

		return 'unknown_api_error';
	}

	protected function getParamsFromError($error)
	{
		if ($error instanceof \XF\Api\ErrorMessage)
		{
			return $error->getParams();
		}

		if ($error instanceof \XF\Phrase)
		{
			return $error->getParams();
		}

		return [];
	}

	public function renderRedirect($url, $type, $message = '')
	{
		$this->response->httpCode($type == 'permanent' ? 301 : 303);
		$this->response->header('Location', $url);

		return [
			'redirect' => $url
		];
	}

	public function renderMessage($message)
	{
		return [
			'message' => $message
		];
	}

	public function renderView($viewName, $templateName, array $params = [])
	{
		return $this->renderErrors(['Views are not renderable in the API.']);
		// note: not phrased as an internal dev message only
	}

	public function renderApiResult(ResultInterface $result)
	{
		return $this->renderApiResultInternal($result->render());
	}

	protected function renderApiResultInternal(array $result)
	{
		foreach ($result AS &$value)
		{
			if ($value instanceof Entity)
			{
				$value = $value->toApiResult();
			}
			else if ($value instanceof AbstractCollection)
			{
				$value = $value->toApiResults();
			}

			if ($value instanceof ResultInterface)
			{
				$value = $value->render();
			}

			if (is_array($value))
			{
				$value = $this->renderApiResultInternal($value);
			}
			else if (is_object($value) && method_exists($value, 'jsonSerialize'))
			{
				$value = $value->jsonSerialize();
			}
			else if (is_object($value) && method_exists($value, '__toString'))
			{
				$value = $value->__toString();
			}
		}

		return $result;
	}

	public function postFilter($content, AbstractReply $reply)
	{
		$visitor = \XF::visitor();
		$response = $this->response;

		$response->header('XF-Latest-Api-Version', \XF::API_VERSION);
		$response->header('XF-Used-Api-Version', $reply->getViewOption('requestedApiVersion') ?: \XF::API_VERSION);
		$response->header('XF-Request-User', $visitor->user_id);
		if ($visitor->user_id)
		{
			$response->header('XF-Request-User-Extras', json_encode($this->getVisitorResponseExtras()));
		}

		return parent::postFilter($content, $reply);
	}

	protected function addDefaultJsonParams(array $content)
	{
		return $content;
	}

	protected function getVisitorResponseExtras()
	{
		$visitor = \XF::visitor();
		return [
			'conversations_unread' => $visitor->conversations_unread,
			'alerts_unread' => $visitor->alerts_unread
		];
	}
}