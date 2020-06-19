<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class User extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		switch (strtolower($action))
		{
			case 'index':
				break;

			default:
				$this->assertAdminPermission('user');
		}
	}

	public function actionIndex()
	{
		return $this->view('XF:Users', 'users');
	}

	public function actionList()
	{
		$criteria = $this->filter('criteria', 'array');
		$order = $this->filter('order', 'str');
		$direction = $this->filter('direction', 'str');

		$page = $this->filterPage();
		$perPage = 20;

		$showingAll = $this->filter('all', 'bool');
		if ($showingAll)
		{
			$page = 1;
			$perPage = 5000;
		}

		if (!$criteria)
		{
			$this->setSectionContext('listAllUsers');
		}
		else
		{
			$this->setSectionContext('searchForUsers');
		}

		$searcher = $this->searcher('XF:User', $criteria);

		if ($order && !$direction)
		{
			$direction = $searcher->getRecommendedOrderDirection($order);
		}

		$searcher->setOrder($order, $direction);

		$finder = $searcher->getFinder();
		$finder->limitByPage($page, $perPage);

		$filter = $this->filter('_xfFilter', [
			'text' => 'str',
			'prefix' => 'bool'
		]);
		if (strlen($filter['text']))
		{
			$finder->where('username', 'LIKE', $finder->escapeLike($filter['text'], $filter['prefix'] ? '?%' : '%?%'));
		}

		$total = $finder->total();
		$users = $finder->fetch();

		$this->assertValidPage($page, $perPage, $total, 'users/list');

		if (!strlen($filter['text']) && $total == 1 && ($user = $users->first()))
		{
			return $this->redirect($this->buildLink('users/edit', $user));
		}

		$viewParams = [
			'users' => $users,

			'total' => $total,
			'page' => $page,
			'perPage' => $perPage,

			'showingAll' => $showingAll,
			'showAll' => (!$showingAll && $total <= 5000),

			'criteria' => $searcher->getFilteredCriteria(),
			'filter' => $filter['text'],
			'sortOptions' => $searcher->getOrderOptions(),
			'order' => $order,
			'direction' => $direction
		];
		return $this->view('XF:User\Listing', 'user_list', $viewParams);
	}

	public function actionSearch()
	{
		$this->setSectionContext('searchForUsers');

		$lastUserId = $this->filter('last_user_id', 'uint');
		$lastUser = $lastUserId ? $this->em()->find('XF:User', $lastUserId) : null;

		$viewParams = $this->getSearcherParams($lastUser ? ['lastUser' => $lastUser] : []);

		return $this->view('XF:User\Search', 'user_search', $viewParams);
	}

	public function actionIpUsers()
	{
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
			return $this->view('XF:User\IpUsers\Listing', 'ip_users_list', $viewParams);
		}
		else
		{
			return $this->message(\XF::phrase('no_users_logged_at_ip'));
		}
	}

	public function actionQuickSearch()
	{
		$query = $this->filter('query', 'str');

		if (\Swift_Validate::email($query))
		{
			// NOTE: don't use Swift_Validate directly for XF-valid email addresses,
			// see \XF\Validator\Email

			$this->request->set('criteria', ['email' => $query]);
			return $this->rerouteController(__CLASS__, 'list');
		}
		else if ($ip = \XF\Util\Ip::parseIpRangeString($query))
		{
			$this->request->set('ip', $query);
			return $this->rerouteController(__CLASS__, 'ip-users');
		}
		else
		{
			$this->request->set('criteria', ['username' => $query]);
			return $this->rerouteController(__CLASS__, 'list');
		}
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
		return $this->view('XF:User\Find', '', $viewParams);
	}

	public function actionBatchUpdate()
	{
		$this->setSectionContext('batchUpdateUsers');

		$viewParams = $this->getSearcherParams(['success' => $this->filter('success', 'bool')]);
		return $this->view('XF:User\BatchUpdate', 'user_batch_update', $viewParams);
	}

	public function actionBatchUpdateConfirm()
	{
		$this->setSectionContext('batchUpdateUsers');

		$this->assertPostOnly();

		$criteria = $this->filter('criteria', 'array');
		$searcher = $this->searcher('XF:User', $criteria);

		$userIds = $this->filter('user_ids', 'array-uint');

		$total = count($userIds) ?: $searcher->getFinder()->total();
		if (!$total)
		{
			throw $this->exception($this->error(\XF::phraseDeferred('no_items_matched_your_filter')));
		}

		$viewParams = [
			'total' => $total,
			'userIds' => $userIds,
			'criteria' => $searcher->getFilteredCriteria(),
			'userGroups' => $this->repository('XF:UserGroup')->findUserGroupsForList()->fetch()
		];
		return $this->view('XF:User\BatchUpdate\Confirm', 'user_batch_update_confirm', $viewParams);
	}

	public function actionBatchUpdateAction()
	{
		$this->setSectionContext('batchUpdateUsers');

		$this->assertPostOnly();

		if ($this->request->exists('user_ids'))
		{
			$userIds = $this->filter('user_ids', 'json-array');
			$total = count($userIds);
			$jobCriteria = null;
		}
		else
		{
			$criteria = $this->filter('criteria', 'json-array');

			$searcher = $this->searcher('XF:User', $criteria);
			$total = $searcher->getFinder()->total();
			$jobCriteria = $searcher->getFilteredCriteria();

			$userIds = null;
		}

		if (!$total)
		{
			throw $this->exception($this->error(\XF::phraseDeferred('no_items_matched_your_filter')));
		}

		$actions = $this->filter('actions', 'array');

		if ($this->request->exists('confirm_delete') && empty($actions['delete']))
		{
			return $this->error(\XF::phrase('you_must_confirm_deletion_to_proceed'));
		}

		$this->app->jobManager()->enqueueUnique('userAction', 'XF:UserAction', [
			'total' => $total,
			'actions' => $actions,
			'userIds' => $userIds,
			'criteria' => $jobCriteria
		]);

		return $this->redirect($this->buildLink('users/batch-update', null, ['success' => true]));
	}

	protected function userAddEdit(\XF\Entity\User $user)
	{
		/** @var \XF\Data\TimeZone $tzData */
		$tzData = $this->data('XF:TimeZone');

		/** @var \XF\Repository\Style $styleRepo */
		$styleRepo = $this->repository('XF:Style');

		/** @var \XF\Repository\Language $languageRepo */
		$languageRepo = $this->repository('XF:Language');

		$viewParams = [
			'user' => $user,
			'userGroups' => $this->em()->getRepository('XF:UserGroup')->getUserGroupTitlePairs(),
			'timeZones' => $tzData->getTimeZoneOptions(),
			'styleTree' => $styleRepo->getStyleTree(false),
			'languageTree' => $languageRepo->getLanguageTree(false),

			'success' => $this->filter('success', 'bool')
		];
		return $this->view('XF:User\Edit', 'user_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$user = $this->assertUserExists($params->user_id, ['Activity', 'Option', 'Profile', 'Privacy']);
		$this->assertCanEditUser($user);
		return $this->userAddEdit($user);
	}

	public function actionAdd()
	{
		$this->setSectionContext('createNewUser');

		$user = $this->getUserRepo()->setupBaseUser();

		return $this->userAddEdit($user);
	}

	protected function customFieldsSaveProcess(FormAction $form, \XF\Entity\UserProfile $userProfile)
	{
		/** @var \XF\CustomField\Set $fieldSet */
		$fieldSet = $userProfile->custom_fields;
		$fieldDefinition = $fieldSet->getDefinitionSet()
			->filterEditable($fieldSet, 'admin');

		$customFields = $this->filter('custom_fields', 'array');
		$customFieldsShown = array_keys($fieldDefinition->getFieldDefinitions());

		if ($customFieldsShown)
		{
			$form->setup(function() use ($fieldSet, $customFields, $customFieldsShown)
			{
				$fieldSet->bulkSet($customFields, $customFieldsShown, 'admin', true);
			});
		}
	}

	protected function userSaveProcess(\XF\Entity\User $user)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'user' => [
				'username' => 'str',
				'email' => 'str',
				'user_group_id' => 'uint',
				'secondary_group_ids' => 'array-uint',
				'user_state' => 'str',
				'is_staff' => 'bool',
				'custom_title' => 'str',
				'message_count' => 'uint',
				'reaction_score' => 'int',
				'trophy_points' => 'uint',
				'style_id' => 'uint',
				'language_id' => 'uint',
				'timezone' => 'str',
				'visible' => 'bool',
				'activity_visible' => 'bool',
			],
			'option' => [
				'is_discouraged' => 'bool',
				'content_show_signature' => 'bool',
				'email_on_conversation' => 'uint',
				'creation_watch_state' => 'str',
				'interaction_watch_state' => 'str',
				'receive_admin_email' => 'bool',
				'show_dob_date' => 'bool',
				'show_dob_year' => 'bool'
			],
			'profile' => [
				'location' => 'str',
				'website' => 'str',
				'about' => 'str',
				'signature' => 'str'
			],
			'privacy' => [
				'allow_view_profile' => 'str',
				'allow_post_profile' => 'str',
				'allow_send_personal_conversation' => 'str',
				'allow_view_identities' => 'str',
				'allow_receive_news_feed' => 'str',
			],
			'dob_day' => 'uint',
			'dob_month' => 'uint',
			'dob_year' => 'uint',
			'change_password' => 'str',
			'password' => 'str',
			'disable_tfa' => 'bool'
		]);

		$password = $this->filter('visitor_password', 'str');
		if ($user->exists() && $user->is_super_admin)
		{
			if (!\XF::visitor()->authenticate($password))
			{
				throw $this->exception($this->error(\XF::phrase('your_existing_password_is_not_correct')));
			}
		}

		$user->setOption('admin_edit', true);
		$form->basicEntitySave($user, $input['user']);

		$userOptions = $user->getRelationOrDefault('Option');
		$form->setupEntityInput($userOptions, $input['option']);

		/** @var \XF\Entity\UserProfile $userProfile */
		$userProfile = $user->getRelationOrDefault('Profile');
		$userProfile->setOption('admin_edit', true);
		$form->setupEntityInput($userProfile, $input['profile']);
		$form->setup(function() use ($userProfile, $input)
		{
			$userProfile->setDob($input['dob_day'], $input['dob_month'], $input['dob_year']);
		});
		$this->customFieldsSaveProcess($form, $userProfile);

		$userPrivacy = $user->getRelationOrDefault('Privacy');
		$form->setupEntityInput($userPrivacy, $input['privacy']);

		$form->validate(function(FormAction $form) use ($input, $user)
		{
			if (!$user->exists() && !$input['password'])
			{
				$form->logError(\XF::phrase('please_enter_valid_password'), 'password');
			}
		});

		/** @var \XF\Entity\UserAuth $userAuth */
		$userAuth = $user->getRelationOrDefault('Auth');
		if ($input['password'] && (!$user->exists() || $input['change_password'] == 'change'))
		{
			$form->setup(function() use ($userAuth, $input)
			{
				$userAuth->setPassword($input['password']);
			});
			if ($user->exists())
			{
				$form->complete(function() use ($user)
				{
					if ($user->user_id == \XF::visitor()->user_id)
					{
						$this->plugin('XF:Login')->handleVisitorPasswordChange();
					}
					else
					{
						$this->repository('XF:UserRemember')->clearUserRememberRecords($user->user_id);
					}
				});
			}
		}
		else if ($input['change_password'] == 'generate')
		{
			/** @var \XF\Service\User\PasswordReset $passwordReset */
			$passwordReset = $this->service('XF:User\PasswordReset', $user);
			$passwordReset->setAdminReset(true);

			$form->setup(function(FormAction $form) use($userAuth, $user)
			{
				if ($user->email)
				{
					$userAuth->resetPassword();
				}
				else
				{
					$form->logError(\XF::phrase('cannot_generate_new_password_without_email_address'));
				}
			});

			$form->complete(function() use ($passwordReset)
			{
				$passwordReset->triggerConfirmation();
			});
		}

		if ($user->exists() && $input['disable_tfa'])
		{
			/** @var \XF\Repository\Tfa $tfaRepo */
			$tfaRepo = $this->repository('XF:Tfa');

			$form->complete(function() use ($user, $tfaRepo)
			{
				$tfaRepo->disableTfaForUser($user);
			});
		}

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->user_id)
		{
			$user = $this->assertUserExists($params->user_id);
			$this->assertCanEditUser($user);
		}
		else
		{
			$user = null;
		}

		$user = $this->getUserRepo()->setupBaseUser($user);
		$this->userSaveProcess($user)->run();

		return $this->redirect($this->buildLink('users/search', null, ['last_user_id' => $user->user_id]));
	}

	public function actionExtra(ParameterBag $params)
	{
		$user = $this->assertUserExists($params->user_id);

		/** @var \XF\Repository\UserUpgrade $upgradeRepo */
		$upgradeRepo = $this->repository('XF:UserUpgrade');
		$upgrades = $upgradeRepo->findActiveUserUpgradesForList()->where('user_id', $user->user_id)->fetch();

		/** @var \XF\Repository\ConnectedAccount $connectedRepo */
		$connectedRepo = $this->repository('XF:ConnectedAccount');
		$connectedProviders = $connectedRepo->getUsableProviders();

		$viewParams = [
			'user' => $user,
			'upgrades' => $upgrades,
			'connectedProviders' => $connectedProviders
		];
		return $this->view('XF:User\Extra', 'user_extra', $viewParams);
	}

	public function actionUserIps(ParameterBag $params)
	{
		$user = $this->assertUserExists($params->user_id);

		/** @var \XF\Repository\Ip $ipRepo */
		$ipRepo = $this->repository('XF:Ip');

		$ips = $ipRepo->getIpsByUser($user);

		$viewParams = [
			'user' => $user,
			'ips' => $ips
		];
		return $this->view('XF:User\IpList', 'user_ip_list', $viewParams);
	}

	public function actionAvatar(ParameterBag $params)
	{
		$user = $this->assertUserExists($params->user_id);
		$this->assertCanEditUser($user);

		if ($this->isPost())
		{
			/** @var \XF\Service\User\Avatar $avatarService */
			$avatarService = $this->service('XF:User\Avatar', $user);
			$avatarService->logIp(false);

			$upload = $this->request->getFile('upload', false, false);
			if ($upload)
			{
				if (!$avatarService->setImageFromUpload($upload))
				{
					return $this->error($avatarService->getError());
				}

				if (!$avatarService->updateAvatar())
				{
					return $this->error(\XF::phrase('new_avatar_could_not_be_processed'));
				}
			}
			else if ($this->filter('delete_avatar', 'bool'))
			{
				$avatarService->deleteAvatar();
			}

			return $this->redirect($this->buildLink('users/edit', $user, ['success' => true]));
		}
		else
		{
			$viewParams = [
				'user' => $user
			];
			return $this->view('XF:User\Avatar', 'user_avatar', $viewParams);
		}
	}

	public function actionResendConfirmation(ParameterBag $params)
	{
		$user = $this->assertUserExists($params->user_id);

		return $this->plugin('XF:EmailConfirmation')->actionResend(
			$user, $this->buildLink('users/resend-confirmation', $user), ['view' => 'XF:User\ResendConfirmation']
		);
	}

	public function actionDelete(ParameterBag $params)
	{
		$user = $this->assertUserExists($params->user_id);

		$this->assertCanEditUser($user);

		if ($this->isPost())
		{
			if ($user->is_super_admin)
			{
				if (!\XF::visitor()->authenticate($this->filter('visitor_password', 'str')))
				{
					return $this->error(\XF::phrase('your_existing_password_is_not_correct'));
				}
			}

			$redirect = $this->getDynamicRedirectIfNot(
				$this->buildLink('users/edit', $user),
				$this->buildLink('users/list')
			);

			/** @var \XF\Service\User\Delete $deleter */
			$deleter = $this->service('XF:User\Delete', $user);

			if ($this->filter('rename', 'bool'))
			{
				$renameTo = $this->filter('rename_to', 'str');
				if (!$renameTo)
				{
					return $this->error(\XF::phrase('please_enter_name_to_rename_this_user_to'));
				}
				$deleter->renameTo($renameTo);
			}

			if (!$deleter->delete($errors))
			{
				return $this->error($errors);
			}

			return $this->redirect($redirect);
		}
		else
		{
			if (!$user->preDelete())
			{
				return $this->error($user->getErrors());
			}

			$viewParams = [
				'user' => $user
			];
			return $this->view('XF:User\Delete', 'user_delete', $viewParams);
		}
	}

	public function actionChangeLog(ParameterBag $params)
	{
		$user = $this->assertUserExists($params->user_id);

		$page = $this->filterPage();
		$perPage = 20;

		$changeRepo = $this->repository('XF:ChangeLog');
		$changeFinder = $changeRepo->findChangeLogsByContent('user', $user->user_id)->limitByPage($page, $perPage);

		$changes = $changeFinder->fetch();
		$changeRepo->addDataToLogs($changes);

		$viewParams = [
			'user' => $user,
			'changesGrouped' => $changeRepo->groupChangeLogs($changes),

			'page' => $page,
			'perPage' => $perPage,
			'total' => $changeFinder->total()
		];
		return $this->view('XF:User\ChangeLog', 'user_change_log', $viewParams);
	}

	public function actionMerge(ParameterBag $params)
	{
		$user = $this->assertUserExists($params->user_id);

		if ($user->is_admin || $user->is_moderator)
		{
			return $this->noPermission();
		}

		if ($this->isPost())
		{
			$targetName = $this->filter('username', 'str');
			/** @var \XF\Entity\User $target */
			$target = $this->em()->findOne('XF:User', ['username' => $targetName]);
			if (!$target)
			{
				return $this->error(\XF::phrase('requested_user_not_found'));
			}

			$jobId = 'userMerge' . $target->user_id . '-' . $user->user_id;
			$this->app->jobManager()->enqueueUnique($jobId, 'XF:UserMerge', [
				'sourceUserId' => $user->user_id,
				'targetUserId' => $target->user_id
			]);

			return $this->redirect($this->buildLink('users/edit', $target, ['success' => true]));
		}
		else
		{
			$viewParams = [
				'user' => $user
			];
			return $this->view('XF:User\Merge', 'user_merge', $viewParams);
		}
	}

	public function actionDeleteConversations(ParameterBag $params)
	{
		$user = $this->assertUserExists($params->user_id);

		if ($user->is_admin || $user->is_moderator)
		{
			return $this->noPermission();
		}

		if ($this->isPost())
		{
			/** @var \XF\Repository\Conversation $convRepo */
			$convRepo = $this->repository('XF:Conversation');

			$db = \XF::db();

			$db->beginTransaction();

			$convFinder = $convRepo->findConversationsStartedByUser($user);
			foreach ($convFinder->fetch() AS $conversation)
			{
				$conversation->delete(false, false);
			}

			$db->commit();

			return $this->redirect($this->buildLink('users/edit', $user, ['success' => true]));
		}
		else
		{
			$viewParams = [
				'user' => $user
			];
			return $this->view('XF:User\DeleteConversations', 'user_delete_conversations', $viewParams);
		}
	}

	public function actionRevertMessageEdit(ParameterBag $params)
	{
		$options = $this->options();
		$user = $this->assertUserExists($params->user_id);

		if ($user->is_super_admin || !$options->editHistory['enabled'])
		{
			return $this->noPermission();
		}

		if ($this->isPost())
		{
			$cutOff = max(0, \XF::$time - $this->filter('cutoff', 'timeoffset'));
			if ($options->editHistory['length'])
			{
				$cutOff = max($cutOff, \XF::$time - $options->editHistory['length'] * 86400);
			}

			$this->app->jobManager()->enqueue('XF:UserRevertMessageEdit', [
				'userId' => $user->user_id,
				'cutOff' => $cutOff
			], true);

			return $this->redirect($this->buildLink('users/edit', $user, ['success' => true]));
		}
		else
		{
			$viewParams = [
				'user' => $user
			];
			return $this->view('XF:User\RevertMessageEdit', 'user_revert_message_edit', $viewParams);
		}
	}

	public function actionRemoveReactions(ParameterBag $params)
	{
		$user = $this->assertUserExists($params->user_id);
		$this->assertCanEditUser($user);

		if ($this->isPost())
		{
			$cutOff = max(0, \XF::$time - $this->filter('cutoff', 'timeoffset'));

			$this->app->jobManager()->enqueue('XF:UserRemoveReactions', [
				'userId' => $user->user_id,
				'cutOff' => $cutOff
			], true);

			return $this->redirect($this->buildLink('users/edit', $user, ['success' => true]));
		}
		else
		{
			$viewParams = [
				'user' => $user
			];
			return $this->view('XF:User\RemoveReactions', 'user_remove_reactions', $viewParams);
		}
	}

	public function actionManageWatchedThreads(ParameterBag $params)
	{
		$user = $this->assertUserExists($params->user_id);
		$this->assertCanEditUser($user);

		if ($this->isPost())
		{
			/** @var \XF\Repository\ThreadWatch $threadWatchRepo */
			$threadWatchRepo = $this->repository('XF:ThreadWatch');

			$action = $this->filter('action', 'str');
			if ($threadWatchRepo->isValidWatchState($action))
			{
				$threadWatchRepo->setWatchStateForAll($user, $action);
			}

			return $this->redirect($this->buildLink('users/edit', $user, ['success' => true]));
		}
		else
		{
			$viewParams = [
				'user' => $user
			];
			return $this->view('XF:User\ManageWatchedThreads', 'user_manage_watched_threads', $viewParams);
		}
	}

	protected function prepareAlertData()
	{
		$alert = $this->filter([
			'from_user' => 'str',

			'link_url' => 'str',
			'link_title' => 'str',
			'alert_body' => 'str'
		]);

		$user = null;
		if ($alert['from_user'])
		{
			$user = $this->finder('XF:User')->where('username', $alert['from_user'])->fetchOne();
			if (!$user)
			{
				throw $this->exception($this->error(\XF::phraseDeferred('requested_user_x_not_found', ['name' => $alert['from_user']])));
			}
		}

		$alert['username'] = $user ? $user->username : '';
		$alert['user_id'] = $user ? $user->user_id : 0;

		if (!$alert['alert_body'] && !$alert['link_url'])
		{
			throw $this->exception($this->error(\XF::phraseDeferred('please_complete_required_fields')));
		}

		$data = $this->plugin('XF:UserCriteriaAction')->getInitializedSearchData();
		$data['alert'] = $alert;
		$data['user'] = $user;

		return $data;
	}

	public function actionAlert()
	{
		$this->setSectionContext('alertUsers');

		return $this->view('XF:User\Alert', 'user_alert', $this->getSearcherParams([
			'sent' => $this->filter('sent', 'uint')
		]));
	}

	public function actionAlertConfirm()
	{
		$this->setSectionContext('alertUsers');

		$this->assertPostOnly();

		$data = $this->prepareAlertData();

		$viewParams = [
			'alert' => $data['alert'],
			'user' => $data['user'],
			'total' => $data['total'],
			'criteria' => $data['criteria']
		];
		return $this->view('XF:User\AlertConfirm', 'user_alert_confirm', $viewParams);
	}

	public function actionAlertSend()
	{
		$this->setSectionContext('alertUsers');

		$this->assertPostOnly();

		$data = $this->prepareAlertData();

		if ($this->filter('test', 'bool'))
		{
			$this->app->job('XF:UserAlert', null, [
				'userIds' => [\XF::visitor()->user_id],
				'alert' => $data['alert']
			])->run(0);
			return $this->rerouteController(__CLASS__, 'alertConfirm');
		}

		$this->app->jobManager()->enqueueUnique('userAlertSend', 'XF:UserAlert', [
			'criteria' => $data['criteria'],
			'alert' => $data['alert']
		]);

		return $this->redirect($this->buildLink(
			'users/alert', null, ['sent' => $data['total']]
		));
	}

	protected function prepareEmailData()
	{
		$email = $this->filter([
			'list_only' => 'bool',
			'from_name' => 'str',
			'from_email' => 'str',

			'email_title' => 'str',
			'email_format' => 'str',
			'email_body' => 'str',
			'email_wrapped' => 'bool',
			'email_unsub' => 'bool'
		]);

		if (!$email['list_only'] && (!$email['from_name'] || !$email['from_email'] || !$email['email_title'] || !$email['email_body']))
		{
			throw $this->exception($this->error(\XF::phraseDeferred('please_complete_required_fields')));
		}

		if (strpos($email['email_body'], '{unsub}') !== false)
		{
			$email['email_unsub'] = false;
		}

		$data = $this->plugin('XF:UserCriteriaAction')->getInitializedSearchData([
			'no_empty_email' => true
		]);
		$data['email'] = $email;

		return $data;
	}

	public function actionEmail()
	{
		$this->setSectionContext('emailUsers');

		return $this->view('XF:User\Email', 'user_email', $this->getSearcherParams([
			'sent' => $this->filter('sent', 'uint')
		]));
	}

	public function actionEmailConfirm()
	{
		$this->setSectionContext('emailUsers');

		$this->assertPostOnly();

		$data = $this->prepareEmailData();

		if ($this->filter('list_only', 'bool'))
		{
			/** @var \XF\Searcher\AbstractSearcher $searcher */
			$searcher = $data['searcher'];

			return $this->view('XF:User\EmailList', 'user_email_list', [
				'users' => $searcher->getFinder()->fetch()
			]);
		}

		$viewParams = [
			'email' => $data['email'],
			'total' => $data['total'],
			'criteria' => $data['criteria'],
			'tested' => $this->filter('tested', 'bool')
		];
		return $this->view('XF:User\EmailConfirm', 'user_email_confirm', $viewParams);
	}

	public function actionEmailSend()
	{
		$this->setSectionContext('emailUsers');

		$this->assertPostOnly();

		$data = $this->prepareEmailData();

		if ($this->filter('test', 'bool'))
		{
			$this->app->job('XF:UserEmail', null, [
				'userIds' => [\XF::visitor()->user_id],
				'email' => $data['email']
			])->run(0);

			$this->request->set('tested', '1');

			return $this->rerouteController(__CLASS__, 'emailConfirm');
		}

		$this->app->jobManager()->enqueueUnique('userEmailSend', 'XF:UserEmail', [
			'criteria' => $data['criteria'],
			'email' => $data['email']
		]);

		return $this->redirect($this->buildLink(
			'users/email', null, ['sent' => $data['total']]
		));
	}

	protected function prepareMessageData()
	{
		$message = $this->filter([
			'from_user' => 'str',

			'message_title' => 'str',
			'message_body' => 'str',

			'open_invite' => 'bool',
			'conversation_locked' => 'bool',

			'delete_type' => 'str'
		]);

		$user = null;
		if ($message['from_user'])
		{
			$user = $this->finder('XF:User')->where('username', $message['from_user'])->fetchOne();
			if (!$user)
			{
				throw $this->exception($this->error(\XF::phraseDeferred('requested_user_x_not_found', ['name' => $message['from_user']])));
			}
		}

		$message['username'] = $user ? $user->username : '';
		$message['user_id'] = $user ? $user->user_id : 0;

		if (!$message['message_title'] && !$message['message_body'])
		{
			throw $this->exception($this->error(\XF::phraseDeferred('please_complete_required_fields')));
		}

		$data = $this->plugin('XF:UserCriteriaAction')->getInitializedSearchData([
			'not_user_id' => $user->user_id
		]);
		$data['message'] = $message;
		$data['user'] = $user;

		return $data;
	}

	public function actionMessage()
	{
		$this->setSectionContext('messageUsers');

		return $this->view('XF:User\Message', 'user_message', $this->getSearcherParams([
			'sent' => $this->filter('sent', 'uint')
		]));
	}

	public function actionMessageConfirm()
	{
		$this->setSectionContext('messageUsers');

		$this->assertPostOnly();

		$data = $this->prepareMessageData();

		$viewParams = [
			'message' => $data['message'],
			'user' => $data['user'],
			'total' => $data['total'],
			'criteria' => $data['criteria']
		];
		return $this->view('XF:User\MessageConfirm', 'user_message_confirm', $viewParams);
	}

	public function actionMessagePreview()
	{
		$this->setSectionContext('messageUsers');

		$this->assertPostOnly();

		$message = $this->filter([
			'message_title' => 'str',
			'message_body' => 'str'
		]);

		$stringFormatter = $this->app->stringFormatter();
		$visitor = \XF::visitor();

		$tokens = [
			'{name}' => $visitor->username,
			'{id}' => $visitor->user_id,
			'{email}' => $visitor->email
		];
		$title = strtr($stringFormatter->replacePhrasePlaceholders($message['message_title']), $tokens);
		$body = strtr($stringFormatter->replacePhrasePlaceholders($message['message_body']), $tokens);

		$viewParams = [
			'title' => $title,
			'content' => $body
		];
		return $this->view('XF:User\MessagePreview', 'user_message_preview', $viewParams);
	}

	public function actionMessageSend()
	{
		$this->setSectionContext('messageUsers');

		$this->assertPostOnly();

		$data = $this->prepareMessageData();

		$this->app->jobManager()->enqueueUnique('userMessageSend', 'XF:UserMessage', [
			'criteria' => $data['criteria'],
			'message' => $data['message']
		]);
		return $this->redirect($this->buildLink(
			'users/message', null, ['sent' => $data['total']]
		));
	}

	protected function getSearcherParams(array $extraParams = [])
	{
		$searcher = $this->searcher('XF:User');

		$viewParams = [
			'criteria' => $searcher->getFormCriteria(),
			'sortOrders' => $searcher->getOrderOptions()
		];
		return $viewParams + $searcher->getFormData() + $extraParams;
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\User
	 */
	protected function assertUserExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:User', $id, $with, $phraseKey);
	}

	protected function assertCanEditUser(\XF\Entity\User $user)
	{
		if ($user->is_admin
			&& $user->Admin->is_super_admin
			&& !\XF::visitor()->Admin->is_super_admin
		)
		{
			throw $this->exception(
				$this->error(\XF::phrase('you_must_be_super_administrator_to_edit_user'))
			);
		}
	}

	/**
	 * @return \XF\Repository\User
	 */
	protected function getUserRepo()
	{
		return $this->repository('XF:User');
	}
}