<?php

namespace XF\Api\Controller;

use XF\Mvc\Entity\Entity;
use XF\Mvc\ParameterBag;

/**
 * @api-group Conversations
 */
class ConversationMessage extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertApiScopeByRequestMethod('conversation');
	}

	/**
	 * @api-desc Gets the specified conversation message.
	 *
	 * @api-out ConversationMessage $message
	 */
	public function actionGet(ParameterBag $params)
	{
		$message = $this->assertViewableMessage($params->message_id);

		$result = $message->toApiResult(Entity::VERBOSITY_VERBOSE, [
			'with_conversation' => true
		]);

		return $this->apiResult(['message' => $result]);
	}

	/**
	 * @api-desc Updates the specified conversation message.
	 *
	 * @api-in str $message The new message content
	 * @api-in str $attachment_key API attachment key to upload files. Attachment key content type must be conversation_message with context[message_id] set to this message ID.
	 *
	 * @api-out true $success
	 * @api-out ConversationMessage $message
	 */
	public function actionPost(ParameterBag $params)
	{
		$message = $this->assertViewableMessage($params->message_id);

		if (\XF::isApiCheckingPermissions() && !$message->canEdit($error))
		{
			return $this->noPermission($error);
		}

		$editor = $this->setupMessageEdit($message);
		$editor->setAutoSpamCheck(false);

		if (\XF::isApiCheckingPermissions())
		{
			$editor->checkForSpam();
		}

		if (!$editor->validate($errors))
		{
			return $this->error($errors);
		}

		$editor->save();

		return $this->apiSuccess([
			'message' => $message->toApiResult(Entity::VERBOSITY_VERBOSE)
		]);
	}

	/**
	 * @param \XF\Entity\ConversationMessage $message
	 *
	 * @return \XF\Service\Conversation\MessageEditor
	 */
	protected function setupMessageEdit(\XF\Entity\ConversationMessage $message)
	{
		/** @var \XF\Service\Conversation\MessageEditor $editor */
		$editor = $this->service('XF:Conversation\MessageEditor', $message);

		$content = $this->filter('message', '?str');
		if ($content !== null)
		{
			$editor->setMessageContent($content);
		}

		if (\XF::isApiBypassingPermissions() || $message->Conversation->canUploadAndManageAttachments())
		{
			$attachmentKey = $this->filter('attachment_key', 'str');
			$hash = $this->getAttachmentTempHashFromKey(
				$attachmentKey, 'conversation_message', ['message_id' => $message->message_id]
			);
			$editor->setAttachmentHash($hash);
		}

		return $editor;
	}

	/**
	 * @api-desc Reacts to the specified conversation message
	 *
	 * @api-see \XF\Api\ControllerPlugin\Reaction::actionReact()
	 */
	public function actionPostReact(ParameterBag $params)
	{
		$message = $this->assertViewableMessage($params->message_id);

		/** @var \XF\Api\ControllerPlugin\Reaction $reactPlugin */
		$reactPlugin = $this->plugin('XF:Api:Reaction');
		return $reactPlugin->actionReact($message);
	}

	/**
	 * @param int $id
	 * @param string|array $with
	 *
	 * @return \XF\Entity\ConversationMessage
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertViewableMessage($id, $with = 'api')
	{
		return $this->assertViewableApiRecord('XF:ConversationMessage', $id, $with);
	}
}