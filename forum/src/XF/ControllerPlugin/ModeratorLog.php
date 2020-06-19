<?php

namespace XF\ControllerPlugin;

use XF\Mvc\Entity\Entity;

class ModeratorLog extends AbstractPlugin
{
	public function actionModeratorActions(Entity $entity, array $linkData, $title = null, array $breadcrumbs = [])
	{
		$contentType = $entity->getEntityContentType();
		$contentId = $entity->getEntityId();

		if (!$contentType)
		{
			throw new \InvalidArgumentException("Provided entity must defined a content type in its structure");
		}

		/** @var \XF\Repository\ModeratorLog $modLogRepo */
		$modLogRepo = $this->repository('XF:ModeratorLog');

		$page = $this->filterPage();
		$perPage = 20;

		$logs = $modLogRepo->findLogsForDiscussion($contentType, $contentId)
			->limitByPage($page, $perPage, 1)
			->fetch();

		if (!count($logs))
		{
			return $this->message(\XF::phrase('no_moderator_actions_have_been_logged'));
		}

		$hasNext = count($logs) > $perPage;
		$logs = $logs->slice(0, $perPage);

		$viewParams = [
			'type' => $contentType,
			'id' => $contentId,

			'linkRoute' => isset($linkData[0]) ? $linkData[0] : '',
			'linkData' => isset($linkData[1]) ? $linkData[1] : null,
			'linkParams' => (isset($linkData[2]) && is_array($linkData[2])) ? $linkData[2] : [],

			'logs' => $logs,
			'hasNext' => $hasNext,
			'page' => $page,

			'title' => $title,
			'breadcrumbs' => $breadcrumbs
		];
		return $this->view('XF:ModeratorLog\ModeratorActions', 'moderator_log_moderator_actions', $viewParams);
	}
}