<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\AbstractReply;

class Conversation extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertRegistrationRequired();
	}

	public function actionIndex(ParameterBag $params)
	{
		if ($params->conversation_id)
		{
			return $this->rerouteController(__CLASS__, 'view', $params);
		}

		$page = $this->filterPage($params->page);
		$perPage = $this->options()->discussionsPerPage;

		$this->assertCanonicalUrl($this->buildLink('conversations', null, ['page' => $page]));

		$visitor = \XF::visitor();
		$filters = $this->getConversationFilterInput();

		$conversationRepo = $this->getConversationRepo();

		$conversationFinder = $conversationRepo->findUserConversations($visitor)
			->limitByPage($page, $perPage);

		$this->applyConversationFilters($conversationFinder, $filters);

		$totalConversations = $conversationFinder->total();
		$this->assertValidPage($page, $perPage, $totalConversations, 'conversations');

		$userConvs = $conversationFinder->fetch();

		$starterFilter = !empty($filters['starter_id']) ? $this->em()->find('XF:User', $filters['starter_id']) : null;
		$receiverFilter = !empty($filters['receiver_id']) ? $this->em()->find('XF:User', $filters['receiver_id']) : null;

		$viewParams = [
			'userConvs' => $userConvs,

			'page' => $page,
			'perPage' => $perPage,
			'total' => $totalConversations,

			'starterFilter' => $starterFilter,
			'receiverFilter' => $receiverFilter,

			'filters' => $filters
		];
		return $this->view('XF:Conversations\Listing', 'conversation_list', $viewParams);
	}

	protected function applyConversationFilters(\XF\Finder\ConversationUser $finder, array $filters)
	{
		if (!empty($filters['starter_id']))
		{
			$finder->where('Master.user_id', intval($filters['starter_id']));
		}

		if (!empty($filters['receiver_id']))
		{
			$finder->exists('Master.Recipients|' . intval($filters['receiver_id']));
		}

		if (!empty($filters['starred']))
		{
			$finder->where('is_starred', 1);
		}

		if (!empty($filters['unread']))
		{
			$finder->where('is_unread', 1);
		}
	}

	protected function getConversationFilterInput()
	{
		$filters = [];

		$input = $this->filter([
			'starter_id' => 'uint',
			'receiver_id' => 'uint',
			'filter_type' => 'str',
			'starter' => 'str',
			'receiver' => 'str',
			'starred' => 'bool',
			'unread' => 'bool'
		]);

		if ($input['starter_id'])
		{
			$filters['starter_id'] = $input['starter_id'];
		}
		else if ($input['filter_type'] == 'started' && $input['starter'])
		{
			$user = $this->em()->findOne('XF:User', ['username' => $input['starter']]);
			if ($user)
			{
				$filters['starter_id'] = $user->user_id;
			}
		}

		if ($input['receiver_id'])
		{
			$filters['receiver_id'] = $input['receiver_id'];
		}
		else if ($input['filter_type'] == 'received' && $input['receiver'])
		{
			$user = $this->em()->findOne('XF:User', ['username' => $input['receiver']]);
			if ($user)
			{
				$filters['receiver_id'] = $user->user_id;
			}
		}

		if ($input['starred'])
		{
			$filters['starred'] = 1;
		}

		if ($input['unread'])
		{
			$filters['unread'] = 1;
		}

		return $filters;
	}

	public function actionFilters()
	{
		$filters = $this->getConversationFilterInput();

		return $this->redirect($this->buildLink('conversations', null, $filters));
	}

	public function actionPopup()
	{
		$visitor = \XF::visitor();
		$conversationRepo = $this->getConversationRepo();
		$cutOff = \XF::$time - $this->options()->conversationPopupExpiryHours * 3600;

		$conversations = $conversationRepo->getUserConversationsForPopup($visitor, 10, $cutOff, ['Master.LastMessageUser']);

		$totalUnread = $conversationRepo->findUserConversationsForPopupList($visitor, true)->total();
		if ($totalUnread != $visitor->conversations_unread)
		{
			$visitor->conversations_unread = $totalUnread;
			$visitor->saveIfChanged();
		}

		$viewParams = [
			'unreadConversations' => $conversations['unread'],
			'readConversations' => $conversations['read']
		];
		return $this->view('XF:Conversations\Popup', 'conversations_popup', $viewParams);
	}

	public function actionView(ParameterBag $params)
	{
		$userConv = $this->assertViewableUserConversation($params->conversation_id,
			['Master.DraftReplies|' . \XF::visitor()->user_id]
		);
		$conversation = $userConv->Master;

		$page = $params->page;
		$perPage = $this->options()->messagesPerPage;

		$messageCount = $conversation->reply_count + 1;

		$this->assertValidPage($page, $perPage, $messageCount, 'conversations', $conversation);
		$this->assertCanonicalUrl($this->buildLink('conversations', $conversation, ['page' => $page]));

		$conversationRepo = $this->getConversationRepo();
		$conversationMessageRepo = $this->getConversationMessageRepo();

		$messageList = $conversationMessageRepo->findMessagesForConversationView($conversation);
		$messages = $messageList->limitByPage($page, $perPage)->fetch();

		/** @var \XF\Repository\Attachment $attachmentRepo */
		$attachmentRepo = $this->repository('XF:Attachment');
		$attachmentRepo->addAttachmentsToContent($messages, 'conversation_message');

		/** @var \XF\Repository\UserAlert $userAlertRepo */
		$userAlertRepo = $this->repository('XF:UserAlert');
		$userAlertRepo->markUserAlertsReadForContent('conversation_message', $messages->keys());

		/** @var \XF\Repository\Unfurl $unfurlRepo */
		$unfurlRepo = $this->repository('XF:Unfurl');
		$unfurlRepo->addUnfurlsToContent($messages, false);

		$lastRead = $userConv->Recipient ? $userConv->Recipient->last_read_date : 0;

		$lastMessage = $messages->last();
		$conversationRepo->markUserConversationRead($userConv, $lastMessage->message_date);

		$viewParams = [
			'userConv' => $userConv,
			'conversation' => $conversation,
			'recipients' => $conversationRepo->findRecipientsForList($conversation)->fetch(),

			'lastRead' => $lastRead,
			'messages' => $messages,
			'lastMessage' => $lastMessage,

			'page' => $page,
			'perPage' => $perPage,

			'attachmentData' => $this->getReplyAttachmentData($conversation)
		];
		return $this->view('XF:Conversation\View', 'conversation_view', $viewParams);
	}

	public function actionUnread(ParameterBag $params)
	{
		$userConv = $this->assertViewableUserConversation($params->conversation_id);
		$conversation = $userConv->Master;
		$recipient = $userConv->Recipient;

		if (!$recipient || !$recipient->last_read_date)
		{
			return $this->redirect($this->buildLink('conversations', $userConv));
		}

		$convMessageRepo = $this->getConversationMessageRepo();

		$firstUnread = $convMessageRepo->getFirstUnreadMessageInConversation($userConv);
		if (!$firstUnread || $firstUnread->message_id == $conversation->last_message_id)
		{
			$messagesBefore = $conversation->reply_count;
			$messageId = $conversation->last_message_id;
		}
		else
		{
			$messagesBefore = $convMessageRepo->findEarlierMessages($conversation, $firstUnread)->total();
			$messageId = $firstUnread->message_id;
		}

		$page = floor($messagesBefore / $this->options()->messagesPerPage) + 1;
		return $this->redirect(
			$this->buildLink('conversations', $conversation, ['page' => $page]) . '#convMessage-' . $messageId
		);
	}

	public function actionLatest(ParameterBag $params)
	{
		$userConv = $this->assertViewableUserConversation($params->conversation_id);
		$conversation = $userConv->Master;

		$messagesBefore = $conversation->reply_count;
		$messageId = $conversation->last_message_id;

		$page = floor($messagesBefore / $this->options()->messagesPerPage) + 1;
		return $this->redirect(
			$this->buildLink('conversations', $conversation, ['page' => $page]) . '#convMessage-' . $messageId
		);
	}

	/**
	 * @return \XF\Service\Conversation\Creator
	 */
	protected function setupConversationCreate()
	{
		$recipients = $this->filter('recipients', 'str');
		$title = $this->filter('title', 'str');
		$message = $this->plugin('XF:Editor')->fromInput('message');

		$conversationLocked = $this->filter('conversation_locked', 'bool');
		$options = $this->filter([
			'open_invite' => 'bool'
		]);
		$options['conversation_open'] = !$conversationLocked;

		$visitor = \XF::visitor();

		/** @var \XF\Service\Conversation\Creator $creator */
		$creator = $this->service('XF:Conversation\Creator', $visitor);
		$creator->setOptions($options);
		$creator->setRecipients($recipients);
		$creator->setContent($title, $message);

		$conversation = $creator->getConversation();

		if ($conversation->canUploadAndManageAttachments())
		{
			$creator->setAttachmentHash($this->filter('attachment_hash', 'str'));
		}

		return $creator;
	}

	protected function finalizeConversationCreate(\XF\Service\Conversation\Creator $creator)
	{
		\XF\Draft::createFromKey('conversation')->delete();
	}

	public function actionAdd()
	{
		$visitor = \XF::visitor();

		if (!$visitor->canStartConversation())
		{
			return $this->noPermission();
		}

		if ($this->isPost())
		{
			$creator = $this->setupConversationCreate();
			if (!$creator->validate($errors))
			{
				return $this->error($errors);
			}
			$this->assertNotFlooding('conversation', $this->app->options()->floodCheckLengthDiscussion ?: null);
			$conversation = $creator->save();

			$this->finalizeConversationCreate($creator);

			return $this->redirect($this->buildLink('conversations', $conversation));
		}
		else
		{
			$to = $this->filter('to', 'str');
			$title = $this->filter('title', 'str');
			$message = $this->filter('message', 'str');

			if ($to !== '' && strpos($to, ',') === false)
			{
				/** @var \XF\Entity\User $toUser */
				$toUser = $this->em()->findOne('XF:User', ['username' => $to]);
				if (!$toUser)
				{
					return $this->notFound(\XF::phrase('requested_user_not_found'));
				}

				if ($visitor->user_id == $toUser->user_id)
				{
					return $this->noPermission(\XF::phrase('you_may_not_start_conversation_with_yourself'));
				}

				if (!$visitor->canStartConversationWith($toUser))
				{
					return $this->noPermission(\XF::phrase('you_may_not_start_conversation_with_x_because_of_their_privacy_settings', ['name' => $toUser->username]));
				}
			}

			/** @var \XF\Entity\ConversationMaster $conversation */
			$conversation = $this->em()->create('XF:ConversationMaster');

			$draft = \XF\Draft::createFromKey('conversation');

			if ($conversation->canUploadAndManageAttachments())
			{
				/** @var \XF\Repository\Attachment $attachmentRepo */
				$attachmentRepo = $this->repository('XF:Attachment');
				$attachmentData = $attachmentRepo->getEditorData('conversation_message', null, $draft->attachment_hash);
			}
			else
			{
				$attachmentData = null;
			}

			$viewParams = [
				'to' => $to ?: $draft->recipients,
				'title' => $title ?: $draft->title,
				'message' => $message ?: $draft->message,

				'conversation' => $conversation,
				'maxRecipients' => $conversation->getMaximumAllowedRecipients(),
				'draft' => $draft,

				'attachmentData' => $attachmentData
			];
			return $this->view('XF:Conversation\Add', 'conversation_add', $viewParams);
		}
	}

	public function actionDraft(ParameterBag $params)
	{
		$extraData = $this->filter([
			'attachment_hash' => 'str'
		]);

		if ($params->conversation_id)
		{
			$conversation = $this->assertViewableUserConversation($params->conversation_id);

			$draft = $conversation->Master->draft_reply;
		}
		else
		{
			$visitor = \XF::visitor();

			if (!$visitor->canStartConversation())
			{
				return $this->noPermission();
			}

			$extraData = $extraData + $this->filter([
				'recipients' => 'str',
				'title' => 'str',
				'open_invite' => 'bool',
				'conversation_locked' => 'bool'
			]);
			$extraData['conversation_open'] = !$extraData['conversation_locked'];
			unset($extraData['conversation_locked']);

			$draft = \XF\Draft::createFromKey('conversation');
		}

		/** @var \XF\ControllerPlugin\Draft $draftPlugin */
		$draftPlugin = $this->plugin('XF:Draft');
		return $draftPlugin->actionDraftMessage($draft, $extraData);
	}

	/**
	 * @param \XF\Entity\ConversationMaster $conversation
	 * @param \XF\Entity\ConversationUser $userConv
	 *
	 * @return \XF\Service\Conversation\Replier
	 */
	protected function setupConversationReply(\XF\Entity\ConversationMaster $conversation, \XF\Entity\ConversationUser $userConv)
	{
		$visitor = \XF::visitor();
		$message = $this->plugin('XF:Editor')->fromInput('message');

		/** @var \XF\Service\Conversation\Replier $replier */
		$replier = $this->service('XF:Conversation\Replier', $conversation, $visitor);
		$replier->setMessageContent($message);

		if ($conversation->canUploadAndManageAttachments())
		{
			$replier->setAttachmentHash($this->filter('attachment_hash', 'str'));
		}

		return $replier;
	}

	protected function afterConversationReply(\XF\Service\Conversation\Replier $replier)
	{
		$conversation = $replier->getConversation();

		$conversation->draft_reply->delete();
	}

	public function actionReply(ParameterBag $params)
	{
		$userConv = $this->assertViewableUserConversation($params->conversation_id);
		$conversation = $userConv->Master;
		if (!$conversation->canReply())
		{
			return $this->noPermission();
		}

		$defaultMessage = '';
		$forceAttachmentHash = null;

		$quote = $this->filter('quote', 'uint');
		if ($quote)
		{
			/** @var \XF\Entity\ConversationMessage $message */
			$message = $this->em()->find('XF:ConversationMessage', $quote, 'User');
			if ($message->conversation_id == $conversation->conversation_id && $message->canView())
			{
				$defaultMessage = $message->getQuoteWrapper(
					$this->app->stringFormatter()->getBbCodeForQuote($message->message, 'conversation_message')
				);
				$forceAttachmentHash = '';
			}
		}
		else
		{
			$defaultMessage = $conversation->draft_reply->message;
		}

		$viewParams = [
			'conversation' => $conversation,
			'attachmentData' => $this->getReplyAttachmentData($conversation, $forceAttachmentHash),
			'defaultMessage' => $defaultMessage
		];
		return $this->view('XF:Conversation\Reply', 'conversation_reply', $viewParams);
	}

	public function actionAddReply(ParameterBag $params)
	{
		$this->assertPostOnly();

		$userConv = $this->assertViewableUserConversation($params->conversation_id);
		$conversation = $userConv->Master;
		if (!$conversation->canReply())
		{
			return $this->noPermission();
		}

		$replier = $this->setupConversationReply($conversation, $userConv);
		if (!$replier->validate($errors))
		{
			return $this->error($errors);
		}
		$this->assertNotFlooding('conversation_message');
		$message = $replier->save();

		$this->afterConversationReply($replier);

		if ($this->filter('_xfWithData', 'bool') && $this->request->exists('last_date') && $message->canView())
		{
			$convMessageRepo = $this->getConversationMessageRepo();

			$limit = 3;
			$lastDate = $this->filter('last_date', 'uint');

			/** @var \XF\Mvc\Entity\Finder $messageList */
			$messageList = $convMessageRepo->findNewestMessagesInConversation($conversation, $lastDate)->limit($limit + 1);
			$messages = $messageList->fetch();

			// We fetched one more post than needed, if more than $limit posts were returned,
			// we can show the 'there are more posts' notice
			if ($messages->count() > $limit)
			{
				$firstUnshownMessage = $messages->first();

				// Remove the extra post
				$messages = $messages->pop();
			}
			else
			{
				$firstUnshownMessage = null;
			}

			// put the posts into oldest-first order
			$messages = $messages->reverse(true);

			/** @var \XF\Repository\Attachment $attachmentRepo */
			$attachmentRepo = $this->repository('XF:Attachment');
			$attachmentRepo->addAttachmentsToContent($messages, 'conversation_message');

			$viewParams = [
				'conversation' => $conversation,
				'messages' => $messages,
				'firstUnshownMessage' => $firstUnshownMessage
			];
			$view = $this->view('XF:Conversation\NewMessages', 'conversation_reply_new_messages', $viewParams);
			$view->setJsonParam('lastDate', $messages->last()->message_date);
			return $view;
		}
		else
		{
			return $this->redirect($this->buildLink('conversations/messages', $message));
		}
	}

	public function actionAddPreview()
	{
		$visitor = \XF::visitor();

		if (!$visitor->canStartConversation())
		{
			return $this->noPermission();
		}

		$creator = $this->setupConversationCreate();
		if (!$creator->validate($errors))
		{
			return $this->error($errors);
		}

		$message = $creator->getMessage();
		$conversation = $creator->getConversation();
		$attachments = null;

		$tempHash = $this->filter('attachment_hash', 'str');
		if ($tempHash && $conversation->canUploadAndManageAttachments())
		{
			$attachRepo = $this->repository('XF:Attachment');
			$attachments = $attachRepo->findAttachmentsByTempHash($tempHash)->fetch();
		}

		return $this->plugin('XF:BbCodePreview')->actionPreview(
			$message->message, 'conversation_message', $message->User, $attachments
		);
	}

	public function actionReplyPreview(ParameterBag $params)
	{
		$this->assertPostOnly();

		$userConv = $this->assertViewableUserConversation($params->conversation_id);
		$conversation = $userConv->Master;
		if (!$conversation->canReply())
		{
			return $this->noPermission();
		}

		$replier = $this->setupConversationReply($conversation, $userConv);
		if (!$replier->validate($errors))
		{
			return $this->error($errors);
		}

		$message = $replier->getMessage();
		$attachments = null;

		$tempHash = $this->filter('attachment_hash', 'str');
		if ($tempHash && $conversation->canUploadAndManageAttachments())
		{
			$attachRepo = $this->repository('XF:Attachment');
			$attachments = $attachRepo->findAttachmentsByTempHash($tempHash)->fetch();
		}

		return $this->plugin('XF:BbCodePreview')->actionPreview(
			$message->message, 'conversation_message', $message->User, $attachments
		);
	}

	public function actionMultiQuote(ParameterBag $params)
	{
		$this->assertPostOnly();

		/** @var \XF\ControllerPlugin\Quote $quotePlugin */
		$quotePlugin = $this->plugin('XF:Quote');

		$quotes = $this->filter('quotes', 'json-array');
		if (!$quotes)
		{
			return $this->error(\XF::phrase('no_messages_selected'));
		}
		$quotes = $quotePlugin->prepareQuotes($quotes);

		$messageFinder = $this->finder('XF:ConversationMessage');

		$messages = $messageFinder
			->with(['Conversation', 'User'])
			->where('message_id', array_keys($quotes))
			->order('message_date', 'DESC')
			->fetch()
			->filterViewable();

		if ($this->request->exists('insert'))
		{
			$insertOrder = $this->filter('insert', 'array');
			return $quotePlugin->actionMultiQuote($messages, $insertOrder, $quotes, 'conversation_message');
		}
		else
		{
			$viewParams = [
				'quotes' => $quotes,
				'messages' => $messages
			];
			return $this->view('XF:Conversation\MultiQuote', 'conversation_multi_quote', $viewParams);
		}
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

		$editor->setTitle($this->filter('title', 'str'));
		$editor->setOpenInvite($this->filter('open_invite', 'bool'));
		$editor->setConversationOpen(!$this->filter('conversation_locked', 'bool'));

		return $editor;
	}


	public function actionEdit(ParameterBag $params)
	{
		$userConv = $this->assertViewableUserConversation($params->conversation_id);
		$conversation = $userConv->Master;

		if (!$conversation->canEdit())
		{
			return $this->noPermission();
		}

		if ($this->isPost())
		{
			$editor = $this->setupConversationEdit($conversation);

			if (!$editor->validate($errors))
			{
				return $this->error($errors);
			}

			$editor->save();

			return $this->redirect($this->buildLink('conversations', $conversation));
		}
		else
		{
			$viewParams = [
				'userConv' => $userConv,
				'conversation' => $conversation
			];
			return $this->view('XF:Conversation\Edit', 'conversation_edit', $viewParams);
		}
	}

	public function actionStar(ParameterBag $params)
	{
		$userConv = $this->assertViewableUserConversation($params->conversation_id);

		$wasStarred = $userConv->is_starred;

		$redirect = $this->getDynamicRedirect(null, false);

		if ($this->isPost())
		{
			if (!$wasStarred)
			{
				$userConv->is_starred = true;
				$message = \XF::phrase('conversation_starred');
			}
			else
			{
				$userConv->is_starred = false;
				$message = \XF::phrase('conversation_unstarred');
			}

			$userConv->save();

			$reply = $this->redirect($redirect, $message);
			$reply->setJsonParam('switchKey', $userConv->is_starred ? 'unstar' : 'star');
			return $reply;
		}
		else
		{
			$viewParams = [
				'userConv' => $userConv,
				'conversation' => $userConv->Master,
				'redirect' => $redirect,
				'isStarred' => $wasStarred
			];
			return $this->view('XF:Conversation\Star', 'conversation_star', $viewParams);
		}
	}

	public function actionMarkUnread(ParameterBag $params)
	{
		$userConv = $this->assertViewableUserConversation($params->conversation_id);

		$wasUnread = $userConv->is_unread;

		$redirect = $this->buildLink('conversations');

		if ($this->isPost())
		{
			if (!$wasUnread)
			{
				$userConv->is_unread = true;
				$message = \XF::phrase('conversation_marked_as_unread');
			}
			else
			{
				$userConv->is_unread = false;
				$message = \XF::phrase('conversation_marked_as_read');
			}

			if ($userConv->Recipient)
			{
				$userConv->Recipient->last_read_date = $userConv->is_unread ? 0 : \XF::$time;
				$userConv->Recipient->save();
			}

			$userConv->save();

			$reply = $this->redirect($redirect, $message);
			$reply->setJsonParam('switchKey', $userConv->is_unread ? 'read' : 'unread');
			return $reply;
		}
		else
		{
			$viewParams = [
				'userConv' => $userConv,
				'conversation' => $userConv->Master,
				'redirect' => $redirect,
				'isUnread' => $wasUnread
			];
			return $this->view('XF:Conversation\MarkUnread', 'conversation_mark_unread', $viewParams);
		}
	}

	public function actionInvite(ParameterBag $params)
	{
		$userConv = $this->assertViewableUserConversation($params->conversation_id);
		$conversation = $userConv->Master;
		if (!$conversation->canInvite())
		{
			return $this->noPermission();
		}

		if ($this->isPost())
		{
			/** @var \XF\Service\Conversation\Inviter $inviter */
			$inviter = $this->service('XF:Conversation\Inviter', $conversation, \XF::visitor());

			$recipients = $this->filter('recipients', 'str');
			$inviter->setRecipients($recipients);
			if (!$inviter->validate($errors))
			{
				return $this->error($errors);
			}

			$inviter->save();

			return $this->redirect($this->buildLink('conversations', $conversation));
		}
		else
		{
			$viewParams = [
				'userConv' => $userConv,
				'conversation' => $conversation,
				'remainingRecipients' => $conversation->getRemainingRecipientsCount()
			];
			return $this->view('XF:Conversation\Invite', 'conversation_invite', $viewParams);
		}
	}

	public function actionLeave(ParameterBag $params)
	{
		$userConv = $this->assertViewableUserConversation($params->conversation_id);
		$conversation = $userConv->Master;

		if ($this->isPost())
		{
			$recipientState = $this->filter('recipient_state', 'str');

			// TODO: turn to service?
			switch ($recipientState)
			{
				case 'deleted':
				case 'deleted_ignored':
					break;

				default:
					$recipientState = 'deleted';
			}

			$recipient = $userConv->Recipient;
			if ($recipient)
			{
				$recipient->recipient_state = $recipientState;
				$recipient->save();
			}

			$this->plugin('XF:InlineMod')->clearIdFromCookie('conversation', $conversation->conversation_id);

			return $this->redirect($this->buildLink('conversations'));
		}
		else
		{
			$viewParams = [
				'conversation' => $conversation
			];
			return $this->view('XF:Conversation\Leave', 'conversation_leave', $viewParams);
		}
	}

	public function actionMessages(ParameterBag $params)
	{
		$message = $this->assertViewableMessage($params->message_id);
		$conversation = $message->Conversation;

		$conversationMessageRepo = $this->getConversationMessageRepo();

		$redirectParams = [];
		$earlierMessages = $conversationMessageRepo->findEarlierMessages($conversation, $message)->total();

		$page = floor($earlierMessages / $this->options()->messagesPerPage) + 1;
		if ($page > 1)
		{
			$redirectParams['page'] = $page;
		}

		return $this->redirectPermanently(
			$this->buildLink('conversations', $conversation, $redirectParams) . '#convMessage-' . $message->message_id
		);
	}

	public function actionMessagesReact(ParameterBag $params)
	{
		$message = $this->assertViewableMessage($params->message_id);

		/** @var \XF\ControllerPlugin\Reaction $reactionPlugin */
		$reactionPlugin = $this->plugin('XF:Reaction');
		return $reactionPlugin->actionReactSimple($message, 'conversations/messages');
	}

	public function actionMessagesReactions(ParameterBag $params)
	{
		$message = $this->assertViewableMessage($params->message_id);

		$breadcrumbs = [];
		$breadcrumbs[] = [
			'value' => $message->Conversation->title,
			'href' => $this->buildLink('conversations', $message->Conversation)
		];

		$title = \XF::phrase('members_who_reacted_to_message_by_x', ['user' => $message->User->username]);

		/** @var \XF\ControllerPlugin\Reaction $reactionPlugin */
		$reactionPlugin = $this->plugin('XF:Reaction');
		return $reactionPlugin->actionReactions(
			$message,
			'conversations/messages/reactions',
			$title, $breadcrumbs
		);
	}

	public function actionMessagesQuote(ParameterBag $params)
	{
		$message = $this->assertViewableMessage($params->message_id);
		if (!$message->Conversation->canReply())
		{
			return $this->noPermission();
		}

		return $this->plugin('XF:Quote')->actionQuote($message, 'conversation_message');
	}

	/**
	 * @param \XF\Entity\ConversationMessage $message
	 *
	 * @return \XF\Service\Conversation\MessageEditor
	 */
	protected function setupMessageEdit(\XF\Entity\ConversationMessage $conversationMessage)
	{
		$message = $this->plugin('XF:Editor')->fromInput('message');

		/** @var \XF\Service\Conversation\MessageEditor $editor */
		$editor = $this->service('XF:Conversation\MessageEditor', $conversationMessage);
		$editor->setMessageContent($message);

		$conversation = $conversationMessage->Conversation;

		if ($conversation->canUploadAndManageAttachments())
		{
			$editor->setAttachmentHash($this->filter('attachment_hash', 'str'));
		}

		return $editor;
	}

	public function actionMessagesEdit(ParameterBag $params)
	{
		$message = $this->assertViewableMessage($params->message_id);
		if (!$message->canEdit($error))
		{
			return $this->noPermission($error);
		}
		$conversation = $message->Conversation;

		if ($this->isPost())
		{
			$editor = $this->setupMessageEdit($message);
			if (!$editor->validate($errors))
			{
				return $this->error($errors);
			}
			$editor->save();

			if ($this->filter('_xfWithData', 'bool') && $this->filter('_xfInlineEdit', 'bool'))
			{
				/** @var \XF\Repository\Attachment $attachmentRepo */
				$attachmentRepo = $this->repository('XF:Attachment');
				$attachmentRepo->addAttachmentsToContent([
					$message->message_id => $message
				], 'conversation_message');

				$viewParams = [
					'conversation' => $conversation,
					'message' => $message
				];
				$reply = $this->view('XF:Conversation\Message\EditNewMessage', 'conversation_message_edit_new_message', $viewParams);
				$reply->setJsonParam('message', \XF::phrase('your_changes_have_been_saved'));
				return $reply;
			}
			else
			{
				return $this->redirect($this->buildLink('conversations/messages', $message));
			}
		}
		else
		{
			if ($conversation->canUploadAndManageAttachments())
			{
				/** @var \XF\Repository\Attachment $attachmentRepo */
				$attachmentRepo = $this->repository('XF:Attachment');
				$attachmentData = $attachmentRepo->getEditorData('conversation_message', $message);
			}
			else
			{
				$attachmentData = null;
			}

			$viewParams = [
				'conversation' => $conversation,
				'message' => $message,

				'attachmentData' => $attachmentData,
				'quickEdit' => $this->filter('_xfWithData', 'bool')
			];
			return $this->view('XF:Conversation\Message\Edit', 'conversation_message_edit', $viewParams);
		}
	}

	public function actionMessagesPreview(ParameterBag $params)
	{
		$this->assertPostOnly();

		$message = $this->assertViewableMessage($params->message_id);
		if (!$message->canEdit($error))
		{
			return $this->noPermission($error);
		}

		$editor = $this->setupMessageEdit($message);
		if (!$editor->validate($errors))
		{
			return $this->error($errors);
		}

		$conversation = $message->Conversation;

		$attachments = [];
		$tempHash = $this->filter('attachment_hash', 'str');

		if ($conversation->canUploadAndManageAttachments())
		{
			/** @var \XF\Repository\Attachment $attachmentRepo */
			$attachmentRepo = $this->repository('XF:Attachment');
			$attachmentData = $attachmentRepo->getEditorData('conversation_message', $message, $tempHash);
			$attachments = $attachmentData['attachments'];
		}

		return $this->plugin('XF:BbCodePreview')->actionPreview(
			$message->message, 'post', $message->User, $attachments, true
		);
	}

	public function actionMessagesIp(ParameterBag $params)
	{
		$message = $this->assertViewableMessage($params->message_id);

		/** @var \XF\ControllerPlugin\Ip $ipPlugin */
		$ipPlugin = $this->plugin('XF:Ip');
		return $ipPlugin->actionIp($message);
	}


	public function actionMessagesReport(ParameterBag $params)
	{
		$message = $this->assertViewableMessage($params->message_id);
		if (!$message->canReport($error))
		{
			return $this->noPermission($error);
		}

		/** @var \XF\ControllerPlugin\Report $reportPlugin */
		$reportPlugin = $this->plugin('XF:Report');
		return $reportPlugin->actionReport(
			'conversation_message', $message,
			$this->buildLink('conversations/messages/report', $message),
			$this->buildLink('conversations/messages', $message),
			[
				'extraViewParams' => [
					'conversation' => $message->Conversation
				]
			]
		);
	}

	/**
	 * @param $conversationId
	 * @param array $extraWith
	 *
	 * @return \XF\Entity\ConversationUser
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertViewableUserConversation($conversationId, array $extraWith = [])
	{
		$visitor = \XF::visitor();

		/** @var \XF\Finder\ConversationUser $finder */
		$finder = $this->finder('XF:ConversationUser');
		$finder->forUser($visitor, false);
		$finder->where('conversation_id', $conversationId);
		$finder->with($extraWith);

		/** @var \XF\Entity\ConversationUser $conversation */
		$conversation = $finder->fetchOne();
		if (!$conversation || !$conversation->Master)
		{
			throw $this->exception($this->notFound(\XF::phrase('requested_conversation_not_found')));
		}

		return $conversation;
	}

	/**
	 * @param $messageId
	 * @param array $extraWith
	 *
	 * @return \XF\Entity\ConversationMessage
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertViewableMessage($messageId, array $extraWith = [])
	{
		$extraWith[] = 'Conversation';

		$visitor = \XF::visitor();
		if ($visitor->user_id)
		{
			$extraWith[] = 'Conversation.Recipients|' . $visitor->user_id;
			$extraWith[] = 'Conversation.Users|' . $visitor->user_id;
		}

		array_unique($extraWith);

		/** @var \XF\Entity\ConversationMessage $message */
		$message = $this->em()->find('XF:ConversationMessage', $messageId, $extraWith);
		if (!$message)
		{
			throw $this->exception($this->notFound(\XF::phrase('requested_message_not_found')));
		}
		if (!$message->canView($error))
		{
			throw $this->exception($this->noPermission($error));
		}

		return $message;
	}

	protected function getReplyAttachmentData(\XF\Entity\ConversationMaster $conversation, $forceAttachmentHash = null)
	{
		if ($conversation->canUploadAndManageAttachments())
		{
			if ($forceAttachmentHash !== null)
			{
				$attachmentHash = $forceAttachmentHash;
			}
			else
			{
				$attachmentHash = $conversation->draft_reply->attachment_hash;
			}

			/** @var \XF\Repository\Attachment $attachmentRepo */
			$attachmentRepo = $this->repository('XF:Attachment');
			return $attachmentRepo->getEditorData('conversation_message', $conversation, $attachmentHash);
		}
		else
		{
			return null;
		}
	}

	protected function canUpdateSessionActivity($action, ParameterBag $params, AbstractReply &$reply, &$viewState)
	{
		if (strtolower($action) == 'addreply')
		{
			$viewState = 'valid';
			return true;
		}
		return parent::canUpdateSessionActivity($action, $params, $reply, $viewState);
	}

	/**
	 * @return \XF\Repository\Conversation
	 */
	protected function getConversationRepo()
	{
		return $this->repository('XF:Conversation');
	}

	/**
	 * @return \XF\Repository\ConversationMessage
	 */
	protected function getConversationMessageRepo()
	{
		return $this->repository('XF:ConversationMessage');
	}

	public function assertPolicyAcceptance($action)
	{
		switch (strtolower($action))
		{
			// mostly just so it doesn't error
			case 'popup':
				break;

			default:
				parent::assertPolicyAcceptance($action);
		}
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('engaged_in_conversation');
	}
}
