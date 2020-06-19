<?php

namespace XF\Reaction;

use XF\Entity\ReactionContent;
use XF\Mvc\Entity\Entity;

class ConversationMessage extends AbstractHandler
{
	public function reactionsCounted(Entity $entity)
	{
		return false;
	}

	public function publishReactionNewsFeed(\XF\Entity\User $sender, $contentId, Entity $content, $reactionId) {}

	public function unpublishReactionNewsFeed(ReactionContent $reactionContent) {}
}