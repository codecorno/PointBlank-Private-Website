<?php

namespace XF;

class DevJsResponse
{
	/**
	 * @var App
	 */
	protected $app;

	public function __construct(App $app)
	{
		$this->app = $app;
	}

	public function run($jsPath, $addOnId)
	{
		$development = $this->app->config('development');
		if (!$development['fullJs'])
		{
			return $this->getConsoleResponse('error', 'Full JS mode is not enabled.');
		}

		if (!$addOnId || !preg_match('#^[a-z][a-z0-9]*(/[a-z][a-z0-9]*)?$#i', $addOnId))
		{
			return $this->getConsoleResponse('error', 'Invalid add-on specified.');
		}

		$addOnManager = $this->app->addOnManager();
		$addOn = $addOnManager->getById($addOnId);

		if (!$addOn || !$addOn->isInstalled())
		{
			return $this->getConsoleResponse('error', 'Add-on (' . \XF::escapeString($addOnId, 'js') . ') is not installed.');
		}

		// Sanity checks for the form. There are some duplicative checks here, but just to make things clear.
		if (
			preg_match('#[^[a-z0-9_/.-]#i', $jsPath)
			|| strpos($jsPath, '..') !== false
			|| strpos($jsPath, './') !== false
			|| substr($jsPath, -3) !== '.js'
		)
		{
			return $this->getConsoleResponse('error', 'JS path format not recognized.');
		}
		if (!preg_match('#^([a-z0-9_-]+(\.[a-z0-9_-]+)*/)*[a-z0-9_-]+(\.[a-z0-9_-]+)*\.js$#i', $jsPath))
		{
			return $this->getConsoleResponse('error', 'JS path format not recognized.');
		}

		$fullPath = $addOn->getFilesDirectory() . '/js/' . $jsPath;
		if (!file_exists($fullPath))
		{
			// fallback to root JS if exists
			$fullPath = \XF::getRootDirectory() . '/js/' . $jsPath;
			if (!file_exists($fullPath))
			{
				return $this->getConsoleResponse('info', 'Response for development JS is empty.');
			}
		}

		$output = file_get_contents($fullPath);

		if (!$output)
		{
			return $this->getConsoleResponse('info', 'Response for development JS is empty.');
		}

		return $this->getResponse($output);
	}

	public function getConsoleResponse($type, $message)
	{
		$type = preg_replace('/[^a-z0-9]/i', '', $type);
		return $this->getResponse('console.' . $type . '(\'' . \XF::escapeString($message, 'js') . '\');');
	}

	public function getResponse($output)
	{
		$response = $this->app->response();
		$response->contentType('text/javascript');
		$response->header('Expires', 'Thu, 19 Nov 1981 08:52:00 GMT');
		$response->header('Cache-control', 'private, max-age=0');
		$response->body($output);

		return $response;
	}
}