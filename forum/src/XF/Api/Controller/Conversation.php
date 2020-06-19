<?php

namespace XF\Api\Controller;

use XF\Mvc\Entity\Entity;
use XF\Mvc\ParameterBag;

/**
 * @api-group Conversations
 */
class Conversation extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertApiScopeByRequestMethod('conversation');
	}

	/**
	 * @api-desc Gets information about the specified conversation.
	 *
	 * @api-in bool $with_messages If specified, the response will include a page of messages.
	 * @api-in int $page The page of messages to include
	 *
	 * @api-out Conversation $conversation
	 * @api-see self::getMessagesInConversationPaginated()
	 */
	public function actionGet(ParameterBag $params)
	{
		$userConv = $this->assertViewableUserConversation($params->conversation_id);

		if ($this->filter('with_messages', 'bool'))
		{
			$messageData = $this->getMessagesInConversationPaginated($userConv->Master, $this->filterPage());
		}
		else
		{
			$messageData = [];
		}

		$result = [
			'conversation' => $userConv->toApiResult(Entity::VERBOSITY_VERBOSE)
		];
		$result += $messageData;

		return $this->apiResult($result);
	}

	/**
	 * @api-desc Gets a page of messages in the specified conversation.
	 *
	 * @api-in int $page
	 *
	 * @api-see self::getMessagesInConversationPaginated
	 */
	public function actionGetMessages(ParameterBag $params)
	{
		$userConv = $this->assertViewableUserConversation($params->conversation_id);

		$messageData = $this->getMessagesInConversationPaginated($userConv->Master, $this->filterPage());

		return $this->apiResult($messageData);
	}

	/**
	 * @api-out ConversationMessage[] $messages List of messages on the requested page
	 * @api-out pagination $pagination Pagination details
	 *
	 * @param \XF\Entity\ConversationMaster $conversation
	 * @param int $page
	 * @param null $perPage
	 *
	 * @return array
	 */
	protected function getMessagesInConversationPaginated(\XF\Entity\ConversationMaster $conversation, $page = 1, $perPage = null)
	{
		$perPage = intval($perPage);
		if ($perPage <= 0)
		{
			$perPage = $this->options()->messagesPerPage;
		}
		$total = $conversation->reply_count + 1;

		$this->assertValidApiPage($page, $perPage, $total);

		$finder = $this->setupMessageFinder($conversation);
		$messages = $finder->limitByPage($page, $perPage)->fetch();

		/** @var \XF\Repository\Attachment $attachmentRepo */
		$attachmentRepo = $this->repository('XF:Attachment');
		$attachmentRepo->addAttachmentsToContent($messages, 'conversation_message');

		$messageResults = $messages->toApiResults();

		return [
			'messages' => $messageResults,
			'pagination' => $this->getPaginationData($messageResults, $page, $perPage, $total)
		];
	}

	/**
	 * @param \XF\Entity\ConversationMaster $conversation
	 *
	 * @return \XF\Finder\ConversationMessage
	 */
	protected function setupMessageFinder(\XF\Entity\ConversationMaster $conversation)
	{
		/** @var \XF\Finder\ConversationMessage $finder */
		$finder = $this->finder('XF:ConversationMessage');
		$finder
			->inConversation($conversation)
			->order('message_date')
			->with('api');

		return $finder;
	}

	/**
	 * @param \XF\Entity\ConversationMaster $conversation
	 *
	 * @return \XF\Service\Conversation\Editor
	 */
	protected function setupConversationEdit(\XF\Entity\ConversationMaster $conversation)
	{
		/** @var \XF\Service\Conversation\Editor $editor */
		$editor = $this->service('XF:Conversation\Editor', $conversation);

		$input = $this->filter([
			'title' => '?str',
			'conversation_open' => '?bool',
			'open_invite' => '?bool'
		]);

		if (isset($input['title']))
		{
			$editor->setTitle($input['title']);
		}
		if (isset($input['open_invite']))
		{
			$editor->setOpenInvite($input['open_invite']);
		}
		if (isset($input['conversation_open']))
		{
			$editor->setConversationOpen($input['conversation_open']);
		}

		return $editor;
	}

	/**
	 * @api-desc Updates the specified conversation
	 *
	 * @api-in str $title Conversation title
	 * @api-in bool $open_invite If true, any member of the conversation can add others
	 * @api-in bool $conversation_open If false, no further replies are allowed.
	 *
	 * @api-out true $success
	 * @api-out Conversation $conversation
	 */
	public function actionPost(ParameterBag $params)
	{
		$userConv = $this->assertViewableUserConversation($params->conversation_id);
		$conversation = $userConv->Master;

		if (\XF::isApiCheckingPermissions() && !$conversation->canEdit())
		{
			return $this->noPermission();
		}

		$editor = $this->setupConversationEdit($conversation);
		if (!$editor->validate($errors))
		{
			return $this->error($errors);
		}

		$editor->save();

		return $this->apiSuccess([
			'conversation' => $conversation->toApiResult(Entity::VERBOSITY_VERBOSE)
		]);
	}

	/**
	 * @api-desc Sets the star status of the specified conversation
	 *
	 * @api-in bool $star If provided, sets the star status as specified. If not provided, toggles the status.
	 *
	 * @api-out true $success
	 */
	public function actionPostStar(ParameterBag $params)
	{
		$userConv = $this->assertViewableUserConversation($params->conversation_id);

		$star = $this->filter('star', '?bool');
		if ($star === null)
		{
			$star = $userConv->is_starred ? false : true;
		}

		$userConv->is_starred = $star;
		$userConv->save();

		return $this->apiSuccess();
	}

	/**
	 * @api-desc Invites the specified users to this conversation.
	 *
	 * @api-in <req> int[] $recipient_ids List of user IDs to invite
	 *
	 * @api-out true $success
	 */
	public function actionPostInvite(ParameterBag $params)
	{
		$userConv = $this->assertViewableUserConversation($params->conversation_id);
		$conversation = $userConv->Master;

		if (\XF::isApiCheckingPermissions() && !$conversation->canInvite())
		{
			return $this->noPermission();
		}

		$this->assertRequiredApiInput('recipient_ids');

		/** @var \XF\Service\Conversation\Inviter $inviter */
		$inviter = $this->service('XF:Conversation\Inviter', $conversation, \XF::visitor());

		$recipientIds = $this->filter('recipient_ids', 'array-uint');
		$recipients = $this->em()->findByIds('XF:User', $recipientIds);

		if (\XF::isApiBypassingPermissions())
		{
			$inviter->overrideMaxAllowed(-1);
		}
		$inviter->setRecipients($recipients, \XF::isApiCheckingPermissions());

		if (!$inviter->validate($errors))
		{
			return $this->error($errors);
		}

		$inviter->save();

		return $this->apiSuccess();
	}

	/**
	 * @api-desc Deletes the specified conversation from the API user's list. Does not delete the conversation for other receivers.
	 *
	 * @api-in bool $ignore If true, further replies to this conversation will be ignored. (Otherwise, replies will restore this conversation to the list.)
	 *
	 * @api-out true $success
	 */
	public function actionDelete(ParameterBag $params)
	{
		$userConv = $this->assertViewableUserConversation($params->conversation_id);

		$recipient = $userConv->Recipient;
		if ($recipient)
		{
			$recipient->recipient_state = $this->filter('ignore', 'bool') ? 'deleted_ignored' : 'deleted';
			$recipient->save();
		}

		return $this->apiSuccess();
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