<?php

namespace XF\Api\Controller;

use XF\Mvc\Entity\Entity;
use XF\Mvc\ParameterBag;

/**
 * @api-group Conversations
 */
class ConversationMessages extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertApiScopeByRequestMethod('conversation');
	}

	/**
	 * @api-desc Replies to a conversation
	 *
	 * @api-in <req> int $conversation_id
	 * @api-in <req> str $message
	 * @api-in str $attachment_key API attachment key to upload files. Attachment key content type must be conversation_message with context[conversation_id] set to this conversation ID.
	 *
	 * @api-out true $success
	 * @api-out ConversationMessage $message The newly inserted message
	 */
	public function actionPost(ParameterBag $params)
	{
		$this->assertRequiredApiInput(['conversation_id', 'message']);

		$conversationId = $this->filter('conversation_id', 'uint');

		/** @var \XF\Entity\ConversationUser $userConv */
		$userConv = $this->assertViewableUserConversation($conversationId);
		$conversation = $userConv->Master;

		if (\XF::isApiCheckingPermissions() && !$conversation->canReply())
		{
			return $this->noPermission();
		}

		$replier = $this->setupConversationReply($conversation);
		$replier->setAutoSpamCheck(false);

		if (\XF::isApiCheckingPermissions())
		{
			$replier->checkForSpam();
		}

		if (!$replier->validate($errors))
		{
			return $this->error($errors);
		}

		/** @var \XF\Entity\ConversationMessage $message */
		$message = $replier->save();
		$this->finalizeConversationReply($replier);

		return $this->apiSuccess([
			'message' => $message->toApiResult(Entity::VERBOSITY_VERBOSE)
		]);
	}

	/**
	 * @param \XF\Entity\ConversationMaster $conversation
	 *
	 * @return \XF\Service\Conversation\Replier
	 */
	protected function setupConversationReply(\XF\Entity\ConversationMaster $conversation)
	{
		/** @var \XF\Service\Conversation\Replier $replier */
		$replier = $this->service('XF:Conversation\Replier', $conversation, \XF::visitor());

		$message = $this->filter('message', 'str');
		$replier->setMessageContent($message);

		if (\XF::isApiBypassingPermissions() || $conversation->canUploadAndManageAttachments())
		{
			$attachmentKey = $this->filter('attachment_key', 'str');
			$hash = $this->getAttachmentTempHashFromKey(
				$attachmentKey, 'conversation_message', ['conversation_id' => $conversation->conversation_id]
			);
			$replier->setAttachmentHash($hash);
		}

		return $replier;
	}

	protected function finalizeConversationReply(\XF\Service\Conversation\Replier $replier)
	{
	}

	/**
	 * @param int $id
	 * @param string|array $with
	 *
	 * @return \XF\Entity\ConversationUser
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertViewableUserConversation($id, $with = 'api')
	{
		/** @var \XF\Api\ControllerPlugin\Conversation $conversationPlugin */
		$conversationPlugin = $this->plugin('XF:Api:Conversation');
		return $conversationPlugin->assertViewableUserConversation($id, $with);
	}
}