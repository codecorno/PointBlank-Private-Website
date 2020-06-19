<?php
// FROM HASH: f62cdfad05dce9f270f21cb52a4124a8
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__compilerTemp1 = '';
	if ($__templater->method($__vars['xf']['visitor'], 'canViewBookmarks', array())) {
		$__compilerTemp1 .= '
					<a class="blockLink ' . (($__vars['pageSelected'] == 'bookmarks') ? 'is-selected' : '') . '" href="' . $__templater->func('link', array('account/bookmarks', ), true) . '">
						' . 'Bookmarks' . '
					</a>
				';
	}
	$__compilerTemp2 = '';
	if ($__templater->method($__vars['xf']['visitor'], 'canEditSignature', array())) {
		$__compilerTemp2 .= '
					<a class="blockLink ' . (($__vars['pageSelected'] == 'signature') ? 'is-selected' : '') . '" href="' . $__templater->func('link', array('account/signature', ), true) . '">
						' . 'Assinatura' . '
					</a>
				';
	}
	$__compilerTemp3 = '';
	if ($__vars['xf']['app']['userUpgradeCount']) {
		$__compilerTemp3 .= '
					<a class="blockLink ' . (($__vars['pageSelected'] == 'upgrades') ? 'is-selected' : '') . '" href="' . $__templater->func('link', array('account/upgrades', ), true) . '">
						' . 'Account upgrades' . '
					</a>
				';
	}
	$__compilerTemp4 = '';
	if ($__vars['xf']['app']['connectedAccountCount']) {
		$__compilerTemp4 .= '
					<a class="blockLink ' . (($__vars['pageSelected'] == 'connected_account') ? 'is-selected' : '') . '" href="' . $__templater->func('link', array('account/connected-accounts', ), true) . '">
						' . 'Contas conectadas' . '
					</a>
				';
	}
	$__templater->modifySideNavHtml(null, '
	<div class="block">
		<div class="block-container">
			<h3 class="block-header">' . 'Sua conta' . '</h3>
			<div class="block-body">
				' . '
				<a class="blockLink" href="' . $__templater->func('link', array('members', $__vars['xf']['visitor'], ), true) . '">' . 'Seu perfil' . '</a>
				<a class="blockLink ' . (($__vars['pageSelected'] == 'alerts') ? 'is-selected' : '') . '" href="' . $__templater->func('link', array('account/alerts', ), true) . '">
					' . 'Alertas' . '
				</a>
				<a class="blockLink ' . (($__vars['pageSelected'] == 'reactions') ? 'is-selected' : '') . '" href="' . $__templater->func('link', array('account/reactions', ), true) . '">
					' . 'Reactions received' . '
				</a>
				' . $__compilerTemp1 . '
				' . '
			</div>

			<h3 class="block-minorHeader">' . 'Configurações' . '</h3>
			<div class="block-body">
				' . '
				<a class="blockLink ' . (($__vars['pageSelected'] == 'account_details') ? 'is-selected' : '') . '" href="' . $__templater->func('link', array('account/account-details', ), true) . '">
					' . 'Detalhes da conta' . '
				</a>
				<a class="blockLink ' . (($__vars['pageSelected'] == 'security') ? 'is-selected' : '') . '" href="' . $__templater->func('link', array('account/security', ), true) . '">
					' . 'Senha e segurança' . '
				</a>
				<a class="blockLink ' . (($__vars['pageSelected'] == 'privacy') ? 'is-selected' : '') . '" href="' . $__templater->func('link', array('account/privacy', ), true) . '">
					' . 'Privacidade' . '
				</a>
				<a class="blockLink ' . (($__vars['pageSelected'] == 'preferences') ? 'is-selected' : '') . '" href="' . $__templater->func('link', array('account/preferences', ), true) . '">
					' . 'Preferências' . '
				</a>
				' . $__compilerTemp2 . '
				' . $__compilerTemp3 . '
				' . $__compilerTemp4 . '
				<a class="blockLink ' . (($__vars['pageSelected'] == 'following') ? 'is-selected' : '') . '" href="' . $__templater->func('link', array('account/following', ), true) . '">
					' . 'Seguindo' . '
				</a>
				<a class="blockLink ' . (($__vars['pageSelected'] == 'ignored') ? 'is-selected' : '') . '" href="' . $__templater->func('link', array('account/ignored', ), true) . '">
					' . 'Ignorando' . '
				</a>
				' . '
			</div>
		</div>
	</div>

	<div class="block">
		<div class="block-container">
			<div class="block-body">
				<a href="' . $__templater->func('link', array('logout', null, array('t' => $__templater->func('csrf_token', array(), false), ), ), true) . '" class="blockLink">' . 'Sair' . '</a>
			</div>
		</div>
	</div>
', 'replace');
	$__finalCompiled .= '
';
	$__templater->setPageParam('sideNavTitle', 'Sua conta');
	$__finalCompiled .= '

';
	$__templater->breadcrumb($__templater->preEscaped('Sua conta'), $__templater->func('link', array('account', ), false), array(
	));
	$__finalCompiled .= '

' . $__templater->filter($__vars['innerContent'], array(array('raw', array()),), true);
	return $__finalCompiled;
});