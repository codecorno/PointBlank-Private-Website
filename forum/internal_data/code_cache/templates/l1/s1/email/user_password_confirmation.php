<?php
// FROM HASH: b8ec508479884757180b293697d0c019
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>
	' . '' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' - Password reset request' . '
</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ', in order to reset your password at ' . (((('<a href="' . $__templater->func('link', array('canonical:index', ), true)) . '">') . $__templater->escape($__vars['xf']['options']['boardTitle'])) . '</a>') . ', you need to click the button below. This will allow you to choose a new password.</p>' . '

<p><a href="' . $__templater->func('link', array('canonical:lost-password/confirm', $__vars['user'], array('c' => $__vars['confirmation']['confirmation_key'], ), ), true) . '" class="button">' . 'Reset password' . '</a></p>

';
	if ($__vars['isAdminReset']) {
		$__finalCompiled .= '
	<p>' . 'This password reset was requested by an admin of ' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' on your behalf.' . '</p>
';
	} else {
		$__finalCompiled .= '
	<p>' . 'If you did not request this email, you may safely ignore it.' . '</p>
';
	}
	return $__finalCompiled;
});