<?php

namespace XF\InlineMod;


use XF\Mvc\Entity\Entity;

class Thread extends AbstractHandler
{
	public function getPossibleActions()
	{
		$actions = [];

		$actions['delete'] = $this->getActionHandler('XF:Thread\Delete');

		$actions['undelete'] = $this->getSimpleActionHandler(
			\XF::phrase('undelete_threads'),
			'canUndelete',
			function(Entity $entity)
			{
				/** @var \XF\Entity\Thread $entity */
				if ($entity->discussion_state == 'deleted')
				{
					$entity->discussion_state = 'visible';
					$entity->save();
				}
			}
		);

		$actions['approve'] = $this->getSimpleActionHandler(
			\XF::phrase('approve_threads'),
			'canApproveUnapprove',
			function(Entity $entity)
			{
				/** @var \XF\Entity\Thread $entity */
				if ($entity->discussion_type != 'redirect' && $entity->discussion_state == 'moderated')
				{
					/** @var \XF\Service\Thread\Approver $approver */
					$approver = \XF::service('XF:Thread\Approver', $entity);
					$approver->setNotifyRunTime(1); // may be a lot happening
					$approver->approve();
				}
			}
		);

		$actions['unapprove'] = $this->getSimpleActionHandler(
			\XF::phrase('unapprove_threads'),
			'canApproveUnapprove',
			function(Entity $entity)
			{
				/** @var \XF\Entity\Thread $entity */
				if ($entity->discussion_type != 'redirect' && $entity->discussion_state == 'visible')
				{
					$entity->discussion_state = 'moderated';
					$entity->save();
				}
			}
		);

		$actions['stick'] = $this->getSimpleActionHandler(
			\XF::phrase('stick_threads'),
			'canStickUnstick',
			function(Entity $entity)
			{
				/** @var \XF\Entity\Thread $entity */
				$entity->sticky = true;
				$entity->save();
			}
		);

		$actions['unstick'] = $this->getSimpleActionHandler(
			\XF::phrase('unstick_threads'),
			'canStickUnstick',
			function(Entity $entity)
			{
				/** @var \XF\Entity\Thread $entity */
				$entity->sticky = false;
				$entity->save();
			}
		);

		$actions['lock'] = $this->getSimpleActionHandler(
			\XF::phrase('lock_threads'),
			'canLockUnlock',
			function(Entity $entity)
			{
				if ($entity->discussion_type != 'redirect')
				{
					/** @var \XF\Entity\Thread $entity */
					$entity->discussion_open = false;
					$entity->save();
				}
			}
		);

		$actions['unlock'] = $this->getSimpleActionHandler(
			\XF::phrase('unlock_threads'),
			'canLockUnlock',
			function(Entity $entity)
			{
				if ($entity->discussion_type != 'redirect')
				{
					/** @var \XF\Entity\Thread $entity */
					$entity->discussion_open = true;
					$entity->save();
				}
			}
		);

		$actions['move'] = $this->getActionHandler('XF:Thread\Move');
		$actions['merge'] = $this->getActionHandler('XF:Thread\Merge');
		$actions['apply_prefix'] = $this->getActionHandler('XF:Thread\ApplyPrefix');

		return $actions;
	}

	public function getEntityWith()
	{
		$visitor = \XF::visitor();

		return ['Forum', 'Forum.Node.Permissions|' . $visitor->permission_combination_id];
	}
}