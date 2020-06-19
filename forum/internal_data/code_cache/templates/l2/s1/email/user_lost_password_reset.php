<?php
// FROM HASH: 6a6e960870eceea8d4ad7ada1ec3800b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>
	' . '' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' - Redefinição de senha' . '
</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ', sua senha em ' . (((('<a href="' . $__templater->func('link', array('canonical:index', ), true)) . '">') . $__templater->escape($__vars['xf']['options']['boardTitle'])) . '</a>') . ' foi redefinida. Agora você pode fazer login usando sua nova senha.</p>' . '

<p><a href="' . $__templater->func('link', array('canonical:index', ), true) . '" class="button">' . 'Entrar' . '</a></p>';
	return $__finalCompiled;
});