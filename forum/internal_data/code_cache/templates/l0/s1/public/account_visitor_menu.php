<?php
// FROM HASH: b953a14d72c911d5bf8ae97723b0a7a9
return array('macros' => array('visitor_panel_row' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(), $__arguments, $__vars);
	$__finalCompiled .= '
		<div class="contentRow">
			<div class="contentRow-figure">
				<span class="avatarWrapper">
					' . $__templater->func('avatar', array($__vars['xf']['visitor'], 'm', false, array(
		'href' => '',
		'notooltip' => 'true',
	))) . '
					';
	if ($__templater->method($__vars['xf']['visitor'], 'canUploadAvatar', array())) {
		$__finalCompiled .= '
						<a class="avatarWrapper-update" href="' . $__templater->func('link', array('account/avatar', ), true) . '" data-xf-click="overlay"><span>' . 'Edit' . '</span></a>
					';
	}
	$__finalCompiled .= '
				</span>
			</div>
			<div class="contentRow-main">
				<h3 class="contentRow-header">' . $__templater->func('username_link', array($__vars['xf']['visitor'], true, array(
		'notooltip' => 'true',
	))) . '</h3>
				<div class="contentRow-lesser">
					' . $__templater->func('user_title', array($__vars['xf']['visitor'], false, array(
	))) . '
				</div>

				<div class="contentRow-minor">
					' . '
					<dl class="pairs pairs--justified fauxBlockLink">
						<dt>' . 'Messages' . '</dt>
						<dd>
							<a href="' . $__templater->func('link', array('search/member', null, array('user_id' => $__vars['xf']['visitor']['user_id'], ), ), true) . '" class="fauxBlockLink-linkRow u-concealed">
								' . $__templater->filter($__vars['xf']['visitor']['message_count'], array(array('number', array()),), true) . '
							</a>
						</dd>
					</dl>
					' . '
					<dl class="pairs pairs--justified fauxBlockLink">
						<dt>' . 'Reaction score' . '</dt>
						<dd>
							<a href="' . $__templater->func('link', array('account/reactions', ), true) . '" class="fauxBlockLink-linkRow u-concealed">
								' . $__templater->filter($__vars['xf']['visitor']['reaction_score'], array(array('number', array()),), true) . '
							</a>
						</dd>
					</dl>
					' . '
					';
	if ($__vars['xf']['options']['enableTrophies']) {
		$__finalCompiled .= '
						<dl class="pairs pairs--justified fauxBlockLink">
							<dt>' . 'Trophy points' . '</dt>
							<dd>
								<a href="' . $__templater->func('link', array('members/trophies', $__vars['xf']['visitor'], ), true) . '" data-xf-click="overlay" class="fauxBlockLink-linkRow u-concealed">
									' . $__templater->filter($__vars['xf']['visitor']['trophy_points'], array(array('number', array()),), true) . '
								</a>
							</dd>
						</dl>
					';
	}
	$__finalCompiled .= '
				</div>
			</div>
		</div>
	';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
				';
	if ($__templater->method($__vars['xf']['visitor'], 'canViewBookmarks', array())) {
		$__compilerTemp1 .= '
					<a href="' . $__templater->func('link', array('account/bookmarks', ), true) . '" class="tabs-tab" role="tab" tabindex="0" aria-controls="' . $__templater->func('unique_id', array('accountMenuBookmarks', ), true) . '">' . 'Bookmarks' . '</a>
				';
	}
	$__compilerTemp1 .= '
				' . '
			';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
	<h4 class="menu-tabHeader tabs" data-xf-init="tabs" role="tablist">
		<span class="hScroller" data-xf-init="h-scroller">
			<span class="hScroller-scroll">
				<a href="' . $__templater->func('link', array('account', ), true) . '" class="tabs-tab is-active" role="tab" tabindex="0" aria-controls="' . $__templater->func('unique_id', array('accountMenu', ), true) . '">' . 'Your account' . '</a>
			' . $__compilerTemp1 . '
			</span>
		</span>
	</h4>
	';
		$__vars['hasTabs'] = true;
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	$__compilerTemp2 = '';
	if ($__vars['xf']['options']['enableNewsFeed']) {
		$__compilerTemp2 .= '
			<li><a href="' . $__templater->func('link', array('whats-new/news-feed', ), true) . '" class="menu-linkRow">' . 'News feed' . '</a></li>
		';
	}
	$__compilerTemp3 = '';
	if ($__templater->method($__vars['xf']['visitor'], 'canEditSignature', array())) {
		$__compilerTemp3 .= '
			<li><a href="' . $__templater->func('link', array('account/signature', ), true) . '" class="menu-linkRow">' . 'Signature' . '</a></li>
		';
	}
	$__compilerTemp4 = '';
	if ($__vars['xf']['app']['userUpgradeCount']) {
		$__compilerTemp4 .= '
			<li><a href="' . $__templater->func('link', array('account/upgrades', ), true) . '" class="menu-linkRow">' . 'Account upgrades' . '</a></li>
		';
	}
	$__compilerTemp5 = '';
	if ($__vars['xf']['app']['connectedAccountCount']) {
		$__compilerTemp5 .= '
			<li><a href="' . $__templater->func('link', array('account/connected-accounts', ), true) . '" class="menu-linkRow">' . 'Connected accounts' . '</a></li>
		';
	}
	$__compilerTemp6 = '';
	if ($__templater->method($__vars['xf']['visitor'], 'canPostOnProfile', array())) {
		$__compilerTemp6 .= '
		' . $__templater->form('

			<span class="u-srOnly" id="ctrl_message">' . 'Update your status' . $__vars['xf']['language']['label_separator'] . '</span>
			' . $__templater->formTextArea(array(
			'name' => 'message',
			'rows' => '1',
			'autosize' => 'true',
			'maxlength' => $__vars['xf']['options']['profilePostMaxLength'],
			'placeholder' => 'Update your status' . $__vars['xf']['language']['ellipsis'],
			'data-xf-init' => 'focus-trigger user-mentioner emoji-completer',
			'data-display' => '< :next',
			'aria-labelledby' => 'ctrl_message',
		)) . '
			<div class="u-hidden u-hidden--transition u-inputSpacer">
				' . $__templater->button('Post', array(
			'type' => 'submit',
			'class' => 'button--primary',
			'icon' => 'reply',
		), '', array(
		)) . '
			</div>
		', array(
			'action' => $__templater->func('link', array('members/post', $__vars['xf']['visitor'], ), false),
			'ajax' => 'true',
			'data-redirect' => 'off',
			'data-reset-complete' => 'true',
			'data-no-auto-focus' => 'true',
			'class' => 'menu-footer',
		)) . '
	';
	}
	$__vars['accountHtml'] = $__templater->preEscaped('
	<div class="menu-row menu-row--alt">
		' . $__templater->callMacro(null, 'visitor_panel_row', array(), $__vars) . '
	</div>

	' . '

	' . '
	<hr class="menu-separator menu-separator--hard" />

	<ul class="listPlain listColumns listColumns--narrow listColumns--together">
		' . '
		' . $__compilerTemp2 . '
		<li><a href="' . $__templater->func('link', array('search/member', null, array('user_id' => $__vars['xf']['visitor']['user_id'], ), ), true) . '" class="menu-linkRow">' . 'Your content' . '</a></li>
		<li><a href="' . $__templater->func('link', array('account/reactions', ), true) . '" class="menu-linkRow">' . 'Reactions received' . '</a></li>
		' . '
	</ul>

	' . '
	<hr class="menu-separator" />

	<ul class="listPlain listColumns listColumns--narrow listColumns--together">
		' . '
		<li><a href="' . $__templater->func('link', array('account/account-details', ), true) . '" class="menu-linkRow">' . 'Account details' . '</a></li>
		<li><a href="' . $__templater->func('link', array('account/security', ), true) . '" class="menu-linkRow">' . 'Password and security' . '</a></li>
		<li><a href="' . $__templater->func('link', array('account/privacy', ), true) . '" class="menu-linkRow">' . 'Privacy' . '</a></li>
		<li><a href="' . $__templater->func('link', array('account/preferences', ), true) . '" class="menu-linkRow">' . 'Preferences' . '</a></li>
		' . $__compilerTemp3 . '
		' . $__compilerTemp4 . '
		' . $__compilerTemp5 . '
		<li><a href="' . $__templater->func('link', array('account/following', ), true) . '" class="menu-linkRow">' . 'Following' . '</a></li>
		<li><a href="' . $__templater->func('link', array('account/ignored', ), true) . '" class="menu-linkRow">' . 'Ignoring' . '</a></li>
		' . '
	</ul>

	' . '
	<hr class="menu-separator" />

	<a href="' . $__templater->func('link', array('logout', null, array('t' => $__templater->func('csrf_token', array(), false), ), ), true) . '" class="menu-linkRow">' . 'Log out' . '</a>

	' . $__compilerTemp6 . '
');
	$__finalCompiled .= '

';
	if ($__vars['hasTabs']) {
		$__finalCompiled .= '
	<ul class="tabPanes">
		<li class="is-active" role="tabpanel" id="' . $__templater->func('unique_id', array('accountMenu', ), true) . '">
			' . $__templater->filter($__vars['accountHtml'], array(array('raw', array()),), true) . '
		</li>
		';
		if ($__templater->method($__vars['xf']['visitor'], 'canViewBookmarks', array())) {
			$__finalCompiled .= '
			<li role="tabpanel" id="' . $__templater->func('unique_id', array('accountMenuBookmarks', ), true) . '"
				data-href="' . $__templater->func('link', array('account/bookmarks-popup', ), true) . '"
				data-load-target=".js-bookmarksMenuBody">
				<div class="js-bookmarksMenuBody">
					<div class="menu-row">' . 'Loading' . $__vars['xf']['language']['ellipsis'] . '</div>
				</div>
				<div class="menu-footer menu-footer--close">
					<a href="' . $__templater->func('link', array('account/bookmarks', ), true) . '">' . 'Show all' . $__vars['xf']['language']['ellipsis'] . '</a>
				</div>
			</li>
		';
		}
		$__finalCompiled .= '
		' . '
	</ul>
';
	} else {
		$__finalCompiled .= '
	' . $__templater->filter($__vars['accountHtml'], array(array('raw', array()),), true) . '
';
	}
	return $__finalCompiled;
});