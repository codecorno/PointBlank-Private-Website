<?php

namespace XF\Api\Controller;

use XF\Mvc\Entity\Entity;
use XF\Mvc\ParameterBag;

/**
 * @api-group Conversations
 */
class Conversations extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertApiScopeByRequestMethod('conversation');
		$this->assertRegisteredUser();
	}

	/**
	 * @api-desc Gets the API user's list of conversations.
	 *
	 * @api-in int $page
	 *
	 * @api-out Conversation[] $conversations
	 * @api-out pagination $pagination
	 */
	public function actionGet()
	{
		$page = $this->filterPage();
		$perPage = $this->options()->discussionsPerPage;

		$conversationFinder = $this->setupConversationFinder();
		$conversationFinder->limitByPage($page, $perPage);

		/** @var \XF\Entity\ConversationUser[]|\XF\Mvc\Entity\AbstractCollection $conversations */
		$conversations = $conversationFinder->fetch();
		$totalConversations = $conversationFinder->total();

		$this->assertValidApiPage($page, $perPage, $totalConversations);

		$conversationsResults = $conversations->toApiResults();

		$return = [
			'conversations' => $conversationsResults,
			'pagination' => $this->getPaginationData($conversationsResults, $page, $perPage, $totalConversations)
		];
		return $this->apiResult($return);
	}

	/**
	 * @return \XF\Finder\ConversationUser
	 */
	protected function setupConversationFinder()
	{
		$conversationFinder = $this->getConversationRepo()->findUserConversations(\XF::visitor())
			->with('api')
			->keyedBy('conversation_id');

		$starterId = $this->filter('starter_id', 'uint');
		if ($starterId)
		{
			$conversationFinder->where('Master.user_id', $starterId);
		}

		$receiverId = $this->filter('receiver_id', 'uint');
		if (!empty($filters['receiver_id']))
		{
			$conversationFinder->exists('Master.Recipients|' . intval($receiverId));
		}

		$starred = $this->filter('starred', 'bool');
		if ($starred)
		{
			$conversationFinder->where('is_starred', 1);
		}

		return $conversationFinder;
	}

	/**
	 * @api-desc Creates a conversation
	 *
	 * @api-in <req> int[] $recipient_ids List of user IDs to send the conversation to
	 * @api-in <req> str $title Conversation title
	 * @api-in <req> str $message Conversation message body
	 * @api-in str $attachment_key API attachment key to upload files. Attachment key content type must be conversation_message with no context.
	 * @api-in bool $conversation_open If false, no replies may be made to this conversation.
	 * @api-in bool $open_invite If true, any member of the conversation may add others
	 *
	 * @api-out true $success
	 * @api-out Conversation $conversation
	 */
	public function actionPost()
	{
		$this->assertRequiredApiInput(['title', 'message', 'recipient_ids']);

		$visitor = \XF::visitor();
		if (\XF::isApiCheckingPermissions() && !$visitor->canStartConversation())
		{
			return $this->noPermission();
		}

		$creator = $this->setupConversationCreate();
		$creator->setAutoSpamCheck(false);

		if (\XF::isApiCheckingPermissions())
		{
			$creator->checkForSpam();
		}

		if (!$creator->validate($errors))
		{
			return $this->error($errors);
		}

		/** @var \XF\Entity\ConversationMaster $conversation */
		$conversation = $creator->save();
		$this->finalizeConversationCreate($creator);

		$userConv = $conversation->Users[$visitor->user_id];

		return $this->apiSuccess([
			'conversation' => $userConv->toApiResult(Entity::VERBOSITY_VERBOSE)
		]);
	}

	/**
	 * @return \XF\Service\Conversation\Creator
	 */
	protected function setupConversationCreate()
	{
		$input = $this->filter([
			'title' => 'str',
			'message' => 'str',
			'attachment_key' => 'str',
			'recipient_ids' => 'array-uint',
			'conversation_open' => '?bool',
			'open_invite' => 'bool'
		]);

		$visitor = \XF::visitor();

		$recipients = $this->em()->findByIds('XF:User', $input['recipient_ids']);

		/** @var \XF\Service\Conversation\Creator $creator */
		$creator = $this->service('XF:Conversation\Creator', $visitor);
		$creator->setOptions([
			'open_invite' => $input['open_invite'],
			'conversation_open' => isset($input['conversation_open']) ? $input['conversation_open'] : true
		]);

		if (\XF::isApiBypassingPermissions())
		{
			$creator->overrideMaxAllowed(-1);
		}
		$creator->setRecipients($recipients, \XF::isApiCheckingPermissions());
		$creator->setContent($input['title'], $input['message']);

		$conversation = $creator->getConversation();

		if (\XF::isApiBypassingPermissions() || $conversation->canUploadAndManageAttachments())
		{
			$hash = $this->getAttachmentTempHashFromKey($input['attachment_key'], 'conversation_message', []);
			$creator->setAttachmentHash($hash);
		}

		return $creator;
	}

	protected function finalizeConversationCreate(\XF\Service\Conversation\Creator $creator)
	{
	}

	/**
	 * @return \XF\Repository\Conversation
	 */
	protected function getConversationRepo()
	{
		return $this->repository('XF:Conversation');
	}
}