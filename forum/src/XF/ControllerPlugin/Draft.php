<?php

namespace XF\ControllerPlugin;

class Draft extends AbstractPlugin
{
	public function actionDraftMessage(
		\XF\Draft $draft, array $extraData = [], $messageInput = 'message', &$actionTaken = null
	)
	{
		$actionTaken = $this->updateMessageDraft($draft, $extraData, $messageInput);
		return $this->getDraftReply($actionTaken);
	}

	public function updateMessageDraft(\XF\Draft $draft, array $extraData = [], $messageInput = 'message')
	{
		$message = $this->controller->plugin('XF:Editor')->fromInput($messageInput);

		if ($this->request->filter('delete', 'bool') || !strlen($message))
		{
			$draft->delete();

			return 'delete';
		}
		else
		{
			$draft->message = $message;
			$draft->extra_data = $extraData;
			$draft->save();

			return 'save';
		}
	}

	public function actionDraftMessageless(\XF\Draft $draft, array $extraData, &$actionTaken = null)
	{
		$actionTaken = $this->updateMessagelessDraft($draft, $extraData);
		return $this->getDraftReply($actionTaken);
	}

	public function updateMessagelessDraft(\XF\Draft $draft, array $extraData)
	{
		if ($this->request->filter('delete', 'bool'))
		{
			$draft->delete();

			return 'delete';
		}
		else
		{
			$draft->message = '';
			$draft->extra_data = $extraData;
			$draft->save();

			return 'save';
		}
	}

	public function getDraftReply($actionTaken)
	{
		if ($actionTaken == 'delete')
		{
			$message = \XF::phrase('draft_deleted_successfully');
		}
		else
		{
			$message = \XF::phrase('draft_saved_successfully');
		}

		$reply = $this->message($message);
		$this->addDraftJsonParams($reply, $actionTaken);

		return $reply;
	}

	public function addDraftJsonParams(\XF\Mvc\Reply\AbstractReply $reply, $action)
	{
		if ($action == 'delete')
		{
			$message = \XF::phrase('draft_deleted_successfully');
		}
		else
		{
			$message = \XF::phrase('draft_saved_successfully');
		}

		$reply->setJsonParam('draft', [
			'action' => $action,
			'message' => $message
		]);
	}
}