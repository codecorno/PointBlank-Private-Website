<?php

namespace XF\InlineMod;


use XF\Mvc\Entity\Entity;

class Post extends AbstractHandler
{
	public function getPossibleActions()
	{
		$actions = [];

		$actions['delete'] = $this->getActionHandler('XF:Post\Delete');

		$actions['undelete'] = $this->getSimpleActionHandler(
			\XF::phrase('undelete_posts'),
			'canUndelete',
			function(Entity $entity)
			{
				/** @var \XF\Entity\Post $entity */
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
				/** @var \XF\Entity\Post $entity */
				if ($entity->isFirstPost())
				{
					if ($entity->Thread->discussion_state == 'moderated')
					{
						$entity->Thread->discussion_state = 'visible';
						$entity->Thread->save();
					}
				}
				else if ($entity->message_state == 'moderated')
				{
					/** @var \XF\Service\Post\Approver $approver */
					$approver = \XF::service('XF:Post\Approver', $entity);
					$approver->setNotifyRunTime(1); // may be a lot happening
					$approver->approve();
				}
			}
		);

		$actions['unapprove'] = $this->getSimpleActionHandler(
			\XF::phrase('unapprove_posts'),
			'canApproveUnapprove',
			function(Entity $entity)
			{
				/** @var \XF\Entity\Post $entity */
				if ($entity->isFirstPost())
				{
					if ($entity->Thread->discussion_state == 'visible')
					{
						$entity->Thread->discussion_state = 'moderated';
						$entity->Thread->save();
					}
				}
				else if ($entity->message_state == 'visible')
				{
					$entity->message_state = 'moderated';
					$entity->save();
				}
			}
		);

		$actions['move'] = $this->getActionHandler('XF:Post\Move');
		$actions['copy'] = $this->getActionHandler('XF:Post\Copy');
		$actions['merge'] = $this->getActionHandler('XF:Post\Merge');

		return $actions;
	}

	public function getEntityWith()
	{
		$visitor = \XF::visitor();

		return ['Thread', 'Thread.Forum', 'Thread.Forum.Node.Permissions|' . $visitor->permission_combination_id];
	}
}