<?php
// FROM HASH: fd73d72ed41d0e1fb7addbf2721170aa
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>
	' . '' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' - Email changed' . '
</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ',</p>

<p>Seu e-mail no ' . (((('<a href="' . $__templater->func('link', array('canonical:index', ), true)) . '">') . $__templater->escape($__vars['xf']['options']['boardTitle'])) . '</a>') . ' foi alterado recentemente para ' . $__templater->escape($__vars['newEmail']) . '. Se você fez essa alteração, você pode ignorar essa mensagem.</p>

<p>Se você não solicitou essa alteração, faça o login e altere sua senha e endereço de e-mail. Se você não conseguir fazer isso, entre em contato com um administrador.</p>

<p>O seu email foi alterado pelo IP ' . $__templater->escape($__vars['ip']) . '.</p>';
	return $__finalCompiled;
});