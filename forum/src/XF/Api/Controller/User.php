<?php

namespace XF\Api\Controller;

use XF\Mvc\Entity\Entity;
use XF\Mvc\ParameterBag;

/**
 * @api-group Users
 */
class User extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertApiScopeByRequestMethod('user', ['delete' => 'delete']);
	}

	/**
	 * @api-desc Gets information about the specified user.
	 *
	 * @api-in bool $with_posts If specified, the response will include a page of profile posts.
	 * @api-in int $page The page of comments to include
	 *
	 * @api-out User $user
	 * @api-see self::getProfilePostsForUserPaginated()
	 */
	public function actionGet(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id);

		if ($this->filter('with_posts', 'bool'))
		{
			$this->assertApiScope('profile_post:read');

			if (\XF::isApiCheckingPermissions() && !$user->canViewPostsOnProfile($error))
			{
				return $this->noPermission($error);
			}

			$postData = $this->getProfilePostsForUserPaginated($user, $this->filterPage());
		}
		else
		{
			$postData = [];
		}

		$result = [
			'user' => $user->toApiResult(Entity::VERBOSITY_VERBOSE)
		];
		$result += $postData;

		return $this->apiResult($result);
	}

	/**
	 * @api-desc Gets a page of profile posts on the specified user's profile.
	 *
	 * @api-in int $page
	 *
	 * @api-see self::getProfilePostsForUserPaginated
	 */
	public function actionGetProfilePosts(ParameterBag $params)
	{
		$this->assertApiScope('profile_post:read');

		$user = $this->assertViewableUser($params->user_id, 'api', true);

		if (\XF::isApiCheckingPermissions() && !$user->canViewPostsOnProfile($error))
		{
			return $this->noPermission($error);
		}

		$postData = $this->getProfilePostsForUserPaginated($user, $this->filterPage());

		return $this->apiResult($postData);
	}

	/**
	 * @api-out ProfilePost[] $profile_posts List of profile posts on the requested page
	 * @api-out pagination $pagination Pagination details
	 *
	 * @param \XF\Entity\User $user
	 * @param int $page
	 * @param null|int $perPage
	 *
	 * @return array
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function getProfilePostsForUserPaginated(\XF\Entity\User $user, $page = 1, $perPage = null)
	{
		$perPage = intval($perPage);
		if ($perPage <= 0)
		{
			$perPage = $this->options()->messagesPerPage;
		}

		$finder = $this->setupProfilePostFinder($user);

		$total = $finder->total();

		$this->assertValidApiPage($page, $perPage, $total);

		$profilePosts = $finder->limitByPage($page, $perPage)->fetch();

		$profilePostRepo = $this->getProfilePostRepo();
		$profilePosts = $profilePostRepo->addCommentsToProfilePosts($profilePosts);

		$postResults = $profilePosts->toApiResults(Entity::VERBOSITY_NORMAL, [
			'with_latest' => true
		]);

		return [
			'profile_posts' => $postResults,
			'pagination' => $this->getPaginationData($postResults, $page, $perPage, $total)
		];
	}

	/**
	 * @param \XF\Entity\User $user
	 * @return \XF\Finder\ProfilePost
	 */
	protected function setupProfilePostFinder(\XF\Entity\User $user)
	{
		/** @var \XF\Finder\ProfilePost $finder */
		$finder = $this->finder('XF:ProfilePost');
		$finder->onProfile($user)
			->order('post_date', 'DESC')
			->with('api');

		return $finder;
	}

	/**
	 * @api-desc Updates an existing user.
	 *
	 * @api-see \XF\Api\ControllerPlugin\User::userSaveProcessAdmin()
	 *
	 * @api-out true $success
	 * @api-out User $user
	 */
	public function actionPost(ParameterBag $params)
	{
		$this->assertAdminPermission('user');

		$user = $this->assertViewableUser($params->user_id);

		// intentionally not bypassable
		if ($user->is_admin
			&& $user->Admin->is_super_admin
			&& !\XF::visitor()->Admin->is_super_admin
		)
		{
			throw $this->exception(
				$this->error(\XF::phrase('you_must_be_super_administrator_to_edit_user'))
			);
		}

		/** @var \XF\Api\ControllerPlugin\User $userPlugin */
		$userPlugin = $this->plugin('XF:Api:User');
		$userPlugin->userSaveProcessAdmin($user)->run();

		return $this->apiSuccess([
			'user' => $user->toApiResult(Entity::VERBOSITY_VERBOSE)
		]);
	}

	/**
	 * @api-desc Deletes the specified user
	 *
	 * @api-in str $rename_to If specified, the user will be renamed before deletion
	 *
	 * @api-out true $success
	 */
	public function actionDelete(ParameterBag $params)
	{
		$this->assertAdminPermission('user');

		$user = $this->assertViewableUser($params->user_id);

		if (!$this->canDeleteUser($user, $error))
		{
			return $this->noPermission($error);
		}

		/** @var \XF\Service\User\Delete $deleter */
		$deleter = $this->service('XF:User\Delete', $user);

		$renameTo = $this->filter('rename_to', 'str');
		if ($renameTo)
		{
			$deleter->renameTo($renameTo);
		}

		if (!$deleter->delete($errors))
		{
			return $this->error($errors);
		}

		return $this->apiSuccess();
	}

	/**
	 * @api-desc Updates the specified user's avatar
	 *
	 * @api-see XF\Api\ControllerPlugin\User::actionUpdateAvatar()
	 */
	public function actionPostAvatar(ParameterBag $params)
	{
		$this->assertAdminPermission('user');

		$user = $this->assertViewableUser($params->user_id);

		/** @var \XF\Api\ControllerPlugin\User $userPlugin */
		$userPlugin = $this->plugin('XF:Api:User');
		return $userPlugin->actionUpdateAvatar($user);
	}

	/**
	 * @api-desc Deletes the specified user's avatar
	 *
	 * @api-see XF\Api\ControllerPlugin\User::actionDeleteAvatar()
	 */
	public function actionDeleteAvatar(ParameterBag $params)
	{
		$this->assertAdminPermission('user');

		$user = $this->assertViewableUser($params->user_id);

		/** @var \XF\Api\ControllerPlugin\User $userPlugin */
		$userPlugin = $this->plugin('XF:Api:User');
		return $userPlugin->actionDeleteAvatar($user);
	}

	protected function canDeleteUser(\XF\Entity\User $user, &$error = null)
	{
		// this function should always be checked, regardless of bypass permission options
		if ($user->is_admin || $user->is_moderator)
		{
			return false;
		}

		if ($user->user_id == \XF::visitor()->user_id)
		{
			return false;
		}

		return true;
	}

	/**
	 * @param int $id
	 * @param mixed $with
	 * @param bool $basicProfileOnly
	 *
	 * @return \XF\Entity\User
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertViewableUser($id, $with = 'api', $basicProfileOnly = true)
	{
		/** @var \XF\Entity\User $user */
		$user = $this->assertRecordExists('XF:User', $id, $with);

		if (\XF::isApiCheckingPermissions())
		{
			$canView = $basicProfileOnly ? $user->canViewBasicProfile($error) : $user->canViewFullProfile($error);
			if (!$canView)
			{
				throw $this->exception($this->noPermission($error));
			}
		}

		return $user;
	}

	/**
	 * @return \XF\Repository\ProfilePost
	 */
	protected function getProfilePostRepo()
	{
		return $this->repository('XF:ProfilePost');
	}
}