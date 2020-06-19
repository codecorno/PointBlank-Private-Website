<?php

namespace XF;

class Logger
{
	protected $app;

	/**
	 * @var \XF\ModeratorLog\Logger|null
	 */
	protected $moderatorLogger;

	public function __construct(App $app)
	{
		$this->app = $app;
	}

	/**
	 * @return \XF\ModeratorLog\Logger
	 */
	public function moderatorLogger()
	{
		if (!$this->moderatorLogger)
		{
			$class = 'XF\ModeratorLog\Logger';
			$class = $this->app->extendClass($class);

			$this->moderatorLogger = new $class($this->app->getContentTypeField('moderator_log_handler_class'));
		}

		return $this->moderatorLogger;
	}

	public function logModeratorAction($type, $content, $action, array $params = [], $throw = true)
	{
		return $this->moderatorLogger()->log($type, $content, $action, $params, $throw);
	}

	public function logModeratorChange($type, $content, $field, $throw = true)
	{
		return $this->moderatorLogger()->logChange($type, $content, $field, $throw);
	}

	public function logModeratorChanges($type, \XF\Mvc\Entity\Entity $content, $throw = true)
	{
		return $this->moderatorLogger()->logChanges($type, $content, $throw);
	}
}