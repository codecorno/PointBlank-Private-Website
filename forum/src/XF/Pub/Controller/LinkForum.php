<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\RouteMatch;

class LinkForum extends AbstractController
{
	public function actionIndex(ParameterBag $params)
	{
		if (!$params->node_id)
		{
			return $this->redirectPermanently($this->buildLink('forums'));
		}

		$linkForum = $this->assertViewableLinkForum($params->node_id);

		return $this->redirectPermanently($linkForum->link_url);
	}

	/**
	 * @param int $nodeId
	 * @param array $extraWith
	 *
	 * @return \XF\Entity\LinkForum
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertViewableLinkForum($nodeId, array $extraWith = [])
	{
		$visitor = \XF::visitor();
		$extraWith[] = 'Node.Permissions|' . $visitor->permission_combination_id;

		/** @var \XF\Entity\LinkForum $forum */
		$forum = $this->em()->find('XF:LinkForum', $nodeId, $extraWith);
		if (!$forum)
		{
			throw $this->exception($this->notFound(\XF::phrase('requested_forum_not_found')));
		}
		if (!$forum->canView($error))
		{
			throw $this->exception($this->noPermission($error));
		}

		return $forum;
	}

	/**
	 * @return \XF\Repository\Node
	 */
	protected function getNodeRepo()
	{
		return $this->repository('XF:Node');
	}
}