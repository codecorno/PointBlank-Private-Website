<?php
// FROM HASH: 7fe42d9e26ffce7b5691e135aa0dc1ba
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>' . '' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' - Login verification' . '</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ',</p>

<p>Para concluir o login em sua conta (ou para concluir a configuração de verificação em duas etapas) em ' . (((('<a href="' . $__templater->func('link', array('canonical:index', ), true)) . '">') . $__templater->escape($__vars['xf']['options']['boardTitle'])) . '</a>') . ', você deve digitar o seguinte código:</p>' . '

<h2>' . $__templater->escape($__vars['code']) . '</h2>

' . '<p>Este código é válido por 15 minutos.</p>

<p>O login foi solicitado pelo IP ' . $__templater->escape($__vars['ip']) . '. Se você não iniciou este pedido, você deve alterar sua senha urgentemente.</p>';
	return $__finalCompiled;
});