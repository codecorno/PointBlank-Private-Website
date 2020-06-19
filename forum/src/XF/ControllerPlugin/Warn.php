<?php

namespace XF\ControllerPlugin;

use XF\Mvc\Entity\Entity;

class Warn extends AbstractPlugin
{
	public function actionWarn($contentType, Entity $content, $warnUrl, array $breadcrumbs = [])
	{
		/** @var \XF\Repository\Warning $warningRepo */
		$warningRepo = $this->repository('XF:Warning');

		$warningHandler = $warningRepo->getWarningHandler($contentType, true);
		if (!$warningHandler)
		{
			return $this->noPermission();
		}

		$user = $warningHandler->getContentUser($content);
		if (!$user)
		{
			return $this->noPermission();
		}

		if ($this->isPost())
		{
			$input = $this->getWarnSubmitInput();

			if ($this->filter('fill', 'bool'))
			{
				return $this->getWarningFillerReply($warningHandler, $user, $contentType, $content, $input);
			}

			$warnService = $this->setupWarnService($warningHandler, $user, $contentType, $content, $input);

			if (!$warnService->validate($errors))
			{
				return $this->error($errors);
			}
			$warnService->save();

			return $this->redirect($this->getDynamicRedirect());
		}
		else
		{
			$title = $warningHandler->getStoredTitle($content);

			$viewParams = [
				'user' => $user,
				'title' => $warningHandler->getDisplayTitle($title),
				'contentUrl' => $warningHandler->getContentUrl($content),
				'warnings' => $warningRepo->findWarningDefinitionsForList()->fetch(),
				'contentActions' => $warningHandler->getAvailableContentActions($content),
				'warnUrl' => $warnUrl,
				'breadcrumbs' => $breadcrumbs
			];
			return $this->view('XF:Member\Warn', 'member_warn', $viewParams);
		}
	}

	protected function getWarnSubmitInput()
	{
		return $this->filter([
			'warning_definition_id' => 'uint',
			'filled_warning_definition_id' => 'uint',
			'custom_title' => 'str',
			'points_enable' => 'bool',
			'points' => 'uint',
			'expiry_enable' => 'bool',
			'expiry_value' => 'uint',
			'expiry_unit' => 'str',
			'notes' => 'str',
			'start_conversation' => 'bool',
			'conversation_title' => 'str',
			'conversation_message' => 'str',
			'open_invite' => 'bool',
			'conversation_locked' => 'bool'
		]);
	}

	/**
	 * @param \XF\Warning\AbstractHandler $warningHandler
	 * @param \XF\Entity\User $user
	 * @param string $contentType
	 * @param \XF\Mvc\Entity\Entity $content
	 * @param array $input
	 *
	 * @return \XF\Service\User\Warn
	 */
	protected function setupWarnService(
		\XF\Warning\AbstractHandler $warningHandler,
		\XF\Entity\User $user,
		$contentType,
		\XF\Mvc\Entity\Entity $content,
		array $input
	)
	{
		$points = $input['points_enable'] ? $input['points'] : 0;

		if ($input['expiry_enable'])
		{
			$expiry = strtotime('+' . $input['expiry_value'] . ' ' . $input['expiry_unit']);
		}
		else
		{
			$expiry = 0;
		}

		if ($input['warning_definition_id']
			&& $input['warning_definition_id'] != $input['filled_warning_definition_id']
		)
		{
			throw $this->exception($this->error(\XF::phrase('warning_not_filled_try_again')));
		}

		/** @var \XF\Service\User\Warn $warnService */
		$warnService = $this->service('XF:User\Warn', $user, $contentType, $content, \XF::visitor());

		$definition = $input['warning_definition_id']
			? $this->em()->find('XF:WarningDefinition', $input['warning_definition_id'])
			: null;
		if ($definition)
		{
			$warnService->setFromDefinition($definition, $points, $expiry);
		}
		else
		{
			$warnService->setFromCustom($input['custom_title'], $points, $expiry);
		}

		$warnService->setNotes($input['notes']);

		if ($input['start_conversation'])
		{
			if (!strlen($input['conversation_title']))
			{
				throw $this->exception(
					$this->error(\XF::phrase('please_enter_valid_title_to_start_conversation'))
				);
			}
			if (!strlen($input['conversation_message']))
			{
				throw $this->exception(
					$this->error(\XF::phrase('please_enter_valid_message_to_start_conversation'))
				);
			}

			$warnService->withConversation($input['conversation_title'], $input['conversation_message'], [
				'open_invite' => $input['open_invite'],
				'conversation_open' => !$input['conversation_locked']
			]);
		}

		$allowedActions = $warningHandler->getAvailableContentActions($content);
		$this->setupContentAction($warnService, $allowedActions, $input);

		return $warnService;
	}

	protected function setupContentAction(\XF\Service\User\Warn $warnService, array $allowedActions, array $input)
	{
		$action = $this->filter('content_action', 'str');
		if ($action == 'public' && !empty($allowedActions['public']))
		{
			$warnService->withContentAction('public', [
				'message' => $this->filter('action_options.public_message', 'str')
			]);
		}
		else if ($action == 'delete' && !empty($allowedActions['delete']))
		{
			$warnService->withContentAction('delete', [
				'reason' => $this->filter('action_options.delete_reason', 'str')
			]);
		}
	}

	protected function getWarningFillerReply(
		\XF\Warning\AbstractHandler $warningHandler,
		\XF\Entity\User $user,
		$contentType,
		\XF\Mvc\Entity\Entity $content,
		array $input
	)
	{
		/** @var \XF\Entity\WarningDefinition $definition */
		$definition = $input['warning_definition_id']
			? $this->em()->find('XF:WarningDefinition', $input['warning_definition_id'])
			: null;
		if ($definition)
		{
			list($conversationTitle, $conversationMessage) = $definition->getSpecificConversationContent(
				$user, $contentType, $content
			);
		}
		else
		{
			$conversationTitle = '';
			$conversationMessage = '';
		}

		$viewParams = [
			'definition' => $definition,
			'conversationTitle' => $conversationTitle,
			'conversationMessage' => $conversationMessage
		];
		return $this->view('XF:Member\WarnFill', '', $viewParams);
	}
}