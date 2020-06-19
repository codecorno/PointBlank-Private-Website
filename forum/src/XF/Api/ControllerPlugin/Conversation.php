<?php

namespace XF\Api\ControllerPlugin;

use XF\Entity\ReactionTrait;
use XF\Mvc\Entity\Entity;
use XF\Mvc\FormAction;

class Conversation extends AbstractPlugin
{
	/**
	 * @param int $id
	 * @param string|array $with
	 *
	 * @return \XF\Entity\ConversationUser
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	public function assertViewableUserConversation($id, $with = 'api')
	{
		$visitor = \XF::visitor();

		/** @var \XF\Finder\ConversationUser $finder */
		$finder = $this->finder('XF:ConversationUser');
		$finder->forUser($visitor, false);
		$finder->where('conversation_id', $id);
		$finder->with($with);

		/** @var \XF\Entity\ConversationUser $conversation */
		$conversation = $finder->fetchOne();
		if (!$conversation || !$conversation->Master)
		{
			throw $this->exception($this->notFound(\XF::phrase('requested_conversation_not_found')));
		}

		return $conversation;
	}
}