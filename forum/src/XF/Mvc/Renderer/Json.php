<?php

namespace XF\Mvc\Renderer;

use XF\Mvc\Reply\AbstractReply;
use XF\Util\File;

class Json extends AbstractRenderer
{
	protected $encodeModifiers = JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;

	protected function initialize()
	{
		$this->response->contentType('application/json');
	}

	public function getResponseType()
	{
		return 'json';
	}

	public function renderRedirect($url, $type, $message = '')
	{
		return [
			'status' => 'ok',
			'message' => $message,
			'redirect' => $url
		];
	}

	public function renderMessage($message)
	{
		return [
			'status' => 'ok',
			'message' => $message
		];
	}

	public function renderErrors(array $errors)
	{
		$app = \XF::app();
		$params = [
			'errors' => $errors,
			'error' => count($errors) == 1 ? reset($errors) : false,
			'forJson' => true
		];
		$html = $this->getTemplate($app['app.defaultType'] . ':error', $params)->render();

		return [
			'status' => 'error',
			'errors' => $errors,
			'errorHtml' => $this->getHtmlOutputStructure($html)
		];
	}

	public function renderView($viewName, $templateName, array $params = [])
	{
		if (isset($params['innerContent']))
		{
			return $params['innerContent'];
		}

		$output = $this->renderViewObject($viewName, $templateName, $params);
		if ($output === null)
		{
			$output = [
				'status' => 'ok',
				'html' => $this->renderHtmlFallback($viewName, $templateName, $params)
			];
		}
		else if (is_array($output) && array_key_exists('html', $output) && $output['html'] === null)
		{
			$output['html'] = $this->renderHtmlFallback($viewName, $templateName, $params);
		}

		return $output;
	}

	public function renderHtmlFallback($viewName, $templateName, array $params = [])
	{
		$htmlOutput = $this->renderViewObject($viewName, $templateName, $params, 'html');
		if ($htmlOutput === null && $templateName)
		{
			$htmlOutput = $this->getTemplate($templateName, $params)->render();
		}

		return $this->getHtmlOutputStructure($htmlOutput);
	}

	public function getHtmlOutputStructure($html)
	{
		$output = [
			'content' => strval($html)
		];

		$templater = $this->templater;
		$pageParams = $templater->pageParams;
		$templateErrors = $templater->getTemplateErrors();

		if ($output['content'] === '' && $templateErrors)
		{
			$output['content'] = \XF::phrase('no_content_returned_try_again_later');
		}

		if (\XF::$debugMode && $templateErrors)
		{
			$output['templateErrors'] = true;

			$templateErrorDetails = [];
			foreach ($templateErrors AS $error)
			{
				$templateErrorDetails[] = sprintf('Template %s: %s (%s:%d)',
					$error['template'],
					$error['error'],
					File::stripRootPathPrefix($error['file']),
					$error['line']
				);
			}
			$output['templateErrorDetails'] = $templateErrorDetails;
		}

		if (isset($pageParams['pageTitle']))
		{
			$output['title'] = $pageParams['pageTitle'];
		}
		if (isset($pageParams['pageH1']))
		{
			$output['h1'] = $pageParams['pageH1'];
		}

		$includedCss = $templater->getIncludedCss();
		if ($includedCss)
		{
			$output['css'] = $includedCss;
		}

		$inlineCss = $templater->getInlineCss();
		if ($inlineCss)
		{
			$output['cssInline'] = $inlineCss;
		}

		$includedJs = $templater->getIncludedJs();
		if ($includedJs)
		{
			$output['js'] = $includedJs;
		}

		$inlineJs = $templater->getInlineJs();
		if ($inlineJs)
		{
			$output['jsInline'] = $inlineJs;
		}

		if (isset($templater->pageParams['jsState']) && is_array($templater->pageParams['jsState']))
		{
			$output['jsState'] = $templater->pageParams['jsState'];
		}

		return $output;
	}

	public function postFilter($content, AbstractReply $reply)
	{
		if (!is_array($content))
		{
			$e = new \LogicException('JSON response output must be an array.');
			if (\XF::$debugMode)
			{
				throw $e;
			}
			else
			{
				\XF::logException($e);
			}
			$content = [];
		}

		$replyJson = $reply->getJsonParams();
		if ($replyJson)
		{
			foreach ($replyJson AS $k => $v)
			{
				if (!array_key_exists($k, $content))
				{
					$content[$k] = $v;
				}
			}
		}

		$content = $this->addDefaultJsonParams($content);

		return json_encode($this->prepareJsonEncode($content), $this->encodeModifiers);
	}

	protected function addDefaultJsonParams(array $content)
	{
		$app = \XF::app();
		$visitor = \XF::visitor();
		$language = \XF::language();
		$container = $app->container();

		if ($visitor->user_id)
		{
			$conversations = $visitor->conversations_unread;
			$alerts = $visitor->alerts_unread;

			$content['visitor'] = [
				'conversations_unread' => $language->numberFormat($conversations),
				'alerts_unread' => $language->numberFormat($alerts),
				'total_unread' => $language->numberFormat($conversations + $alerts)
			];
		}

		if ($container->isCached('job.manager'))
		{
			$jobManager = $app->jobManager();
			$manualJobs = $jobManager->hasManualEnqueued();
			$autoJobs = $jobManager->hasAutoEnqueued();
			$autoBlocking = $jobManager->hasAutoBlocking();

			if ($manualJobs || $autoJobs)
			{
				if ($autoBlocking)
				{
					$autoBlockingMessage = $jobManager->getAutoBlockingMessage();
					if ($autoBlockingMessage)
					{
						$autoBlockingMessage = strval($autoBlockingMessage);
					}
				}
				else
				{
					$autoBlockingMessage = null;
				}

				$content['job'] = [
					'manual' => $manualJobs ? array_keys($jobManager->getManualEnqueued()) : null,
					'autoBlocking' => $autoBlocking ? array_keys($jobManager->getAutoBlocking()) : null,
					'autoBlockingMessage' => $autoBlockingMessage,
					'auto' => $autoJobs
				];
			}
		}

		if (\XF::$debugMode)
		{
			if ($container->isCached('db'))
			{
				$queryCount = \XF::db()->getQueryCount();
			}
			else
			{
				$queryCount = null;
			}

			$content['debug'] = [
				'time' => round(microtime(true) - \XF::app()->container('time.granular'), 4),
				'queries' => $queryCount,
				'memory' => round(memory_get_peak_usage() / 1024 / 1024, 2)
			];
		}

		return $content;
	}

	protected function prepareJsonEncode($value)
	{
		if (is_array($value))
		{
			foreach ($value AS &$innerValue)
			{
				$innerValue = $this->prepareJsonEncode($innerValue);
			}
		}
		else if (is_object($value) && method_exists($value, 'jsonSerialize'))
		{
			$value = $value->jsonSerialize();
		}
		else if (is_object($value) && method_exists($value, '__toString'))
		{
			$value = $value->__toString();
		}

		return $value;
	}
}