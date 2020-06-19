<?php

namespace XF\Pub\Controller;

class Misc extends AbstractController
{
	protected function setupContactService()
	{
		/** @var \XF\Service\Contact $contactService */
		$contactService = $this->service('XF:Contact');

		$visitor = \XF::visitor();

		$input = $this->filter([
			'username' => 'str',
			'email' => 'str',
			'subject' => 'str',
			'message' => 'str'
		]);

		if ($visitor->user_id)
		{
			$contactService->setFromUser($visitor);
			if (!$visitor->email)
			{
				if (!$contactService->setEmail($input['email'], $error))
				{
					throw $this->exception($this->error($error));
				}
			}
		}
		else
		{
			$contactService->setFromGuest($input['username'], $input['email']);
		}

		$contactService
			->setMessageDetails($input['subject'], $input['message'])
			->setFromIp($this->request->getIp());

		return $contactService;
	}

	public function actionContact()
	{
		$options = $this->options();
		if ($options->contactUrl['type'] == 'custom')
		{
			return $this->redirect($options->contactUrl['custom']);
		}
		else if (!$options->contactUrl['type'])
		{
			return $this->redirect($this->buildLink('index'));
		}

		if (!\XF::visitor()->canUseContactForm())
		{
			return $this->noPermission();
		}

		$redirect = $this->getDynamicRedirect(null, false);

		if ($this->isPost())
		{
			$contactService = $this->setupContactService();

			if (!$this->captchaIsValid())
			{
				return $this->error(\XF::phrase('did_not_complete_the_captcha_verification_properly'));
			}

			$contactService->checkForSpam();

			if (!$contactService->validate($errors))
			{
				return $this->error($errors);
			}

			$this->assertNotFlooding('contact');

			$contactService->send();

			return $this->redirect($redirect, \XF::phrase('your_message_has_been_sent'));
		}
		else
		{
			$viewParams = [
				'redirect' => $redirect
			];
			return $this->view('XF:Misc\Contact', 'contact_form', $viewParams);
		}
	}

	public function actionLanguage()
	{
		$visitor = \XF::visitor();
		if (!$visitor->canChangeLanguage($error))
		{
			return $this->noPermission($error);
		}

		$redirect = $this->getDynamicRedirect(null, true);

		if ($this->request->exists('language_id'))
		{
			$this->assertValidCsrfToken($this->filter('t', 'str'));

			$language = $this->app->language($this->filter('language_id', 'uint'));

			if ($visitor->user_id)
			{
				$visitor->language_id = $language->getId();
				$visitor->save();

				$this->app->response()->setCookie('language_id', false);
			}
			else
			{
				$this->app->response()->setCookie('language_id', $language->getId());
			}
			return $this->redirect($redirect);
		}
		else
		{
			$viewParams = [
				'redirect' => $redirect,
				'languageTree' => $this->repository('XF:Language')->getLanguageTree(false)
			];
			return $this->view('XF:Misc\Language', 'language_chooser', $viewParams);
		}
	}

	public function actionStyle()
	{
		$visitor = \XF::visitor();
		if (!$visitor->canChangeStyle($error))
		{
			return $this->noPermission($error);
		}

		$redirect = $this->getDynamicRedirect(null, true);

		$csrfValid = true;
		if ($visitor->user_id)
		{
			$csrfValid = $this->validateCsrfToken($this->filter('t', 'str'));
		}

		if ($this->request->exists('style_id') && $csrfValid)
		{
			$styleId = $this->filter('style_id', 'uint');
			$style = $this->app->style($styleId);

			if ($style['user_selectable'] || $visitor->is_admin)
			{
				if ($visitor->user_id)
				{
					$visitor->style_id = $styleId;
					$visitor->save();

					$this->app->response()->setCookie('style_id', false);
				}
				else
				{
					$this->app->response()->setCookie('style_id', $style->getId());
				}
			}
			return $this->redirect($redirect);
		}
		else
		{
			$styles = $this->repository('XF:Style')->getSelectableStyles();

			$styleId = $this->filter('style_id', 'uint');
			if ($styleId && !empty($styles[$styleId]['user_selectable']))
			{
				$style = $styles[$styleId];
			}
			else
			{
				$style = false;
			}

			$viewParams = [
				'redirect' => $redirect,
				'style' => $style,
				'styles' => $styles
			];
			return $this->view('XF:Misc\Style', 'style_chooser', $viewParams);
		}
	}

	public function actionCaptcha()
	{
		$withRow = $this->filter('with_row', 'bool');
		$rowType = preg_replace('#[^a-z0-9_ -]#i', '', $this->filter('row_type', 'str'));

		return $this->view('XF:Misc\Captcha', 'captcha', [
			'withRow' => $withRow,
			'rowType' => $rowType
		]);
	}

	public function actionIpInfo()
	{
		if (!\XF::visitor()->canViewIps())
		{
			return $this->noPermission();
		}

		$ip = $this->filter('ip', 'str');
		$url = $this->options()->ipInfoUrl;

		if (strpos($url, '{ip}') === false)
		{
			$url = 'https://whatismyipaddress.com/ip/{ip}';
		}

		return $this->redirectPermanently(str_replace('{ip}', urlencode($ip), $url));
	}

	public function actionLocationInfo()
	{
		$location = $this->filter('location', 'str');

		$url = $this->options()->geoLocationUrl;
		if (strpos($url, '{location}') === false)
		{
			$url = 'https://maps.google.com/maps?q={location}';
		}

		return $this->redirectPermanently(str_replace('{location}', urlencode($location), $url));
	}

	public function actionTagAutoComplete()
	{
		if (!$this->options()->enableTagging)
		{
			return $this->noPermission();
		}

		$tagRepo = $this->repository('XF:Tag');

		$q = $this->filter('q', 'str');
		$q = $tagRepo->normalizeTag($q);

		if (strlen($q) >= 2)
		{
			$tags = $this->repository('XF:Tag')->getTagAutoCompleteResults($q);
			
			$results = [];
			foreach ($tags AS $tag)
			{
				$results[] = [
					'id' => $tag->tag,
					'text' => $tag->tag,
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

	public function actionCodeEditorModeLoader()
	{
		$language = $this->filter('language', 'str');

		/** @var \XF\ControllerPlugin\CodeEditor $plugin */
		$plugin = $this->plugin('XF:CodeEditor');

		return $plugin->actionModeLoader($language);
	}

	public function actionAcceptPrivacyPolicy()
	{
		$visitor = \XF::visitor();
		$lastUpdate = $this->options()->privacyPolicyLastUpdate;

		if (!$visitor->user_id || !$lastUpdate)
		{
			return $this->noPermission();
		}

		if ($visitor->privacy_policy_accepted > $lastUpdate)
		{
			return $this->redirect(
				$this->getDynamicRedirectIfNot(
					$this->buildLink('misc/accept-privacy-policy')
				)
			);
		}

		if ($this->isPost())
		{
			if (!$this->filter('accept', 'bool'))
			{
				return $this->error(\XF::phrase('please_read_and_accept_our_privacy_policy_before_continuing'));
			}

			$visitor->privacy_policy_accepted = time();
			$visitor->save();

			return $this->redirect($this->getDynamicRedirect());
		}
		else
		{
			$privacyPolicyOption = $this->options()->privacyPolicyUrl;

			$viewParams = [
				'privacyPolicyOption' => $privacyPolicyOption
			];

			if ($privacyPolicyOption['type'] == 'default')
			{
				/** @var \XF\Entity\HelpPage $page */
				$page = $this->finder('XF:HelpPage')
					->where('page_name', 'privacy-policy')
					->fetchOne();
				if (!$page)
				{
					return $this->notFound();
				}

				$viewParams['page'] = $page;
				$viewParams['templateName'] = 'public:_help_page_' . $page->page_id;
			}

			return $this->view('XF:Misc\AcceptPrivacyPolicy', 'accept_privacy_policy', $viewParams);
		}
	}

	public function actionAcceptTerms()
	{
		$visitor = \XF::visitor();
		$lastUpdate = $this->options()->termsLastUpdate;

		if (!$visitor->user_id || !$lastUpdate)
		{
			return $this->noPermission();
		}

		if ($visitor->terms_accepted > $lastUpdate)
		{
			return $this->redirect(
				$this->getDynamicRedirectIfNot(
					$this->buildLink('misc/accept-terms')
				)
			);
		}

		if ($this->isPost())
		{
			if (!$this->filter('accept', 'bool'))
			{
				return $this->error(\XF::phrase('please_read_and_accept_our_terms_and_rules_before_continuing'));
			}

			$visitor->terms_accepted = time();
			$visitor->save();

			return $this->redirect($this->getDynamicRedirect());
		}
		else
		{
			$termsOption = $this->options()->tosUrl;

			$viewParams = [
				'termsOption' => $termsOption
			];

			if ($termsOption['type'] == 'default')
			{
				/** @var \XF\Entity\HelpPage $page */
				$page = $this->finder('XF:HelpPage')
					->where('page_name', 'terms')
					->fetchOne();
				if (!$page)
				{
					return $this->notFound();
				}

				$viewParams['page'] = $page;
				$viewParams['templateName'] = 'public:_help_page_' . $page->page_id;
			}

			return $this->view('XF:Misc\AcceptTerms', 'accept_terms', $viewParams);
		}
	}

	public function actionUpdatePushSubscription()
	{
		$this->assertPostOnly();

		$visitor = \XF::visitor();

		$subscription = $this->filter([
			'endpoint' => 'str',
			'unsubscribed' => 'bool',
			'key' => 'str',
			'token' => 'str',
			'encoding' => 'str'
		]);

		/** @var \XF\Repository\UserPush $userPushRepo */
		$userPushRepo = $this->repository('XF:UserPush');

		if (!$userPushRepo->validateSubscriptionDetails($subscription, $validationError))
		{
			return $this->error($validationError ?: \XF::phrase('invalid_subscription_endpoint_provided'));
		}

		if ($subscription['unsubscribed'])
		{
			if ($visitor->user_id)
			{
				$userPushRepo->deleteUserPushSubscription($visitor, $subscription);
			}
			else
			{
				$userPushRepo->deletePushSubscription($subscription);
			}
		}
		else if ($visitor->user_id && $this->options()->enablePush)
		{
			$userPushRepo->insertUserPushSubscription($visitor, $subscription);
			$userPushRepo->limitUserPushSubscriptionCount($visitor, 20);
		}

		return $this->message(\XF::phrase('push_subscription_updated_successfully'));
	}

	public function actionFindEmoji()
	{
		$q = ltrim($this->filter('q', 'str', ['no-trim']));

		if ($q !== '' && utf8_strlen($q) >= 2)
		{
			/** @var \XF\Repository\Emoji $emojiRepo */
			$emojiRepo = $this->repository('XF:Emoji');
			$results = $emojiRepo->getMatchingEmojiByString($q);
		}
		else
		{
			$results = [];
			$q = '';
		}

		$viewParams = [
			'q' => $q,
			'results' => $results
		];
		return $this->view('XF:Misc\FindEmoji', '', $viewParams);
	}

	public static function getActivityDetails(array $activities)
	{
		$output = [];

		foreach ($activities AS $key => $activity)
		{
			if (strtolower($activity->controller_action) == 'contact')
			{
				$output[$key] = \XF::phrase('contacting_staff');
			}
			else
			{
				$output[$key] = false;
			}
		}

		return $output;
	}

	public function assertNotRejected($action)
	{
		if (strtolower($action) == 'contact')
		{
			// bypass rejection for the default contact form
		}
		else
		{
			parent::assertNotRejected($action);
		}
	}

	public function assertNotDisabled($action)
	{
		if (strtolower($action) == 'contact')
		{
			// bypass disabled notice for the default contact form
		}
		else
		{
			parent::assertNotRejected($action);
		}
	}

	public function assertViewingPermissions($action)
	{
		if (strtolower($action) == 'contact')
		{
			// bypass viewing permissions for the default contact form
		}
		else
		{
			parent::assertViewingPermissions($action);
		}
	}

	public function assertPolicyAcceptance($action) {}

	public function actionException()
	{
		throw $this->exception($this->error('ERROR'));
	}
}