<?php
// FROM HASH: c1c6f62979bd3c293fa1ae116e26dfba
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>
	' . 'Conta aprovada no ' . $__templater->escape($__vars['xf']['options']['boardTitle']) . '' . '
</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ', a conta que você registrou no ' . (((('<a href="' . $__templater->func('link', array('canonical:index', ), true)) . '">') . $__templater->escape($__vars['xf']['options']['boardTitle'])) . '</a>') . ' foi aprovada. Você pode visitar nosso site como um membro registrado. </p>' . '

<h2>' . '<a href="' . $__templater->func('link', array('canonical:index', ), true) . '">Visitar ' . $__templater->escape($__vars['xf']['options']['boardTitle']) . '</a>' . '</h2>';
	return $__finalCompiled;
});