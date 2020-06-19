<?php
// FROM HASH: 6a6e960870eceea8d4ad7ada1ec3800b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>
	' . '' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' - Password reset' . '
</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ', your password at ' . (((('<a href="' . $__templater->func('link', array('canonical:index', ), true)) . '">') . $__templater->escape($__vars['xf']['options']['boardTitle'])) . '</a>') . ' has been reset. You may now log in using your new password.</p>' . '

<p><a href="' . $__templater->func('link', array('canonical:index', ), true) . '" class="button">' . 'Log in' . '</a></p>';
	return $__finalCompiled;
});