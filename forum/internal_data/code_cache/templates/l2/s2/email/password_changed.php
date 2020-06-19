<?php
// FROM HASH: 7399f4b060d06d410e1bdd76c5348780
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>
	' . '' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' - Password changed' . '
</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ',</p>

<p>Sua senha em ' . (((('<a href="' . $__templater->func('link', array('canonical:index', ), true)) . '">') . $__templater->escape($__vars['xf']['options']['boardTitle'])) . '</a>') . ' foi alterada recentemente. Se você fez essa alteração, você pode ignorar esta mensagem.</p>

<p>Se você não solicitou essa alteração, use o processo de senha perdida para gerar uma nova senha. Se você não conseguir fazer isso, entre em contato com um administrador.</p>

<p>Sua senha foi alterada pelo IP ' . $__templater->escape($__vars['ip']) . '.</p>';
	return $__finalCompiled;
});