<?php

namespace XF\Repository;

use XF\Entity\ConversationMaster;
use XF\Mvc\Entity\Repository;

class ConversationMessage extends Repository
{
	public function findMessagesForConversationView(ConversationMaster $conversation)
	{
		/** @var \XF\Finder\ConversationMessage $finder */
		$finder = $this->finder('XF:ConversationMessage');
		$finder
			->inConversation($conversation)
			->order('message_date')
			->with('full');

		return $finder;
	}

	public function findNewestMessagesInConversation($conversation, $lastDate)
	{
		/** @var \XF\Finder\ConversationMessage $finder */
		$finder = $this->finder('XF:ConversationMessage');
		$finder
			->inConversation($conversation)
			->order('message_date', 'DESC')
			->where('message_date', '>', $lastDate)
			->with('full');

		return $finder;
	}

	public function findNextMessageInConversation(ConversationMaster $conversation, $newerThan)
	{
		/** @var \XF\Finder\ConversationMessage $finder */
		$finder = $this->finder('XF:ConversationMessage');
		$finder
			->inConversation($conversation)
			->order('message_date')
			->where('message_date', '>', $newerThan)
			->limit(1);

		return $finder;
	}

	public function getFirstUnreadMessageInConversation(\XF\Entity\ConversationUser $userConv, array $with = [])
	{
		if (!$userConv->isUnread())
		{
			return null;
		}

		$lastReadDate = $userConv->Recipient->last_read_date;
		$conversation = $userConv->Master;

		return $this->findNextMessageInConversation($conversation, $lastReadDate)->with($with)->fetchOne();
	}

	/**
	 * @param ConversationMaster $conversation
	 *
	 * @return \XF\Mvc\Entity\Finder
	 */
	public function findLatestMessage(ConversationMaster $conversation)
	{
		/** @var \XF\Finder\ConversationMessage $finder */
		$finder = $this->finder('XF:ConversationMessage');
		$finder
			->inConversation($conversation)
			->order('message_date', 'DESC')
			->limit(1);

		return $finder;
	}

	/**
	 * @param ConversationMaster $conversation
	 *
	 * @param \XF\Entity\ConversationMessage $message
	 *
	 * @return \XF\Mvc\Entity\Finder
	 */
	public function findEarlierMessages(ConversationMaster $conversation, \XF\Entity\ConversationMessage $message)
	{
		/** @var \XF\Finder\ConversationMessage $finder */
		$finder = $this->finder('XF:ConversationMessage');
		$finder->inConversation($conversation)
			->earlierThan($message);

		return $finder;
	}
}