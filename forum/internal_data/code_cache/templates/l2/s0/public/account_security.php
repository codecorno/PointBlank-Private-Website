<?php
// FROM HASH: c4a74f7c2e93e55efb79ef6b1910693b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Senha e segurança');
	$__finalCompiled .= '

';
	$__templater->wrapTemplate('account_wrapper', $__vars);
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['xf']['visitor']['Option']['use_tfa']) {
		$__compilerTemp1 .= '
					' . 'Ativado (' . $__templater->filter($__vars['enabledTfaProviders'], array(array('join', array(', ', )),), true) . ')' . '
				';
	} else {
		$__compilerTemp1 .= '
					' . 'Desativado' . '
				';
	}
	$__compilerTemp2 = '';
	if ($__vars['hasPassword']) {
		$__compilerTemp2 .= '
				' . $__templater->formPasswordBoxRow(array(
			'name' => 'old_password',
			'autocomplete' => 'current-password',
			'autofocus' => 'autofocus',
		), array(
			'label' => 'Sua senha existente',
			'explain' => 'Por motivos de segurança, você deve verificar sua senha existente antes de poder definir uma nova senha.',
		)) . '

				' . $__templater->formPasswordBoxRow(array(
			'name' => 'password',
			'autocomplete' => 'new-password',
			'checkstrength' => 'true',
		), array(
			'label' => 'Nova senha',
		)) . '

				' . $__templater->formPasswordBoxRow(array(
			'name' => 'password_confirm',
			'autocomplete' => 'new-password',
		), array(
			'label' => 'Confirmar nova senha',
		)) . '
			';
	} else {
		$__compilerTemp2 .= '
				' . $__templater->formRow('
					' . 'Atualmente, sua conta não possui uma senha.' . ' <a href="' . $__templater->func('link', array('account/request-password', ), true) . '" data-xf-click="overlay">' . 'Request a password be emailed to you' . '</a>
				', array(
			'label' => 'Senha',
		)) . '
			';
	}
	$__compilerTemp3 = '';
	if ($__vars['hasPassword']) {
		$__compilerTemp3 .= '
			' . $__templater->formSubmitRow(array(
			'icon' => 'save',
		), array(
		)) . '
		';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formRow('

				' . $__compilerTemp1 . '
				' . $__templater->button('Alterar', array(
		'href' => $__templater->func('link', array('account/two-step', ), false),
		'class' => 'button--link',
	), '', array(
	)) . '
			', array(
		'rowtype' => 'button',
		'label' => 'Verificação em duas etapas',
	)) . '

			<hr class="formRowSep" />

			' . $__compilerTemp2 . '
		</div>
		' . $__compilerTemp3 . '
	</div>
', array(
		'action' => $__templater->func('link', array('account/security', ), false),
		'ajax' => 'true',
		'class' => 'block',
		'data-force-flash-message' => 'true',
	));
	return $__finalCompiled;
});