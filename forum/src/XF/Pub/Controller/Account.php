<?php

namespace XF\Pub\Controller;

use XF\Mvc\Entity\ArrayCollection;
use XF\Mvc\ParameterBag;
use XF\Mvc\FormAction;
use XF\Mvc\Reply\View;

class Account extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertRegistrationRequired();
	}

	public function actionIndex()
	{
		return $this->rerouteController(__CLASS__, 'account-details');
	}

	protected function addAccountWrapperParams(View $view, $selected)
	{
		$view->setParam('pageSelected', $selected);
		return $view;
	}

	public function actionAccountDetails()
	{
		if ($this->isPost())
		{
			$visitor = \XF::visitor();

			if ($visitor->canEditProfile())
			{
				$this->accountDetailsSaveProcess($visitor)->run();
			}

			return $this->redirect($this->buildLink('account/account-details'));
		}
		else
		{
			$view = $this->view('XF:Account\AccountDetails', 'account_details');
			return $this->addAccountWrapperParams($view, 'account_details');
		}
	}

	protected function accountDetailsSaveProcess(\XF\Entity\User $visitor)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'option' => [
				'receive_admin_email' => 'bool',
				'show_dob_year' => 'bool',
				'show_dob_date' => 'bool',
			],
			'profile' => [
				'location' => 'str',
				'website' => 'str'
			],
			'user' => [
				'custom_title' => 'str'
			],
			'dob_day' => 'uint',
			'dob_month' => 'uint',
			'dob_year' => 'uint'
		]);

		if (!$visitor->hasPermission('general', 'editCustomTitle'))
		{
			unset($input['user']['custom_title']);
		}

		$input['profile']['about'] = $this->plugin('XF:Editor')->fromInput('about');

		$form->basicEntitySave($visitor, $input['user']);

		$userOptions = $visitor->getRelationOrDefault('Option');
		$form->setupEntityInput($userOptions, $input['option']);

		/** @var \XF\Entity\UserProfile $userProfile */
		$userProfile = $visitor->getRelationOrDefault('Profile');
		$form->setup(function() use ($userProfile, $input)
		{
			if (!$userProfile['dob_day'] || !$userProfile['dob_month'] || !$userProfile['dob_year'])
			{
				$userProfile->setDob($input['dob_day'], $input['dob_month'], $input['dob_year']);
			}
		});
		$this->customFieldsSaveProcess($form, 'personal', $userProfile);
		$this->customFieldsSaveProcess($form, 'contact', $userProfile);
		$form->setupEntityInput($userProfile, $input['profile']);

		$form->validate(function(FormAction $form) use($input, $visitor)
		{
			if ($input['profile']['about'] && $visitor->isSpamCheckRequired())
			{
				$checker = $this->app()->spam()->contentChecker();
				$checker->check($visitor, $input['profile']['about'], [
					'content_type' => 'user'
				]);

				$decision = $checker->getFinalDecision();
				switch ($decision)
				{
					case 'moderated':
					case 'denied':
						$checker->logSpamTrigger('user_about', $visitor->user_id);
						$form->logError(\XF::phrase('your_content_cannot_be_submitted_try_later'));
						break;
				}
			}
		});

		$form->complete(function() use ($visitor)
		{
			/** @var \XF\Repository\IP $ipRepo */
			$ipRepo = $this->repository('XF:Ip');
			$ipRepo->logIp($visitor->user_id, $this->request->getIp(), 'user', $visitor->user_id, 'account_details_edit');
		});

		return $form;
	}

	public function actionEmail()
	{
		$visitor = \XF::visitor();
		$auth = $visitor->Auth->getAuthenticationHandler();
		if (!$auth)
		{
			return $this->noPermission();
		}

		if ($this->isPost())
		{
			$this->emailSaveProcess($visitor)->run();

			return $this->redirect($this->buildLink('account/account-details'));
		}
		else
		{
			$viewParams = [
				'hasPassword' => $auth->hasPassword()
			];
			$view = $this->view('XF:Account\Email', 'account_email', $viewParams);

			return $this->addAccountWrapperParams($view, 'account_details');
		}
	}

	protected function emailSaveProcess(\XF\Entity\User $visitor)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'email' => 'str',
			'password' => 'str'
		]);

		if ($input['email'] != $visitor->email)
		{
			/** @var \XF\Service\User\EmailChange $emailChange */
			$emailChange = $this->service('XF:User\EmailChange', $visitor, $input['email']);

			$form->validate(function(FormAction $form) use ($visitor, $input, $emailChange)
			{
				if (!$visitor->authenticate($input['password']))
				{
					$form->logError(\XF::phrase('your_existing_password_is_not_correct'), 'visitor_password');
				}
				else if (!$emailChange->isValid($changeError))
				{
					$form->logError($changeError, 'email');
				}
				else if (!$emailChange->canChangeEmail($error))
				{
					if (!$error)
					{
						$error = \XF::phrase('your_email_may_not_be_changed_at_this_time');
					}
					$form->logError($error, 'email');
				}
			});
			$form->apply(function() use ($emailChange)
			{
				$emailChange->save();
			});
		}

		return $form;
	}

	public function actionSignature()
	{
		$visitor = \XF::visitor();
		if (!$visitor->canEditSignature())
		{
			return $this->noPermission();
		}

		$sigEditor = $this->service('XF:User\SignatureEdit', $visitor);

		if ($this->isPost())
		{
			$signature = $this->plugin('XF:Editor')->fromInput('signature');
			$this->signatureSaveProcess($sigEditor, $signature)->run();

			return $this->redirect($this->buildLink('account/signature'));
		}
		else
		{
			$viewParams = [
				'disabledButtons' => $sigEditor->getDisabledEditorButtons()
			];
			$view = $this->view('XF:Account\Signature', 'account_signature', $viewParams);
			return $this->addAccountWrapperParams($view, 'signature');
		}
	}

	protected function signatureSaveProcess(\XF\Service\User\SignatureEdit $sigEditor, $inputSignature)
	{
		$form = $this->formAction();

		$form->validate(function(FormAction $form) use ($sigEditor, $inputSignature)
		{
			if (!$sigEditor->setSignature($inputSignature, $errors))
			{
				$form->logErrors($errors);
			}
		});
		$form->apply(function() use ($sigEditor)
		{
			$sigEditor->save();
		});

		$visitor = \XF::visitor();
		$form->complete(function() use ($visitor)
		{
			/** @var \XF\Repository\IP $ipRepo */
			$ipRepo = $this->repository('XF:Ip');
			$ipRepo->logIp($visitor->user_id, $this->request->getIp(), 'user', $visitor->user_id, 'signature_edit');
		});

		return $form;
	}

	public function actionPrivacy()
	{
		if ($this->isPost())
		{
			$this->savePrivacyProcess(\XF::visitor())->run();
			return $this->redirect($this->buildLink('account/privacy'));
		}
		else
		{
			$view = $this->view('XF:Account\Privacy', 'account_privacy');
			return $this->addAccountWrapperParams($view, 'privacy');
		}
	}

	protected function savePrivacyProcess(\XF\Entity\User $visitor)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'user' => [
				'visible' => 'bool',
				'activity_visible' => 'bool'
			],
			'option' => [
				'receive_admin_email' => 'bool',
				'show_dob_date' => 'bool',
				'show_dob_year' => 'bool'
			],
			'privacy' => [
				'allow_view_profile' => 'str',
				'allow_post_profile' => 'str',
				'allow_receive_news_feed' => 'str',
				'allow_send_personal_conversation' => 'str',
				'allow_view_identities' => 'str'
			]
		]);

		$form->basicEntitySave($visitor, $input['user']);

		$userOptions = $visitor->getRelationOrDefault('Option');
		$form->setupEntityInput($userOptions, $input['option']);

		$userPrivacy = $visitor->getRelationOrDefault('Privacy');
		$form->setupEntityInput($userPrivacy, $input['privacy']);

		$form->complete(function() use ($visitor)
		{
			/** @var \XF\Repository\IP $ipRepo */
			$ipRepo = $this->repository('XF:Ip');
			$ipRepo->logIp($visitor->user_id, $this->request->getIp(), 'user', $visitor->user_id, 'privacy_edit');
		});

		return $form;
	}

	public function actionPreferences()
	{
		$styles = $this->repository('XF:Style')->getSelectableStyles();

		$defaultStyleId = $this->app->options()->defaultStyleId;
		$defaultStyle = isset($styles[$defaultStyleId]) ? $styles[$defaultStyleId] : null;

		if ($this->isPost())
		{
			$this->preferencesSaveProcess(\XF::visitor())->run();
			return $this->redirect($this->buildLink('account/preferences'));
		}
		else
		{
			/** @var \XF\Repository\Language $languageRepo */
			$languageRepo = $this->repository('XF:Language');
			$languages = $languageRepo->getLanguageTree(false);

			/** @var \XF\Data\TimeZone $tzData */
			$tzData = $this->data('XF:TimeZone');

			$alertRepo = $this->repository('XF:UserAlert');
			$alertOptOuts = $alertRepo->getAlertOptOuts();

			$viewParams = [
				'styles' => $styles,
				'defaultStyle' => $defaultStyle ? $defaultStyle : [],

				'languageTree' => $languages,

				'timeZones' => $tzData->getTimeZoneOptions(),

				'alertOptOuts' => $alertOptOuts
			];

			$view = $this->view('XF:Account\Preferences', 'account_preferences', $viewParams);
			return $this->addAccountWrapperParams($view, 'preferences');
		}
	}

	protected function preferencesSaveProcess(\XF\Entity\User $visitor)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'user' => [
				'style_id' => 'uint',
				'language_id' => 'uint',
				'timezone' => 'str',
				'visible' => 'bool',
				'activity_visible' => 'bool'
			],
			'option' => [
				'creation_watch_state' => 'str',
				'interaction_watch_state' => 'str',
				'content_show_signature' => 'bool',
				'receive_admin_email' => 'bool',
				'email_on_conversation' => 'bool',
				'push_on_conversation' => 'bool'
			],
			'restore_notices' => 'bool'
		]);

		$alertRepo = $this->repository('XF:UserAlert');
		$optOutActions = $alertRepo->getAlertOptOutActions();
		$alert = $this->filter('alert', 'array-bool');
		$push = $this->filter('push', 'array-bool');
		$pushShown = $this->filter('push_shown', 'array-bool');

		$alertOptOuts = [];
		$pushOptOuts = [];
		foreach (array_keys($optOutActions) AS $optOut)
		{
			if (empty($alert[$optOut]))
			{
				$alertOptOuts[$optOut] = $optOut;
			}
			if (empty($push[$optOut]) && isset($pushShown[$optOut]))
			{
				$pushOptOuts[$optOut] = $optOut;
			}
		}

		$input['option']['alert_optout'] = $alertOptOuts;
		$input['option']['push_optout'] = $pushOptOuts;

		$form->basicEntitySave($visitor, $input['user']);

		$userOptions = $visitor->getRelationOrDefault('Option');
		$form->setupEntityInput($userOptions, $input['option']);

		$this->customFieldsSaveProcess($form, 'preferences');

		$form->apply(function() use($input, $visitor)
		{
			if ($input['restore_notices'])
			{
				$this->repository('XF:Notice')->restoreDismissedNotices($visitor);
				$this->session()->remove('dismissedNotices'); // force recache
			}
		});
		$form->complete(function() use ($visitor)
		{
			/** @var \XF\Repository\IP $ipRepo */
			$ipRepo = $this->repository('XF:Ip');
			$ipRepo->logIp($visitor->user_id, $this->request->getIp(), 'user', $visitor->user_id, 'preferences_edit');
		});

		return $form;
	}

	public function actionDismissNotice()
	{
		/** @var \XF\Entity\Notice $notice */
		$notice = $this->assertRecordExists('XF:Notice', $this->filter('notice_id', 'uint'));

		if (!$notice->canDismissNotice($error))
		{
			return $this->error($error);
		}

		if ($this->isPost())
		{
			$this->repository('XF:Notice')->dismissNotice($notice, \XF::visitor());
			$this->session()->remove('dismissedNotices'); // force recache
			return $this->redirect($this->getDynamicRedirect());
		}
		else
		{
			return $this->view('XF:Account\DismissNotice', 'notice_dismiss', ['notice' => $notice]);
		}
	}

	public function actionAvatar()
	{
		$visitor = \XF::visitor();
		if (!$visitor->canUploadAvatar())
		{
			return $this->noPermission();
		}

		if ($this->isPost())
		{
			$useCustom = $this->filter('use_custom', 'bool');
			$message = '';

			/** @var \XF\Service\User\Avatar $avatarService */
			$avatarService = $this->service('XF:User\Avatar', $visitor);

			if ($this->filter('delete_avatar', 'bool'))
			{
				$avatarService->deleteAvatar();
			}
			else if ($useCustom)
			{
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
				else if ($visitor->avatar_date)
				{
					// recrop existing avatar
					$cropX = $this->filter('avatar_crop_x', 'uint');
					$cropY = $this->filter('avatar_crop_y', 'uint');
					if ($cropX != $visitor->Profile->avatar_crop_x || $cropY != $visitor->Profile->avatar_crop_y)
					{
						$avatarService->setImageFromExisting();
						$avatarService->setCrop($cropX, $cropY);
						if (!$avatarService->updateAvatar())
						{
							return $this->error(\XF::phrase('new_avatar_could_not_be_processed'));
						}
					}
					else
					{
						$avatarService->removeGravatar();
					}
				}
			}
			else if ($this->options()->gravatarEnable)
			{
				$gravatar = $this->filter('gravatar', 'str');
				if ($this->filter('test_gravatar', 'str'))
				{
					$gravatarValidator = $this->app->validator('Gravatar');
					if (!$gravatarValidator->isValid($gravatar, $errorKey))
					{
						return $this->error($gravatarValidator->getPrintableErrorValue($errorKey));
					}


					$reply = $this->view('XF:Account\Avatar');
					$reply->setJsonParams([
						'gravatarTest' => $gravatar,
						'gravatarPreview' => $visitor->getGravatarUrl('m', $gravatar)
					]);
					return $reply;
				}

				if (!$avatarService->setGravatar($gravatar))
				{
					return $this->error($avatarService->getError());
				}
			}

			if ($this->filter('_xfWithData', 'bool'))
			{
				return $this->view('XF:Account\AvatarUpdate', '');
			}
			else
			{
				return $this->redirect($this->buildLink('account/avatar'));
			}
		}
		else
		{
			$viewParams = [
				'maxSize' => $this->app->container('avatarSizeMap')['m'],
				'maxDimension' => ($visitor->avatar_width > $visitor->avatar_height ? 'height' : 'width'),
				'x' => $visitor->Profile->avatar_crop_x,
				'y' => $visitor->Profile->avatar_crop_y
			];
			$view = $this->view('XF:Account\Avatar', 'account_avatar', $viewParams);
			return $this->addAccountWrapperParams($view, 'account_details');
		}
	}

	public function actionFollowing()
	{
		$followingUsers = [];
		$visitor = \XF::visitor();
		if ($following = $visitor->Profile->following)
		{
			$followingUsers = $this->finder('XF:User')
				->where('user_id', $following)
				->order('username')
				->fetch();
		}

		$viewParams = [
			'following' => $followingUsers
		];
		$view = $this->view('XF:Account\Following', 'account_following', $viewParams);
		return $this->addAccountWrapperParams($view, 'following');
	}

	public function actionIgnored()
	{
		$visitor = \XF::visitor();
		if ($ignored = $visitor->Profile->ignored)
		{
			$ignoringUsers = $this->finder('XF:User')
				->where('user_id', array_keys($ignored))
				->order('username')
				->fetch();
		}
		else
		{
			$ignoringUsers = [];
		}

		$viewParams = [
			'ignoring' => $ignoringUsers
		];
		$view = $this->view('XF:Account\Ignored', 'account_ignored', $viewParams);
		return $this->addAccountWrapperParams($view, 'ignored');
	}

	public function actionReactions()
	{
		$visitor = \XF::visitor();

		/** @var \XF\Repository\Reaction $reactionRepo */
		$reactionRepo = $this->repository('XF:Reaction');

		$page = $this->filterPage();
		$perPage = 20;

		$typeTotals = $reactionRepo->getUserReactionsTabSummary($visitor);
		$total = array_sum($typeTotals);
		$tabSummary = [0 => $total] + $typeTotals;

		$reactionFinder = $reactionRepo->findUserReactions($visitor)
			->limitByPage($page, $perPage, 1);

		$reactionId = $this->filter('reaction_id', 'uint');

		if ($reactionId)
		{
			$reactionFinder->where('reaction_id', $reactionId);
		}
		else
		{
			// showing all, but limit this to the keys that we got from the total to implicitly skip inactive entries
			$reactionFinder->where('reaction_id', array_keys($typeTotals));
		}

		/** @var ArrayCollection|\XF\Entity\ReactionContent[] $reactions */
		$reactions = $reactionFinder->fetch();
		$reactionRepo->addContentToReactions($reactions);
		$reactions = $reactions->filter(function(\XF\Entity\ReactionContent $reaction)
		{
			return $reaction->canView() && $reaction->isRenderable();
		});

		$hasNext = count($reactions) > $perPage;
		$reactions = $reactions->slice(0, $perPage);

		$this->assertValidPage($page, $perPage, $total, 'account/reactions');

		$viewParams = [
			'tabSummary' => $tabSummary,

			'activeReactionId' => $reactionId,
			'reactions' => $reactions,
			'hasNext' => $hasNext,
			'page' => $page,

			'listOnly' => $this->filter('list_only', 'bool')
		];
		$view = $this->view('XF:Account\Reactions', 'account_reactions', $viewParams);
		return $this->addAccountWrapperParams($view, 'reactions');
	}

	public function actionSecurity()
	{
		$visitor = \XF::visitor();
		$auth = $visitor->Auth->getAuthenticationHandler();
		if (!$auth)
		{
			return $this->noPermission();
		}

		if ($this->isPost())
		{
			$passwordChange = $this->setupPasswordChange();
			if (!$passwordChange->isValid($error))
			{
				return $this->error($error);
			}

			$passwordChange->setInvalidateRememberKeys(false); // about to handle this
			$passwordChange->save();

			$this->plugin('XF:Login')->handleVisitorPasswordChange();

			return $this->redirect($this->buildLink('account/security'));
		}
		else
		{
			/** @var \XF\Repository\Tfa $tfaRepo */
			$tfaRepo = $this->repository('XF:Tfa');
			$enabledProviders = [];
			$userId = \XF::visitor()->user_id;

			foreach ($tfaRepo->getValidProviderList(\XF::visitor()->user_id) AS $provider)
			{
				if ($provider->isEnabled($userId))
				{
					$enabledProviders[] = $provider->getTitle();
				}
			}

			$viewParams = [
				'hasPassword' => $auth->hasPassword(),
				'enabledTfaProviders' => $enabledProviders
			];

			$view = $this->view('XF:Account\Security', 'account_security', $viewParams);
			return $this->addAccountWrapperParams($view, 'security');
		}
	}

	/**
	 * @return \XF\Service\User\PasswordChange
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function setupPasswordChange()
	{
		$input = $this->filter([
			'old_password' => 'str',
			'password' => 'str',
			'password_confirm' => 'str'
		]);

		$visitor = \XF::visitor();

		if (!$visitor->authenticate($input['old_password']))
		{
			throw $this->errorException(\XF::phrase('your_existing_password_is_not_correct'));
		}

		if ($input['password'] !== $input['password_confirm'])
		{
			throw $this->errorException(\XF::phrase('passwords_did_not_match'));
		}

		return $this->service('XF:User\PasswordChange', $visitor, $input['password']);
	}

	public function actionConnectedAccount()
	{
		$visitor = \XF::visitor();
		$auth = $visitor->Auth->getAuthenticationHandler();

		$providers = $this->getConnectedAccountRepo()->getUsableProviders();

		$viewParams = [
			'providers' => $providers,
			'hasPassword' => $auth->hasPassword()
		];
		$view = $this->view('XF:Account\Connected', 'account_connected', $viewParams);
		return $this->addAccountWrapperParams($view, 'connected_account');
	}

	public function actionConnectedAccountDisassociate(ParameterBag $params)
	{
		$this->assertPostOnly();

		$visitor = \XF::visitor();
		$auth = $visitor->Auth->getAuthenticationHandler();
		if (!$auth)
		{
			return $this->noPermission();
		}

		$connectedAccounts = $visitor->ConnectedAccounts;

		$provider = $this->assertProviderExists($params->provider_id);
		$handler = $provider->getHandler();

		/** @var \XF\Entity\UserConnectedAccount $connectedAccount */
		$connectedAccount = isset($connectedAccounts[$provider->provider_id]) ? $connectedAccounts[$provider->provider_id] : null;
		if ($this->filter('disassociate', 'bool') && $connectedAccount)
		{
			$totalConnected = $connectedAccounts->count();

			$connectedAccount->delete();

			if (!$auth->hasPassword() && $totalConnected <= 1)
			{
				$visitor->Auth->resetPassword();
				$this->plugin('XF:Login')->handleVisitorPasswordChange();
				$sendConfirmation = true;
			}
			else
			{
				$sendConfirmation = false;
			}

			$storageState = $handler->getStorageState($provider, $visitor);
			$storageState->clearToken();

			$profile = $visitor->getRelationOrDefault('Profile');
			$profileConnectedAccounts = $profile->connected_accounts;
			unset($profileConnectedAccounts[$provider->provider_id]);
			$profile->connected_accounts = $profileConnectedAccounts;

			$visitor->save();

			if ($sendConfirmation)
			{
				/** @var \XF\Service\User\PasswordReset $passwordConfirmation */
				$passwordConfirmation = $this->service('XF:User\PasswordReset', $visitor);
				$passwordConfirmation->triggerConfirmation();
			}
		}
		return $this->redirect($this->buildLink('account/connected-accounts'));
	}

	public function actionRequestPassword()
	{
		$visitor = \XF::visitor();
		$auth = $visitor->Auth->getAuthenticationHandler();
		if (!$auth)
		{
			return $this->noPermission();
		}

		if ($auth->hasPassword())
		{
			return $this->error(\XF::phrase('your_account_already_has_password'));
		}

		if ($this->isPost())
		{
			$visitor->Auth->resetPassword();
			$passwordConfirmation = $this->service('XF:User\PasswordReset', $visitor);
			$passwordConfirmation->triggerConfirmation();
			return $this->redirect($this->buildLink('account/security'));
		}
		else
		{
			$view = $this->view('XF:Account\RequestPassword', 'account_request_password');
			return $this->addAccountWrapperParams($view, 'security');
		}
	}

	public function actionTwoStep()
	{
		$this->assertTwoStepPasswordVerified();

		/** @var \XF\Repository\Tfa $tfaRepo */
		$tfaRepo = $this->repository('XF:Tfa');

		/** @var \XF\ControllerPlugin\Login $loginPlugin */
		$loginPlugin = $this->plugin('XF:Login');
		$currentTrustKey = $loginPlugin->getCurrentTrustKey();

		/** @var \XF\Repository\UserTfaTrusted $tfaTrustRepo */
		$tfaTrustRepo = $this->repository('XF:UserTfaTrusted');

		$userId = \XF::visitor()->user_id;

		$viewParams = [
			'providers' => $tfaRepo->getValidProviderList($userId),
			'backupAdded' => $this->filter('backup', 'bool'),
			'currentTrustRecord' => $tfaTrustRepo->getTfaTrustRecord($userId, $currentTrustKey),
			'hasOtherTrusted' => $tfaTrustRepo->hasOtherTrustedDevices($userId, $currentTrustKey)
		];
		$view = $this->view('XF:Account\TwoStep', 'account_two_step', $viewParams);
		return $this->addAccountWrapperParams($view, 'security');
	}

	public function actionTwoStepBackupCodes()
	{
		$this->assertTwoStepPasswordVerified();

		/** @var \XF\Entity\TfaProvider $provider */
		$provider = $this->em()->find('XF:TfaProvider', 'backup');
		if (!$provider || !$provider->canManage())
		{
			return $this->redirect($this->buildLink('account/two-step'));
		}

		/** @var \XF\Tfa\Backup $handler */
		$handler = $provider->handler;
		$userConfig = $provider->getUserProviderConfig();
		if (!$userConfig || empty($userConfig['codes']))
		{
			return $this->redirect($this->buildLink('account/two-step'));
		}

		$viewParams = [
			'codes' => $handler->formatCodesForDisplay($userConfig['codes'])
		];
		$view = $this->view('XF:Account\TwoStepBackupCodes', 'account_two_step_backup_codes', $viewParams);
		return $this->addAccountWrapperParams($view, 'security');
	}

	public function actionTwoStepEnable(ParameterBag $params)
	{
		$this->assertTwoStepPasswordVerified();
		$this->assertPostOnly();

		/** @var \XF\Entity\TfaProvider $provider */
		$provider = $this->em()->find('XF:TfaProvider', $params->provider_id);
		if (!$provider || !$provider->canEnable())
		{
			return $this->redirect($this->buildLink('account/two-step'));
		}

		$visitor = \XF::visitor();

		/** @var \XF\Tfa\AbstractProvider $handler */
		$handler = $provider->handler;
		if (!$handler->meetsRequirements($visitor, $error))
		{
			return $this->error($error);
		}

		$sessionKey = 'tfaData_' . $provider->provider_id;
		$session = $this->session();

		$step = $this->filter('step', 'str');

		if ($step == 'confirm')
		{
			$providerData = $session->get($sessionKey);
			if (!is_array($providerData))
			{
				return $this->redirect($this->buildLink('account/two-step'));
			}

			if (!$handler->verify('setup', $visitor, $providerData, $this->request))
			{
				return $this->error(\XF::phrase('two_step_verification_value_could_not_be_confirmed'));
			}

			/** @var \XF\Repository\Tfa $tfaRepo */
			$tfaRepo = $this->repository('XF:Tfa');
			$tfaRepo->enableUserTfaProvider($visitor, $provider, $providerData, $backupAdded);

			/** @var \XF\Repository\IP $ipRepo */
			$ipRepo = $this->repository('XF:Ip');
			$ipRepo->logIp($visitor->user_id, $this->request->getIp(), 'user', $visitor->user_id, 'tfa_enable');

			$session->remove($sessionKey);

			return $this->redirect($this->buildLink('account/two-step', null, ['backup' => $backupAdded ? 1 : null]));
		}

		$providerData = [];

		if ($handler->requiresConfig())
		{
			$result = $handler->handleConfig($this, $provider, \XF::visitor(), $providerData);
			if ($result)
			{
				if ($result instanceof \XF\Mvc\Reply\View)
				{
					$result = $this->addAccountWrapperParams($result, 'security');
				}
				return $result;
			}
		}

		$providerData = $handler->generateInitialData($visitor, $providerData);
		$triggerData = $handler->trigger('setup', $visitor, $providerData, $this->request);

		$session->set($sessionKey, $providerData);

		$viewParams = [
			'provider' => $provider,
			'handler' => $handler,
			'providerData' => $providerData,
			'triggerData' => $triggerData
		];
		$view = $this->view('XF:Account\TwoStepEnable', 'account_two_step_enable', $viewParams);
		return $this->addAccountWrapperParams($view, 'security');
	}

	public function actionTwoStepDisable(ParameterBag $params)
	{
		$this->assertTwoStepPasswordVerified();

		if ($params->provider_id)
		{
			/** @var \XF\Entity\TfaProvider $provider */
			$provider = $this->em()->find('XF:TfaProvider', $params->provider_id);
			if (!$provider || !$provider->canDisable())
			{
				return $this->redirect($this->buildLink('account/two-step'));
			}
		}
		else
		{
			$provider = null;
		}

		if ($this->isPost())
		{
			$visitor = \XF::visitor();

			if ($provider)
			{
				/** @var \XF\Entity\UserTfa|null $userTfa */
				$userTfa = $provider->UserEntries[\XF::visitor()->user_id];
				if ($userTfa)
				{
					$userTfa->delete();
				}
			}
			else
			{
				/** @var \XF\Repository\Tfa $tfaRepo */
				$tfaRepo = $this->repository('XF:Tfa');
				$tfaRepo->disableTfaForUser(\XF::visitor());
			}

			/** @var \XF\Repository\IP $ipRepo */
			$ipRepo = $this->repository('XF:Ip');
			$ipRepo->logIp($visitor->user_id, $this->request->getIp(), 'user', $visitor->user_id, 'tfa_disable');

			return $this->redirect($this->buildLink('account/two-step'));
		}
		else
		{
			$viewParams = [
				'provider' => $provider
			];
			$view = $this->view('XF:Account\TwoStepDisable', 'account_two_step_disable', $viewParams);
			return $this->addAccountWrapperParams($view, 'security');
		}
	}

	public function actionTwoStepManage(ParameterBag $params)
	{
		$this->assertTwoStepPasswordVerified();

		/** @var \XF\Entity\TfaProvider $provider */
		$provider = $this->em()->find('XF:TfaProvider', $params->provider_id);
		if (!$provider || !$provider->canManage())
		{
			return $this->redirect($this->buildLink('account/two-step'));
		}

		/** @var \XF\Tfa\AbstractProvider $handler */
		$handler = $provider->handler;
		$result = $handler->handleManage($this, $provider, \XF::visitor(), $provider->getUserProviderConfig());
		if (!$result)
		{
			return $this->redirect($this->buildLink('account/two-step'));
		}

		if ($result instanceof \XF\Mvc\Reply\View)
		{
			$result = $this->addAccountWrapperParams($result, 'security');
		}
		return $result;
	}

	public function actionTwoStepTrustedDisable()
	{
		$this->assertPostOnly();
		$this->assertTwoStepPasswordVerified();

		/** @var \XF\Repository\UserTfaTrusted $tfaTrustRepo */
		$tfaTrustRepo = $this->repository('XF:UserTfaTrusted');

		/** @var \XF\ControllerPlugin\Login $loginPlugin */
		$loginPlugin = $this->plugin('XF:Login');
		$currentTrustKey = $loginPlugin->getCurrentTrustKey();

		$userId = \XF::visitor()->user_id;

		if ($this->filter('others', 'bool'))
		{
			$tfaTrustRepo->untrustOtherDevices($userId, $currentTrustKey);
		}
		else
		{
			$tfaTrustRepo->untrustDevice($userId, $currentTrustKey);
		}

		return $this->redirect($this->buildLink('account/two-step'));
	}

	protected function assertTwoStepPasswordVerified()
	{
		$this->assertPasswordVerified(3600, null, function($view)
		{
			return $this->addAccountWrapperParams($view, 'security');
		});
	}

	public function actionUpgrades()
	{
		$purchasable = $this->em()->find('XF:Purchasable', 'user_upgrade', 'AddOn');
		if (!$purchasable->isActive())
		{
			return $this->message(\XF::phrase('no_account_upgrades_can_be_purchased_at_this_time'));
		}

		$upgradeRepo = $this->repository('XF:UserUpgrade');
		list ($available, $purchased) = $upgradeRepo->getFilteredUserUpgradesForList();

		if (!$available && !$purchased)
		{
			return $this->message(\XF::phrase('no_account_upgrades_can_be_purchased_at_this_time'));
		}

		if (\XF::visitor()->user_state != 'valid')
		{
			return $this->error(\XF::phrase('account_upgrades_cannot_be_purchased_account_unconfirmed'));
		}

		/** @var \XF\Repository\Payment $paymentRepo */
		$paymentRepo = $this->repository('XF:Payment');
		$profiles = $paymentRepo->getPaymentProfileOptionsData();

		$viewParams = [
			'available' => $available,
			'purchased' => $purchased,
			'profiles' => $profiles
		];
		$view = $this->view('XF:Account\Upgrades', 'account_upgrades', $viewParams);
		return $this->addAccountWrapperParams($view, 'upgrades');
	}

	public function actionUpgradePurchase()
	{
		$view = $this->view('XF:Account\UpgradePurchase', 'account_upgrade_purchase');
		return $this->addAccountWrapperParams($view, 'upgrades');
	}

	public function actionNewsFeed()
	{
		return $this->redirectPermanently(
			$this->buildLink('whats-new/news-feed')
		);
	}

	public function actionAlerts()
	{
		$visitor = \XF::visitor();

		$page = $this->filterPage();
		$perPage = $this->options()->alertsPerPage;

		/** @var \XF\Repository\UserAlert $alertRepo */
		$alertRepo = $this->repository('XF:UserAlert');

		$alertsFinder = $alertRepo->findAlertsForUser($visitor->user_id);
		$alerts = $alertsFinder->limitByPage($page, $perPage)->fetch();

		$alertRepo->addContentToAlerts($alerts);
		$alerts = $alerts->filterViewable();

		$skipMarkRead = $this->filter('skip_mark_read', 'bool');
		if ($page == 1 && $visitor->alerts_unread && !$skipMarkRead)
		{
			$alertRepo->markUserAlertsRead($visitor);
		}

		$viewParams = [
			'alerts' => $alerts,

			'page' => $page,
			'perPage' => $perPage,
			'totalAlerts' => $alertsFinder->total()
		];
		$view = $this->view('XF:Account\Alerts', 'account_alerts', $viewParams);
		return $this->addAccountWrapperParams($view, 'alerts');
	}

	public function actionAlertsPopup()
	{
		$visitor = \XF::visitor();

		/** @var \XF\Repository\UserAlert $alertRepo */
		$alertRepo = $this->repository('XF:UserAlert');

		$cutOff = \XF::$time - $this->options()->alertsPopupExpiryHours * 3600;
		$alertsFinder = $alertRepo->findAlertsForUser($visitor->user_id, $cutOff);
		$alerts = $alertsFinder->fetch(25);

		$alertRepo->addContentToAlerts($alerts);
		$alerts = $alerts->filterViewable();

		if ($visitor->alerts_unread)
		{
			$alertRepo->markUserAlertsRead($visitor);
		}

		$viewParams = [
			'alerts' => $alerts
		];
		return $this->view('XF:Account\AlertsPopup', 'account_alerts_popup', $viewParams);
	}

	public function actionBookmarks()
	{
		$visitor = \XF::visitor();

		if (!$visitor->canViewBookmarks())
		{
			return $this->noPermission();
		}

		if ($this->isPost())
		{
			$label = $this->filter('label', 'str');
			return $this->redirect($this->buildLink('account/bookmarks', null, ['label' => $label]));
		}

		$page = $this->filterPage();
		$perPage = 20;

		/** @var \XF\Repository\Bookmark $bookmarkRepo */
		$bookmarkRepo = $this->repository('XF:Bookmark');

		$label = $this->filter('label', 'str');
		if ($label)
		{
			$bookmarksFinder = $bookmarkRepo->findBookmarksForUserByLabel($visitor->user_id, $label);
		}
		else
		{
			$bookmarksFinder = $bookmarkRepo->findBookmarksForUser($visitor->user_id);
		}

		// quite a big over-fetch but should mostly avoid pagination
		// issues resulting from invisible content
		$bookmarks = $bookmarksFinder->limitByPage($page, $perPage, $perPage * 4)->fetch();

		$bookmarkRepo->addContentToBookmarks($bookmarks);
		$viewableBookmarks = $bookmarks->filterViewable();
		$difference = min($bookmarks->count(), 20) - min($viewableBookmarks->count(), 20);
		$bookmarks = $viewableBookmarks->slice($this->filter('difference', 'uint', 0), $perPage);

		$labelFinder = $bookmarkRepo->findLabelsForUser($visitor->user_id);
		$labels = $labelFinder->fetch()->pluckNamed('label', 'label');

		$viewParams = [
			'bookmarks' => $bookmarks,
			'label' => $label,
			'allLabels' => $labels,

			'page' => $page,
			'perPage' => $perPage,
			'totalBookmarks' => $bookmarksFinder->total(),

			'paginationDifference' => $difference
		];
		$view = $this->view('XF:Account\Bookmarks', 'account_bookmarks', $viewParams);
		return $this->addAccountWrapperParams($view, 'bookmarks');
	}

	public function actionBookmarksPopup()
	{
		$visitor = \XF::visitor();

		if (!$visitor->canViewBookmarks())
		{
			return $this->noPermission();
		}

		/** @var \XF\Repository\Bookmark $bookmarkRepo */
		$bookmarkRepo = $this->repository('XF:Bookmark');

		$label = $this->filter('label', 'str');
		if ($label)
		{
			$bookmarksFinder = $bookmarkRepo->findBookmarksForUserByLabel($visitor->user_id, $label);
		}
		else
		{
			$bookmarksFinder = $bookmarkRepo->findBookmarksForUser($visitor->user_id);
		}

		$bookmarks = $bookmarksFinder->fetch(25);

		$bookmarkRepo->addContentToBookmarks($bookmarks);
		$bookmarks = $bookmarks->filterViewable();

		$labelFinder = $bookmarkRepo->findLabelsForUser($visitor->user_id);
		$labels = $labelFinder->fetch()->pluckNamed('label', 'label');

		$viewParams = [
			'bookmarks' => $bookmarks,
			'label' => $label,
			'allLabels' => $labels
		];
		return $this->view('XF:Account\BookmarksPopup', 'account_bookmarks_popup', $viewParams);
	}

	public function actionBookmarksAutoComplete()
	{
		$visitor = \XF::visitor();

		if (!$visitor->canViewBookmarks())
		{
			return $this->noPermission();
		}

		$q = $this->filter('q', 'str');

		if (strlen($q) >= 2)
		{
			$labels = $this->repository('XF:Bookmark')->getLabelAutoCompleteResults($q);

			$results = [];
			foreach ($labels AS $label)
			{
				$results[] = [
					'id' => $label->label,
					'text' => $label->label,
					'q' => $q
				];
			}
		}
		else
		{
			$results = [];
		}
		$view = $this->view();
		$view->setJsonParam('results', $results);
		return $view;
	}

	public function actionVisitorMenu()
	{
		$viewParams = [];
		return $this->view('XF:Account\VisitorMenu', 'account_visitor_menu', $viewParams);
	}

	protected function customFieldsSaveProcess(FormAction $form, $group, \XF\Entity\UserProfile $userProfile = null, $entitySave = false)
	{
		if ($userProfile === null)
		{
			$userProfile = \XF::visitor()->getRelationOrDefault('Profile');
		}

		/** @var \XF\CustomField\Set $fieldSet */
		$fieldSet = $userProfile->custom_fields;
		$fieldDefinition = $fieldSet->getDefinitionSet()
			->filterGroup($group)
			->filterEditable($fieldSet, 'user');

		$customFields = $this->filter('custom_fields', 'array');
		$customFieldsShown = array_keys($fieldDefinition->getFieldDefinitions());

		if ($customFieldsShown)
		{
			$form->setup(function() use ($fieldSet, $customFields, $customFieldsShown)
			{
				$fieldSet->bulkSet($customFields, $customFieldsShown);
			});
		}

		if ($entitySave)
		{
			$form->validateEntity($userProfile)->saveEntity($userProfile);
		}
	}

	public function checkCsrfIfNeeded($action, ParameterBag $params)
	{
		if (strtolower($action) == 'upgradepurchase')
		{
			return;
		}

		parent::checkCsrfIfNeeded($action, $params);
	}

	public function assertViewingPermissions($action) {}
	public function assertTfaRequirement($action) {}

	public function assertPolicyAcceptance($action)
	{
		$action = strtolower($action);

		if (strpos($action, 'twostep') === 0)
		{
			return;
		}

		switch ($action)
		{
			case 'dismissnotice':
			case 'visitormenu':
				break;

			default:
				parent::assertPolicyAcceptance($action);
		}
	}

	public function assertBoardActive($action)
	{
		switch (strtolower($action))
		{
			case 'dismissnotice':
			case 'visitormenu':
				break;

			default:
				parent::assertBoardActive($action);
		}
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('managing_account_details');
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\ConnectedAccountProvider
	 */
	protected function assertProviderExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:ConnectedAccountProvider', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\ConnectedAccount
	 */
	protected function getConnectedAccountRepo()
	{
		return $this->repository('XF:ConnectedAccount');
	}
}