<?php

namespace XF\Job;

class ApprovalQueueProcess extends AbstractJob
{
	protected $defaultData = [
		'asUserId' => 0,
		'queue' => [],
		'rawInput' => []
	];

	public function run($maxRunTime)
	{
		$timer = new \XF\Timer($maxRunTime);

		/** @var \XF\Entity\User $asUser */
		$asUser = $this->app->em()->find('XF:User', $this->data['asUserId']);
		if (!$asUser)
		{
			$asUser = $this->app->repository('XF:User')->getGuestUser();
		}

		/** @var \XF\Repository\ApprovalQueue $repo */
		$repo = $this->app->repository('XF:ApprovalQueue');

		foreach ($this->data['queue'] AS $contentType => $actions)
		{
			if (!$actions)
			{
				unset($this->data['queue'][$contentType]);
				continue;
			}

			$handler = $repo->getApprovalQueueHandler($contentType);
			if (!$handler)
			{
				unset($this->data['queue'][$contentType]);
				continue;
			}

			$handler->setInput($this->data['rawInput']);

			foreach ($actions AS $contentId => $action)
			{
				if (!$repo->isContentAwaitingApproval($contentType, $contentId))
				{
					// as this is job based now, this is more likely to happen
					unset($this->data['queue'][$contentType][$contentId]);
					continue;
				}

				\XF::asVisitor($asUser, function() use($handler, $action, $contentType, $contentId)
				{
					if (!$action)
					{
						return;
					}

					$content = $handler->getContent($contentId);
					if (!$content || !$handler->canView($content))
					{
						return;
					}

					// successful actions here will remove queue entries
					$handler->performAction($action, $content);
				});

				unset($this->data['queue'][$contentType][$contentId]);

				if ($timer->limitExceeded())
				{
					return $this->resume();
				}
			}
		}

		return $this->complete();
	}

	public function getStatusMessage()
	{
		return \XF::phrase('processing_approval_queue...');
	}

	public function canCancel()
	{
		return false;
	}

	public function canTriggerByChoice()
	{
		return true;
	}
}