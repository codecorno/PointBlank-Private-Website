<?php

namespace XF\Reaction;

use XF\Mvc\Entity\Entity;

class Post extends AbstractHandler
{
	public function reactionsCounted(Entity $entity)
	{
		if (!$entity->Thread || !$entity->Thread->Forum)
		{
			return false;
		}

		return ($entity->message_state == 'visible' && $entity->Thread->discussion_state == 'visible');
	}

	public function getEntityWith()
	{
		$visitor = \XF::visitor();

		return ['Thread', 'Thread.Forum', 'Thread.Forum.Node.Permissions|' . $visitor->permission_combination_id];
	}
}