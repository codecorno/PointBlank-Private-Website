<?php

namespace XF\Pub\Controller;

use XF\Entity\User;
use XF\Entity\UserProfile;
use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class Member extends AbstractController
{
	public function actionIndex(ParameterBag $params)
	{
		if ($params->user_id)
		{
			return $this->rerouteController(__CLASS__, 'view', $params);
		}

		if (!\XF::visitor()->canViewMemberList())
		{
			return $this->noPermission();
		}

		$username = $this->filter('username', 'str');
		if ($username)
		{
			$user = $this->em()->findOne('XF:User', ['username' => $username]);
			if ($user)
			{
				return $this->redirect($this->buildLink('members', $user));
			}
			else
			{
				$userNotFound = true;
			}
		}
		else
		{
			$userNotFound = false;
		}

		$this->assertCanonicalUrl($this->buildLink('members'));

		/** @var \XF\Repository\MemberStat $memberStatRepo */
		$memberStatRepo = $this->repository('XF:MemberStat');

		/** @var \XF\Entity\MemberStat[] $memberStats */
		$memberStats = $memberStatRepo
			->findMemberStatsForDisplay()
			->fetch()
			->filterViewable();

		$active = null;

		$key = $this->filter('key', 'str');
		if (isset($memberStats[$key]))
		{
			$active = $memberStats[$key];
		}

		$userFinder = $this->finder('XF:User');
		$resultsData = [];

		if ($active)
		{
			if (!$active->canView())
			{
				return $this->noPermission();
			}

			$results = $active->getResults();

			$users = $userFinder
				->with('Option', true)
				->with('Profile', true)
				->where('user_id', array_unique(array_keys($results)))
				->isValidUser()
				->fetch();

			foreach ($results AS $userId => $value)
			{
				if (!isset($users[$userId]))
				{
					// no valid user record found
					continue;
				}

				$resultsData[$active->member_stat_key][$userId] = [
					'user' => $users[$userId],
					'value' => $value
				];
			}

			if (isset($resultsData[$active->member_stat_key]))
			{
				if ($active->user_limit > 0)
				{
					$resultsData[$active->member_stat_key] = array_slice(
						$resultsData[$active->member_stat_key], 0, $active->user_limit, true
					);
				}
			}
			else
			{
				$resultsData[$active->member_stat_key] = [];
			}
		}
		else
		{
			$userIds = [];

			foreach ($memberStats AS $memberStat)
			{
				$results = $memberStat->getResults(true);
				$userIds = array_merge(array_keys($results), $userIds);
			}

			$users = $userFinder
				->with('Option', true)
				->with('Profile', true)
				->where('user_id', array_unique($userIds))
				->isValidUser()
				->fetch();

			foreach ($memberStats AS $key => $memberStat)
			{
				$results = $memberStat->getResults(true);

				$count = 0;

				foreach ($results AS $userId => $value)
				{
					if ($count == 5)
					{
						// we have enough for this stat
						break;
					}

					if (!isset($users[$userId]))
					{
						// no valid user record found
						continue;
					}

					$resultsData[$key][$userId] = [
						'user' => $users[$userId],
						'value' => $value
					];

					$count++;
				}
			}
		}

		$viewParams = [
			'userNotFound' => $userNotFound,
			'memberStats' => $memberStats,
			'resultsData' => $resultsData,
			'active' => $active,
			'users' => $users
		];
		return $this->view('XF:Member\Notable', 'member_notable', $viewParams);
	}

	public function actionList()
	{
		if (!$this->options()->enableMemberList || !\XF::visitor()->canViewMemberList())
		{
			return $this->noPermission();
		}

		$this->assertCanonicalUrl($this->buildLink('members/list'));

		/** @var \XF\Repository\MemberStat $memberStatRepo */
		$memberStatRepo = $this->repository('XF:MemberStat');

		/** @var \XF\Entity\MemberStat[] $memberStats */
		$memberStats = $memberStatRepo
			->findMemberStatsForDisplay()
			->fetch();

		$page = $this->filterPage();
		$perPage = $this->options()->membersPerPage;

		$searcher = $this->searcher('XF:User');

		$finder = $searcher->getFinder()
			->isValidUser()
			->with(['Profile', 'Option'])
			->limitByPage($page, $perPage);

		$total = $finder->total();
		$this->assertValidPage($page, $perPage, $total, 'members/list');

		$viewParams = [
			'users' => $finder->fetch(),
			'memberStats' => $memberStats,

			'total' => $total,
			'page' => $page,
			'perPage' => $perPage
		];
		return $this->view('XF:Member\Listing', 'member_list', $viewParams);
	}

	public function actionView(ParameterBag $params)
	{
		if ($this->filter('tooltip', 'bool'))
		{
			return $this->rerouteController(__CLASS__, 'tooltip', $params);
		}

		$user = $this->assertViewableUser($params->user_id);

		$page = $params->page;
		$perPage = $this->options()->messagesPerPage;

		$this->assertCanonicalUrl($this->buildLink('members', $user, ['page' => $page]));

		/** @var \XF\Repository\UserAlert $userAlertRepo */
		$userAlertRepo = $this->repository('XF:UserAlert');

		if ($user->canViewPostsOnProfile())
		{
			$profilePostRepo = $this->getProfilePostRepo();
			$profilePostFinder = $profilePostRepo->findProfilePostsOnProfile($user, [
				'allowOwnPending' => $this->hasContentPendingApproval()
			]);
			$profilePosts = $profilePostFinder->limitByPage($page, $perPage)->fetch();

			$total = $profilePostFinder->total();

			$isRobot = $this->isRobot();
			$profilePosts = $profilePostRepo->addCommentsToProfilePosts($profilePosts, $isRobot);

			/** @var \XF\Repository\Unfurl $unfurlRepo */
			$unfurlRepo = $this->repository('XF:Unfurl');
			$unfurlRepo->addUnfurlsToContent($profilePosts, $isRobot);

			$commentIds = [];
			foreach ($profilePosts AS $profilePost)
			{
				if ($profilePost->LatestComments)
				{
					$commentIds = array_merge($commentIds, $profilePost->LatestComments->keys());
				}
			}

			$userAlertRepo->markUserAlertsReadForContent('profile_post', $profilePosts->keys());
			$userAlertRepo->markUserAlertsReadForContent('profile_post_comment', $commentIds);
		}
		else
		{
			$total = 0;
			$profilePosts = $this->em()->getEmptyCollection();
		}

		$this->assertValidPage($page, $perPage, $total, 'members', $user);

		$visitor = \XF::visitor();
		if ($user->user_id != $visitor->user_id)
		{
			$userAlertRepo->markUserAlertsReadForContent('user', $visitor->user_id, 'following');
		}

		$canInlineMod = false;
		foreach ($profilePosts AS $profilePost)
		{
			if ($profilePost->canUseInlineModeration())
			{
				$canInlineMod = true;
				break;
			}
		}

		$viewParams = [
			'user' => $user,

			'profilePosts' => $profilePosts,
			'canInlineMod' => $canInlineMod,
			'page' => $page,
			'perPage' => $perPage,
			'total' => $total
		];
		return $this->view('XF:Member\View', 'member_view', $viewParams);
	}

	public function actionAbout(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id);

		/** @var \XF\Repository\UserFollow $userFollowRepo */
		$userFollowRepo = $this->repository('XF:UserFollow');

		$following = [];
		$followingCount = 0;
		if ($user->Profile->following)
		{
			$userFollowingFinder = $userFollowRepo->findFollowingForProfile($user);
			$userFollowingFinder->order($userFollowingFinder->expression('RAND()'));

			$following = $userFollowingFinder->fetch(12)->pluckNamed('FollowUser');
			$followingCount = $userFollowingFinder->total();
		}

		$userFollowersFinder = $userFollowRepo->findFollowersForProfile($user);
		$userFollowersFinder->order($userFollowersFinder->expression('RAND()'));

		$followers = $userFollowersFinder->fetch(12)->pluckNamed('User');
		$followersCount = $userFollowersFinder->total();

		if ($this->options()->enableTrophies)
		{
			/** @var \XF\Repository\Trophy $trophyRepo */
			$trophyRepo = $this->repository('XF:Trophy');
			$trophies = $trophyRepo->findUserTrophies($user->user_id)
				->with('Trophy')
				->fetch();
		}
		else
		{
			$trophies = null;
		}

		$viewParams = [
			'user' => $user,

			'following' => $following,
			'followingCount' => $followingCount,
			'followers' => $followers,
			'followersCount' => $followersCount,

			'trophies' => $trophies
		];
		return $this->view('XF:Member\About', 'member_about', $viewParams);
	}

	public function actionFollowing(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id);

		$page = $this->filterPage($params->page);
		$perPage = $this->options()->membersPerPage;

		$userFollowRepo = $this->repository('XF:UserFollow');
		$userFollowingFinder = $userFollowRepo->findFollowingForProfile($user)
			->order('FollowUser.username')
			->limitByPage($page, $perPage, 1);

		$following = $userFollowingFinder->fetch()->pluckNamed('FollowUser');
		$hasMore = ($following->count() > $perPage);
		$following = $following->slice(0, $perPage);
		$followingCount = $userFollowingFinder->total();

		$viewParams = [
			'user' => $user,
			'following' => $following,
			'page' => $page,
			'perPage' => $perPage,
			'total' => $followingCount,
			'hasMore' => $hasMore
		];
		return $this->view('XF:Member\Following', 'member_following', $viewParams);
	}

	public function actionTooltip(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id, [], true);

		$viewParams = [
			'user' => $user
		];
		return $this->view('XF:Member\Tooltip', 'member_tooltip', $viewParams);
	}

	public function actionFollowers(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id);

		$page = $this->filterPage($params->page);
		$perPage = $this->options()->membersPerPage;

		$userFollowRepo = $this->repository('XF:UserFollow');
		$userFollowersFinder = $userFollowRepo->findFollowersForProfile($user)
			->order('User.username')
			->limitByPage($page, $perPage, 1);

		$followers = $userFollowersFinder->fetch()->pluckNamed('User');
		$hasMore = ($followers->count() > $perPage);
		$followers = $followers->slice(0, $perPage);
		$followersCount = $userFollowersFinder->total();

		$viewParams = [
			'user' => $user,
			'followers' => $followers,
			'page' => $page,
			'perPage' => $perPage,
			'total' => $followersCount,
			'hasMore' => $hasMore
		];
		return $this->view('XF:Member\Followers', 'member_followers', $viewParams);
	}

	/**
	 * @param User $followUser
	 *
	 * @return \XF\Service\User\Follow
	 */
	protected function setupFollowService(\XF\Entity\User $followUser)
	{
		return $this->service('XF:User\Follow', $followUser);
	}

	public function actionFollow(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id, [], true);
		$visitor = \XF::visitor();

		$wasFollowing = $visitor->isFollowing($user);

		if (!$wasFollowing && !$visitor->canFollowUser($user))
		{
			return $this->error(\XF::phrase('you_not_currently_able_to_follow_this_user'));
		}

		$redirect = $this->getDynamicRedirect(null, false);

		if ($this->isPost())
		{
			$followService = $this->setupFollowService($user);

			if ($wasFollowing)
			{
				$userFollow = $followService->unfollow();
			}
			else
			{
				$userFollow = $followService->follow();
			}

			if ($userFollow->hasErrors())
			{
				return $this->error($userFollow->getErrors());
			}

			$reply = $this->redirect($redirect);
			$reply->setJsonParam('switchKey', $wasFollowing ? 'follow' : 'unfollow');
			return $reply;
		}
		else
		{
			$viewParams = [
				'user' => $user,
				'redirect' => $redirect,
				'isFollowing' => $wasFollowing
			];
			return $this->view('XF:Member\Follow', 'member_follow', $viewParams);
		}
	}

	/**
	 * @param User $ignoreUser
	 *
	 * @return \XF\Service\User\Ignore
	 */
	protected function setupIgnoreService(\XF\Entity\User $ignoreUser)
	{
		return $this->service('XF:User\Ignore', $ignoreUser);
	}

	public function actionIgnore(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id, [], true);
		$visitor = \XF::visitor();

		$wasIgnoring = $visitor->isIgnoring($user);

		if (!$wasIgnoring && !$visitor->canIgnoreUser($user, $error))
		{
			return $this->noPermission($error);
		}

		$redirect = $this->getDynamicRedirect(null, false);

		if ($this->isPost())
		{
			$ignoreService = $this->setupIgnoreService($user);

			if ($wasIgnoring)
			{
				$userIgnored = $ignoreService->unignore();
			}
			else
			{
				$userIgnored = $ignoreService->ignore();
			}

			if ($userIgnored->hasErrors())
			{
				return $this->error($userIgnored->getErrors());
			}

			$reply = $this->redirect($redirect);
			$reply->setJsonParam('switchKey', $wasIgnoring ? 'ignore' : 'unignore');
			return $reply;
		}
		else
		{
			$viewParams = [
				'user' => $user,
				'redirect' => $redirect,
				'isIgnoring' => $wasIgnoring
			];
			return $this->view('XF:Member\Ignore', 'member_ignore', $viewParams);
		}
	}

	public function actionLatestActivity(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id);

		if (!$user->canViewLatestActivity())
		{
			$viewParams = [
				'user' => $user,
				'restricted' => true
			];
			return $this->view('XF:Member\LatestActivityRestricted', 'member_latest_activity', $viewParams);
		}

		$maxItems = $this->options()->newsFeedMaxItems;

		$newsFeedRepo = $this->repository('XF:NewsFeed');

		$beforeId = $this->filter('before_id', 'uint');

		$newsFeedFinder = $newsFeedRepo->findMembersActivity($user);
		$newsFeedFinder->beforeFeedId($beforeId);

		$newsFeed = $newsFeedFinder->fetch($maxItems * 2);
		$newsFeedRepo->addContentToNewsFeedItems($newsFeed);

		$newsFeed = $newsFeed->filterViewable();
		$newsFeed = $newsFeed->slice(0, $maxItems);

		$viewParams = [
			'user' => $user,
			'newsFeedItems' => $newsFeed,
			'oldestItemId' => $newsFeed->count() ? min(array_keys($newsFeed->toArray())) : 0,
			'beforeId' => $beforeId
		];
		return $this->view('XF:Member\LatestActivity', 'member_latest_activity', $viewParams);
	}

	public function actionRecentContent(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id);

		$searcher = $this->app->search();
		$query = $searcher->getQuery();

		$query->byUserId($user->user_id)
			->orderedBy('date');

		$resultSet = $searcher->getResultSet($searcher->search($query));
		$resultSet->limitResults(15);

		$results = $searcher->wrapResultsForRender($resultSet);
		$resultCount = $resultSet->countResults();

		$viewParams = [
			'user' => $user,
			'results' => $results,
			'resultCount' => $resultCount
		];
		return $this->view('XF:Member\RecentContent', 'member_recent_content', $viewParams);
	}

	protected function moderatorCustomFieldsSaveProcess(FormAction $form, \XF\Entity\UserProfile $userProfile)
	{
		/** @var \XF\CustomField\Set $fieldSet */
		$fieldSet = $userProfile->custom_fields;
		$fieldDefinition = $fieldSet->getDefinitionSet()
			->filterEditable($fieldSet, 'moderator');

		$customFields = $this->filter('custom_fields', 'array');
		$customFieldsShown = array_keys($fieldDefinition->getFieldDefinitions());

		if ($customFieldsShown)
		{
			$form->setup(function() use ($fieldSet, $customFields, $customFieldsShown)
			{
				$fieldSet->bulkSet($customFields, $customFieldsShown, 'moderator');
			});
		}
	}

	protected function memberSaveProcess(\XF\Entity\User $user)
	{
		$form = $this->formAction();

		// TODO: double check editability restrictions (particularly when the user can't edit elements)

		$input = $this->filter([
			'delete_avatar' => 'bool',
			'user' => [
				'custom_title' => 'str'
			],
			'profile' => [
				'location' => 'str',
				'website' => 'str',
				'about' => 'str',
				'signature' => 'str'
			]
		]);

		$form->basicEntitySave($user, $input['user']);

		/** @var \XF\Entity\UserProfile $userProfile */
		$userProfile = $user->getRelationOrDefault('Profile');
		$form->setupEntityInput($userProfile, $input['profile']);
		$this->moderatorCustomFieldsSaveProcess($form, $userProfile);
		$form->validateEntity($userProfile)->saveEntity($userProfile);

		if ($input['delete_avatar'])
		{
			/** @var \XF\Service\User\Avatar $avatarService */
			$avatarService = $this->service('XF:User\Avatar', $user);
			$form->apply(function() use ($avatarService)
			{
				$avatarService->deleteAvatar();
			});
		}

		return $form;
	}

	public function actionEdit(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id);
		if (!$user->canEdit())
		{
			return $this->noPermission();
		}

		if ($this->isPost())
		{
			$this->memberSaveProcess($user)->run();

			return $this->redirect($this->buildLink('members/edit', $user));
		}
		else
		{
			if (\XF::visitor()->hasAdminPermission('user') && !$this->request()->exists('not-admin'))
			{
				/** @var \XF\Mvc\Router $router */
				$router = $this->app->container('router.admin');
				return $this->redirect($router->buildLink('users/edit', $user));
			}

			$viewParams = [
				'user' => $user
			];
			return $this->view('XF:Member\Edit', 'member_edit', $viewParams);
		}
	}

	public function actionUserIps(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id);
		if (!\XF::visitor()->canViewIps())
		{
			return $this->noPermission();
		}

		/** @var \XF\Repository\Ip $ipRepo */
		$ipRepo = $this->repository('XF:Ip');

		$ips = $ipRepo->getIpsByUser($user);
		if (!$ips)
		{
			return $this->message(\XF::phrase('no_ip_logs_for_requested_user'));
		}

		$viewParams = [
			'user' => $user,
			'ips' => $ips
		];
		return $this->view('XF:Member\UserIps', 'member_ip_list', $viewParams);
	}

	public function actionIpUsers()
	{
		if (!\XF::visitor()->canViewIps())
		{
			return $this->noPermission();
		}

		/** @var \XF\Repository\Ip $ipRepo */
		$ipRepo = $this->repository('XF:Ip');

		$ip = $this->filter('ip', 'str');
		$parsed = \XF\Util\Ip::parseIpRangeString($ip);

		if (!$parsed)
		{
			return $this->message(\XF::phrase('please_enter_valid_ip_or_ip_range'));
		}
		else if ($parsed['isRange'])
		{
			$ips = $ipRepo->getUsersByIpRange($parsed['startRange'], $parsed['endRange']);
		}
		else
		{
			$ips = $ipRepo->getUsersByIp($parsed['startRange']);
		}

		if ($ips)
		{
			$viewParams = [
				'ip' => $ip,
				'ipParsed' => $parsed,
				'ipPrintable' => $parsed['printable'],
				'ips' => $ips
			];
			return $this->view('XF:Member\IpUsers', 'member_ip_users_list', $viewParams);
		}
		else
		{
			return $this->message(\XF::phrase('no_users_logged_at_ip'));
		}
	}

	public function actionSharedIps(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id);

		if (!\XF::visitor()->canViewIps())
		{
			return $this->noPermission();
		}

		$shared = $user->getSharedIpUsers($this->options()->sharedIpsCheckLimit);

		$viewParams = [
			'user' => $user,
			'shared' => $shared
		];
		return $this->view('XF:Member\SharedIps', 'member_shared_ips_list', $viewParams);
	}

	public function actionReport(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id);
		if (!\XF::visitor()->canReport())
		{
			return $this->noPermission();
		}

		/** @var \XF\ControllerPlugin\Report $reportPlugin */
		$reportPlugin = $this->plugin('XF:Report');
		return $reportPlugin->actionReport(
			'user', $user,
			$this->buildLink('members/report', $user),
			$this->buildLink('members', $user)
		);
	}

	/**
	 * @param User $user
	 *
	 * @return \XF\Service\ProfilePost\Creator
	 */
	protected function setupProfilePostCreate(UserProfile $userProfile)
	{
		$message = $this->plugin('XF:Editor')->fromInput('message');

		/** @var \XF\Service\ProfilePost\Creator $creator */
		$creator = $this->service('XF:ProfilePost\Creator', $userProfile);
		$creator->setContent($message);

		return $creator;
	}

	protected function finalizeProfilePostCreate(\XF\Service\ProfilePost\Creator $creator)
	{
		$creator->sendNotifications();

		$profilePost = $creator->getProfilePost();

		if (\XF::visitor()->user_id)
		{
			if ($profilePost->message_state == 'moderated')
			{
				$this->session()->setHasContentPendingApproval();
			}
		}
	}

	public function actionPost(ParameterBag $params)
	{
		$this->assertPostOnly();
		$user = $this->assertViewableUser($params->user_id);
		if (!$user->canPostOnProfile())
		{
			return $this->noPermission();
		}

		$creator = $this->setupProfilePostCreate($user->Profile);
		$creator->checkForSpam();

		if (!$creator->validate($errors))
		{
			return $this->error($errors);
		}
		$this->assertNotFlooding('post');
		$profilePost = $creator->save();

		$this->finalizeProfilePostCreate($creator);

		if ($this->filter('_xfWithData', 'bool') && $this->request->exists('last_date') && $profilePost->canView())
		{
			$profilePostRepo = $this->getProfilePostRepo();

			$limit = 3;
			$lastDate = $this->filter('last_date', 'uint');
			$style = $this->filter('style', 'str');
			$context = $this->filter('context', 'str');
			$firstUnshownProfilePost = null;

			if ($context == 'all')
			{
				/** @var \XF\Mvc\Entity\Finder $profilePostList */
				$profilePostList = $profilePostRepo->findNewestProfilePosts($lastDate)->with('fullProfile');
				$profilePosts = $profilePostList->fetch($limit)->filterViewable();
			}
			else
			{
				/** @var \XF\Mvc\Entity\Finder $profilePostList */
				$profilePostList = $profilePostRepo->findNewestProfilePostsOnProfile($user, $lastDate)->with('fullProfile');
				$profilePosts = $profilePostList->fetch($limit + 1)->filterViewable();

				// We fetched one more post than needed, if more than $limit posts were returned,
				// we can show the 'there are more posts' notice
				if ($profilePosts->count() > $limit)
				{
					$firstUnshownProfilePost = $profilePosts->last();

					// Remove the extra post
					$profilePosts = $profilePosts->pop();
				}
			}

			// put the posts into oldest-first order as they will be (essentially prepended) in that order
			$profilePosts = $profilePosts->reverse(true);

			$viewParams = [
				'user' => $user,
				'style' => $style,
				'profilePosts' => $profilePosts,
				'firstUnshownProfilePost' => $firstUnshownProfilePost
			];
			$view = $this->view('XF:Member\NewProfilePosts', 'member_post_new_profile_posts', $viewParams);
			$view->setJsonParam('lastDate', $profilePosts->last()->post_date);
			return $view;
		}
		else
		{
			return $this->redirect($this->buildLink('profile-posts', $profilePost), \XF::phrase('your_profile_post_has_been_posted'));
		}
	}

	public function userBanAddEdit(User $user)
	{
		if (!$user->canBan($error))
		{
			return $this->error($error);
		}

		$viewParams = [
			'user' => $user,
			'userBan' => $user->getRelationOrDefault('Ban')
		];
		return $this->view('XF:Member\Ban\Edit', 'member_ban_edit', $viewParams);
	}

	public function actionBanEdit(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id);
		if (!$user->is_banned)
		{
			return $this->notFound();
		}
		return $this->userBanAddEdit($user);
	}

	public function actionBan(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id);
		return $this->userBanAddEdit($user);
	}

	protected function userBanSaveProcess(User $user)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'ban_length' => 'str',
			'end_date' => 'datetime',
			'user_reason' => 'str'
		]);

		$form->apply(function(FormAction $form) use ($input, $user)
		{
			if ($input['ban_length'] == 'permanent')
			{
				$input['end_date'] = 0;
			}

			/** @var \XF\Repository\Banning $banningRepo */
			$banningRepo = $this->repository('XF:Banning');
			if (!$banningRepo->banUser($user, $input['end_date'], $input['user_reason'], $error))
			{
				$form->logError($error);
			}
		});

		return $form;
	}

	public function actionBanSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		$user = $this->assertViewableUser($params->user_id);
		if (!$user->canBan($error))
		{
			return $this->error($error);
		}
		$this->userBanSaveProcess($user)->run();

		return $this->redirect($this->buildLink('members', $user));
	}

	public function actionBanLift(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id);
		if (!$user->is_banned)
		{
			return $this->notFound();
		}

		if (!$user->canBan($error))
		{
			return $this->error($error);
		}

		if ($this->isPost())
		{
			$user->Ban->delete();
			return $this->redirect($this->buildLink('members', $user));
		}
		else
		{
			$viewParams = [
				'user' => $user,
				'userBan' => $user->Ban
			];
			return $this->view('XF:Member\Ban\Lift', 'member_ban_lift', $viewParams);
		}
	}

	public function actionTrophies(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id);

		if (!$this->options()->enableTrophies)
		{
			return $this->redirect($this->buildLink('members', $user));
		}

		/** @var \XF\Repository\Trophy $trophyRepo */
		$trophyRepo = $this->repository('XF:Trophy');
		$trophies = $trophyRepo->findUserTrophies($user->user_id)
			->with('Trophy')
			->fetch();

		if ($user->user_id == \XF::visitor()->user_id)
		{
			$trophyIds = $trophies->pluckNamed('trophy_id');

			/** @var \XF\Repository\UserAlert $userAlertRepo */
			$userAlertRepo = $this->repository('XF:UserAlert');
			$userAlertRepo->markUserAlertsReadForContent('trophy', $trophyIds);
		}

		$viewParams = [
			'user' => $user,
			'trophies' => $trophies
		];
		return $this->view('XF:Member\Trophy\Listing', 'member_trophies', $viewParams);
	}

	public function actionWarn(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id);

		if (!$user->canWarn())
		{
			return $this->noPermission();
		}

		/** @var \XF\ControllerPlugin\Warn $warnPlugin */
		$warnPlugin = $this->plugin('XF:Warn');
		return $warnPlugin->actionWarn(
			'user', $user,
			$this->buildLink('members/warn', $user)
		);
	}

	public function actionWarnings(ParameterBag $params)
	{
		$user = $this->assertViewableUser($params->user_id);
		if (!\XF::visitor()->canViewWarnings())
		{
			return $this->noPermission();
		}

		/** @var \XF\Repository\Warning $warningRepo */
		$warningRepo = $this->repository('XF:Warning');
		$warnings = $warningRepo->findUserWarningsForList($user->user_id)->fetch();
		if (!$warnings->count())
		{
			return $this->message(\XF::phrase('this_member_has_not_been_warned'));
		}

		$viewParams = [
			'user' => $user,
			'warnings' => $warnings
		];
		return $this->view('XF:Member\Warnings', 'member_warnings', $viewParams);
	}

	public function actionFind()
	{
		$q = ltrim($this->filter('q', 'str', ['no-trim']));

		if ($q !== '' && utf8_strlen($q) >= 2)
		{
			/** @var \XF\Finder\User $userFinder */
			$userFinder = $this->finder('XF:User');

			$users = $userFinder
				->where('username', 'like', $userFinder->escapeLike($q, '?%'))
				->isValidUser(true)
				->fetch(10);
		}
		else
		{
			$users = [];
			$q = '';
		}

		$viewParams = [
			'q' => $q,
			'users' => $users
		];
		return $this->view('XF:Member\Find', '', $viewParams);
	}

	/**
	 * @param int $userId
	 * @param array $extraWith
	 * @param bool $basicProfileOnly
	 *
	 * @return \XF\Entity\User
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertViewableUser($userId, array $extraWith = [], $basicProfileOnly = false)
	{
		$extraWith[] = 'Option';
		$extraWith[] = 'Privacy';
		$extraWith[] = 'Profile';
		array_unique($extraWith);

		/** @var \XF\Entity\User $user */
		$user = $this->em()->find('XF:User', $userId, $extraWith);
		if (!$user)
		{
			throw $this->exception($this->notFound(\XF::phrase('requested_user_not_found')));
		}

		$canView = $basicProfileOnly ? $user->canViewBasicProfile($error) : $user->canViewFullProfile($error);
		if (!$canView)
		{
			throw $this->exception($this->noPermission($error));
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

	public static function getActivityDetails(array $activities)
	{
		$userIds = [];
		$userData = [];

		$router = \XF::app()->router('public');
		$defaultPhrase = \XF::phrase('viewing_members');

		if (!\XF::visitor()->hasPermission('general', 'viewProfile'))
		{
			return $defaultPhrase;
		}

		foreach ($activities AS $activity)
		{
			$userId = $activity->pluckParam('user_id');
			if ($userId)
			{
				$userIds[$userId] = $userId;
			}
		}

		if ($userIds)
		{
			$users = \XF::em()->findByIds('XF:User', $userIds, 'Privacy');
			foreach ($users AS $user)
			{
				$userData[$user->user_id] = [
					'username' => $user->username,
					'url' => $router->buildLink('members', $user),
				];
			}
		}

		$output = [];

		foreach ($activities AS $key => $activity)
		{
			$userId = $activity->pluckParam('user_id');
			$user = $userId && isset($userData[$userId]) ? $userData[$userId] : null;
			if ($user)
			{
				$output[$key] = [
					'description' => \XF::phrase('viewing_member_profile'),
					'title' => $user['username'],
					'url' => $user['url']
				];
			}
			else
			{
				$output[$key] = $defaultPhrase;
			}
		}

		return $output;
	}
}