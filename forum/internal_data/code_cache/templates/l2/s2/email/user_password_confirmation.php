<?php
// FROM HASH: b8ec508479884757180b293697d0c019
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>
	' . '' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' - Password reset request' . '
</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ', para redefinir sua senha no ' . (((('<a href="' . $__templater->func('link', array('canonical:index', ), true)) . '">') . $__templater->escape($__vars['xf']['options']['boardTitle'])) . '</a>') . ', você precisa clicar no botão abaixo. Isso permitirá que você escolha uma nova senha.</p>' . '

<p><a href="' . $__templater->func('link', array('canonical:lost-password/confirm', $__vars['user'], array('c' => $__vars['confirmation']['confirmation_key'], ), ), true) . '" class="button">' . 'Resetar senha' . '</a></p>

';
	if ($__vars['isAdminReset']) {
		$__finalCompiled .= '
	<p>' . 'Esta redefinição de senha foi solicitada por um administrador do ' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' em seu nome.' . '</p>
';
	} else {
		$__finalCompiled .= '
	<p>' . 'Se você não solicitou este e-mail, você pode ignorá-lo com segurança.' . '</p>
';
	}
	return $__finalCompiled;
});