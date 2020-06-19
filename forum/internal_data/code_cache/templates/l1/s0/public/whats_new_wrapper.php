<?php
// FROM HASH: b5c31269a60fea13083cd23d0879ec06
return array('macros' => array('links' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'pageSelected' => '!',
		'baseClass' => '!',
		'selectedClass' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<a class="' . $__templater->escape($__vars['baseClass']) . ' ' . (($__vars['pageSelected'] == 'overview') ? $__templater->escape($__vars['selectedClass']) : '') . '" href="' . $__templater->func('link', array('whats-new', ), true) . '">' . 'What\'s new' . '</a>
	' . '
	<a class="' . $__templater->escape($__vars['baseClass']) . ' ' . (($__vars['pageSelected'] == 'new_thread') ? $__templater->escape($__vars['selectedClass']) : '') . '" href="' . $__templater->func('link', array('whats-new/posts', ), true) . '" rel="nofollow">' . 'New posts' . '</a>
	' . '
	';
	if ($__templater->method($__vars['xf']['visitor'], 'canViewProfilePosts', array())) {
		$__finalCompiled .= '
		<a class="' . $__templater->escape($__vars['baseClass']) . ' ' . (($__vars['pageSelected'] == 'new_profile_post') ? $__templater->escape($__vars['selectedClass']) : '') . '" href="' . $__templater->func('link', array('whats-new/profile-posts', ), true) . '" rel="nofollow">' . 'New profile posts' . '</a>
	';
	}
	$__finalCompiled .= '
	' . '
	';
	if ($__vars['xf']['options']['enableNewsFeed']) {
		$__finalCompiled .= '
		';
		if ($__vars['xf']['visitor']['user_id']) {
			$__finalCompiled .= '
			<a class="' . $__templater->escape($__vars['baseClass']) . ' ' . (($__vars['pageSelected'] == 'news_feed') ? $__templater->escape($__vars['selectedClass']) : '') . '" href="' . $__templater->func('link', array('whats-new/news-feed', ), true) . '" rel="nofollow">' . 'Your news feed' . '</a>
		';
		}
		$__finalCompiled .= '

		<a class="' . $__templater->escape($__vars['baseClass']) . ' ' . (($__vars['pageSelected'] == 'latest_activity') ? $__templater->escape($__vars['selectedClass']) : '') . '" href="' . $__templater->func('link', array('whats-new/latest-activity', ), true) . '" rel="nofollow">' . 'Latest activity' . '</a>
	';
	}
	$__finalCompiled .= '
	' . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['pageSelected'] != 'overview') {
		$__finalCompiled .= '
	';
		$__templater->setPageParam('head.' . 'metaNoindex', $__templater->preEscaped('<meta name="robots" content="noindex" />'));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->func('property', array('whatsNewNavStyle', ), false) == 'tabs') {
		$__finalCompiled .= '
	<div class="tabs tabs--standalone">
		<div class="hScroller" data-xf-init="h-scroller">
			<span class="hScroller-scroll">
				' . $__templater->callMacro(null, 'links', array(
			'pageSelected' => $__vars['pageSelected'],
			'baseClass' => 'tabs-tab',
			'selectedClass' => 'is-active',
		), $__vars) . '
			</span>
		</div>
	</div>
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->modifySideNavHtml(null, '
		<div class="block">
			<div class="block-container">
				<h3 class="block-header">' . 'What\'s new' . '</h3>
				<div class="block-body">
					' . $__templater->callMacro(null, 'links', array(
			'pageSelected' => $__vars['pageSelected'],
			'baseClass' => 'blockLink',
			'selectedClass' => 'is-selected',
		), $__vars) . '
				</div>
			</div>
		</div>

		' . $__templater->widgetPosition('whats_new_sidenav', array()) . '
	', 'replace');
		$__finalCompiled .= '
	';
		$__templater->setPageParam('sideNavTitle', 'What\'s new');
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

' . $__templater->filter($__vars['innerContent'], array(array('raw', array()),), true) . '

';
	return $__finalCompiled;
});