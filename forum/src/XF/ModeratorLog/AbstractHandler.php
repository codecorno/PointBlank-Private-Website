<?php

namespace XF\ModeratorLog;

use XF\Entity\ModeratorLog;
use XF\Mvc\Entity\Entity;

abstract class AbstractHandler
{
	protected $contentType;

	public function __construct($contentType)
	{
		$this->contentType = $contentType;
	}

	abstract protected function getLogActionForChange(Entity $content, $field, $newValue, $oldValue);
	abstract protected function setupLogEntityContent(ModeratorLog $log, Entity $content);

	public function isLoggable(Entity $content, $action, \XF\Entity\User $actor)
	{
		return true;
	}

	public function isLoggableUser(\XF\Entity\User $actor)
	{
		return ($actor->user_id  && $actor->is_moderator);
	}

	public function logChange(Entity $content, $field, $newValue, $oldValue, \XF\Entity\User $actor)
	{
		$action = $this->getLogActionForChange($content, $field, $newValue, $oldValue);
		if (!$action)
		{
			return null;
		}

		if (is_array($action))
		{
			list($action, $params) = $action;
		}
		else
		{
			$params = [];
		}

		return $this->log($content, $action, $params, $actor);
	}

	public function log(Entity $content, $action, array $params, \XF\Entity\User $actor)
	{
		if (!$this->isLoggable($content, $action, $actor))
		{
			return null;
		}

		$log = \XF::em()->create('XF:ModeratorLog');
		$log->content_type = $this->contentType;
		$id = $content->getIdentifierValues();
		if (!$id || count($id) != 1)
		{
			throw new \InvalidArgumentException("Entity does not have an ID or does not have a simple key");
		}
		$log->content_id = intval(reset($id));

		$this->setupLogEntityActor($log, $actor);
		$this->setupLogEntityContent($log, $content);

		$log->action = $action;
		$log->action_params = $params;
		$log->save();

		return $log;
	}

	protected function setupLogEntityActor(ModeratorLog $log, \XF\Entity\User $actor)
	{
		$log->user_id = $actor->user_id;

		if ($actor->user_id == \XF::visitor()->user_id)
		{
			$log->ip_address = \XF\Util\Ip::convertIpStringToBinary(\XF::app()->request()->getIp());
		}
	}

	public function getContentTitle(ModeratorLog $log)
	{
		return \XF::app()->stringFormatter()->censorText($log->content_title_);
	}

	public function getAction(ModeratorLog $log)
	{
		return \XF::phrase(
			$this->getActionPhraseName($log),
			$this->getActionPhraseParams($log)
		);
	}

	public function getActionPhraseName(ModeratorLog $log)
	{
		return 'mod_log.' . $log->content_type . '_' . $log->action;
	}

	protected function getActionPhraseParams(ModeratorLog $log)
	{
		return $log->action_params;
	}

	public function getEntityWith()
	{
		return [];
	}

	public function getContent($id)
	{
		return \XF::app()->findByContentType($this->contentType, $id, $this->getEntityWith());
	}

	public function getContentType()
	{
		return $this->contentType;
	}
}