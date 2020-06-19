<?php

namespace XF\Pub\Controller;

use XF\Diff;
use XF\Mvc\ParameterBag;

class EditHistory extends AbstractController
{
	public function actionIndex(ParameterBag $params)
	{
		$contentType = $this->filter('content_type', 'str', $params->content_type);
		$contentId = $this->filter('content_id', 'uint', $params->content_id);

		$historyRepo = $this->getEditHistoryRepo();

		$handler = $historyRepo->getEditHistoryHandler($contentType);
		if (!$handler)
		{
			return $this->noPermission();
		}

		$content = $handler->getContent($contentId);
		if (!$content || !$handler->canViewHistory($content))
		{
			return $this->noPermission();
		}

		$editCount = $handler->getEditCount($content);
		if (!$editCount)
		{
			return $this->error(\XF::phrase('this_content_has_not_been_edited'));
		}

		$editHistory = $historyRepo->findEditHistoryForContent($contentType, $contentId)->fetch();
		if (!$editHistory->count())
		{
			return $this->error(\XF::phrase('this_content_edit_history_has_been_removed'));
		}
		$currentHistory = $editHistory->first();

		$oldId = $this->filter('old', 'uint');
		$newId = $this->filter('new', 'uint');

		if ($oldId)
		{
			// doing a comparison
			$old = $this->assertRecordExists('XF:EditHistory', $oldId);
			$oldText = $old->old_text;

			if ($newId)
			{
				$new = $this->assertRecordExists('XF:EditHistory', $newId);
				$newText = $new->old_text;
			}
			else
			{
				$newText = $handler->getContentText($content);
			}

			$diffHandler = new Diff();
			$diffs = $diffHandler->findDifferences($oldText, $newText, Diff::DIFF_TYPE_LINE);
		}
		else
		{
			$diffs = [];
		}

		$this->setSectionContext($handler->getSectionContext());

		$viewParams = [
			'content' => $content,
			'contentType' => $contentType,
			'contentId' => $contentId,

			'title' => $handler->getContentTitle($content),
			'breadcrumbs' => $handler->getBreadcrumbs($content),

			'editCount' => $editCount,
			'editHistory' => $editHistory,

			'diffs' => $diffs,
			'oldId' => $oldId ?: $currentHistory->edit_history_id,
			'newId' => $newId
		];
		if ($oldId)
		{
			return $this->view('XF:EditHistory\Compare', 'edit_history_compare', $viewParams);
		}
		else
		{
			return $this->view('XF:EditHistory\Index', 'edit_history_index', $viewParams);
		}
	}

	public function actionView(ParameterBag $params)
	{
		$editHistory = $this->assertHistoryViewable($params->edit_history_id);

		$handler = $editHistory->getHandler();
		$content = $editHistory->Content;

		$this->setSectionContext($handler->getSectionContext());

		$viewParams = [
			'content' => $content,

			'editHistory' => $editHistory,
			'handler' => $handler,

			'title' => $handler->getContentTitle($content),
			'breadcrumbs' => $handler->getBreadcrumbs($content),

			'canRevert' => $handler->canRevertContent($content)
		];
		return $this->view('XF:EditHistory\View', 'edit_history_view', $viewParams);
	}

	public function actionRevert(ParameterBag $params)
	{
		$this->assertPostOnly();

		$editHistory = $this->assertHistoryViewable($params->edit_history_id);

		$handler = $editHistory->getHandler();
		$content = $editHistory->Content;

		if (!$handler->canRevertContent($content))
		{
			return $this->redirect($this->buildLink('edit-history/view', $editHistory), '');
		}

		$this->getEditHistoryRepo()->revertToHistory($editHistory, $content, $handler);

		return $this->redirect($handler->getContentLink($content));
	}

	/**
	 * @param $historyId
	 * @param array $extraWith
	 *
	 * @return \XF\Entity\EditHistory
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertHistoryViewable($historyId, array $extraWith = [])
	{
		$extraWith[] = 'User';
		array_unique($extraWith);

		/** @var \XF\Entity\EditHistory $history */
		$history = $this->em()->find('XF:EditHistory', $historyId, $extraWith);
		if (!$history)
		{
			throw $this->exception($this->notFound(\XF::phrase('requested_history_not_found')));
		}

		$handler = $history->getHandler();
		$content = $history->Content;

		if (!$content || !$handler->canViewHistory($content))
		{
			throw $this->exception($this->noPermission());
		}

		return $history;
	}

	/**
	 * @return \XF\Repository\EditHistory
	 */
	protected function getEditHistoryRepo()
	{
		return $this->repository('XF:EditHistory');
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('performing_moderation_duties');
	}
}