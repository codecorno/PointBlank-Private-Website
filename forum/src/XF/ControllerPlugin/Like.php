<?php

namespace XF\ControllerPlugin;

use XF\Mvc\Entity\Entity;

class Like extends AbstractPlugin
{
	public function actionToggleLike(Entity $entity, $confirmUrl, $returnUrl, $likesUrl, $contentTitle = null)
	{
		$visitor = \XF::visitor();

		$contentType = $entity->getEntityContentType();
		$contentId = $entity->getEntityId();

		if (!$contentType)
		{
			throw new \InvalidArgumentException("Provided entity {$entity->structure()->shortName} must define a content type in its structure");
		}

		/** @var \XF\Repository\LikedContent $likeRepo */
		$likeRepo = $this->repository('XF:LikedContent');

		$likeHandler = $likeRepo->getLikeHandler($contentType, true);

		if ($this->isPost())
		{
			$isLiked = $likeRepo->toggleLike($contentType, $contentId, $visitor);
			$cache = $likeHandler->getContentLikeCaches($entity);

			if ($this->filter('_xfWithData', 'bool'))
			{
				$viewParams = [
					'isLiked' => $isLiked,
					'count' => isset($cache['count']) ? $cache['count'] : null,
					'likes' => isset($cache['recent']) ? $cache['recent'] : null,
					'listUrl' => $likesUrl
				];
				return $this->view('XF:Like\Like', '', $viewParams);
			}
			else
			{
				return $this->redirect($returnUrl);
			}
		}
		else
		{
			$isLiked = (bool)$likeRepo->getLikeByContentAndLiker($contentType, $contentId, $visitor->user_id);

			$viewParams = [
				'confirmUrl' => $confirmUrl,
				'contentTitle' => $contentTitle,
				'isLiked' => $isLiked
			];
			return $this->view('XF:Like\Confirm', 'like_confirm', $viewParams);
		}
	}

	public function actionLikes(Entity $entity, array $likeLinkData, $title = null, array $breadcrumbs = [])
	{
		$contentType = $entity->getEntityContentType();
		$contentId = $entity->getEntityId();

		if (!$contentType)
		{
			throw new \InvalidArgumentException("Provided entity must defined a content type in its structure");
		}

		/** @var \XF\Repository\LikedContent $likeRepo */
		$likeRepo = $this->repository('XF:LikedContent');

		$page = $this->filterPage();
		$perPage = 50;

		$likes = $likeRepo->findContentLikes($contentType, $contentId)
			->with('Liker')
			->limitByPage($page, $perPage, 1)
			->fetch();

		if (!count($likes))
		{
			return $this->message(\XF::phrase('no_one_has_liked_this_content_yet'));
		}

		$hasNext = count($likes) > $perPage;
		$likes = $likes->slice(0, $perPage);

		$viewParams = [
			'type' => $contentType,
			'id' => $contentId,

			'linkRoute' => isset($likeLinkData[0]) ? $likeLinkData[0] : '',
			'linkData' => isset($likeLinkData[1]) ? $likeLinkData[1] : null,
			'linkParams' => (isset($likeLinkData[2]) && is_array($likeLinkData[2])) ? $likeLinkData[2] : [],

			'likes' => $likes,
			'hasNext' => $hasNext,
			'page' => $page,

			'title' => $title,
			'breadcrumbs' => $breadcrumbs
		];
		return $this->view('XF:Like\Listing', 'like_list', $viewParams);
	}
}