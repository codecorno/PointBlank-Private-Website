<?php

namespace XF\InlineMod;


use XF\Mvc\Entity\Entity;

class ProfilePost extends AbstractHandler
{
	public function getPossibleActions()
	{
		$actions = [];

		$actions['delete'] = $this->getActionHandler('XF:ProfilePost\Delete');

		$actions['undelete'] = $this->getSimpleActionHandler(
			\XF::phrase('undelete_posts'),
			'canUndelete',
			function(Entity $entity)
			{
				/** @var \XF\Entity\ProfilePost $entity */
				if ($entity->message_state == 'deleted')
				{
					$entity->message_state = 'visible';
					$entity->save();
				}
			}
		);

		$actions['approve'] = $this->getSimpleActionHandler(
			\XF::phrase('approve_posts'),
			'canApproveUnapprove',
			function(Entity $entity)
			{
				/** @var \XF\Entity\ProfilePost $entity */
				if ($entity->message_state == 'moderated')
				{
					/** @var \XF\Service\ProfilePost\Approver $approver */
					$approver = \XF::service('XF:ProfilePost\Approver', $entity);
					$approver->approve();
				}
			}
		);

		$actions['unapprove'] = $this->getSimpleActionHandler(
			\XF::phrase('unapprove_posts'),
			'canApproveUnapprove',
			function(Entity $entity)
			{
				/** @var \XF\Entity\ProfilePost $entity */
				if ($entity->message_state == 'visible')
				{
					$entity->message_state = 'moderated';
					$entity->save();
				}
			}
		);

		return $actions;
	}

	public function getEntityWith()
	{
		return ['ProfileUser', 'ProfileUser.Privacy'];
	}
}