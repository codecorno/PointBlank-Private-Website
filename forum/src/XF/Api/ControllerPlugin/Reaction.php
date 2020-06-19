<?php

namespace XF\Api\ControllerPlugin;

use XF\Entity\ReactionTrait;
use XF\Mvc\Entity\Entity;
use XF\Mvc\FormAction;

class Reaction extends AbstractPlugin
{
	/**
	 * @api-in <req> int $reaction_id ID of the reaction to use. Use the current reaction ID to undo.
	 *
	 * @api-out true $success
	 * @api-out str $action "insert" or "delete" based on whether the reaction was added or removed.
	 *
	 * @param Entity|ReactionTrait $content
	 *
	 * @return \XF\Api\Mvc\Reply\ApiResult
	 * @throws \XF\Mvc\Reply\Exception
	 */
	public function actionReact(Entity $content)
	{
		$this->assertRequiredApiInput('reaction_id');

		if (\XF::isApiCheckingPermissions() && !$content->canReact($error))
		{
			throw $this->exception($this->noPermission($error));
		}

		$reactionId = $this->filter('reaction_id', 'uint');
		if (!$reactionId)
		{
			throw $this->exception($this->notFound());
		}

		$visitor = \XF::visitor();

		$contentType = $content->getEntityContentType();
		$contentId = $content->getEntityId();

		if (!$contentType)
		{
			throw new \InvalidArgumentException("Provided entity must define a content type in its structure");
		}

		/** @var \XF\Entity\Reaction $reaction */
		$reaction = $this->em()->find('XF:Reaction', $reactionId);
		if (!$reaction)
		{
			throw $this->exception($this->notFound());
		}

		$reactRepo = $this->getReactionRepo();
		$reaction = $reactRepo->reactToContent($reaction->reaction_id, $contentType, $contentId, $visitor, true);

		return $this->apiSuccess([
			'action' => $reaction ? 'insert' : 'delete'
		]);
	}

	/**
	 * @return \XF\Repository\Reaction
	 */
	protected function getReactionRepo()
	{
		return $this->repository('XF:Reaction');
	}
}