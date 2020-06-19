<?php

namespace XF\Mvc\Renderer;

use XF\Mvc\Reply\AbstractReply;
use XF\Util\File;

class Html extends AbstractRenderer
{
	protected function initialize()
	{
		$this->response->contentType('text/html');
	}

	public function getResponseType()
	{
		return 'html';
	}

	public function renderRedirect($url, $type, $message = '')
	{
		$this->setResponseCode($type == 'permanent' ? 301 : 303);
		$this->response->header('Location', $url);
	}

	public function renderMessage($message)
	{
		$app = \XF::app();
		return $this->renderView('XF:MessagePage', $app['app.defaultType'] . ':message_page', [
			'message' => $message
		]);
	}

	public function renderErrors(array $errors)
	{
		$app = \XF::app();
		return $this->renderView('XF:Error', $app['app.defaultType'] . ':error', [
			'errors' => $errors,
			'error' => count($errors) == 1 ? reset($errors) : false
		]);
	}

	public function renderView($viewName, $templateName, array $params = [])
	{
		$output = $this->renderViewObject($viewName, $templateName, $params);
		if ($output === null && $templateName)
		{
			$output = $this->getTemplate($templateName, $params)->render();
		}
		return $output;
	}

	public function postFilter($content, AbstractReply $reply)
	{
		$content = strval($content);

		if (\XF::$debugMode)
		{
			$errors = $this->templater->getTemplateErrors();
			if ($errors)
			{
				$errorHtml = '<div class="blockMessage blockMessage--error"><h2 style="margin: 0 0 .5em 0">Template errors</h2><ul>';
				foreach ($errors AS $error)
				{
					$errorHtml .= sprintf('<li>Template %s: %s (%s:%d)</li>',
						htmlspecialchars($error['template']),
						htmlspecialchars($error['error']),
						htmlspecialchars(File::stripRootPathPrefix($error['file'])),
						$error['line']
					);
				}
				$errorHtml .= '</ul></div>';

				if (strpos($content, '<!--XF:EXTRA_OUTPUT-->') !== false)
				{
					$content = str_replace('<!--XF:EXTRA_OUTPUT-->', $errorHtml . '<!--XF:EXTRA_OUTPUT-->', $content);
				}
				else
				{
					$content = preg_replace('#<body[^>]*>#i', "\\0$errorHtml", $content);
				}
			}
		}

		$templater = $this->templater;
		$cssReplace = '';
		$jsReplace = '';

		$includedCss = $templater->getIncludedCss(['public:extra.less']);
		if ($includedCss)
		{
			$cssReplace .= '<link rel="stylesheet" href="'
				. htmlspecialchars($templater->getCssLoadUrl($includedCss))
				. '" />' . "\n";

			$cssReplaceJson = json_encode(array_fill_keys($includedCss, true));
		}
		else
		{
			$cssReplaceJson = '{}';
		}

		$inlineCss = $templater->getInlineCss();
		if ($inlineCss)
		{
			foreach ($inlineCss AS $inline)
			{
				$cssReplace .= "<style>\n$inline\n</style>\n";
			}
		}

		$includedJs = $templater->getIncludedJs();
		if ($includedJs)
		{
			foreach ($includedJs AS $js)
			{
				$jsReplace .= '<script src="' . htmlspecialchars($js) . '"></script>' . "\n";
			}

			$jsReplaceJson = json_encode(array_fill_keys($includedJs, true));
		}
		else
		{
			$jsReplaceJson = '{}';
		}

		$inlineJs = $templater->getInlineJs();
		if ($inlineJs)
		{
			foreach ($inlineJs AS $inline)
			{
				$jsReplace .= "<script>\n$inline\n</script>\n";
			}
		}

		if (\XF::app()->container()->isCached('db'))
		{
			$queryCount = \XF::db()->getQueryCount();
		}
		else
		{
			$queryCount = '-';
		}

		$time = microtime(true) - \XF::app()->container('time.granular');

		$content = strtr($content, [
			'<!--XF:CSS-->' => $cssReplace,
			'{\'<!--XF:CSS:JSON-->\'}' => $cssReplaceJson,
			'<!--XF:JS-->' => $jsReplace,
			'{\'<!--XF:JS:JSON-->\'}' => $jsReplaceJson,
			'<!--XF:QUERIES-->' => $queryCount,
			'<!--XF:PAGE_TIME-->' => number_format($time, 4),
			'<!--XF:MEMORY_PEAK-->' => number_format(memory_get_peak_usage() / 1024 / 1024, 2) . 'MB',
		]);

		return $content;
	}
}