<?php

namespace XF\Admin;

use XF\Container;
use XF\HTTP\Response;
use XF\Mvc\Renderer\AbstractRenderer;
use XF\Mvc\Reply\AbstractReply;

class App extends \XF\App
{
	protected $preLoadLocal = [
		'adminNavigation',
		'masterStyleModifiedDate',
		'routesAdmin',
		'routesPublic',
		'routesApi',
		'routeFilters'
	];

	public function initializeExtra()
	{
		$container = $this->container;

		$container['app.classType'] = 'Admin';
		$container['app.defaultType'] = 'admin';
		$container['job.manual.allow'] = true;

		$container['router'] = function (Container $c)
		{
			return $c['router.admin'];
		};
		$container['session'] = function (Container $c)
		{
			return $c['session.admin'];
		};
	}

	public function setup(array $options = [])
	{
		parent::setup($options);
		$this->assertConfigExists();

		$this->fire('app_admin_setup', [$this]);
	}

	public function start($allowShortCircuit = false)
	{
		parent::start($allowShortCircuit);

		$this->fire('app_admin_start_begin', [$this]);

		$user = $this->getVisitorFromSession($this->session(), ['Admin']);
		\XF::setVisitor($user);

		if ($user->user_id)
		{
			if ($user->Admin && $user->Admin->admin_language_id)
			{
				$languageId = $user->Admin->admin_language_id;
			}
			else
			{
				$languageId = $user->language_id;
			}

			$language = $this->language($languageId);
			$language->setTimeZone($user->timezone);
			\XF::setLanguage($language);
		}

		$this->fire('app_admin_start_end', [$this]);
	}

	public function complete(Response $response)
	{
		parent::complete($response);

		if ($this->container->isCached('session'))
		{
			$session = $this->session();

			if ($session->isStarted() && $session->hasData())
			{
				$session->save();
				$session->applyToResponse($response);
			}
		}

		$this->fire('app_admin_complete', [$this, &$response]);
	}

	public function preRender(AbstractReply $reply, $responseType)
	{
		$style = $this->container['style.fallback'];
		$this->templater()->setStyle($style);

		parent::preRender($reply, $responseType);
	}

	public function renderPage($content, AbstractReply $reply, AbstractRenderer $renderer)
	{
		$response = $renderer->getResponse();
		if ($response->httpCode() >= 300 && $response->httpCode() <= 307)
		{
			if ($this->container->isCached('job.manager') && $this->jobManager()->hasManualEnqueued())
			{
				$pageParams = $renderer->getTemplater()->pageParams;
				if (empty($pageParams['skipManualJobRun']))
				{
					$onlyIds = implode(',', array_keys($this->jobManager()->getManualEnqueued()));

					$url = $response->redirect();
					$response->redirect(
						'admin.php?tools/run-job&only_ids=' . urlencode($onlyIds) . '&_xfRedirect=' . urlencode($url),
						303
					);
				}
			}
		}

		return parent::renderPage($content, $reply, $renderer);
	}

	protected function renderPageHtml($content, array $params, AbstractReply $reply, AbstractRenderer $renderer)
	{
		$templateName = isset($params['template']) ? $params['template'] : 'PAGE_CONTAINER';

		$viewOptions = $reply->getViewOptions();
		if (!empty($viewOptions['force_page_template']))
		{
			$templateName = $viewOptions['force_page_template'];
		}

		if (!$templateName)
		{
			return $content;
		}

		if (!\XF::visitor()->is_admin)
		{
			$templateName = 'LOGIN_CONTAINER';
		}

		if (!strpos($templateName, ':'))
		{
			$templateName = 'admin:' . $templateName;
		}

		if ($reply instanceof \XF\Mvc\Reply\View)
		{
			$params['view'] = $reply->getViewClass();
			$params['template'] = $reply->getTemplateName();
		}

		/** @var \XF\AdminNavigation $nav */
		$nav = $this->container['navigation.admin'];
		$navigation = $nav->getTree();

		$sectionContext = isset($params['section']) ? $params['section'] : $reply->getSectionContext();
		$path = $navigation->getPathTo($sectionContext, true);

		if ($path)
		{
			$sectionGroup = reset($path);
			$selectedTab = $sectionGroup['navigation_id'];
		}
		else
		{
			$selectedTab = null;
		}

		$breadcrumbPath = isset($params['breadcrumbPath']) ? $navigation->getPathTo($params['breadcrumbPath'], true) : $path;
		if ($breadcrumbPath)
		{
			$finalBreadcrumb = end($breadcrumbPath);

			if (isset($params['skipBreadcrumb']))
			{
				$skipBreadcrumb = $params['skipBreadcrumb'];
			}
			else if ($finalBreadcrumb['link']
				&& $this->request()->getRequestUri() == $this->router()->buildLink($finalBreadcrumb['link'])
			)
			{
				$skipBreadcrumb = $finalBreadcrumb['navigation_id'];
			}
			else
			{
				$skipBreadcrumb = [];
			}

			if ($skipBreadcrumb === true)
			{
				$breadcrumbPath = [];
			}
			else
			{
				if (!is_array($skipBreadcrumb))
				{
					$skipBreadcrumb = [$skipBreadcrumb];
				}
				foreach ($skipBreadcrumb AS $skip)
				{
					unset($breadcrumbPath[$skip]);
				}
			}

			$appendBreadcrumbs = !empty($params['breadcrumbs']) ? $params['breadcrumbs'] : [];
			$breadcrumbs = $this->getBreadcrumbs($breadcrumbPath);
			$breadcrumbs = array_merge($breadcrumbs, $appendBreadcrumbs);
		}
		else
		{
			$breadcrumbs = !empty($params['breadcrumbs']) ? $params['breadcrumbs'] : [];
		}

		$params['controller'] = $reply->getControllerClass();

		$params['action'] = $reply->getAction();
		$params['actionMethod'] = 'action' . \XF\Util\Php::camelCase($reply->getAction(), '-');

		$params['content'] = $content;
		$params['navigation'] = $navigation;
		$params['selectedNavTab'] = $selectedTab;
		$params['selectedNavLink'] = $sectionContext;
		$params['breadcrumbs'] = $breadcrumbs;

		$params['upgradePending'] = (
			\XF::$debugMode
			&& \XF::$versionId != $this->options()->currentVersionId
		);
		$params['listenersDisabled'] = $this->config('enableListeners') ? false : true;
		$params['mailDisabled'] = $this->config('enableMail') ? false : true;

		/** @var \XF\Repository\UpgradeCheck $upgradeCheckRepo */
		$upgradeCheckRepo = $this->repository('XF:UpgradeCheck');
		$upgradeCheck = $upgradeCheckRepo->canCheckForUpgrades() ? $upgradeCheckRepo->getLatestUpgradeCheck() : null;
		$params['upgradeCheck'] = $upgradeCheck;

		$this->fire('app_admin_render_page', [$this, &$params, &$reply, &$renderer]);

		$rendered = $this->templater()->renderTemplate($templateName, $params);

		return $rendered;
	}

	protected function getBreadcrumbs(array $breadcrumbPath)
	{
		$router = $this->router();
		$breadcrumbs = [];
		foreach ($breadcrumbPath AS $crumb)
		{
			if (!$crumb['link'])
			{
				continue;
			}

			$breadcrumbs[] = [
				'value' => $crumb['title'],
				'href' => $router->buildLink($crumb['link'])
			];
		}

		return $breadcrumbs;
	}
}