<?php

namespace XF\ControllerPlugin;

class InlineMod extends AbstractPlugin
{
	public function clearIdFromCookie($type, $id)
	{
		$handler = $this->getInlineModHandler($type);
		if (!$handler)
		{
			return;
		}

		$ids = $handler->getCookieIds($this->request);
		$position = array_search($id, $ids);

		if ($position !== false)
		{
			unset($ids[$position]);
			$handler->updateCookieIds($this->app->response(), $ids);
		}
	}

	public function clearCookie($type)
	{
		$handler = $this->getInlineModHandler($type);
		if ($handler)
		{
			$handler->clearCookie($this->app->response());
		}
	}

	/**
	 * @param string $type
	 *
	 * @return null|\XF\InlineMod\AbstractHandler
	 */
	public function getInlineModHandler($type)
	{
		if (!$type)
		{
			return null;
		}

		$class = $this->app->getContentTypeFieldValue($type, 'inline_mod_handler_class');
		if (!$class)
		{
			return null;
		}

		$class = $this->app->extendClass($class);

		return new $class($type, $this->app, $this->request);
	}
}