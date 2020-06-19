<?php

namespace XF\InlineMod;

use XF\Mvc\Entity\Entity;

class Conversation extends AbstractHandler
{
	public function getPossibleActions()
	{
		$actions = [];

		$actions['leave'] = $this->getActionHandler('XF:Conversation\Leave');

		$actions['mark_read'] = $this->getSimpleActionHandler(
			\XF::phrase('mark_conversations_read'),
			true,
			function(Entity $entity)
			{
				/** @var \XF\Entity\ConversationMaster $entity */
				$userConv = $entity->Users[\XF::visitor()->user_id];
				if ($userConv)
				{
					$userConv->is_unread = false;
					$userConv->save();

					if ($userConv->Recipient)
					{
						$userConv->Recipient->last_read_date = \XF::$time;
						$userConv->Recipient->save();
					}
				}
			}
		);

		$actions['mark_unread'] = $this->getSimpleActionHandler(
			\XF::phrase('mark_conversations_unread'),
			true,
			function(Entity $entity)
			{
				/** @var \XF\Entity\ConversationMaster $entity */
				$userConv = $entity->Users[\XF::visitor()->user_id];
				if ($userConv)
				{
					$userConv->is_unread = true;
					$userConv->save();

					if ($userConv->Recipient)
					{
						$userConv->Recipient->last_read_date = 0;
						$userConv->Recipient->save();
					}
				}
			}
		);

		$actions['star'] = $this->getSimpleActionHandler(
			\XF::phrase('star_conversations'),
			true,
			function(Entity $entity)
			{
				/** @var \XF\Entity\ConversationMaster $entity */
				$userConv = $entity->Users[\XF::visitor()->user_id];
				if ($userConv)
				{
					$userConv->is_starred = true;
					$userConv->save();
				}
			}
		);

		$actions['unstar'] = $this->getSimpleActionHandler(
			\XF::phrase('unstar_conversations'),
			true,
			function(Entity $entity)
			{
				/** @var \XF\Entity\ConversationMaster $entity */
				$userConv = $entity->Users[\XF::visitor()->user_id];
				if ($userConv)
				{
					$userConv->is_starred = false;
					$userConv->save();
				}
			}
		);

		return $actions;
	}

	public function getEntityWith()
	{
		$visitor = \XF::visitor();

		return ['Users|' . $visitor->user_id, 'Recipients|' . $visitor->user_id];
	}
}