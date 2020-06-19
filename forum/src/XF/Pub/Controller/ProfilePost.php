<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\RouteMatch;

class ProfilePost extends AbstractController
{
	public function actionIndex(ParameterBag $params)
	{
		$profilePost = $this->assertViewableProfilePost($params->profile_post_id);

		if ($this->filter('_xfWithData', 'bool'))
		{
			$this->request->set('_xfDisableInlineMod', true);
			return $this->rerouteController(__CLASS__, 'show', $params);
		}

		$profilePostRepo = $this->getProfilePostRepo();

		$profilePostFinder = $profilePostRepo->findProfilePostsOnProfile($profilePost->ProfileUser);
		$profilePostsTotal = $profilePostFinder->where('post_date', '>', $profilePost->post_date)->total();

		$page = floor($profilePostsTotal / $this->options()->messagesPerPage) + 1;

		return $this->redirectPermanently(
			$this->buildLink('members', $profilePost->ProfileUser, ['page' => $page]) . '#profile-post-' . $profilePost->profile_post_id
		);
	}

	public function actionShow(ParameterBag $params)
	{
		$profilePost = $this->assertViewableProfilePost($params->profile_post_id);

		$profilePostRepo = $this->getProfilePostRepo();
		$profilePost = $profilePostRepo->addCommentsToProfilePost($profilePost);

		$viewParams = [
			'profilePost' => $profilePost,
			'showTargetUser' => true,
			'canInlineMod' => $profilePost->canUseInlineModeration(),
			'allowInlineMod' => !$this->request->get('_xfDisableInlineMod')
		];
		return $this->view('XF:ProfilePost\Show', 'profile_post', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$profilePost = $this->assertViewableProfilePost($params->profile_post_id);
		if (!$profilePost->canEdit($error))
		{
			return $this->noPermission($error);
		}

		$noInlineMod = $this->filter('_xfNoInlineMod', 'bool');

		if ($this->isPost())
		{
			$editor = $this->setupEdit($profilePost);
			$editor->checkForSpam();

			if (!$editor->validate($errors))
			{
				return $this->error($errors);
			}
			$editor->save();

			$this->finalizeEdit($editor);

			if ($this->filter('_xfWithData', 'bool') && $this->filter('_xfInlineEdit', 'bool'))
			{
				$viewParams = [
					'profilePost' => $profilePost,

					'noInlineMod' => $noInlineMod
				];
				$reply = $this->view('XF:ProfilePost\EditNewProfilePost', 'profile_post_edit_new_post', $viewParams);
				$reply->setJsonParam('message', \XF::phrase('your_changes_have_been_saved'));
				return $reply;
			}
			else
			{
				return $this->redirect($this->buildLink('profile-posts', $profilePost));
			}
		}
		else
		{
			$viewParams = [
				'profilePost' => $profilePost,
				'profileUser' => $profilePost->ProfileUser,

				'quickEdit' => $this->filter('_xfWithData', 'bool'),
				'noInlineMod' => $noInlineMod
			];
			return $this->view('XF:ProfilePost\Edit', 'profile_post_edit', $viewParams);
		}
	}

	public function actionDelete(ParameterBag $params)
	{
		$profilePost = $this->assertViewableProfilePost($params->profile_post_id);
		if (!$profilePost->canDelete('soft', $error))
		{
			return $this->noPermission($error);
		}

		if ($this->isPost())
		{
			$type = $this->filter('hard_delete', 'bool') ? 'hard' : 'soft';
			$reason = $this->filter('reason', 'str');

			if (!$profilePost->canDelete($type, $error))
			{
				return $this->noPermission($error);
			}

			/** @var \XF\Service\ProfilePost\Deleter $deleter */
			$deleter = $this->service('XF:ProfilePost\Deleter', $profilePost);

			if ($this->filter('author_alert', 'bool') && $profilePost->canSendModeratorActionAlert())
			{
				$deleter->setSendAlert(true, $this->filter('author_alert_reason', 'str'));
			}

			$deleter->delete($type, $reason);

			$this->plugin('XF:InlineMod')->clearIdFromCookie('profile_post', $profilePost->profile_post_id);

			return $this->redirect(
				$this->getDynamicRedirect($this->buildLink('members', $profilePost->ProfileUser), false)
			);
		}
		else
		{
			$viewParams = [
				'profilePost' => $profilePost,
				'profileUser' => $profilePost->ProfileUser
			];
			return $this->view('XF:ProfilePost\Delete', 'profile_post_delete', $viewParams);
		}
	}

	public function actionUndelete(ParameterBag $params)
	{
		$profilePost = $this->assertViewableProfilePost($params->profile_post_id);

		/** @var \XF\ControllerPlugin\Undelete $plugin */
		$plugin = $this->plugin('XF:Undelete');
		return $plugin->actionUndelete(
			$profilePost,
			$this->buildLink('profile-posts/undelete', $profilePost),
			$this->buildLink('profile-posts', $profilePost),
			\XF::phrase('profile_post_by_x', ['name' => $profilePost->username]),
			'message_state'
		);
	}

	public function actionIp(ParameterBag $params)
	{
		$profilePost = $this->assertViewableProfilePost($params->profile_post_id);
		$breadcrumbs = $this->getProfilePostBreadcrumbs($profilePost);

		/** @var \XF\ControllerPlugin\Ip $ipPlugin */
		$ipPlugin = $this->plugin('XF:Ip');
		return $ipPlugin->actionIp($profilePost, $breadcrumbs);
	}

	public function actionReport(ParameterBag $params)
	{
		$profilePost = $this->assertViewableProfilePost($params->profile_post_id);
		if (!$profilePost->canReport($error))
		{
			return $this->noPermission($error);
		}

		/** @var \XF\ControllerPlugin\Report $reportPlugin */
		$reportPlugin = $this->plugin('XF:Report');
		return $reportPlugin->actionReport(
			'profile_post', $profilePost,
			$this->buildLink('profile-posts/report', $profilePost),
			$this->buildLink('profile-posts', $profilePost)
		);
	}

	public function actionReact(ParameterBag $params)
	{
		$profilePost = $this->assertViewableProfilePost($params->profile_post_id);

		/** @var \XF\ControllerPlugin\Reaction $reactionPlugin */
		$reactionPlugin = $this->plugin('XF:Reaction');
		return $reactionPlugin->actionReactSimple($profilePost, 'profile-posts');
	}

	public function actionReactions(ParameterBag $params)
	{
		$profilePost = $this->assertViewableProfilePost($params->profile_post_id);

		$breadcrumbs = $this->getProfilePostBreadcrumbs($profilePost);

		/** @var \XF\ControllerPlugin\Reaction $reactionPlugin */
		$reactionPlugin = $this->plugin('XF:Reaction');
		return $reactionPlugin->actionReactions(
			$profilePost,
			'profile-posts/reactions',
			null, $breadcrumbs
		);
	}

	public function actionWarn(ParameterBag $params)
	{
		$profilePost = $this->assertViewableProfilePost($params->profile_post_id);

		if (!$profilePost->canWarn($error))
		{
			return $this->noPermission($error);
		}

		$breadcrumbs = $this->getProfilePostBreadcrumbs($profilePost);

		/** @var \XF\ControllerPlugin\Warn $warnPlugin */
		$warnPlugin = $this->plugin('XF:Warn');
		return $warnPlugin->actionWarn(
			'profile_post', $profilePost,
			$this->buildLink('profile-posts/warn', $profilePost),
			$breadcrumbs
		);
	}

	/**
	 * @param \XF\Entity\ProfilePost $profilePost
	 *
	 * @return \XF\Service\ProfilePostComment\Creator
	 */
	protected function setupProfilePostComment(\XF\Entity\ProfilePost $profilePost)
	{
		$message = $this->plugin('XF:Editor')->fromInput('message');

		/** @var \XF\Service\ProfilePostComment\Creator $creator */
		$creator = $this->service('XF:ProfilePostComment\Creator', $profilePost);
		$creator->setContent($message);

		return $creator;
	}

	protected function finalizeProfilePostComment(\XF\Service\ProfilePostComment\Creator $creator)
	{
		$creator->sendNotifications();
	}

	public function actionAddComment(ParameterBag $params)
	{
		$this->assertPostOnly();

		$profilePost = $this->assertViewableProfilePost($params->profile_post_id);
		if (!$profilePost->canComment($error))
		{
			return $this->noPermission($error);
		}

		$creator = $this->setupProfilePostComment($profilePost);
		$creator->checkForSpam();

		if (!$creator->validate($errors))
		{
			return $this->error($errors);
		}
		$this->assertNotFlooding('post');
		$comment = $creator->save();

		$this->finalizeProfilePostComment($creator);

		if ($this->filter('_xfWithData', 'bool') && $this->request->exists('last_date') && $profilePost->canView())
		{
			$profilePostRepo = $this->getProfilePostRepo();

			$lastDate = $this->filter('last_date', 'uint');

			/** @var \XF\Mvc\Entity\Finder $profilePostCommentList */
			$profilePostCommentList = $profilePostRepo->findNewestCommentsForProfilePost($profilePost, $lastDate);
			$profilePostComments = $profilePostCommentList->fetch();

			// put the posts into oldest-first order
			$profilePostComments = $profilePostComments->reverse(true);

			$viewParams = [
				'profilePost' => $profilePost,
				'profilePostComments' => $profilePostComments
			];
			$view = $this->view('XF:Member\NewProfilePostComments', 'profile_post_new_profile_post_comments', $viewParams);
			$view->setJsonParam('lastDate', $profilePostComments->last()->comment_date);
			return $view;
		}
		else
		{
			return $this->redirect($this->buildLink('profile-posts/comments', $comment));
		}
	}

	public function actionLoadPrevious(ParameterBag $params)
	{
		$profilePost = $this->assertViewableProfilePost($params->profile_post_id);

		$repo = $this->getProfilePostRepo();

		$comments = $repo->findProfilePostComments($profilePost)
			->with('full')
			->where('comment_date', '<', $this->filter('before', 'uint'))
			->order('comment_date', 'DESC')
			->limit(20)
			->fetch()
			->reverse();

		if ($comments->count())
		{
			$firstCommentDate = $comments->first()->comment_date;

			$moreCommentsFinder = $repo->findProfilePostComments($profilePost)
				->where('comment_date', '<', $firstCommentDate);

			$loadMore = ($moreCommentsFinder->total() > 0);
		}
		else
		{
			$firstCommentDate = 0;
			$loadMore = false;
		}

		$viewParams = [
			'profilePost' => $profilePost,
			'comments' => $comments,
			'firstCommentDate' => $firstCommentDate,
			'loadMore' => $loadMore
		];
		return $this->view('XF:ProfilePost\LoadPrevious', 'profile_post_comments', $viewParams);
	}

	public function actionComments(ParameterBag $params)
	{
		$comment = $this->assertViewableComment($params->profile_post_comment_id);
		$profilePost = $this->assertViewableProfilePost($comment->profile_post_id);

		$profilePostRepo = $this->getProfilePostRepo();

		$profilePostFinder = $profilePostRepo->findProfilePostsOnProfile($profilePost->ProfileUser);
		$profilePostsTotal = $profilePostFinder->where('post_date', '>', $profilePost->post_date)->total();

		$page = floor($profilePostsTotal / $this->options()->messagesPerPage) + 1;

		$commentId = $comment->profile_post_comment_id;
		$anchor = '#profile-post-comment-' . $commentId;
		if (!isset($profilePost->latest_comment_ids[$commentId]))
		{
			$anchor = '#profile-post-' . $profilePost->profile_post_id;
		}

		return $this->redirectPermanently(
			$this->buildLink('members', $profilePost->ProfileUser, ['page' => $page]) . $anchor
		);
	}

	public function actionCommentsShow(ParameterBag $params)
	{
		$comment = $this->assertViewableComment($params->profile_post_comment_id);

		$viewParams = [
			'comment' => $comment,
			'profilePost' => $comment->ProfilePost,
		];
		return $this->view('XF:ProfilePost\Comments\Show', 'profile_post_comment', $viewParams);
	}

	public function actionCommentsEdit(ParameterBag $params)
	{
		$comment = $this->assertViewableComment($params->profile_post_comment_id);
		if (!$comment->canEdit($error))
		{
			return $this->noPermission($error);
		}

		if ($this->isPost())
		{
			$editor = $this->setupCommentEdit($comment);
			$editor->checkForSpam();

			if (!$editor->validate($errors))
			{
				return $this->error($errors);
			}
			$editor->save();

			$this->finalizeCommentEdit($editor);

			if ($this->filter('_xfWithData', 'bool') && $this->filter('_xfInlineEdit', 'bool'))
			{
				$viewParams = [
					'profilePost' => $comment->ProfilePost,
					'comment' => $comment
				];
				$reply = $this->view('XF:ProfilePost\Comments\EditNewComment', 'profile_post_comment_edit_new_comment', $viewParams);
				$reply->setJsonParam('message', \XF::phrase('your_changes_have_been_saved'));
				return $reply;
			}
			else
			{
				return $this->redirect($this->buildLink('profile-posts/comments', $comment));
			}
		}
		else
		{
			$viewParams = [
				'comment' => $comment,
				'profilePost' => $comment->ProfilePost,
				'quickEdit' => $this->responseType() == 'json'
			];
			return $this->view('XF:ProfilePost\Comments\Edit', 'profile_post_comment_edit', $viewParams);
		}
	}

	public function actionCommentsDelete(ParameterBag $params)
	{
		$comment = $this->assertViewableComment($params->profile_post_comment_id);
		if (!$comment->canDelete('soft', $error))
		{
			return $this->noPermission($error);
		}

		if ($this->isPost())
		{
			$type = $this->filter('hard_delete', 'bool') ? 'hard' : 'soft';
			$reason = $this->filter('reason', 'str');

			if (!$comment->canDelete($type, $error))
			{
				return $this->noPermission($error);
			}

			/** @var \XF\Service\ProfilePostComment\Deleter $deleter */
			$deleter = $this->service('XF:ProfilePostComment\Deleter', $comment);

			if ($this->filter('author_alert', 'bool') && $comment->canSendModeratorActionAlert())
			{
				$deleter->setSendAlert(true, $this->filter('author_alert_reason', 'str'));
			}

			$deleter->delete($type, $reason);

			return $this->redirect(
				$this->getDynamicRedirect($this->buildLink('profile-posts', $comment), false)
			);
		}
		else
		{
			$viewParams = [
				'comment' => $comment,
				'profilePost' => $comment->ProfilePost
			];
			return $this->view('XF:ProfilePost\Comments\Delete', 'profile_post_comment_delete', $viewParams);
		}
	}

	public function actionCommentsUndelete(ParameterBag $params)
	{
		$comment = $this->assertViewableComment($params->profile_post_comment_id);

		/** @var \XF\ControllerPlugin\Undelete $plugin */
		$plugin = $this->plugin('XF:Undelete');
		return $plugin->actionUndelete(
			$comment,
			$this->buildLink('profile-posts/comments/undelete', $comment),
			$this->buildLink('profile-posts/comments', $comment),
			\XF::phrase('profile_post_comment_by_x', ['username' => $comment->username]),
			'message_state'
		);
	}

	public function actionCommentsApprove(ParameterBag $params)
	{
		$this->assertValidCsrfToken($this->filter('t', 'str'));

		$comment = $this->assertViewableComment($params->profile_post_comment_id);
		if (!$comment->canApproveUnapprove($error))
		{
			return $this->noPermission($error);
		}

		/** @var \XF\Service\ProfilePostComment\Approver $approver */
		$approver = \XF::service('XF:ProfilePostComment\Approver', $comment);
		$approver->approve();

		return $this->redirect($this->buildLink('profile-posts/comments', $comment));
	}

	public function actionCommentsUnapprove(ParameterBag $params)
	{
		$this->assertValidCsrfToken($this->filter('t', 'str'));

		$comment = $this->assertViewableComment($params->profile_post_comment_id);
		if (!$comment->canApproveUnapprove($error))
		{
			return $this->noPermission($error);
		}

		$comment->message_state = 'moderated';
		$comment->save();

		return $this->redirect($this->buildLink('profile-posts/comments', $comment));
	}

	public function actionCommentsWarn(ParameterBag $params)
	{
		$comment = $this->assertViewableComment($params->profile_post_comment_id);
		if (!$comment->canWarn($error))
		{
			return $this->noPermission($error);
		}

		$breadcrumbs = $this->getProfilePostBreadcrumbs($comment->ProfilePost);

		/** @var \XF\ControllerPlugin\Warn $warnPlugin */
		$warnPlugin = $this->plugin('XF:Warn');
		return $warnPlugin->actionWarn(
			'profile_post_comment', $comment,
			$this->buildLink('profile-posts/comments/warn', $comment),
			$breadcrumbs
		);
	}

	public function actionCommentsIp(ParameterBag $params)
	{
		$comment = $this->assertViewableComment($params->profile_post_comment_id);
		$breadcrumbs = $this->getProfilePostBreadcrumbs($comment->ProfilePost);

		/** @var \XF\ControllerPlugin\Ip $ipPlugin */
		$ipPlugin = $this->plugin('XF:Ip');
		return $ipPlugin->actionIp($comment, $breadcrumbs);
	}

	public function actionCommentsReport(ParameterBag $params)
	{
		$comment = $this->assertViewableComment($params->profile_post_comment_id);
		if (!$comment->canReport($error))
		{
			return $this->noPermission($error);
		}

		/** @var \XF\ControllerPlugin\Report $reportPlugin */
		$reportPlugin = $this->plugin('XF:Report');
		return $reportPlugin->actionReport(
			'profile_post_comment', $comment,
			$this->buildLink('profile-posts/comments/report', $comment),
			$this->buildLink('profile-posts/comments', $comment)
		);
	}

	public function actionCommentsReact(ParameterBag $params)
	{
		$comment = $this->assertViewableComment($params->profile_post_comment_id);

		/** @var \XF\ControllerPlugin\Reaction $reactionPlugin */
		$reactionPlugin = $this->plugin('XF:Reaction');
		return $reactionPlugin->actionReactSimple($comment, 'profile-posts/comments');
	}

	public function actionCommentsReactions(ParameterBag $params)
	{
		$comment = $this->assertViewableComment($params->profile_post_comment_id);

		$breadcrumbs = [
			'href' => $this->buildLink('members', $comment->ProfilePost->ProfileUser),
			'value' => $comment->ProfilePost->ProfileUser->username
		];

		/** @var \XF\ControllerPlugin\Reaction $reactionPlugin */
		$reactionPlugin = $this->plugin('XF:Reaction');
		return $reactionPlugin->actionReactions(
			$comment,
			'profile-posts/comments/reactions',
			null, $breadcrumbs
		);
	}

	/**
	 * @param \XF\Entity\ProfilePost $profilePost
	 *
	 * @return \XF\Service\ProfilePost\Editor
	 */
	protected function setupEdit(\XF\Mvc\Entity\Entity $profilePost)
	{
		$message = $this->plugin('XF:Editor')->fromInput('message');

		/** @var \XF\Service\ProfilePost\Editor $editor */
		$editor = $this->service('XF:ProfilePost\Editor', $profilePost);
		$editor->setMessage($message);

		if ($this->filter('author_alert', 'bool') && $profilePost->canSendModeratorActionAlert())
		{
			$editor->setSendAlert(true, $this->filter('author_alert_reason', 'str'));
		}

		return $editor;
	}

	protected function finalizeEdit(\XF\Service\ProfilePost\Editor $editor)
	{
	}

	/**
	 * @param \XF\Entity\ProfilePostComment $comment
	 *
	 * @return \XF\Service\ProfilePostComment\Editor
	 */
	protected function setupCommentEdit(\XF\Entity\ProfilePostComment $comment)
	{
		$message = $this->plugin('XF:Editor')->fromInput('message');

		/** @var \XF\Service\ProfilePostComment\Editor $editor */
		$editor = $this->service('XF:ProfilePostComment\Editor', $comment);
		$editor->setMessage($message);

		if ($this->filter('author_alert', 'bool') && $comment->canSendModeratorActionAlert())
		{
			$editor->setSendAlert(true, $this->filter('author_alert_reason', 'str'));
		}

		return $editor;
	}

	protected function finalizeCommentEdit(\XF\Service\ProfilePostComment\Editor $editor)
	{
	}

	protected function getProfilePostBreadcrumbs(\XF\Entity\ProfilePost $profilePost)
	{
		$breadcrumbs = [
			[
				'href' => $this->buildLink('members', $profilePost->ProfileUser),
				'value' => $profilePost->ProfileUser->username
			]
		];

		return $breadcrumbs;
	}

	/**
	 * @param $profilePostId
	 * @param array $extraWith
	 *
	 * @return \XF\Entity\ProfilePost
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertViewableProfilePost($profilePostId, array $extraWith = [])
	{
		$extraWith[] = 'User';
		$extraWith[] = 'ProfileUser';
		$extraWith[] = 'ProfileUser.Privacy';
		array_unique($extraWith);

		/** @var \XF\Entity\ProfilePost $profilePost */
		$profilePost = $this->em()->find('XF:ProfilePost', $profilePostId, $extraWith);
		if (!$profilePost)
		{
			throw $this->exception($this->notFound(\XF::phrase('requested_profile_post_not_found')));
		}
		if (!$profilePost->canView($error))
		{
			throw $this->exception($this->noPermission($error));
		}

		return $profilePost;
	}

	/**
	 * @param $commentId
	 * @param array $extraWith
	 *
	 * @return \XF\Entity\ProfilePostComment
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertViewableComment($commentId, array $extraWith = [])
	{
		$extraWith[] = 'User';
		$extraWith[] = 'ProfilePost.ProfileUser';
		$extraWith[] = 'ProfilePost.ProfileUser.Privacy';
		array_unique($extraWith);

		/** @var \XF\Entity\ProfilePostComment $comment */
		$comment = $this->em()->find('XF:ProfilePostComment', $commentId, $extraWith);
		if (!$comment)
		{
			throw $this->exception($this->notFound(\XF::phrase('requested_comment_not_found')));
		}
		if (!$comment->canView($error))
		{
			throw $this->exception($this->noPermission($error));
		}

		return $comment;
	}

	/**
	 * @return \XF\Repository\ProfilePost
	 */
	protected function getProfilePostRepo()
	{
		return $this->repository('XF:ProfilePost');
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('viewing_members');
	}
}
