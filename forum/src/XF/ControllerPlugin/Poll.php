<?php

namespace XF\ControllerPlugin;

use XF\Mvc\Entity\Entity;

class Poll extends AbstractPlugin
{
	public function actionCreate($contentType, Entity $content, array $breadcrumbs = [])
	{
		/** @var \XF\Repository\Poll $pollRepo */
		$pollRepo = $this->repository('XF:Poll');
		$handler = $pollRepo->getPollHandler($contentType);

		if (!$handler->canCreate($content, $error))
		{
			return $this->noPermission();
		}

		if ($this->isPost())
		{
			$creator = $this->setupPollCreate($contentType, $content);

			if (!$creator->validate($errors))
			{
				return $this->error($errors);
			}

			$creator->save();

			return $this->redirect($this->getDynamicRedirect());
		}
		else
		{
			$viewParams = [
				'createFormUrl' => $handler->getPollLink('create', $content),

				'breadcrumbs' => $breadcrumbs
			];
			return $this->view('XF:Poll\Create', 'poll_create', $viewParams);
		}
	}

	/**
	 * @param $contentType
	 * @param Entity $content
	 *
	 * @return \XF\Service\Poll\Creator
	 */
	public function setupPollCreate($contentType, Entity $content)
	{
		$pollInput = $this->getPollInput();

		/** @var \XF\Service\Poll\Creator $creator */
		$creator = $this->service('XF:Poll\Creator', $contentType, $content);

		$creator->setQuestion($pollInput['question']);
		$creator->setMaxVotes($pollInput['max_votes_type'], $pollInput['max_votes_value']);

		if ($pollInput['close'])
		{
			$creator->setCloseDateRelative($pollInput['close_length'], $pollInput['close_units']);
		}

		$creator->setOptions([
			'change_vote' => $pollInput['change_vote'],
			'public_votes' => $pollInput['public_votes'],
			'view_results_unvoted' => $pollInput['view_results_unvoted']
		]);

		$creator->addResponses($pollInput['new_responses']);

		return $creator;
	}

	public function actionEdit($poll, array $breadcrumbs = [])
	{
		if (!($poll instanceof \XF\Entity\Poll))
		{
			return $this->notFound();
		}

		/** @var \XF\Poll\AbstractHandler $handler */
		$handler = $poll->Handler;
		$contentType = $poll->content_type;
		$content = $poll->Content;

		if (!$poll->canEdit($error))
		{
			return $this->noPermission($error);
		}

		if ($this->isPost())
		{
			$editor = $this->setupPollEdit($poll, $contentType, $content, $handler);
			if (!$editor->validate($errors))
			{
				return $this->error($errors);
			}

			$editor->save();

			return $this->redirect($this->getDynamicRedirect());
		}
		else
		{
			$viewParams = [
				'poll' => $poll,
				'breadcrumbs' => $breadcrumbs
			];
			return $this->view('XF:Poll\Edit', 'poll_edit', $viewParams);
		}
	}

	/**
	 * @param \XF\Entity\Poll $poll
	 * @param string $contentType
	 * @param Entity $content
	 * @param \XF\Poll\AbstractHandler $handler
	 *
	 * @return \XF\Service\Poll\Editor
	 */
	protected function setupPollEdit(\XF\Entity\Poll $poll, $contentType, Entity $content, \XF\Poll\AbstractHandler $handler)
	{
		$pollInput = $this->getPollInput();

		/** @var \XF\Service\Poll\Editor $editor */
		$editor = $this->service('XF:Poll\Editor', $poll);

		if ($poll->canEditDetails())
		{
			$editor->setQuestion($pollInput['question']);
			$editor->updateExistingResponses($pollInput['existing_responses']);
		}
		$editor->addResponses($pollInput['new_responses']);

		if ($poll->canEditMaxVotes())
		{
			$editor->setMaxVotes($pollInput['max_votes_type'], $pollInput['max_votes_value']);
		}

		if ($poll->canChangePollVisibility())
		{
			$editor->setPublicVotes($pollInput['public_votes']);
		}

		if ($pollInput['close'])
		{
			$editor->setCloseDateRelative($pollInput['close_length'], $pollInput['close_units']);
		}
		else if (!$pollInput['remove_close'])
		{
			$editor->removeCloseDate();
		}

		$editor->setOptions([
			'change_vote' => $pollInput['change_vote'],
			'view_results_unvoted' => $pollInput['view_results_unvoted']
		]);

		return $editor;
	}

	public function actionDelete($poll, array $breadcrumbs = [])
	{
		if (!($poll instanceof \XF\Entity\Poll))
		{
			return $this->notFound();
		}

		if (!$poll->canDelete($error))
		{
			return $this->noPermission($error);
		}

		if ($this->isPost())
		{
			$action = $this->filter('poll_action', 'str');

			if ($action == 'remove')
			{
				$this->service('XF:Poll\Deleter', $poll)->delete();
			}
			else if ($action == 'reset')
			{
				$this->service('XF:Poll\Resetter', $poll)->reset();
			}

			return $this->redirect($this->getDynamicRedirect());
		}
		else
		{
			$viewParams = [
				'poll' => $poll,
				'breadcrumbs' => $breadcrumbs
			];
			return $this->view('XF:Poll\Delete', 'poll_delete', $viewParams);
		}
	}

	public function actionVote($poll, array $breadcrumbs = [])
	{
		if (!($poll instanceof \XF\Entity\Poll))
		{
			return $this->notFound();
		}

		if (!$poll->canVote($error))
		{
			return $this->noPermission($error);
		}

		if ($this->isPost())
		{
			$voteResponseIds = $this->filter('responses', 'array-uint');

			/** @var \XF\Service\Poll\Voter $voter */
			$voter = $this->service('XF:Poll\Voter', $poll, $voteResponseIds);
			if (!$voter->validate($errors))
			{
				return $this->error($errors);
			}

			$voter->save();

			$viewParams = [
				'poll' => $poll,
				'breadcrumbs' => $breadcrumbs,
				'simpleDisplay' => $this->filter('simple_display', 'bool')
			];
			return $this->view('XF:Poll\Block', 'poll_block', $viewParams);
		}
		else
		{
			$viewParams = [
				'poll' => $poll,
				'breadcrumbs' => $breadcrumbs,
				'simpleDisplay' => $this->filter('simple_display', 'bool')
			];
			return $this->view('XF:Poll\Vote', 'poll_vote', $viewParams);
		}
	}

	public function actionResults($poll, array $breadcrumbs = [])
	{
		if (!($poll instanceof \XF\Entity\Poll))
		{
			return $this->notFound();
		}

		if (!$poll->canViewResults($error))
		{
			return $this->noPermission($error);
		}

		$responseId = $this->filter('response', 'uint');

		if ($responseId)
		{
			if (!isset($poll->Responses[$responseId]))
			{
				return $this->notFound();
			}

			if (!$poll->public_votes)
			{
				return $this->noPermission();
			}

			$viewParams = [
				'poll' => $poll,
				'response' => $poll->Responses[$responseId],
				'breadcrumbs' => $breadcrumbs
			];
			return $this->view('XF:Poll\Voters', 'poll_voters', $viewParams);
		}
		else
		{
			$viewParams = [
				'poll' => $poll,
				'breadcrumbs' => $breadcrumbs
			];
			return $this->view('XF:Poll\Results', 'poll_results', $viewParams);
		}
	}

	public function getPollInput()
	{
		$input = $this->filter([
			'poll' => [
				'question' => 'str',

				'existing_responses' => 'array-str',
				'new_responses' => 'array-str',

				'max_votes_type' => 'str',
				'max_votes_value' => 'uint',

				'close' => 'bool',
				'remove_close' => 'bool',
				'close_length' => 'uint',
				'close_units' => 'str',

				'change_vote' => 'bool',
				'public_votes' => 'bool',
				'view_results_unvoted' => 'bool'
			],
		]);

		return $input['poll'];
	}
}