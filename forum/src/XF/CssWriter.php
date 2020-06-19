<?php

namespace XF;

use XF\Http\Request;

class CssWriter
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var CssRenderer
	 */
	protected $renderer;

	/**
	 * @var callable|null
	 */
	protected $validator;

	public function __construct(App $app, CssRenderer $renderer)
	{
		$this->app = $app;
		$this->renderer = $renderer;
	}

	public function run(array $templates, $styleId, $languageId, $validation = null)
	{
		$language = $this->app->language($languageId);
		\XF::setLanguage($language);
		$this->renderer->getTemplater()->setLanguage($language);

		if ($styleId)
		{
			$style = $this->app->container()->create('style', $styleId);
			$this->renderer->setStyle($style);
		}

		if (count($templates) > 1 && $validation !== null && $this->validator)
		{
			$validator = $this->validator;
			$validationExpected = $validator($templates);
			if ($validationExpected !== $validation)
			{
				// if we don't have a matching validation value, don't try to write to the cache
				$this->renderer->setAllowFinalCacheUpdate(false);
			}
		}

		$output = $this->renderer->render($templates);
		$output = $this->finalizeOutput($output);

		return $this->getResponse($output);
	}

	public function setValidator(callable $validator)
	{
		$this->validator = $validator;
	}

	public function finalizeOutput($output)
	{
		return '@charset "UTF-8";' . "\n\n" . $output;
	}

	public function canSend304(Request $request)
	{
		if (!$this->renderer->getAllowCached())
		{
			return false;
		}

		$browserModified = $request->getServer('HTTP_IF_MODIFIED_SINCE');

		if (!$browserModified || !is_scalar($browserModified))
		{
			return false;
		}

		return true;
	}

	public function getResponse($output)
	{
		$response = $this->app->response();
		$response->contentType('text/css', 'utf-8');
		if ($this->renderer->getAllowCached())
		{
			$response->header('Expires', gmdate('D, d M Y H:i:s', \XF::$time + 365 * 86400) . ' GMT');
			$response->header('Last-Modified', gmdate('D, d M Y H:i:s', $this->renderer->getLastModifiedDate()) . ' GMT');
			$response->header('Cache-Control', 'public, max-age=' . (365 * 86400));
		}
		else
		{
			$response->header('Last-Modified', gmdate('D, d M Y H:i:s', \XF::$time) . ' GMT');
			$response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
		}
		$response->body($output);

		return $response;
	}

	public function get304Response()
	{
		$response = $this->app->response();
		$response->contentType('text/css', 'utf-8');
		$response->httpCode(304);

		return $response;
	}
}