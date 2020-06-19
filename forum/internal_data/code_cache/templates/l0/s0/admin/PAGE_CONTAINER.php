<?php
// FROM HASH: 1d6d8ed532866c360dec384e3054c4ef
return array('macros' => array('navigation_list' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'children' => '!',
		'selectedTab' => '!',
		'selectedLink' => '!',
		'navigation' => '',
		'depth' => '1',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	if (!$__templater->test($__vars['children'], 'empty', array())) {
		$__finalCompiled .= '
		';
		if ($__vars['depth'] == 1) {
			$__finalCompiled .= '
			<ul class="p-nav-listRoot" id="js-navAccordion">
		';
		} else if ($__vars['depth'] == 2) {
			$__finalCompiled .= '
			<ul class="p-nav-listSection ' . (($__vars['navigation']['navigation_id'] == $__vars['selectedTab']) ? 'is-active' : '') . '">
		';
		} else {
			$__finalCompiled .= '
			<ul class="p-nav-subList p-nav-list--depth' . $__templater->escape($__vars['depth']) . '">
		';
		}
		$__finalCompiled .= '
		';
		if ($__templater->isTraversable($__vars['children'])) {
			foreach ($__vars['children'] AS $__vars['child']) {
				$__finalCompiled .= '
			' . $__templater->callMacro(null, 'navigation_list_entry', array(
					'navigation' => $__vars['child']['record'],
					'children' => $__vars['child']['children'],
					'selectedTab' => $__vars['selectedTab'],
					'selectedLink' => $__vars['selectedLink'],
					'depth' => $__vars['depth'],
				), $__vars) . '
		';
			}
		}
		$__finalCompiled .= '
		</ul>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'navigation_list_entry' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'navigation' => '!',
		'children' => '!',
		'selectedTab' => '!',
		'selectedLink' => '!',
		'depth' => '1',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__vars['depth'] == 1) {
		$__finalCompiled .= '
		<li class="p-nav-section' . (($__vars['navigation']['navigation_id'] == $__vars['selectedTab']) ? ' is-active' : '') . '" data-navigation-id="' . $__templater->escape($__vars['navigation']['navigation_id']) . '">
			<div class="p-nav-sectionHeader">
				<a href="' . $__templater->func('link', array($__vars['navigation']['link'], ), true) . '" class="p-nav-sectionLink js-navSectionToggle">
					' . $__templater->fontAwesome($__templater->escape($__vars['navigation']['icon']) . ' fa-fw', array(
		)) . ' ' . $__templater->escape($__vars['navigation']['title']) . '
				</a>
				<a class="p-nav-sectionToggle js-navSectionToggle" role="button" tabindex="0" aria-label="' . $__templater->filter('Toggle expanded', array(array('for_attr', array()),), true) . '"></a>
			</div>
	';
	} else if ($__vars['depth'] == 2) {
		$__finalCompiled .= '
		<li class="' . ($__vars['children'] ? 'p-nav-subSection' : 'p-nav-el') . ' ' . (($__vars['navigation']['navigation_id'] == $__vars['selectedLink']) ? ' is-active' : '') . '" data-navigation-id="' . $__templater->escape($__vars['navigation']['navigation_id']) . '">
			';
		if ($__vars['children'] OR (!$__vars['navigation']['link'])) {
			$__finalCompiled .= '
				<span>
					';
			if ($__vars['navigation']['icon']) {
				$__finalCompiled .= $__templater->fontAwesome($__templater->escape($__vars['navigation']['icon']) . ' fa-fw', array(
				)) . ' ';
			}
			$__finalCompiled .= $__templater->escape($__vars['navigation']['title']) . '</span>
			';
		} else {
			$__finalCompiled .= '
				<a href="' . $__templater->func('link', array($__vars['navigation']['link'], ), true) . '">' . $__templater->escape($__vars['navigation']['title']) . '</a>
			';
		}
		$__finalCompiled .= '
	';
	} else {
		$__finalCompiled .= '
		<li class="p-nav-el ' . (($__vars['navigation']['navigation_id'] == $__vars['selectedLink']) ? ' is-active' : '') . '" data-navigation-id="' . $__templater->escape($__vars['navigation']['navigation_id']) . '">
			';
		if ($__vars['navigation']['link']) {
			$__finalCompiled .= '
				<a href="' . $__templater->func('link', array($__vars['navigation']['link'], ), true) . '">' . $__templater->escape($__vars['navigation']['title']) . '</a>
			';
		} else {
			$__finalCompiled .= '
				<span>' . $__templater->escape($__vars['navigation']['title']) . '</span>
			';
		}
		$__finalCompiled .= '
	';
	}
	$__finalCompiled .= '
		' . $__templater->callMacro(null, 'navigation_list', array(
		'navigation' => $__vars['navigation'],
		'children' => $__vars['children'],
		'selectedTab' => $__vars['selectedTab'],
		'selectedLink' => $__vars['selectedLink'],
		'depth' => ($__vars['depth'] + 1),
	), $__vars) . '
	</li>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<!DOCTYPE html>
<html id="XF" lang="' . $__templater->escape($__vars['xf']['language']['language_code']) . '" dir="' . $__templater->escape($__vars['xf']['language']['text_direction']) . '"
	class="has-no-js ' . ($__vars['template'] ? ('template-' . $__templater->escape($__vars['template'])) : '') . '" data-template="' . $__templater->escape($__vars['template']) . '"
	data-app="admin"
	data-cookie-prefix="' . $__templater->escape($__vars['xf']['cookie']['prefix']) . '"
	data-csrf="' . $__templater->filter($__templater->func('csrf_token', array(), false), array(array('escape', array('js', )),), true) . '"
	' . ($__vars['xf']['runJobs'] ? ' data-run-jobs=""' : '') . '>
<head>

	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

	<title>
		' . $__templater->func('page_title', array('%s | ' . $__vars['xf']['options']['boardTitle'] . ' - ' . 'Admin control panel', $__vars['xf']['options']['boardTitle'] . ' - ' . 'Admin control panel', null)) . '
	</title>
	';
	if ($__templater->isTraversable($__vars['head'])) {
		foreach ($__vars['head'] AS $__vars['headTag']) {
			$__finalCompiled .= '
		' . $__templater->escape($__vars['headTag']) . '
	';
		}
	}
	$__finalCompiled .= '

	' . $__templater->callMacro('public:helper_js_global', 'head', array(
		'app' => 'admin',
	), $__vars) . '
</head>
<body>

<!-- HEADER -->

<header class="p-header">
	<div class="p-header-buttons p-header-buttons--main">
		<a class="p-header-button p-header-button--nav"
			data-xf-click="off-canvas"
			data-menu="#js-nav"
			role="button"
			tabindex="0"
			aria-label="' . 'Menu' . '">
			' . $__templater->fontAwesome('fa-bars', array(
	)) . '
		</a>
		<a href="' . $__templater->func('link', array('index', ), true) . '" class="p-header-button" aria-label="' . $__templater->filter('Home', array(array('for_attr', array()),), true) . '">' . $__templater->fontAwesome('fa-home', array(
	)) . '</a>
		<a href="index.php" class="p-header-button p-header-button--title" target="_blank">' . $__templater->escape($__vars['xf']['options']['boardTitle']) . '</a>
	</div>

	<div class="p-header-buttons p-header-buttons--opposite">
		';
	if ($__templater->method($__vars['xf']['visitor'], 'hasAdminPermission', array('option', ))) {
		$__finalCompiled .= '
			<a class="p-header-button" data-xf-key="' . $__templater->filter('o', array(array('for_attr', array()),), true) . '" data-xf-click="menu" role="button" tabindex="0"
				aria-label="' . $__templater->filter('Options', array(array('for_attr', array()),), true) . '"
				aria-expanded="false"
				aria-haspopup="true">
				' . $__templater->fontAwesome('fa-cogs', array(
		)) . '
			</a>
			<div class="menu menu--structural menu--superWide" data-menu="menu" aria-hidden="true"
				data-href="' . $__templater->func('link', array('options/menu', ), true) . '"
				data-load-target=".js-optionGroupsMenuBody">
				<div class="menu-content js-optionGroupsMenuBody">
					<div class="menu-row">
						' . 'Loading' . $__vars['xf']['language']['ellipsis'] . '
					</div>
				</div>
			</div>
		';
	}
	$__finalCompiled .= '
		<a class="p-header-button" data-xf-key="' . $__templater->filter('/', array(array('for_attr', array()),), true) . '" data-xf-click="menu" role="button" tabindex="0"
			aria-label="' . $__templater->filter('Search', array(array('for_attr', array()),), true) . '"
			aria-expanded="false"
			aria-haspopup="true">
			' . $__templater->fontAwesome('fa-search', array(
	)) . '
		</a>
		<form action="' . $__templater->func('link', array('search/search', ), true) . '" class="menu menu--structural menu--veryWide" data-xf-init="admin-search" data-menu="menu" aria-hidden="true">
			<div class="menu-content">
				<h3 class="menu-header">' . 'Search' . $__vars['xf']['language']['ellipsis'] . '</h3>
				<div class="menu-row">
					<input type="text" name="q" autocomplete="off" class="input js-adminSearchInput" data-menu-autofocus="" />
				</div>
				<div class="menu-scroller p-quickSearchResultsWrapper js-adminSearchResultsWrapper">
					<div class="p-quickSearchResults js-adminSearchResults"></div>
				</div>
			</div>
		</form>
	</div>
</header>

<!-- BODY -->

<div class="p-body-container">
	<div class="p-body">
		<nav class="p-nav" id="js-nav" data-ocm-class="offCanvasMenu offCanvasMenu--adminNav" data-ocm-builder="acpNav">
			<div data-ocm-class="offCanvasMenu-backdrop" data-menu-close="true"></div>
			<div class="p-nav-inner" data-xf-init="admin-nav" data-ocm-class="offCanvasMenu-content">
				<div class="offCanvasMenu-shown offCanvasMenu-header">
					' . 'Menu' . '
					<a class="offCanvasMenu-closer" data-menu-close="true" role="button" tabindex="0" aria-label="' . $__templater->filter('Close', array(array('for_attr', array()),), true) . '"></a>
				</div>
				<div class="p-nav-content js-navSticky">
					' . $__templater->callMacro(null, 'navigation_list', array(
		'children' => $__vars['navigation'],
		'selectedTab' => $__vars['selectedNavTab'],
		'selectedLink' => $__vars['selectedNavLink'],
	), $__vars) . '
				</div>
				<div class="p-nav-tester js-navTester"></div>
			</div>
		</nav>

		<div class="p-main">
			<main class="p-main-inner">

				<!--XF:EXTRA_OUTPUT-->

				' . $__templater->callMacro('public:browser_warning_macros', 'javascript', array(), $__vars) . '
				' . $__templater->callMacro('public:browser_warning_macros', 'browser', array(), $__vars) . '

				';
	if ($__vars['upgradeCheck']) {
		$__finalCompiled .= '
					' . $__templater->callMacro('upgrade_check_macros', 'serious_errors', array(
			'upgradeCheck' => $__vars['upgradeCheck'],
		), $__vars) . '
				';
	}
	$__finalCompiled .= '

				';
	if ($__vars['upgradePending']) {
		$__finalCompiled .= '
					<div class="blockMessage blockMessage--important blockMessage--iconic">
						' . $__templater->includeTemplate('public:notice_upgrade_pending', $__vars) . '
					</div>
				';
	}
	$__finalCompiled .= '

				';
	if ($__vars['listenersDisabled']) {
		$__finalCompiled .= '
					<div class="blockMessage blockMessage--important blockMessage--iconic">
						' . 'Code event listeners and extensions have been disabled via config.php. This is designed as an emergency measure to allow you to regain access to the control panel if an add-on blocks access and disable it. This should not be used as a debugging tool.' . '
					</div>
				';
	}
	$__finalCompiled .= '

				';
	if ($__vars['mailDisabled']) {
		$__finalCompiled .= '
					<div class="blockMessage blockMessage--important blockMessage--iconic">
						' . 'Mail has been disabled via config.php. No outgoing mails will be sent to any users until the <code>$config[\'enableMail\'] = false;</code> line is removed from config.php.' . '
					</div>
				';
	}
	$__finalCompiled .= '

				';
	if (!$__templater->test($__vars['breadcrumbs'], 'empty', array())) {
		$__finalCompiled .= '
					<ul class="p-breadcrumbs">
						';
		if ($__templater->isTraversable($__vars['breadcrumbs'])) {
			foreach ($__vars['breadcrumbs'] AS $__vars['breadcrumb']) {
				$__finalCompiled .= '
							<li><a href="' . $__templater->escape($__vars['breadcrumb']['href']) . '">' . $__templater->escape($__vars['breadcrumb']['value']) . '</a></li>
						';
			}
		}
		$__finalCompiled .= '
					</ul>
				';
	}
	$__finalCompiled .= '

				';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
						';
	$__compilerTemp2 = '';
	$__compilerTemp2 .= '
								';
	if (!$__vars['noH1']) {
		$__compilerTemp2 .= '
									<h1 class="p-title-value">
										' . $__templater->func('page_h1', array($__vars['xf']['options']['boardTitle'])) . '
									</h1>
								';
	}
	$__compilerTemp2 .= '
								';
	$__compilerTemp3 = '';
	$__compilerTemp3 .= (isset($__templater->pageParams['pageAction']) ? $__templater->pageParams['pageAction'] : '');
	if (strlen(trim($__compilerTemp3)) > 0) {
		$__compilerTemp2 .= '
									<div class="p-title-pageAction">' . $__compilerTemp3 . '</div>
								';
	}
	$__compilerTemp2 .= '
							';
	if (strlen(trim($__compilerTemp2)) > 0) {
		$__compilerTemp1 .= '
							<div class="p-title ' . ($__vars['noH1'] ? 'p-title--noH1' : '') . '">
							' . $__compilerTemp2 . '
							</div>
						';
	}
	$__compilerTemp1 .= '

						';
	$__compilerTemp4 = '';
	$__compilerTemp4 .= $__templater->func('page_description');
	if (strlen(trim($__compilerTemp4)) > 0) {
		$__compilerTemp1 .= '
							<div class="p-description">' . $__compilerTemp4 . '</div>
						';
	}
	$__compilerTemp1 .= '
					';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
					<div class="p-main-header">
					' . $__compilerTemp1 . '
					</div>
				';
	}
	$__finalCompiled .= '

				<div class="p-content" id="content">
					' . $__templater->filter($__vars['content'], array(array('raw', array()),), true) . '
				</div>
			</main>
		</div>
	</div>
</div>

<footer class="p-footer">
	<div class="p-footer-row">
		<div class="p-footer-row-main">
			<a href="' . $__templater->func('link', array('login/logout', null, array('t' => $__templater->func('csrf_token', array(), false), ), ), true) . '">' . 'Log out' . '</a>
		</div>

		<div class="p-footer-row-opposite">
			<span class="p-footer-version">v' . $__templater->escape($__vars['xf']['version']) . '</span>
			';
	if ($__templater->method($__vars['xf']['visitor'], 'canChangeLanguage', array())) {
		$__finalCompiled .= '
				<a href="' . $__templater->func('link', array('languages/admin', ), true) . '" data-xf-click="overlay" data-xf-init="tooltip" title="' . $__templater->filter('Language chooser', array(array('for_attr', array()),), true) . '">
					' . $__templater->fontAwesome('fa-globe', array(
		)) . ' ' . $__templater->escape($__vars['xf']['language']['title']) . '
				</a>
			';
	}
	$__finalCompiled .= '
		</div>
	</div>

	';
	$__compilerTemp5 = '';
	$__compilerTemp5 .= '
			' . $__templater->func('copyright') . '
			' . '' . '
		';
	if (strlen(trim($__compilerTemp5)) > 0) {
		$__finalCompiled .= '
		<div class="p-footer-copyright">
		' . $__compilerTemp5 . '
		</div>
	';
	}
	$__finalCompiled .= '

	';
	$__compilerTemp6 = '';
	$__compilerTemp6 .= '
			' . $__templater->callMacro('public:debug_macros', 'debug', array(
		'controller' => $__vars['controller'],
		'action' => $__vars['actionMethod'],
		'template' => $__vars['template'],
	), $__vars) . '
		';
	if (strlen(trim($__compilerTemp6)) > 0) {
		$__finalCompiled .= '
		<div class="p-footer-debug">
		' . $__compilerTemp6 . '
		</div>
	';
	}
	$__finalCompiled .= '
</footer>

<div class="u-bottomFixer js-bottomFixTarget"></div>

' . $__templater->callMacro('admin:helper_js_global', 'body', array(
		'jsState' => $__vars['jsState'],
	), $__vars) . '

</body>
</html>

' . '
';
	return $__finalCompiled;
});